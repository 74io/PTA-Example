<?php
class PtPtp extends PtMis implements iPtMis
{
	public $ptpDbName;
	public $ptpDeptDataTable;
	public $ptpSchoolDetailsTable;
	public $ptpPupilDetailsTable;
	
	private $_sharedFieldsWithAlias=array();
	private $_sharedFields;
	private $_subjects=array();
	private $_subjectsWithAlias=array();

	
	/**
	 * Here we init our class properties in the __construct rather than when using init()
	 * If we had extended CApplicationComponent then we could init our vars from config.php by overriding init()
	 */
	public function __construct()
	{
		$this->ptpDbName=strtolower(Yii::app()->settings->get("schoolSetUp","ptpDbName"));
		//Construct PTP table names
		$this->ptpDeptDataTable = 'sec_'.$this->ptpDbName.'.'.$this->ptpDbName.'_dept_data';
		$this->ptpSchoolDetailsTable = 'sec_'.$this->ptpDbName.'.'.$this->ptpDbName.'_school_details';
		$this->ptpPupilDetailsTable = 'sec_'.$this->ptpDbName.'.'.$this->ptpDbName.'_pupil_details';
	}
	
	/**
	 * 
	 * Returns the subject table name
	 * @param string $subject The name of the subject
	 * @return string
	 */
	private function getSubjectTableName($subject)
	{
		return 'sec_'.$this->ptpDbName.'.'.$this->ptpDbName."_".strtolower($subject);
	}
	
	/**
	 * 
	 * Returns the set column name
	 * @param string $subject The name of the subject
	 * @return string
	 */
	private function getSetColumnName($subject)
	{
		return $subject."_Set";
	}
	
	/**
	 * @return array an array of real and alias field names from PTP
	 */
	public function getSharedFields()
	{
		if($this->_sharedFields!==null)
		return $this->_sharedFields;
		
		$sql="SELECT Shared_data FROM ".$this->ptpDeptDataTable." WHERE Shared_data IS NOT NULL and Shared_data <>''";
		$connection=Yii::app()->db; 
		$command=$connection->createCommand($sql);
		return $this->_sharedFields = $command->queryColumn();
	}
	
	/**
	 * @return array an array of real and alias field names from PTP
	 */
	public function getSharedFieldsWithAlias()
	{
		if($this->_sharedFieldsWithAlias)
		return $this->_sharedFieldsWithAlias;
		
		$sql="SELECT Shared_data, real_shared FROM ".$this->ptpDeptDataTable." WHERE Shared_data IS NOT NULL and Shared_data <>''";
		$connection=Yii::app()->db; 
		$command=$connection->createCommand($sql);
		return $this->_sharedFieldsWithAlias = $command->queryAll(); 
	}
	
	/**
	 * @return array returns the shared fields on thier own
	 */
	public function getSharedFieldsDropDown()
	{
		$fields = $this->sharedFieldsWithAlias;
		foreach($fields as $row=>$value)
				{
					$array[]=$value['Shared_data'];
				}
				return array_combine(array_values($array), $array);
	}
	
	/**
	 * @return array An array of options to be used in CHtml.dropDownList()'s options property
	 */
	public function getSharedFieldsDropDownOptions()
	{
		$fields = $this->sharedFieldsWithAlias;
		$jsDataField='data-field-alias';
		foreach($fields as $key=>$value)
		{
			$array[$value['Shared_data']]=array($jsDataField=>$value['real_shared']);
		}
			return $array;						
	}
	
	/**
	 * Returns a list of general fields from the PTP database
	 * @return array
	 */
	public function getGeneralFields()
	{
		if($this->_generalFields)
		return $this->_generalFields;
		
		$sql="SELECT DISTINCT(General_data) FROM ".$this->ptpDeptDataTable." WHERE General_data IS NOT NULL and General_data <>''";
		$command=Yii::app()->db->createCommand($sql);
		return $this->_generalFields =  $command->queryColumn();

	}
	
	/**
	 * Returns general fields in a format for CHtml.dropDownList()
	 * @return array
	 */
	public function getGeneralFieldsDropDown()
	{
		$array = $this->generalFields;
		return array_combine(array_values($array), $array);
	}
	
