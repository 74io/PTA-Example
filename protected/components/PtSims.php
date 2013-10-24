<?php
class PtSims extends PtMis implements iPtMis
{	
	private $_filesLoadedToday;

	
	protected $dir;
	protected $db;
	protected $pupilsPathToFile;
	protected $classesPathToFile;
	protected $teachersPathToFile;
	protected $generalPathToFile;
	protected $attendancePathToFile;
	protected $ks2PathToFile;
	
	public function __construct($db="")
	{
		if($db)
		$this->db=$db;
		else 
		$this->db = Yii::app()->params['dbName'];
		$this->init();
		
	}
	
	public function init()
	{

		//IMPORTANT This might not work on linux
		//Note we previously used $_SERVER['OS'] for this but it does not work on Mac
		if($_SERVER['LOGNAME']=="Air" || $_SERVER['SERVER_PORT']==80)
		$this->dir="/Users/Air/Sites/SIMS/";
		else
		$this->dir="/home/roneill/secure.pupiltracking.com/SIMS/";
		
		$this->pupilsPathToFile=$this->dir.$this->db."_pt_pupils.xml";
		$this->classesPathToFile=$this->dir.$this->db."_pt_classes.xml";
		$this->teachersPathToFile=$this->dir.$this->db."_pt_teachers.xml";
		$this->generalPathToFile=$this->dir.$this->db."_pt_general_data.xml";
		$this->attendancePathToFile=$this->dir.$this->db."_pt_attendance.xml";
		$this->ks2PathToFile=$this->dir.$this->db."_pt_ks2.xml";
	}
	
	/**
	 * Returns a list of indicator fields available on the system
	 * @return array
	 */
	public function getIndicatorList()
	{
		if($this->_indicators)
		return $this->_indicators;
		
		$flag=false;
		$file=$this->generalPathToFile;

		if(file_exists($file)){
			$date= filemtime($file);//Date the file was last modified
			if(Cohort::dateIsInCurrentCohort($date)){ //&& $this->getFileLoadedToday('general')){
				$flag=true;
			}
		}

		foreach($this->requiredIndicators as $indicator)
		{
			if($flag){
			$array[]=array('field'=>$indicator,'present'=>1);
			}
			else{
			$array[]=array('field'=>$indicator,'present'=>0);
			}
		}
		
		return $this->_indicators = array('list'=>$array,'complete'=>$flag);
	}
	
	/**
	 * Returns general fields in a format for CHtml.dropDownList()
	 * NOTE. For SIMS this is simply the KS2 data
	 * @return array
	 */
	public function getGeneralFieldsDropDown()
	{
		$array = $this->generalFields;
		if($array)
		return array_combine(array_values($array), $array);
		else
		return array();
	}
	
	/**
	 * Returns a list of KS2 fields from the SIMS mis_ks2 table
	 * @return array
	 */
	public function getGeneralFields()
	{
		if($this->_generalFields)
		return $this->_generalFields;
		
		$sql="SELECT DISTINCT(field) FROM mis_ks2 WHERE field!=''";
		$command=Yii::app()->db->cache(1000)->createCommand($sql);
		return $this->_generalFields =  $command->queryColumn();

	}
	
