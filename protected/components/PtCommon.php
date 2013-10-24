<?php
class PtCommon extends CApplicationComponent
{
	
	/**
	 * @var array  The system allowed roles. Note' super' is not provided because it cannot be created.
	 * However, it will be the default role of the first new user of the system.
	 */
	public $roles;
	
	/**
	 * @var array All the year groups that this system supports. Set in config/main.php
	 */
	public $systemYearGroups;
	
	/**
	 * @var array An array of allowed key stages. Set in config/main.php
	 */
	public $systemKeyStages;
	
	/**
	 * @var array  The No of volume indicators available e.g. 1-4 set in config/main.php
	 * These represent how many GCSEs a qualification is worth e.g. Single=1, Double=2 etc.
	 */
	public $volumeIndicators;
	
	/**
	 * @var array An array of options to indicate if a subject is equivalnent to a GCSE or not.
	 * e.g. 0,1 set in config/main.php
	 */
	public $equivalents;
	
	/**
	 * @var array An array of special subject types set in config/main.php
	 * A type of subject can be e.g. Humanities or MFL or Maths etc.
	 */
	public $subjectTypes;
	
	/**
	 * @var array of modes set in config/main.php
	 * A type of subject can be e.g. Humanities or MFL or Maths etc.
	 */
	public $mode;
	
	/**
	 * @var integer The total amount of users that can be created. Set in config/main.php
	 */
	public $totalUsers;
	
	
	/**
	 * @var integer The total amount of DCPs/Targets that can be created. Set in config/main.php
	 */
	public $totalDcps;
	
	/**
	 * @var array  The No of Targets available e.g. 1-4 set in config/main.php
	 */
	//public $noTargets;
	
	/**
	 * @var array The MIS systems supported
	 */
	public $misSystems;

	/**
	 * @var array of supported truthy values that will evaluate to true in the database. Set in config/main.php
	 */
	public $truthyValues;

	/**
	 * @var array of supported falsy values that will evaluate to false in the database. Set in config/main.php
	 */
	public $falsyValues;

	/**
	 * @var array of male values that will be evaluated as male in the database. Set in config/main.php
	 */
	public $maleValues;
	
	/**
	 * @var array of female values that will be evaluated as female in the database. Set in config/main.php
	 */
	public $femaleValues;
	
	/**
	 * @var array of sen values that will be evaluated as pupils who are SEN in the database. Set in config/main.php
	 */
	public $senValues;

	/**
	 * @var array of sen values that will be evaluated as pupils who are SEN without statement in the database. Set in config/main.php
	 */
	public $senWithoutStatementValues;

	/**
	 * @var array of eal values that will be evaluated as pupils who have english as their first language.
	 * Using NOT IN this value we can get pupils who's first language is NOT English in the database. Set in config/main.php
	 */
	public $ealValues;

	/**
	 * @var array of eal values that will be evaluated as pupils who are english.
	 * Using NOT IN this value we can get pupils who are an ethnic minority in the database. Set in config/main.php
	 */
	public $ethnicMinorityValues;



	/**
	 * @var integer The number of seconds that cached queries will be cached for. 1000=16mins
	 */
	public $queryCacheTime=1000;
	
	public $filterColumn;
	
	//Used for caching
	private $_qualifications=array();
	private $_subjects=array();
	private $_discountCodes=array();
	private $_subjectOptions=array();

	private $_cohorts=array();
	private $_fieldsToMapOptions=array();
	private $_fieldsToMap=array();
	private $_ksFieldsToMap=array();
	private $_keyStages=array();
	private $_mis;


	
	/*
	 * Initialse the component
	 * In here you can put stuff that you want doing once the component has been initialized.
	 * If we don't want anything doing then maybe we don't need it?
	 */
	/*
	public function init()
	{
		parent::init();
	}
	*/
	
	
	/**
	 * @return array An array of system year groups
	 */
	public function getSystemYearGroups()
	{
			return $this->systemYearGroups;
	}
	
