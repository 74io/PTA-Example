<?php
class PtBuild extends CApplicationComponent
{
	
	public $currentCohortId;
	public $_cappedPointScoreBuilt=array();
	/**
	 * @var An array of cohorts in which KS2 English Maths and Science results are expected to exist
	 * and thus the average is calculated by dividing by 3. This may change. Set in main config file
	 */
	public $ks2EngMathsSciCohorts;
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$cohort= Cohort::getCurrentCohort();
		$this->currentCohortId = $cohort['id'];
	}
	
	/**
	 * @see CApplicationComponent::init()
	 */
	public function init(){}

	
	/**
	 * Builds the data set for the relevant MIS
	 * @return bool
	 */
	public function buildCoreData()
	{
		$defaultCohort = Yii::app()->controller->schoolSetUp ['defaultCohort'];
		//Validate the cohort
		if ($this->currentCohortId != $defaultCohort)
		{
			$message = 'Todays date is not between the start and end dates of the default cohort.';
			Yii::app()->eventLog->log( "warning", PtEventLog::BUILD_1, $message );
			return false;
		}
		//Validate that setup is complete
		if (! Yii::app()->common->coreDatasetUpIsComplete)
		{
			Yii::app()->eventLog->log( "warning", PtEventLog::BUILD_1, 'The core data could not be built as setup is incomplete.' );
			return false;
		}
		
		/**
		 * Validate KS2 fields. NOTE this may need to change if we discover ks2 fields differently on SIMS
		 */
		if(!PtMisFactory::mis()->ks2FieldsAreValid)
		return false;
		
		
		/**
		 * Begin building of core data
		 */
		$lastInsertId = Yii::app()->eventLog->log( "info", PtEventLog::BUILD_4, "Building core data..." );
		
		if(PtMisFactory::mis()->buildCoreData($this->currentCohortId))
		{
			$this->deleteObsoletePupils();
			//Delete pupils from the subjectdata table who now no longer have entries in the setdata table
			$this->cleanSubjectDataTable();
			$this->cleanExcludedPupils();
			$this->cleanExcludedSets();
			
			//Log stuff
			Yii::app()->eventLog->deleteEvent( $lastInsertId ); 
			Yii::app()->eventLog->log('success',PtEventLog::BUILD_1,'The core data has been built.');
			return true;
		} else
		{
			Yii::app()->eventLog->deleteEvent( $lastInsertId );
			return false;
		}
	}
	
	/**
	 * Builds the data set for a specific field.
	 * @param FieldMapping $object An active record object
	 * @return void
	 */
	public function buildSubjectDataForFieldMapping($object)
	{
		$this->deleteSubjectDataForFieldMapping($object);
		PtMisFactory::mis()->buildSubjectDataForFieldMapping( $object, $this->currentCohortId ); 
	}
	
	/**
	 * Builds the field mapping for a specific subject. This is commonly called when a new subject is created
	 * @param Subject $object An active record object
	 */
	
	public function buildSubjectDataForSubject($object)
	{
		PtMisFactory::mis()->buildSubjectDataForSubject( $object, $this->currentCohortId ); 
	}
	
	/**
	 * Deletes a specific field mapping id from the subjectdata table and the ks4meta
	 * @param object $object The FieldMapping object 
	 * @return void
	 */
	public function deleteSubjectDataForFieldMapping($object)
	{
		$sql = "DELETE FROM subjectdata WHERE fieldmapping_id = '{$object->id}'";
		Yii::app()->db->createCommand( $sql )->execute();
	}
	
	/**
	 * Deletes a subject from the subjectdata table
	 * @param object $object A Subject object
	 * @return void
	 */
	public function deleteSubjectData($object)
	{
		$sql = "DELETE FROM subjectdata WHERE subjectmapping_id = '{$object->id}'";
		$command = Yii::app()->db->createCommand($sql)->execute();
	}
	
	
	/**
	 * Returns array (true) if the data set has been built today and false if not
	 * @return boolean
	 */
	public function getCoreDataBuiltToday()
	{
		$sql = "SELECT id FROM eventlog WHERE TO_DAYS(NOW())-TO_DAYS(date_time)=0
		AND level='success' AND category='" . PtEventLog::BUILD_1 . "'";
		$command = Yii::app()->db->createCommand( $sql );
		return ( bool ) $command->queryRow();
	}
	
	/**
	 * Returns the data and time the core data was last built
	 */
	public function getCoreDataLastBuilt()
	{
		$sql = "SELECT date_time FROM eventlog 
		WHERE level='success' 
		AND category='".PtEventLog::BUILD_1."'
		ORDER BY id DESC LIMIT 1";
		$command = Yii::app()->db->createCommand( $sql );
		$value = $command->queryScalar();
		if($value)
		return Yii::app()->dateFormatter->format('dd-MM-yyyy HH:mm:ss',$value);
		
		else
		return false;
	}
	
	/**
	 * Returns true if core data has been built for the current cohort
	 */
	public function getSetUpIsComplete()
	{
		$cohortId = Yii::app()->controller->schoolSetUp['defaultCohort'];
		
		$sql="SELECT COUNT(*) FROM pupil WHERE cohort_id='$cohortId'";
		$command = Yii::app()->db->createCommand( $sql );
		$value1 = $command->queryScalar();
		
		$sql="SELECT COUNT(*) FROM setdata WHERE cohort_id='$cohortId'";
		$command = Yii::app()->db->createCommand( $sql );
		$value2 = $command->queryScalar();
		
		if(!$value1 || !$value2){
		return false;
		}
		return true;
	}
	
	
	/**
	 * Returns true if the data set is currently being built or false if not
	 * @return boolean
	 */
	public function getBuilding($category)
	{
		$sql = "SELECT * FROM eventlog WHERE category='$category'";
		$command = Yii::app()->db->createCommand( $sql );
		return ( bool ) $command->queryRow();
	}
	
	/**
	 * Generic method that determines which meta data to build
	 * @param object $object The object that called the method. In this case currently either Fieldmapping or Subject
	 */
	public function buildMetaData($object)
	{
		$class=get_class($object);
		

		if($class=="FieldMapping"){
			$keyStage = Yii::app()->common->getKeyStageForYearGroup($object->year_group);
			if($keyStage==4)
			$this->buildKs4Meta($object,$keyStage);
		}
		
		if($class=="Subject"){
			$keyStage = $object->key_stage;
			if($keyStage==4)
			$this->buildKs4Meta($object,$keyStage);
		}
	}
	
	/**
	 * Builds the KS4 meta data necessary to extract data from.
	 * Note. This table does not care about included pupils and subjects it simply builds data for all.
	 * It is only fully re-built if FIELDMAPPING_4=Field mapping rebuild (i.e. user clicks on rebuild on a DCP or Target)
	 * or SUBJECT_1 = A new subject is created. It gets a partial rebuild when a qualification is changed.
	 * @param object $object The object that called the method. Here it is either FieldMapping or Subject
	 * @return void
	 */
	public function buildKs4Meta($object,$keyStage)
	{

		$class=get_class($object);
		
		if($class=="Subject"){
			$yearGroups = Yii::app()->common->getYearGroupsForKeyStage($keyStage);
			$yearGroupsString  = implode(',',$yearGroups);	
		}

		$lastInsertId = Yii::app()->eventLog->log( "info", PtEventLog::BUILD_8, "Building KS4 meta data..." );
		$this->deleteKs4Meta($object);

		
		$sql="INSERT INTO ks4meta (
			cohort_id,
			subjectmapping_id,
			pupil_id,
			fieldmapping_id,
            astar_a,
            astar_c,
            astar_g,
            result,
            standardised_points
			)
			SELECT
			'$this->currentCohortId', 
			subjectmapping_id, 
			subjectdata.pupil_id, 
			subjectdata.fieldmapping_id,
			'0',
			'0',
			'0',
			subjectdata.result,
			'0'
			FROM subjectdata 
			INNER JOIN pupil
			USING(cohort_id,pupil_id) 
			WHERE pupil.cohort_id='$this->currentCohortId'";
		
			if($class=="Subject")
			$sql .= "AND pupil.year IN ($yearGroupsString)";
			if($class=="FieldMapping")
			$sql.=" AND pupil.year = '{$object->year_group}'";
			
			if($class=="Subject")
			$sql .= " AND subjectdata.subjectmapping_id='{$object->id}'";
			if($class=="FieldMapping")
			$sql .= " AND subjectdata.fieldmapping_id='{$object->id}'";
		
		
		$command = Yii::app()->db->createCommand( $sql );
		$command->execute();
		

		$sql="
			UPDATE ks4meta t1
			INNER JOIN subjectmapping t2 ON(t2.id = t1.subjectmapping_id)
			INNER JOIN lookup.ks4pointscore t3 ON (t3.qualification = t2.qualification)
			SET astar_a=IF((t3.score >=52),1,0), 
			astar_c = IF((t3.score >=40),1,0), 
			astar_g = IF((t3.score >=16),1,0), 
			standardised_points = t3.score 
			WHERE t1.cohort_id='$this->currentCohortId'
			AND t1.result = t3.result";
		
			if($class=="Subject")
			$sql .= " AND t1.subjectmapping_id='{$object->id}'";
			if($class=="FieldMapping")
			$sql .= " AND t1.fieldmapping_id='{$object->id}'";

			#AND t1.fieldmapping_id='40'
			#AND t1.subjectmapping_id = ";

		
		$command = Yii::app()->db->createCommand( $sql );
		$command->execute();
		Yii::app()->eventLog->deleteEvent( $lastInsertId );
		
		
		if($class=="Subject")
		$message = "KS4 meta data has been built for subject ID " . $object->id . ".";
		if($class=="FieldMapping")
		$message = "KS4 meta data has been built for DCP/Target ID " . $object->id . ".";
		Yii::app()->eventLog->log( "success", PtEventLog::BUILD_5, $message);
		
		
		/**
		 * Clear the cache
		 */
		Yii::app()->dataCache->deleteDataCache($this->currentCohortId,$keyStage);
		return true;
	}
	
	/**
	 * Creates subjects automatically based upon the subjects contained in the setdata table
	 */
	public function autoCreateSubjects()
	{
		$keyStages = Yii::app()->common->keyStages;
		$defaultCohort = Yii::app()->controller->schoolSetUp['defaultCohort'];
		
		$sql="SELECT DISTINCT(mapped_subject), subject FROM setdata WHERE cohort_id='$defaultCohort'";
		$command=Yii::app()->db->createCommand($sql);
		$rows=$command->queryAll(); 
		$count=1; //Using count here we can attach the subject id to any duplicated real dept name
		
		foreach($keyStages as $keyStage){
			$cachedSubject=array();
			$qualification = ($keyStage==4) ? 'GCSE' : 'None';
			$equivalent = ($keyStage==4) ? 1 : 0;
			foreach($rows as $row)
			{
				//Populate real dept name if left blank
				$subject = ($row['subject']) ? $row['subject'] : $row['mapped_subject'];
				//Generate unique name if real dept name is repeated
				$subject = (in_array($subject,$cachedSubject)) ? $subject.$count : $subject;
				
				$sql="INSERT INTO subjectmapping (cohort_id,key_stage,mapped_subject,subject,qualification,volume,equivalent,type,include)
						VALUES(:defaultCohort,:keyStage,:mappedSubject,:subject,:qualification,'1.0',:equivalent,'None','1')";
				
				$command=Yii::app()->db->createCommand($sql);
				$command->bindParam(':defaultCohort', $defaultCohort,PDO::PARAM_STR);
				$command->bindParam(':keyStage', $keyStage,PDO::PARAM_INT);
				$command->bindParam(':mappedSubject', $row['mapped_subject'],PDO::PARAM_STR);
				$command->bindParam(':subject', $subject,PDO::PARAM_STR);
				$command->bindParam(':qualification', $qualification,PDO::PARAM_STR);
				$command->bindParam(':equivalent', $equivalent,PDO::PARAM_STR);

				$command->execute(); 
				
				$cachedSubject[]=$row['subject'];
				$count++;
			}
		}
	}
	
	/**
	 * Deletes pupils from the subjectdata and ks4meta tables who are not present in the pupil table.
	 * Any table that stores data for pupils will need to be added here in the future.
	 * @return void
	 */
	public function deleteObsoletePupils()
	{
		//Delete pupils from the excluded pupils table who no longer exist in the pupil table.
		//Note we use subject mapping table here to get the cohort_id
		$sql="DELETE excludedpupils FROM excludedpupils
		INNER JOIN subjectmapping 
		ON(subjectmapping.id=`excludedpupils`.subjectmapping_id)
		LEFT JOIN pupil ON(excludedpupils.pupil_id = pupil.pupil_id AND pupil.cohort_id = subjectmapping.cohort_id) 
		WHERE subjectmapping.cohort_id='".$this->currentCohortId."'
		AND pupil.pupil_id IS NULL";
		Yii::app()->db->createCommand($sql)->execute();
		
		//Delete pupils from the subject data table who are longer in the pupil table
		$sql="DELETE subjectdata FROM subjectdata
		WHERE NOT EXISTS (
		SELECT * FROM pupil WHERE pupil.cohort_id=subjectdata.cohort_id AND pupil.pupil_id=subjectdata.pupil_id AND pupil.cohort_id='$this->currentCohortId'
		)
		AND subjectdata.cohort_id='$this->currentCohortId'";
		Yii::app()->db->createCommand($sql)->execute();

		//Delete pupils from the ks4meta table who are no longer in the pupil table
		$sql="DELETE ks4meta FROM ks4meta
		WHERE NOT EXISTS (
		SELECT * FROM pupil WHERE pupil.cohort_id=ks4meta.cohort_id AND pupil.pupil_id=ks4meta.pupil_id AND pupil.cohort_id='$this->currentCohortId'
		)
		AND ks4meta.cohort_id='$this->currentCohortId'";
		Yii::app()->db->createCommand($sql)->execute();
	}
	
	/**
	 * Here we need to remove pupils from the subjectdata table who are still on the system, but do not
	 * have entries in the setdata table
	 * NOTE. The queries below were faster then using a sub select in testing
	 * @return void
	 */
	public function cleanSubjectDataTable()
	{
		//Fetch pupils who are in the pupil table, but not in the setdata table
		$sql="SELECT pupil.pupil_id FROM pupil LEFT JOIN setdata
		ON(pupil.cohort_id=setdata.cohort_id
		AND pupil.pupil_id = setdata.pupil_id)
		WHERE pupil.cohort_id='".$this->currentCohortId."'
		AND setdata.pupil_id IS NULL";
		$pupils = Yii::app()->db->createCommand($sql)->queryColumn();
		
		//If pupils exist delete them from the subjectdata table
		if($pupils){
		$inClause="'".implode("','",$pupils)."'";
		$sql="DELETE FROM subjectdata WHERE cohort_id='".$this->currentCohortId."'
		AND pupil_id IN ($inClause)";
		Yii::app()->db->createCommand($sql)->execute();
		
		}	
	}
	
	/**
	 * Deletes data from the ks4meta table
	 * @param object $object The object calling this method. Ususally FieldMapping or Subject
	 * @return void
	 */
	public function deleteKs4Meta($object)
	{
		$class=get_class($object);
		

		/* @TODO We can write scripts that purge these tables on all bds from time to time. Once a month say.
		$sql="CREATE TABLE ks4meta_copy LIKE ks4meta";
		Yii::app()->db->createCommand($sql)->execute();
		$sql="INSERT ks4meta_copy SELECT * FROM ks4meta WHERE fieldmapping_id!='{$object->id}'";
		Yii::app()->db->createCommand($sql)->execute();
		
		
		$sql="RENAME TABLE ks4meta TO ks4meta_old, ks4meta_copy TO ks4meta";
		Yii::app()->db->createCommand($sql)->execute();
		$sql="DROP TABLE ks4meta_old";
		Yii::app()->db->createCommand($sql)->execute();
		*/
		
		$sql = "DELETE FROM ks4meta WHERE cohort_id='$this->currentCohortId'";
		if($class=="Subject")
		$sql .= " AND subjectmapping_id='{$object->id}'";
		
		if($class=="FieldMapping")
		$sql .= " AND fieldmapping_id='{$object->id}'";
		
		$command = Yii::app()->db->createCommand( $sql );
		$command->execute();
		
		
		if($class=="Subject")
		$message = "KS4 meta data has been deleted for subject ID ".$object->id.".";
		if($class=="FieldMapping")
		$message = "KS4 meta data has been deleted for DCP/Target ID ".$object->id.".";
		
		Yii::app()->eventLog->log( "info", PtEventLog::BUILD_7, $message);
	}
	
	/**
	 * Returns true if the table requires a rebuild. False if it doesn't.
	 * Note that the key stage is always written to the log for FIELDMAPPING_4 and SUBJECT_1 events
	 * @return boolean
	 */
	public function getRequiresRebuildKs4Meta()
	{
		$triggers = array (
		PtEventLog::FIELDMAPPING_4, 
		PtEventLog::SUBJECT_1 );
		
		return Yii::app()->eventLog->getTriggerEvent( PtEventLog::BUILD_5, $triggers, 4 );
	}

	/**
	 * Removes pupils from the exludedpupils table who no longer have a matching set in the setdata table
	 * @return void
	 */
	public function cleanExcludedPupils()
	{
		$sql="DELETE excludedpupils FROM excludedpupils INNER JOIN subjectmapping ON(excludedpupils.subjectmapping_id=subjectmapping.id)
		WHERE NOT EXISTS(
		SELECT * FROM setdata
		WHERE setdata.cohort_id = subjectmapping.cohort_id
		AND excludedpupils.pupil_id = setdata.pupil_id
		AND setdata.mapped_subject = subjectmapping.mapped_subject
		AND excludedpupils.set_code = setdata.set_code
		AND excludedpupils.subjectmapping_id = subjectmapping.id
		)
		AND subjectmapping.cohort_id='$this->currentCohortId'";

		Yii::app()->db->createCommand($sql)->execute();

	}

	/**
	 * Removes sets from the exludedsets table who no longer have a matching set in the setdata table
	 * @return void
	 */
	public function cleanExcludedSets()
	{
		$sql="DELETE excludedsets FROM excludedsets INNER JOIN subjectmapping ON(excludedsets.subjectmapping_id=subjectmapping.id)
		WHERE NOT EXISTS(
		SELECT * FROM setdata
		WHERE setdata.cohort_id = subjectmapping.cohort_id
		AND setdata.mapped_subject = subjectmapping.mapped_subject
		AND excludedsets.set_code = setdata.set_code
		AND excludedsets.subjectmapping_id = subjectmapping.id
		)
		AND subjectmapping.cohort_id='$this->currentCohortId'";

		Yii::app()->db->createCommand($sql)->execute();

	}
	
	

	


	

}