	/**
	 * Returns true if the ks2 fields in the settings are available on the MIS system
	 * @return bool
	 */
	public function getKs2FieldsAreValid()
	{
		$ks2Fields = Yii::app()->settings->get( "keyStage" );
		$misKs2Fields = $this->generalFieldsDropDown;
		foreach ( $ks2Fields as $field )
		{
			if ($field != "")
			{
				if (! in_array( $field, $misKs2Fields ))
				{
					Yii::app()->eventLog->log( "error", PtEventLog::BUILD_1, "The mapped KS2 fields are missing from your MIS." );
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Builds the core data. Note that the check against setUpIsComplete is used to build the data set the first time round regardless
	 * @param string $currentCohortId The id of the current cohort. I.e. the cohort that today's date
	 * currently belongs to.
	 * @return bool
	 */
	public function buildCoreData($currentCohortId)
	{
		$preservedPupils = $this->getPupilIdsToSave($currentCohortId);
		$setUpIsComplete = Yii::app()->build->setUpIsComplete;

		
		if($this->getFileLoadedToday('pupils') || !$setUpIsComplete){
			$this->deleteCohort('pupil', $currentCohortId,$preservedPupils);
			
			if(!$this->buildPupils($currentCohortId))
			return false;
		}
			
		if($this->getFileLoadedToday('general') || !$setUpIsComplete){
			if(!$this->updateGeneral($currentCohortId))
			return false;
		}
			
		if($this->getFileLoadedToday('ks2') || !$setUpIsComplete){
			if(!$this->updateKs2($currentCohortId))
			return false;		
		}
		
		if($this->getFileLoadedToday('classes') || !$setUpIsComplete){
			$this->deleteCohort('setdata', $currentCohortId,$preservedPupils);
			if(!$this->buildSets($currentCohortId))
			return false;
		}

		if($this->getFileLoadedToday('attendance') || !$setUpIsComplete){
			$this->deleteCohort('attendance', $currentCohortId,$preservedPupils);
			if(!$this->buildAttendance($currentCohortId))
			return false;
		}

		if($this->getFileLoadedToday('teachers') || !$setUpIsComplete){
			$this->deleteCohort('teacher', $currentCohortId,false);
			if(!$this->buildTeachers($currentCohortId))
			return false;
		}

		/**
		 * If files have been loaded today it means that the hashes of the xml files have changed
		 * and thus have been reloaded. If they have not changed then we do not need to flush the cache
		 */
		if($this->filesLoadedToday)
		Yii::app()->dataCache->deleteDataCache($currentCohortId);

		return true;
		
	}

	/**
	 * Returns an array of year groups that are in the pupil table but no longer in the mis_pupils table. These year groups are now (most likely) 'Off Roll' on SIMS
	 * and need to have the data preserved for all pupils who match this year group in the pupil table
	 * @return [type] [description]
	 */
	public function getYearGroupsToBeSaved($currentCohortId)
	{
		$sql="SELECT DISTINCT(t1.year) FROM pupil AS t1 LEFT JOIN mis_pupils AS t2 using(year) WHERE cohort_id=:cohortId AND t2.year IS NULL";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':cohortId', $currentCohortId,PDO::PARAM_STR);
		return $command->queryColumn();//Returns empty array if no results

	}

	/**
	 * Returns an array of pupil_id's that need to be preserved in all build tables or false if no pupils need preserving
	 * @param  string $currentCohortId The current cohort id
	 * @return mixed
	 */
	public function getPupilIdsToSave($currentCohortId)
	{
		$yearGroups = $this->getYearGroupsToBeSaved($currentCohortId);
		if(count($yearGroups)){
		$yearGroups = implode(",",$yearGroups);
		$sql="SELECT pupil_id FROM pupil WHERE cohort_id=:cohortId AND year IN ($yearGroups)";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':cohortId', $currentCohortId,PDO::PARAM_STR);
		return $command->queryColumn();//Returns empty array if no results

		}

		return false;
	}

	/**
	 * Deletes data from a table for a specific cohort
	 * @param string $table The table to delete from
	 * @param string $currentCohortId The ID of the current cohort
	 * @param array $preservedPupild An array of pupils to reserve
	 * @return void
	 */
	public function deleteCohort($table, $currentCohortId,$preservedPupils)
	{

		if($preservedPupils){
			$preservedPupils ="'".implode("','",$preservedPupils)."'";
			$sql = "DELETE FROM $table WHERE cohort_id=:cohortId AND pupil_id NOT IN ($preservedPupils)";
		}
		else{
			$sql = "DELETE FROM $table WHERE cohort_id=:cohortId";
		}
		$command = Yii::app()->db->createCommand( $sql );
		$command->bindParam(':cohortId', $currentCohortId,PDO::PARAM_STR);
		$command->execute();

	}
	
	/**
	 * Builds the pupil table
	 * @param string $currentCohortId See above
	 * @return bool
	 */
	public function buildPupils($currentCohortId)
	{

		$yearGroups= implode(",",Yii::app()->common->yearGroups);
		
		$sql="INSERT INTO pupil (
		pupil_id,
		cohort_id,
		surname,
		forename,
		year,
		form,
		dob,
		gender";

		$sql.="
		)
		SELECT 
		upn, 
		'{$currentCohortId}',
		surname, 
		forename, 
		year, 
		form, 
		DATE_FORMAT(dob,'%d-%m-%Y'),
		gender";

		$sql.=" FROM mis_pupils
		WHERE Year IN ($yearGroups)";
		
		$command=Yii::app()->db->createCommand($sql);
		//Try and catch the query so we can write to the pt error log
		try{
            $command->execute();
           }catch(Exception $e)
            {
            //Gets the full message $e->getMessage();
            Yii::app()->eventLog->log("error",PtEventLog::BUILD_1,$e->errorInfo[2]);
            return false;
           }             
           
        return true;
		
	}
	
	/**
	 * Updates the mis_general table with the indicators
	 * @param string $currentCohortId The current cohort ID
	 * @return bool
	 */
	public function updateGeneral($currentCohortId)
	{
		$sql="UPDATE pupil t1 INNER JOIN mis_general t2 ON(t1.pupil_id = t2.upn)
		SET t1.ethnicity=t2.ethnicity,
		t1.sen_code = t2.sen_code,
		t1.fsm = t2.fsm,
		t1.gifted = t2.gifted,
		t1.cla = t2.cla,
		t1.eal = t2.eal,
		t1.post_code = t2.post_code,
		t1.pupil_premium = t2.pupil_premium
		WHERE t1.cohort_id='$currentCohortId'";
		$command=Yii::app()->db->createCommand($sql);
		try{
            $command->execute();
           }catch(Exception $e)
            {
            Yii::app()->eventLog->log("error",PtEventLog::BUILD_1,$e->errorInfo[2]);
            return false;
           }             
        return true;
	}
	
	/**
	 * Updates the pupil table with the KS2 data
	 */
	public function updateKs2($currentCohortId)
	{
		$ks2 = Yii::app()->settings->get("keyStage");
		
		foreach($ks2 as $key=>$value){
			switch($key){
				case('ks2English'):
					$field='ks2_english';
					break;
				case('ks2Maths'):
					$field="ks2_maths";
					break;
				case('ks2Science'):
					$field='ks2_science';
					break;
			}
		$sql="UPDATE pupil t1 INNER JOIN mis_ks2 t2 ON(t1.pupil_id = t2.upn)
		SET t1.$field= t2.result
		WHERE t1.cohort_id='$currentCohortId'
		AND t2.field=:field";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':field', $value,PDO::PARAM_STR);
		try{
            $command->execute();
           }catch(Exception $e)
            {
            Yii::app()->eventLog->log("error",PtEventLog::BUILD_1,$e->errorInfo[2]);
            return false;
           }
		}             
        return true;
	}
	
	
	/**
	 * Builds the setdata.
	 * @param string $currentCohortId See above
	 * @return bool
	 */
	public function buildSets($currentCohortId)
	{
		$yearGroups= implode(",",Yii::app()->common->yearGroups);
		
		$sql="INSERT INTO setdata (
				cohort_id,
				pupil_id,
				mapped_subject,
				subject,
				set_code,
				teacher_id)
				SELECT 
				'$currentCohortId',
				upn,
				subject_code,
				subject,
				class,
				teacher_id
				FROM
				mis_classes t1 INNER JOIN mis_pupils t2 USING(upn)
				WHERE t1.subject_code !=''
				AND t2.year IN ($yearGroups)
				GROUP BY t1.subject_code,t1.upn
				ORDER BY t1.class
				";
				
				$command=Yii::app()->db->createCommand($sql);
				
		try{
			$command->execute();	
		}
		catch(Exception $e){
			Yii::app()->eventLog->log("error",PtEventLog::BUILD_1,$e->errorInfo[2]);
			return false;
		}
		
		return true;
	}