	/**
	 * @return array An array of system year groups formated for dropDownList
	 */
	public function getSystemYearGroupsDropDown()
	{
		$array = $this->systemYearGroups;
		
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	/**
	 * Returns an array of year groups created from each allowed key stage year group collection
	 * @return array 
	 */
	public function getYearGroups()
	{
		
		foreach($this->systemKeyStages as $keyStage){
			$stageName = 'ks'.$keyStage.'YearGroups';
				foreach(Yii::app()->controller->schoolSetUp[$stageName] as $year){
					$years[]=$year;
				}
		}

		return $years;

	}
	
	/**
	 * @return array an array of year groups formated for dropDownList
	 */
	public function getYearGroupsDropDown()
	{
		$array = $this->getYearGroups();
		
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	
	/**
	 * @return array an array of MIS systems formated for dropDownList
	 */
	public function getMisSystemsDropDown()
	{
		$array=$this->misSystems;
		
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	
	/**
	 * @return array An array of available cohorts
	 */
	public function getCohorts()
	{
		if($this->_cohorts)
		return $this->_cohorts;
		
		return $this->_cohorts=Cohort::getCohortIds();
	}
	

	
	/**
	 * @return array an array of cohorts for use in a Drop down list
	 */
	public function getCohortsDropDown()
	{
		$array =  $this->cohorts;
		
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	/**
	 * Returns an array of distinct values from columns in the pupil table.
	 * These are commonly used to build drop downs
	 * @return array
	 */
	public function getPupilFilter($cohortId)
	{
		$sql="SELECT DISTINCT(".$this->filterColumn.") FROM pupil WHERE cohort_id=:cohortId ORDER BY $this->filterColumn";
		$command=Yii::app()->db->cache($this->queryCacheTime)->createCommand($sql);
		$command->bindParam(":cohortId",$cohortId,PDO::PARAM_INT);
		
		return $command->queryColumn();
		
	}
	/**
	 * Returns an array to be used in a drop down list
	 * @param string $column The name of the column to get the data for within the pupil table
	 */
	public function getPupilFilterDropDown($filterColumn,$cohortId)
	{
		$this->filterColumn=$filterColumn;
		
		$array  = $this->getPupilFilter($cohortId);



		if($array){
			if($array[0]=='')
				$array[0]='Blank';
		return array_combine(array_values($array), $array);
		}
		
		return array();
	}
	
	
	/**
	 * Returns a list of distinct subjects that exist in the setdata table. When setdata gets updated which it does
	 * every day then this list will also be up-to-date
	 * @return array An array of existing subjects.
	 */
	public function getSubjects($cohortId)
	{
		if($this->_subjects)
		return $this->_subjects;
		
		
		$sql="SELECT DISTINCT mapped_subject FROM setdata WHERE cohort_id='$cohortId' ORDER BY mapped_subject";
		$command=Yii::app()->db->createCommand($sql);
		$column=$command->queryColumn();
		
		return $this->_subjects=$column;
	}
	
	/**
	 * @return array An array of existing subjects in key=>value format that can be used in a drop down list
	 */
	public function getSubjectsDropDown($cohortId)
	{
		$array =  $this->getSubjects($cohortId);

		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}

	/**
	 * Returns a list of distinct discount codes that exist in the setdata table.
	 * @return array An array of existing discount codes.
	 */
	public function getDiscountCodes($cohortId){

		if($this->_discountCodes)
		return $this->_discountCodes;
		
		$sql="SELECT DISTINCT discount_code FROM subjectmapping WHERE cohort_id='$cohortId' AND discount_code !='' ORDER BY discount_code";
		$command=Yii::app()->db->createCommand($sql);
		$column=$command->queryColumn();
		
		return $this->_discountCodes=$column;

	}

	/**
	 * @return array An array of existing discount_codes in key=>value format that can be used in a drop down list
	 */
	public function getDiscountCodesDropDown($cohortId)
	{
		$array =  $this->getDiscountCodes($cohortId);

		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	/**
	 * 
	 * Returns an array of HTML options containing the mapped_subject and the subject alias (subject)
	 */
	public function getSubjectsDropDownOptions()
	{
		if($this->_subjectOptions)
		return $this->_subjectOptions;
		
		$fields = $this->subjectsWithAlias;
		$jsDataField='data-field-alias';
		foreach($fields as $key=>$value)
		{
			$array[$value['mapped_subject']]=array($jsDataField=>$value['subject']);
		}
			return $this->_subjectOptions=$array;
	}
	
	/**
	 * Returns an array of HTML options containing the mapped_subject and the subject alias (subject) from the DB
	 */
	public function getSubjectsWithAlias()
	{
		$defaultCohort=Yii::app()->controller->schoolSetUp['defaultCohort'];
		$sql="SELECT DISTINCT(mapped_subject), subject FROM setdata WHERE cohort_id='$defaultCohort'";
		$connection=Yii::app()->db; 
		$command=$connection->createCommand($sql);
		$rows=$command->queryAll(); 
		return $rows;	
	}
	
	/**
	 * Retrieves an array of fields that can be mapped to a DCP these fields will come from different places
	 * depending on the MIS being used
	 * @return array the array of fields that can be mapped to DCPs
	 */
	public function getFieldsToMapDropDown()
	{
		if($this->_fieldsToMap)
		return $this->_fieldsToMap;
		
		$array = PtMisFactory::mis()->sharedFieldsDropDown;
		return $this->_fieldsToMap = $array;
		
	}
	
	/**
	 * @return array The array of options to add to as the 'options' param to DropDownList
	 * This helps with auto completion of the field alias
	 */
	public function getFieldsToMapDropDownOptions()
	{
		if($this->_fieldsToMapOptions)
		return $this->_fieldsToMapOptions;
		
		$array = PtMisFactory::mis()->sharedFieldsDropDownOptions;
		return $this->_fieldsToMapOptions = $array;
	}
	
	/**
	 * Retrieves an array of possible KS fields (general fields on PTP)
	 * depending on the MIS being used
	 * @return array the array of fields that can be mapped to key stage data
	 */
	public function getKsFieldsToMapDropDown()
	{
		if($this->_ksFieldsToMap)
		return $this->_ksFieldsToMap;

		$array = PtMisFactory::mis()->generalFieldsDropDown;
		return $this->_ksFieldsToMap = $array;
	}
	
	/**
	 * @return array An array of fields in key=>value format that can be used in a drop down list
	 */
	/*CURRENTLY OBSOLETE ????????
	public function getKsFieldsToMapDropDownOptions()
	{
		if($this->_ksFieldsToMapOptions)
		return $this->_ksFieldsToMapOptions;
	
		$array = PtMisFactory::mis()->generalFieldsDropDownOptions;
		return $this->_ksFieldsToMapOptions = $array;
		
	}*/
	
	/**
	 * @return array An array of key stages
	 */
	public function getKeyStages()
	{
		return $this->systemKeyStages;
	}
	
	/**
	 * @return array An array of fields in key=>value format that can be used in a drop down list
	 */
	public function getKeyStagesDropDown()
	{
		$array = $this->keyStages;
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	/**
	 * @return array An array of volume indicators in key=>value format that can be used in a drop down list
	 */
	public function getVolumeIndicatorsDropDown()
	{
		$array=$this->volumeIndicators;
		
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	/**
	 * @return array An array of equivalences in key=>value format that can be used in a drop down list
	 */
	public function getEquivalentsDropDown()
	{
		$array=$this->equivalents;
		
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	/**
	 * @return array An array of equivalences in key=>value JSON format
	 */
	public function getEquivalentsDropDownJson()
	{
		$array=$this->equivalents;
		
		if($array)
		return CJSON::encode(array_combine(array_values($array), $array));
		
		return CJSON::encode(array());
	}
	
	/**
	 * @return array An array of volume indicators in key=>value JSON format
	 */
	public function getVolumeDropDownJson()
	{
		$array=$this->volumeIndicators;

		if($array)
		return CJSON::encode(array_combine(array_values($array), $array));
		
		return CJSON::encode(array());
	}
	
	/**
	 * Returns an array of supported qualifications
	 */
	public function getQualifications()
	{
		if($this->_qualifications)
		return $this->_qualifications;
		
		$sql="SELECT DISTINCT(qualification) FROM lookup.ks4pointscore ORDER BY qualification";
		$command=Yii::app()->db->cache($this->queryCacheTime)->createCommand($sql);
		
		return $this->_qualifications=$command->queryColumn();
		
	}
	
	/**
	 * @return array An array of qualifications in key=>value format that can be used in a drop down list
	 */
	public function getQualificationsDropDown()
	{
		$array=$this->qualifications;
		
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	/**
	 * @return array An array of qualifications in key=>value JSON format
	 */
	public function getQualificationsDropDownJson()
	{
		$array=$this->qualifications;
		return CJSON::encode(array_combine(array_values($array), $array));
	}
	
	/**
	 * @return array An array of subject types in key=>value format that can be used in a drop down list
	 */
	public function getSubjectTypesDropDown()
	{
		$array=$this->subjectTypes;
		return array_combine(array_values($array), $array);
	}
	
	/**
	 * @return array An array of subject types in key=>value JSON format
	 */
	public function getSubjectTypesDropDownJson()
	{
		$array=$this->subjectTypes;
		return CJSON::encode(array_combine(array_values($array), $array));
	}
	
	/**
	 * Returns an array of year groups for a given key stage
	 * @param integer $ks The key stage to check
	 * @return array The array of year groups
	 */
	public function getYearGroupsForKeyStage($ks)
	{
		
		switch($ks)
		{
			case(3):
				$array = Yii::app()->controller->schoolSetUp['ks3YearGroups'];
				break;
			case(4):
				$array = Yii::app()->controller->schoolSetUp['ks4YearGroups'];
				break;
			case(5):
				$array = Yii::app()->controller->schoolSetUp['ks5YearGroups'];
				break;
				
		}
		return $array;
	}
	
	/**
	 * Returns an array in key=>value format that can be used in a drop down list
	 * @param integer $ks The key stage to get the year groups for
	 */
	public function getYearGroupsForKeyStageDropDown($ks)
	{
		$array=$this->getYearGroupsForKeyStage($ks);
		
		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
		
	}
	
	/**
	 * Returns the key stage for a given year group. This method loops through the allowed Key Stages
	 * and if the year group is in one of the allocated key stage groups it returns that key stage.
	 * This ensures that only year groups from system key stages can be returned
	 * @param integer $yearGroup The specified year group
	 * @return mixed
	 */
	public function getKeyStageForYearGroup($yearGroup)
	{
		foreach($this->systemKeyStages as $keyStage){
			$stageName = 'ks'.$keyStage.'YearGroups';
			$ks = Yii::app()->controller->schoolSetUp[$stageName];
			if(in_array($yearGroup,$ks))
			return $keyStage;
		}
	}
	
	
	/**
	 * @return array An array of roles in key=>value format that can be used in a drop down list
	 */
	public function getRolesDropDown()
	{
		$array =  $this->roles;

		if($array)
		return array_combine(array_values($array), $array);
		
		return array();
	}
	
	/**
	 * Returns true if data setup is complete. That is all setup components have been completed
	 */
	public function getSetUpIsComplete()
	{
		if(Yii::app()->controller->schoolSetUp===null)
		return false;
		
		if(!Cohort::getSetUpIsComplete())
		return false;
		
		if(!Indicator::getSetUpIsComplete())
		return false;
		
		if(!KeyStage::getSetUpIsComplete())
		return false;
		
		if(!Subject::getSetUpIsComplete())
		return false;
		
		if(!FieldMapping::getSetUpIsComplete('dcp'))
		return false;
		
		if(!FieldMapping::getSetUpIsComplete('target'))
		return false;
		
		return true;
		
	}
	
	/**
	 * Returns true if all the steps necessary to build the core data have been completed.
	 */
	public function getCoreDataSetUpIsComplete()
	{
		if(Yii::app()->controller->schoolSetUp===null)
		return false;
		
		if(!Cohort::getSetUpIsComplete())
		return false;
		
		if(!Indicator::getSetUpIsComplete())
		return false;
		
		if(!KeyStage::getSetUpIsComplete())
		return false;
		
		return true;
		
	}
	
	/**
	 * Returns a string that can be appended to email sending success flash message
	 * @return string;
	 */
	public function getEmailAdvice()
	{
		$message = "<br><br><strong>Some Friendly Email Advice</strong>";
		$message .="<br>Provided you entered a valid email address you <strong>will</strong> receive this email.";
		$message.="<br>It <strong>has been sent</strong> and no gremlins have kidnapped it on route to you.";
		$message .="<br>However, here is some helpful advice should the email not appear in your inbox.";
		$message .="<ul><li>Check your spam or junk email folder. It might be in there</li>";
		$message .="<li>If the email is in your spam folder then make sure you add the domain pupiltracking.com to your safe senders list</li>";
		$message .="<li>Have you exceeded your email quota? Some web based email systems allow minimum storage capacity.";
		$message .=" You may have to delete some old emails before you can receive new ones</li></ul>";
		return $message;
		
	}
	
	
	/**
	 * Returns the text to be displayed in CGridView for the column default
	 * @return string the HTML to display within the default column cell
	 */
	public function gridDefaultColumn($data,$row)
	{
		return ($data->default) ? '<span class="label label-success">Default</span>' : '';
	}

	/**
	 * Returns a string of truthy values accepted by analytics suitable for direct use in an SQL IN clause
	 * This is set in config/main.php
	 * @return string
	 */
	public function getTruthyValuesString()
	{
		return "'".implode("','",$this->truthyValues)."'";
	}

	/**
	 * Returns a string of truthy values accepted by analytics suitable for direct use in an SQL IN clause
	 * This is set in config/main.php
	 * @return string
	 */
	public function getFalsyValuesString()
	{
		return "'".implode("','",$this->falsyValues)."'";
	}

	/**
	 * Returns a string of male values accepted by analytics suitable for direct use in an SQL IN clause
	 * This is set in config/main.php
	 * @return string
	 */
	public function getMaleValuesString()
	{
		return "'".implode("','",$this->maleValues)."'";
	}

	/**
	 * Returns a string of female values accepted by analytics suitable for direct use in an SQL IN clause
	 * This is set in config/main.php
	 * @return string
	 */
	public function getFemaleValuesString()
	{
		return "'".implode("','",$this->femaleValues)."'";
	}

	/**
	 * Returns a string of SEN values accepted by analytics suitable for direct use in an SQL IN clause
	 * This is set in config/main.php
	 * @return string
	 */
	public function getSenValuesString()
	{
		return "'".implode("','",$this->senValues)."'";
	}

	/**
	 * Returns a string of 'SEN without statement' values accepted by analytics suitable for direct use in an SQL IN clause
	 * This is set in config/main.php
	 * @return string
	 */
	public function getSenWithoutStatementValuesString()
	{
		return "'".implode("','",$this->senWithoutStatementValues)."'";
	}

	/**
	 * Returns a string of eal values accepted by analytics suitable for direct use in an SQL IN clause
	 * This is set in config/main.php
	 * @return string
	 */
	public function getEalValuesString()
	{
		return "'".implode("','",$this->ealValues)."'";
	}

	/**
	 * Returns a string of ethnic minority values accepted by analytics suitable for direct use in an SQL IN clause
	 * This is set in config/main.php
	 * @return string
	 */
	public function getEthnicMinorityValuesString()
	{
		return "'".implode("','",$this->ethnicMinorityValues)."'";
	}


	/**
	 * Returns an array of 2 columns. This is a utility method for parsing
	 * columns when they are used in the format col1, col2, col3, col4 etc. The method takes the column name and then gets the next column e.g. col4
	 * @param string $columnName The name of the column
	 * @param string $shift The direction to shift in , forwards, backwards or empty string to stay where you are
	 * @param int $amount The amount or number of cells to shift in either direction
	 * @return array
	 */
	public function parseGridColumns($columnName,$shift='',$amount=0)
	{
		$column = array();
		$colNo = str_replace('col', '', $columnName);

		switch($shift){
			case('forward'):
			$colNo +=$amount;
			break;
			case('backwards'):
			$colNo -= $amount;
			break;
		}
	
		$column[0] = 'col'.$colNo;
		$column[1] = 'col'.($colNo+1);

		return $column;

	}
	

	
	

	
}