	/**
	 * Returns the subjects with the subject alias
	 * @return array
	 */
	public function getSubjectsWithAlias()
	{
		if($this->_subjectsWithAlias)
		return $this->_subjectsWithAlias;
		
		$sql="SELECT department, real_dept_name FROM ".$this->ptpSchoolDetailsTable." WHERE department IS NOT NULL AND department <>'Administration' AND department <>''";
		$connection=Yii::app()->db; 
		$command=$connection->createCommand($sql);
		$rows=$command->queryAll(); 
		return $this->_subjectsWithAlias = $rows;	
	}
	
	/**
	 * Returns the subjects with the subject alias
	 * @return array
	 */
	public function getSubjects()
	{
		if($this->_subjects)
		return $this->_subjects;
		
		$sql="SELECT department FROM ".$this->ptpSchoolDetailsTable." WHERE department IS NOT NULL AND department <>'Administration' AND department <>''";
		$connection=Yii::app()->db; 
		$command=$connection->createCommand($sql);
		$column=$command->queryColumn(); 
		return $this->_subjects = $column;	
	}
	
	/**
	 * Returns subjects in a format for CHtml.dropDownList()
	 * @return array
	 */
	/* CURRENTLY OBSELETE 17/10/2012
	public function getSubjectsDropDown()
	{
		$fields = $this->subjectsWithAlias;
		foreach($fields as $row=>$value)
				{
					$array[]=$value['department'];
				}
				return array_combine(array_values($array), $array);	
	}*/
	
	/**
	 * Returns a list of options to be attached to CHtml.dropDownList()
	 */
	/* CURRENTLY OBSELETE 17/10/2012
	public function getSubjectsDropDownOptions()
	{
		$fields = $this->subjectsWithAlias;
		$jsDataField='data-field-alias';
		foreach($fields as $key=>$value)
		{
			$array[$value['department']]=array($jsDataField=>$value['real_dept_name']);
		}
			return $array;	
	}*/
	
	/**
	 * Returns a list of sets for a given subject
	 * @param string $subject The name of the mapped_subject to get the data for
	 * @param integer $year The year group to return sets for
	 * @return array an array of sets for the given year group
	 */
	/* CURRENTLY OBSELETE
	public function getSubjectSetsForYearGroup($subject,$year)
	{
		$pupilTable = $this->ptpPupilDetailsTable;
		$subjectTable = $this->getSubjectTableName($subject);
		$setColumn = $this->getSetColumnName($subject);
	
		$sql="SELECT DISTINCT($setColumn) FROM $subjectTable, $pupilTable ";
		$sql.="WHERE $setColumn IS NOT NULL ";
		$sql.="AND $setColumn <> '' ";
		$sql.="AND $pupilTable.Adm_No = $subjectTable.Adm_No ";
		$sql.="AND $pupilTable.Year =:year ";
		$sql.="ORDER BY $setColumn";
		
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':year', $year, PDO::PARAM_INT);
		return $command->queryColumn();
		
	}*/
	
	/**
	 * Returns an array of sets in the format
	 * Array
		(
		    [10] => Array
		        (
		            [0] => 10D/H1
		            [1] => 10X/H1
		            [2] => 10X/H2
		            [3] => 10Y/H1
		            [4] => 10Y/H2
		        )
		
		    [11] => Array
		        (
		            [0] => 111/H1
		            [1] => 111/H2
		            [2] => 111/H3
		            [3] => 112/H1
		        )
		
		)

	 * @param string $subject The name of the mapped subject to get the year groups for
	 * @param array $yearGroups An array of year groups e.g. 7,8,9
	 * @return array
	 * 
	 */
	/* CURRENTLY OBSOLETE
	public function getSubjectSetsForYearGroups($subject, $yearGroups)
	{
		
		$pupilTable = $this->ptpPupilDetailsTable;
		$subjectTable = $this->getSubjectTableName($subject);
		$setColumn = $this->getSetColumnName($subject);
		$yearGroups = implode(",",$yearGroups);
	
		$sql="SELECT DISTINCT($setColumn) AS set_code, $pupilTable.Year AS year FROM $subjectTable, $pupilTable ";
		$sql.="WHERE $setColumn IS NOT NULL ";
		$sql.="AND $setColumn <> '' ";
		$sql.="AND $pupilTable.Adm_No = $subjectTable.Adm_No ";
		$sql.="AND $pupilTable.Year IN($yearGroups) ";
		$sql.="ORDER BY $setColumn";
		
		$command=Yii::app()->db->createCommand($sql);
		$dataReader=$command->query();
		
		foreach($dataReader as $row){
			$array[$row['year']][]=$row['set_code'];
		}

		return $array;
		
	}*/
	