	/**
	 * Builds the attendance table.
	 * @param string $currentCohortId See above
	 * @return bool
	 */
	public function buildAttendance($currentCohortId)
	{
		$yearGroups= implode(",",Yii::app()->common->yearGroups);
		
		$sql="INSERT INTO attendance (
				cohort_id,
				pupil_id,
				adno,
				possible_marks,
				present_marks,
				approved_ed_activity,
				authorised_absences,
				unauthorised_absences,
				present_plus_aea,
				unexplained_absences,
				late_before_reg,
				late_after_reg,
				late_both,
				missing_marks,
				attendance_not_required,
				date
				)
				SELECT 
				'$currentCohortId',
				t1.upn,
				t1.adno,
				possible_marks,
				present_marks,
				approved_ed_activity,
				authorised_absences,
				unauthorised_absences,
				present_plus_aea,
				unexplained_absences,
				late_before_reg,
				late_after_reg,
				late_both,
				missing_marks,
				attendance_not_required,
				date
				FROM
				mis_attendance t1 INNER JOIN mis_pupils t2 USING(upn)
				WHERE t2.year IN ($yearGroups)
				";
				
				$command=Yii::app()->db->createCommand($sql);
				
		try{
			$command->execute();	
		}
		catch(Exception $e){
			Yii::app()->eventLog->log("error",PtEventLog::BUILD_1,$e->errorInfo[2]);
			return false;
		}
		