	/**
	 * Returns an array of pupils belonging to a specific set in a specific subject
	 * @param string $subject The name of the mapped_subject to get the data for
	 * @param string $set The set code
	 * @return array An array of pupils in a specific set and year group
	 */
	public function getPupilsInFilteredSet($subject,$set)
	{
		$pupilTable = $this->ptpPupilDetailsTable;
		$subjectTable = $this->getSubjectTableName($subject);
		$setColumn = $this->getSetColumnName($subject);
	
		$sql="SELECT $pupilTable.Adm_No AS pupil_id, Surname, Forename FROM $subjectTable, $pupilTable ";
		$sql.="WHERE $setColumn =:set ";
		$sql.="AND $pupilTable.Adm_No = $subjectTable.Adm_No ";
		$sql.="ORDER BY Surname, Forename";
		
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':set', $set, PDO::PARAM_STR);
		return $command->queryAll(); 
	}
	

	/**
	 * Validates a school's school name and school ID
	 * @param string $schoolName
	 * @param string $schoolId
	 * @return mixed An array if there is a result. False if not
	 */
	public static function schoolIsValid($schoolName, $schoolId)
	{
		$sql="SELECT id FROM admin.sec_schools WHERE school_name=:school_name AND schoolID=:school_id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':school_name', $schoolName, PDO::PARAM_STR);
		$command->bindParam(':school_id', $schoolId, PDO::PARAM_STR);
		return $command->queryRow();
	}
	
	/**
	 * Returns an associative array. list=>A list of data that can be used in a data providers
	 * and complete=> a boolean that indicates whether all fields are present or not
	 * @return array
	 */
	public function getIndicatorList()
	{
		if($this->_indicators)
		return $this->_indicators;
			
		$generalFields = $this->generalfields;
		$array=array();
		$present=0;
		$complete=false;
		
		foreach($this->requiredIndicators as $indicator)
		{
			if(in_array($indicator,$generalFields)){
			$array[]=array('field'=>$indicator,'present'=>1);
			$present++;
			}
			else{
			$array[]=array('field'=>$indicator,'present'=>0);
			}
		}
			
		if(count($this->requiredIndicators)==$present)
		$complete=true;
		
		return $this->_indicators = array('list'=>$array,'complete'=>$complete);
	}
	
	/**
	 * Builds the data set from PTP
	 * @param string $currentCohortId the id of the cohort that todays date falls between
	 * @return bool
	 */
	public function buildCoreData($currentCohortId)
	{
		$this->deleteCohort('pupil', $currentCohortId);
		if(!$this->buildPupils($currentCohortId)){
			return false;
		}
		
		$this->deleteCohort('setdata', $currentCohortId);
		if(!$this->buildSets($currentCohortId)){
			return false;
		}

		$this->deleteCohort('attendance', $currentCohortId);
		if(!$this->buildAttendance($currentCohortId)){
			return false;
		}

		$this->deleteCohort('teacher', $currentCohortId);
		if(!$this->buildTeachers($currentCohortId)){
			return false;
		}
		else{
			$this->updateClasses($currentCohortId);
		}
		
		Yii::app()->dataCache->deleteDataCache($currentCohortId);
		return true;
	}
	
	/**
	 * Builds the set table
	 * @param string $currentCohortId The current cohort id
	 * @return bool
	 */
	public function buildSets($currentCohortId)
	{
		$subjects=$this->subjectsWithAlias;
		$yearGroups= implode(",",Yii::app()->common->yearGroups);
		
		
		try{
		foreach($subjects as $subject)
		{
				$setName = $subject['department']."_Set";
				$subjectTableName = $this->getSubjectTableName($subject['department']);
				$realDeptName = addslashes($subject['real_dept_name']);
				
				$sql="INSERT INTO setdata (
				cohort_id,
				pupil_id,
				mapped_subject,
				subject,
				set_code)
				SELECT 
				'$currentCohortId',
				$subjectTableName.Adm_No,
				'{$subject['department']}',
				'{$realDeptName}',
				$setName
				FROM
				$subjectTableName INNER JOIN $this->ptpPupilDetailsTable USING(Adm_No)
				WHERE $setName!=''
				AND $setName IS NOT NULL
				AND Year IN ($yearGroups)
				";
				
				$command=Yii::app()->db->createCommand($sql);
				$command->execute();
						
			}
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
	 */
	public function buildAttendance($currentCohortId)
	{
		$yearGroups= implode(",",Yii::app()->common->yearGroups);
		$attendanceTable = 'sec_'.$this->ptpDbName.'.'.$this->ptpDbName."_pt_attendance_sims";

		
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
				UPN,
				t1.Adno,
				Possible_marks,
				Present_marks,
				Approved_ed_activity,
				Authorised_absences,
				Unauthorised_absences,
				Present_plus_aea,
				Unexplained_absences,
				Late_before_reg,
				Late_after_reg,
				Late_both,
				Missing_marks,
				Attendance_not_required,
				date
				FROM
				$attendanceTable t1 INNER JOIN $this->ptpPupilDetailsTable t2 ON(t2.Adm_No=t1.UPN)
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
	 * Builds the teachers table.
	 * @param string $currentCohortId See above
	 */
	public function buildTeachers($currentCohortId)
	{
		$teachersTable = 'sec_'.$this->ptpDbName.'.'.$this->ptpDbName."_pt_teachers_sims";
		
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
				$teachersTable";
				
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
	 * Updates the teacher_id in the setdata table
	 * @return [type] [description]
	 */
	public function updateClasses($currentCohortId)
	{
		$classesTable = 'sec_'.$this->ptpDbName.'.'.$this->ptpDbName."_pt_classes_sims";
		$sql="UPDATE setdata t1 INNER JOIN $classesTable t2 ON(t1.set_code=t2.Class) SET t1.teacher_id=t2.Teacher_ID
		WHERE t1.cohort_id='$currentCohortId'";
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
	 * Builds the pupil table
	 * @param string $currentCohortId The current cohort id
	 * @return bool
	 */
	public function buildPupils($currentCohortId)
	{

		$ks2 = Yii::app()->settings->get("keyStage");
		$yearGroups= implode(",",Yii::app()->common->yearGroups);
		
		$sql="INSERT INTO pupil (
		id,
		pupil_id,
		cohort_id,
		surname,
		forename,
		year,
		form,
		dob,
		gender,
		ethnicity,
		sen_code,
		fsm,
		gifted,
		cla,
		eal,
		pupil_premium,
		ks2_english,
		ks2_maths
		";
		if($ks2['ks2Science'])
		$sql.=",ks2_science";

		$sql.="
		)
		SELECT 
		null,
		Adm_No, 
		'{$currentCohortId}',
		Surname, 
		Forename, 
		Year, 
		Form, 
		DOB, 
		IFNULL(Gender,''),
		IFNULL(Ethnicity,''),
		IFNULL(SEN_Code,''),
		IFNULL(FSM,''),
		IFNULL(Gifted,''),
		IFNULL(CLA,''),
		IFNULL(EAL,''),
		IFNULL(Pupil_Premium,''),
		IFNULL(LEFT({$ks2['ks2English']},1),0),
		IFNULL(LEFT({$ks2['ks2Maths']},1),0)
		";
		if($ks2['ks2Science'])
		$sql.=",IFNULL(LEFT({$ks2['ks2Science']},1),0)";
		
		$sql.=" FROM $this->ptpPupilDetailsTable
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
	 * Builds the data set for a specific mapped field. This is fired when a DCP or target is 
	 * edited and the mapped field is changed. Note there is a check here to ensure that if a subject on PTA
	 * does not exist on PTP then it will be skipped.
	 * @param FieldMapping $object An active record object
	 * @param string $currentCohortId The current cohort id
	 * @return void
	 */
	public function buildSubjectDataForFieldMapping($object,$currentCohortId)
	{
		$keyStage=Yii::app()->common->getKeyStageForYearGroup($object->year_group);
		$subjects = Subject::getSubjectsForKeyStage($currentCohortId,$keyStage);
		$missing=array();
		
		foreach($subjects as $subject)
		{
			if(!in_array($subject['mapped_subject'],$this->subjects)){
			$missing[]=	$subject['mapped_subject'];
			continue;
			}
			
			
			$subjectTableName = $this->getSubjectTableName($subject['mapped_subject']);
						$setName = $subject['mapped_subject']."_Set";
						$field=$subject['mapped_subject']."_".$object->mapped_field;
						
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
						$subjectTableName.Adm_No,
						'{$object->id}',
						$field
						FROM
						$subjectTableName,
						$this->ptpPupilDetailsTable 
						WHERE $this->ptpPupilDetailsTable.Adm_No = $subjectTableName.Adm_No
						AND $this->ptpPupilDetailsTable.Year='$object->year_group'
						AND $setName!=''
						AND $setName IS NOT NULL
						";
						
						$command=Yii::app()->db->createCommand($sql);
				        $command->execute();		        
		}
			if($missing){
				$missingString = implode(",",array_unique($missing));
				Yii::app()->user->setFlash('warning',
				'<strong>Warning!</strong> The subjects <strong>'.$missingString.'</strong> no longer exist on PTP and thus could not be built.
				To ensure data integrity you should delete them from this system. DCPs/Targets were built for all other subjects without problem.
				Check the log if you need to review this warning.');
				//Write error to log
				Yii::app()->eventLog->log( "warning", PtEventLog::FIELDMAPPING_4, "The subjects '$missingString' could not be found on PTP when the DCP/Target ID
				".$object->id." was built.");
			}
		Yii::app()->eventLog->log("success",PtEventLog::FIELDMAPPING_4,"Field mapping ID $object->id has been built/rebuilt.",$keyStage,$object->id);
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
		$missing=array();

	
		foreach($fieldMappings as $fieldMapping)
		{
			//If the mapped field does not exist on PTP skip it
			if(!in_array($fieldMapping['mapped_field'],$this->sharedFields)){
				$missing[]=$fieldMapping['mapped_field'];
				continue;
			}
			
			
			$setName = $object->mapped_subject."_Set";
			$subjectTableName = $this->getSubjectTableName($object->mapped_subject);

						$field=$object->mapped_subject."_".$fieldMapping['mapped_field'];
						
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
						$subjectTableName.Adm_No,
						'{$fieldMapping['id']}',
						$field
						FROM
						$subjectTableName,
						$this->ptpPupilDetailsTable 
						WHERE $this->ptpPupilDetailsTable.Adm_No = $subjectTableName.Adm_No
						AND $this->ptpPupilDetailsTable.Year='{$fieldMapping['year_group']}'
						AND $setName!=''
						AND $setName IS NOT NULL
						";
						
						$command=Yii::app()->db->createCommand($sql);
				        $command->execute();
				        

			}
			if($missing){
				$missingString = implode(",",array_unique($missing));
				Yii::app()->user->setFlash('warning',
				'<strong>Warning!</strong> The result sets (shared fields) <strong>'.$missingString.'</strong> no longer exist on PTP
				and could not be built for this subect. 
				It could be that you renamed the shared fields on PTP? To ensure data integrity you should update DCPs or Targets that use these fields.
				All other DCPs/Targets were updated without problem. Check the log if you need to review this warning.');
				//Write error to log
				Yii::app()->eventLog->log( "warning", PtEventLog::SUBJECT_1, "The result sets '$missingString' could not be found on PTP when  subject ID
				".$object->id." was built" );
		}	
	}
	
	/**
	 * Returns true if the ks2 fields required to build the core data are available on PTP
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
	 * Returns true if the system has access to other MIS data e.g. teacher, attendance
	 * @return boolean
	 */
	public function hasMisAccess()
	{
		$schoolId = Yii::app()->controller->schoolSetUp['ptpSchoolId'];
		$sql="SELECT COUNT(*) FROM admin.sec_schools WHERE schoolID='$schoolId' AND mis='SIMS'";
		$command=Yii::app()->db->cache(1000)->createCommand($sql);
		return $command->queryScalar(); //Returns the first column in the first row of data. In our case 0 or 1.

	}

	
	/**
	 * Extracts field mapping rows for a particular year group
	 * @param integer $yearGroup
	 * @param array $rows
	 * @return array
	 */
	/* CURRENTLY OBSOLETE????
	private function getFieldMappingForYearGroup($yearGroup,$rows)
	{
		foreach($rows as $row)
		{
			if($row['year_group']==$yearGroup){
				$array[]=$row;
			}
		}
		
		return $array;
	}*/


	

	
	

	
	
	
		

		
		
		
		
		
	
	
}