		return true;
	}

	/**
	 * Builds the teacher table.
	 * @param string $currentCohortId See above
	 */
	public function buildTeachers($currentCohortId)
	{

		
		$sql="INSERT INTO teacher (
				cohort_id,
				initials,
				title,
				surname,
				forename,
				teacher_id
				)
				SELECT 
				'$currentCohortId',
				initials,
				title,
				surname,
				forename,
				teacher_id
				FROM
				mis_teachers";
				
				$command=Yii::app()->db->createCommand($sql);
				
		try{
			$command->execute();	
		}
		catch(Exception $e){
			Yii::app()->eventLog->log("error",PtEventLog::BUILD_1,$e->errorInfo[2]);
			return false;
		}
		
		return true;
	}
	
	/**
	 * Builds the subject data for a specific subject
	 * @param Subject $object an active record object
	 * @param string $currentCohortId The current cohort id
	 * @return void
	 */
	public function buildSubjectDataForSubject($object,$currentCohortId)
	{
		
		$yearGroups = Yii::app()->common->getYearGroupsForKeyStage($object->key_stage);
		$fieldMappings = FieldMapping::getFieldMappingsForYearGroup($currentCohortId,$yearGroups);
		
		foreach($fieldMappings as $fieldMapping)
		{
					$resultSet = Result::getResultSetFromFieldName($fieldMapping['mapped_field']);
					
						$sql="INSERT INTO subjectdata (
						cohort_id,
						subjectmapping_id,
						pupil_id,
						fieldmapping_id,
						result
						)
						SELECT 
						'$currentCohortId',
						'{$object->id}',
						t1.pupil_id,
						'{$fieldMapping['id']}',
						result
						FROM
						resultdata t1 INNER JOIN pupil t2 ON(t1.pupil_id = t2.pupil_id)
						INNER JOIN setdata t3 
						ON(t1.pupil_id=t3.pupil_id
						AND t3.pupil_id = t2.pupil_id
						AND t3.cohort_id = t2.cohort_id
						AND t1.mapped_subject = t3.mapped_subject
						) 
						WHERE t2.year='{$fieldMapping['year_group']}'
						AND t1.mapped_subject = t3.mapped_subject
						AND t1.resultmapping_id = '{$resultSet['id']}'
						AND t1.mapped_subject = '{$object->mapped_subject}'
						AND t2.cohort_id='$currentCohortId'
						";
						
						$command=Yii::app()->db->createCommand($sql);
				        $command->execute();
		}	
	}
	
	/**
	 * Builds the data set for a specific mapped field. This is fired when a DCP or target is edited and the 
	 * mapped field is changed or when rebuild is clicked directly
	 * @param FieldMapping $object An active record object
	 * @param string $currentCohortId The current cohort id
	 */
	public function buildSubjectDataForFieldMapping($object,$currentCohortId)
	{
		$keyStage=Yii::app()->common->getKeyStageForYearGroup($object->year_group);
		$subjects = Subject::getSubjectsForKeyStage($currentCohortId,$keyStage);
		$resultSet = Result::getResultSetFromFieldName($object->mapped_field);


		foreach($subjects as $subject)
		{
		
						$sql="INSERT INTO subjectdata (
						cohort_id,
						subjectmapping_id,
						pupil_id,
						fieldmapping_id,
						result
						)
						SELECT 
						'$currentCohortId',
						'{$subject['id']}',
						t1.pupil_id,
						'{$object->id}',
						result
						FROM
						resultdata t1 
						INNER JOIN pupil t2 ON(t1.pupil_id = t2.pupil_id)
						INNER JOIN setdata t3 
						ON(t1.pupil_id=t3.pupil_id
						AND t3.pupil_id = t2.pupil_id
						AND t3.cohort_id = t2.cohort_id
						AND t1.mapped_subject = t3.mapped_subject
						) 
						WHERE t2.year='$object->year_group'
						AND t1.resultmapping_id = '{$resultSet['id']}'
						AND t1.mapped_subject = '{$subject['mapped_subject']}'
						AND t2.cohort_id='$currentCohortId'
						";
						
						$command=Yii::app()->db->createCommand($sql);
				        $command->execute();		        
		}
		
		Yii::app()->eventLog->log("success",PtEventLog::FIELDMAPPING_4,"Field mapping ID $object->id has been built/rebuilt.",$keyStage,$object->id);
		
	}
	
	/**
	 * Returns all the xml files loaded into the relevant mis_ tables today. If they have not been
	 * loaded it means that either they did not need loading i.e. their hashes were the same or the loading failed
	 */
	public function getfilesLoadedToday()
	{
		if($this->_filesLoadedToday!==null)
		return $this->_filesLoadedToday;
		
		$sql="SELECT message FROM eventlog WHERE date = CURDATE() AND category='".PtEventLog::CRON_1."' AND level='success'";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryColumn();
	}
	
	/**
	 * Returns true if a specific xml file was loaded into the db today
	 * @param string $filename The name of the file that appears in the eventlog message e.g. classes, teachers, general. 
	 * Not the path to an actual file
	 * @return bool
	 */
	public function getFileLoadedToday($filename)
	{	
		if(in_array($filename,$this->filesLoadedToday))
		return true;
		else
		return false;
	}
	
	/**
	 * Returns a list of the result set field names in a format to be used in CHtml.dropDownList()
	 * @return array 
	 */
	public function getSharedFieldsDropDown()
	{
		$array=Result::getResultSetFieldNames();
		if($array)
		return array_combine(array_values($array), $array);
		else 
		return array();
	}
	
	/**
	 * @return array An array of options to be used in CHtml.dropDownList()'s options property.
	 * In the case of SIMS this simply returns an emtpy array as their are no field aliases
	 * @return array
	 */
	public function getSharedFieldsDropDownOptions()
	{
		return array();						
	}

	/**
	 * Returns true if the system has access to other MIS data e.g. teacher, attendance
	 * @return boolean
	 */
	public function hasMisAccess()
	{
		return true;
	}
	
	
	/**
	 * Returns true if the file type has ever been loaded. If it has it means that the file physically exists
	 * and has been loaded into the db table e.g. mis_ at least once in its life time meaning that
	 * the xml file physically exists. If the file did not exists then the entry would not be in the event log.
	 * @param string $file Name of the file e.g. pupils, classes, teachers, general
	 * @return bool
	 */
	/*
	public function getFileLoadedEver($file)
	{
		$sql="SELECT message FROM eventlog WHERE category='".PtEventLog::CRON_1."' AND level='success' AND message='$file' LIMIT 1";
		return Yii::app()->db->createCommand($sql)->queryRow();
	}*/
	
	
}