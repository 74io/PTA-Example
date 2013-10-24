<?php

/**
 * This is the model class for table "subject".
 *
 * The followings are the available columns in table 'subject':
 * @property integer $id
 * @property string $cohort_id
 * @property integer $key_stage
 * @property string $mapped_subject
 * @property string $subject
 * @property string $qualification
 * @property integer $volume
 * @property integer equivalent
 * @property string $type
 * @property integer $include
 */
class Subject extends CActiveRecord implements iPtSetUp
{
	public $oldQualification;
	public $attributeName;
	
	//Set defualts
	public $include=1;
	public $equivalent=1;
	public $volume=1;
	public $type="None";
	public $qualification ="GCSE";

	private $_excludedSetIds;
	private $_excludedPupilIds;
	private $_excludedSubjectIds;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Subject the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subjectmapping';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$qualificationRange = Yii::app()->common->qualifications;
		$subjectTypes= Yii::app()->common->subjectTypes;
		$this->oldQualification = $this->qualification;
		
		return array(
			array('cohort_id, key_stage, mapped_subject, subject', 'required', 'on'=>'create,editable'),
			array('key_stage, include, equivalent', 'numerical', 'integerOnly'=>true),
			array('volume,equivalent','safe'),
			array('discount_code','length','max'=>10),
			array('discount_code','match', 'pattern'=>'/^([a-zA-Z0-9_])+$/','message'=>'Alphanumeric chars only please.'),
			array('subject, discount_code','filter', 'filter'=>'trim'),
			array('cohort_id','validateCohortId'),
			//array('discount_code','filter'=>array('PtFilter','stripSpecialChars')),
			array('subject','validateSubjectName'),
			array('qualification','in','range'=>$qualificationRange),
			array('type','in','range'=>$subjectTypes),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cohort_id, key_stage, mapped_subject, subject, qualification, volume, equivalent type, discount_code, include', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}
	
	public function defaultScope()
    {
        return array(
            'order'=>"key_stage, subject",
        );
    }
	
	/**
	 * Validates that the given subject name has not been used before with the
	 * same combination of cohort_id, key_stage and mapped_subject
	 */
	public function validateSubjectName($attribute,$params)
	{
		
		$criteria=new CDbCriteria;
		$criteria->select='subject';
		$criteria->condition='cohort_id=:cohort_id AND key_stage=:key_stage AND subject=:subject ';
		if($this->scenario=='update')
		$criteria->condition.='AND id!='.$this->id;
		$criteria->params=	array(':cohort_id'=>$this->cohort_id,
		':key_stage'=>$this->key_stage,
		':subject'=>$this->subject,
		);
		
		$subject=self::model()->find($criteria);
		
		if($subject)
		$this->addError('subject','A Subject with this name already exists for this Key Stage and Cohort');
		
	}
	
	/**
	 * Validates the cohort id
	 */
	public function validateCohortId()
	{
		$currentCohortId=Yii::app()->build->currentCohortId;
		$dateNow = substr($currentCohortId, -4);
		$date =  substr($this->cohort_id, -4);
		
		if($date<$dateNow)
		{
			$this->addError('cohort_id','You can\'t create a subject for a cohort in the past.');
		}
		
	}
	
	/*
	 * Triggered before the record is saved to the database
	 */
	public function beforeSave()
	{
	
		//Set defaults
		$this->discount_code=strtoupper($this->discount_code);
		$this->volume = ($this->volume == "") ? '1.0' : $this->volume;
		$this->equivalent = ($this->equivalent == "") ? '0' : $this->equivalent;
		$this->type = ($this->type == "") ? "None" : $this->type;
		switch($this->key_stage)
			{
			case(3):
				$this->qualification="None";//Always override for now
				$this->volume='1.0';
				$this->type="None";
			break;
			case(4):
				if($this->qualification=="")
				$this->qualification="GCSE";
			break;		
			} 
			
		parent::beforeSave();
		
		return true;
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
		if($this->isNewRecord)
		{
			$currentCohortId = Yii::app()->build->currentCohortId;
			if($currentCohortId==$this->cohort_id){
			Yii::app()->build->buildSubjectDataForSubject($this);
			Yii::app()->build->buildMetaData($this);
			}
			else{
				Yii::app()->user->setFlash('error',$this->mapped_subject.' could not be built because it is not within the cohort <strong>'.$currentCohortId.'</strong>.');
			}
		
			//Write to log if it is a new record
			Yii::app()->eventLog->log("success",
			PtEventLog::SUBJECT_1,
			"The subject '{$this->subject}' ID $this->id has been created.",
			$this->key_stage,
			$this->id);
		}
		
		
		//Rebuild the ks4meta table if the qualification changes. This will assign new point scores to results
		if(!$this->isNewRecord){

			if($this->oldQualification!=$this->qualification)
			{
				Yii::app()->build->buildMetaData($this);
			}
		}
		
		//Clear the cache
		Yii::app()->dataCache->deleteDataCache($this->cohort_id,$this->key_stage);
		parent::afterSave();
		
	}
	
	/*
	 * Triggered after the record is deleted from the database
	 * Deletes all entries in the excludedpupils and excluded sets table
	 */
	public function afterDelete()
	{
		$tables=array('excludedpupils','excludedsets');
		foreach($tables as $table){
		$sql="DELETE FROM $table WHERE subjectmapping_id=:subject_id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":subject_id",$this->id,PDO::PARAM_INT);
		$command->execute(); 
		}
		//Clean up...
		Yii::app()->build->deleteSubjectData($this);
		Yii::app()->build->deleteKs4Meta($this);
		Yii::app()->dataCache->deleteDataCache($this->cohort_id,$this->key_stage);
		Yii::app()->eventLog->log("info",PtEventLog::SUBJECT_3,"The subject '{$this->mapped_subject}' ID $this->id has been deleted.",$this->key_stage);
	}
	
	
	/*
	 * This behaviour remembers the filter used for search in this model
	 */
	public function behaviors() {
	       return array(
	           'ERememberFiltersBehavior' => array(
	               'class' => 'application.components.ERememberFiltersBehavior',
	               'defaults'=>array('cohort_id'=>Yii::app()->controller->schoolSetUp['defaultCohort']),           /* optional line */
	               'defaultStickOnClear'=>false   /* optional line */
	           ),
	       );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cohort_id' => 'Cohort',
			'key_stage' => 'Key Stage',
			'mapped_subject' => 'Mapped Subject',
			'subject' => 'Subject Name',
			'qualification' => 'Qualification',
			'volume' => 'Volume',
			'equivalent'=> 'Equivalent',
			'type' => 'Type',
			'discount_code'=>'Discount Code',
			'include' => 'Include',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('cohort_id',$this->cohort_id,true);
		$criteria->compare('key_stage',$this->key_stage);
		$criteria->compare('mapped_subject',$this->mapped_subject,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('qualification',$this->qualification,true);
		$criteria->compare('volume',$this->volume);
		$criteria->compare('equivalent',$this->equivalent);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('discount_code',$this->discount_code);

		$dataProvider =  new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'pagination'=>array(
        		'pageSize'=>40,
    			),
		));
		
		return $dataProvider;
	}
	
	
	/**
	 * Checks that subject mapping table contains entries for the current cohort
	 * @return integer
	 */
	public static function getSetUpIsComplete()
	{
		$defaultCohort = Yii::app()->controller->schoolSetUp['defaultCohort'];
		$sql="SELECT COUNT(*) FROM subjectmapping WHERE cohort_id='$defaultCohort'";
		$command=Yii::app()->db->createCommand($sql);
		$value=$command->queryScalar();
		return $value;
	}
	
	
	/**
	 * Updates the include column in the subjectmapping table
	 * @param array $param An array of parameters passed via jquery data-paramname
	 * @return void 
	 */
	public static function updateInclude($param)
	{
		$sql="UPDATE subjectmapping SET include=:include WHERE id=:id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":include",$param['value'],PDO::PARAM_INT);
		$command->bindParam(":id",$param['id'],PDO::PARAM_INT);
		$numRows = $command->execute(); 
		
		Yii::app()->dataCache->deleteDataCache($param['cohortId'],$param['keyStage']);
		Yii::app()->eventLog->log("success",PtEventLog::SUBJECT_2,"Update included/excluded subject",$param['keyStage']);
	}
	
	/**
	 * Updates the excludedpupils table
	 * @param array $param The posted values
	 * @return void
	 */
	public static function updateExcludedPupil($param)
	{
		if($param['checked']==1){
		$sql="INSERT INTO excludedpupils (subjectmapping_id, pupil_id, set_code) VALUES(:subject_id, :pupil_id, :set)";
		}
		else{
			$sql="DELETE FROM excludedpupils WHERE subjectmapping_id=:subject_id AND pupil_id=:pupil_id AND set_code=:set";
		}
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":subject_id",$param['subject_id'],PDO::PARAM_INT);
		$command->bindParam(":pupil_id",$param['pupil_id'],PDO::PARAM_STR);
		$command->bindParam(":set",$param['set'],PDO::PARAM_STR);
		$command->execute(); 
		
		Yii::app()->dataCache->deleteDataCache($param['cohortId'],$param['keyStage']);
		
		Yii::app()->eventLog->log("success",PtEventLog::SUBJECT_2,"Update included/excluded pupil",$param['keyStage']);
	}
	
	/**
	 * Updates the excludedpupils table
	 * @param array $param The posted values
	 * @return void
	 */
	public static function updateExcludedSet($param)
	{
		if($param['checked']==1){
		$sql="INSERT INTO excludedsets (subjectmapping_id, set_code) VALUES(:subject_id, :set)";
		}
		else{
			$sql="DELETE FROM excludedsets WHERE subjectmapping_id=:subject_id AND set_code=:set";
		}
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":subject_id",$param['subject_id'],PDO::PARAM_INT);
		$command->bindParam(":set",$param['set'],PDO::PARAM_STR);
		$command->execute(); 
		

		Yii::app()->dataCache->deleteDataCache($param['cohortId'],$param['keyStage']);
		
		Yii::app()->eventLog->log("success",PtEventLog::SUBJECT_2,"Update included/excluded set",$param['keyStage']);
	}
	
	/**
	 * 
	 * Returns an array of pupils from the excludedpupils table for a given subject id and set
	 * @param integer $subject_id
	 * @param string $set
	 * @return array
	 */
	public static function getExcludedPupils($subject_id, $set)
	{
		
		$sql="SELECT pupil_id FROM excludedpupils WHERE subjectmapping_id=:subject_id AND set_code=:set";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":subject_id",$subject_id,PDO::PARAM_INT);
		$command->bindParam(":set",$set,PDO::PARAM_STR);
		return $command->queryColumn(); 
	}
	
	/**
	 * Returns an array of set codes from the excludedpupils table for a given subject id
	 * @param integer $subject_id
	 * @return array
	 */
	public static function getExcludedPupilsSets($subject_id)
	{
		$sql="SELECT DISTINCT(set_code) FROM excludedpupils WHERE subjectmapping_id=:subject_id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":subject_id",$subject_id,PDO::PARAM_INT);
		return $command->queryColumn(); 
	}
	
	/**
	 * Returns an array of set codes from the excludedsets table for a given subject id
	 * @param integer $subject_id
	 * @return array
	 */
	public static function getExcludedSets($subject_id)
	{
		$sql="SELECT DISTINCT(set_code) FROM excludedsets WHERE subjectmapping_id=:subject_id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":subject_id",$subject_id,PDO::PARAM_INT);
		return $command->queryColumn(); 
	}
	
	/**
	 * Returns an array of subject ids from the excludedsets table
	 * @return array
	 */
	public function getExcludedSetIds()
	{
		if($this->_excludedSetIds!==null)
		return $this->_excludedSetIds;
		
		$sql="SELECT DISTINCT(subjectmapping_id) FROM excludedsets";
		$command=Yii::app()->db->createCommand($sql);
		return $this->_excludedSetIds = $command->queryColumn();
	}
	
	/**
	 * Returns an array of subject ids from the excluded pupils table
	 * @return array
	 */
	public function getExcludedPupilIds()
	{
		if($this->_excludedPupilIds!==null)
		return $this->_excludedPupilIds;
		
		$sql="SELECT DISTINCT(subjectmapping_id) FROM excludedpupils";
		$command=Yii::app()->db->createCommand($sql);
		return $this->_excludedPupilIds = $command->queryColumn();
	}
	
	/**
	 * Returns an array of subject ids combined from both the excluded sets and excluded pupils tables
	 * @return array
	 */
	public function getExcludedSubjectIds()
	{
		if($this->_excludedSubjectIds!==null)
		return $this->_excludedSubjectIds;
			
		$ids = array_merge($this->excludedSetIds,$this->excludedPupilIds);
		return  $this->_excludedSubjectIds = array_unique($ids);
	}

	/**
	 * [getExcludedPupilsInSimilarSubject description]
	 * @param object $model The model object
	 * @param string $set The pupils set
	 * @return array
	 */
	public static function getPupilsExcludedAlready($model, $set)
	{

		$sql="SELECT pupil_id, set_code FROM excludedpupils
		WHERE subjectmapping_id IN(
		SELECT DISTINCT(id) FROM subjectmapping 
		WHERE cohort_id='{$model->cohort_id}'
		AND mapped_subject='{$model->mapped_subject}'
		)
		AND set_code='$set'
		AND subjectmapping_id!='{$model->id}'";

		$command=Yii::app()->db->createCommand($sql);
		return $command->queryColumn();
	}
	
	/**
	 * Returns an array of subject data. Note this returns an array which always needs to reference the
	 * subject e.g. $subject['id'], $subject['mapped_subject']. Therefore more fields can be added to this
	 * query as necessary
	 * @param string $cohort_id the cohort id e.g. 2011-2012
	 * @param integter $keyStage The keystage to get subjects for e.g. 3,4 or 5
	 * @return array
	 */
	public static function getSubjectsForKeyStage($cohortId,$keyStage)
	{
		
		$sql="SELECT id, mapped_subject FROM subjectmapping WHERE cohort_id='$cohortId' AND key_stage='$keyStage'";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryAll();
		
	}
	
	/**
	 * Returns an array of subject ids for a cohort and keystage
	 * @param string $cohort_id the cohort id e.g. 2011-2012
	 * @param integter $keyStage The keystage to get subjects for e.g. 3,4 or 5
	 * @return array;
	 */
	public static function getSubjectsIdsForKeyStage($cohortId,$keyStage)
	{
		$sql="SELECT id FROM subjectmapping WHERE cohort_id='$cohortId' AND key_stage='$keyStage'";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryColumn();
	}
	
	/**
	 * Returns an array of distinct subjects for a cohort
	 * @param string $cohort_id
	 * @return array
	 */
	public static function getSubjects()
	{
		$defaultCohort = Yii::app()->controller->schoolSetUp['defaultCohort'];
		$sql="SELECT DISTINCT mapped_subject FROM subjectmapping WHERE cohort_id='$defaultCohort'";
		$command=Yii::app()->db->createCommand($sql);
		$column=$command->queryColumn();
		
		return $column;
	}
	
	/**
	 * Returns the subjects in mapped_subject => subject format
	 * @return array
	 */
	public static function getSubjectKeyValuePairs()
	{
		$defaultCohort = Yii::app()->controller->schoolSetUp['defaultCohort'];
		$sql="SELECT DISTINCT mapped_subject, subject FROM subjectmapping WHERE cohort_id='$defaultCohort'";
		$command=Yii::app()->db->createCommand($sql);
		$rows=$command->queryAll();

		return $rows;

/*
		if($rows){
			foreach($rows as $key=>$value){
				$array2[]=$array[$value['mapped_subject']]=$value['subject'];
			}
			return $array2;
		}
		else{
			return array();
		}*/
		
	}
	
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
	 * @param string $mappedSubject The subject to get sets for
	 * @param array $yearGroups An array of year groups e.g. 7,8,9
	 * @param string $cohortId The id of the cohort to fetch sets for
	 * @return array
	 * 
	 */
	public static function getSubjectSetsForYearGroups($mappedSubject,$yearGroups,$cohortId)
	{
		
		$yearGroups = implode(",",$yearGroups);
	
		$sql="SELECT DISTINCT(set_code) AS set_code, pupil.year
		FROM setdata INNER JOIN pupil using(cohort_id,pupil_id)
		WHERE setdata.mapped_subject = '$mappedSubject'
		AND pupil.cohort_id = :cohortId
		AND pupil.year IN($yearGroups)";

		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':cohortId', $cohortId, PDO::PARAM_STR);
		$dataReader=$command->query();
		
		foreach($dataReader as $row){
			$array[$row['year']][]=$row['set_code'];
		}

		return $array;
	}
	
	
	/**
	 * Returns a list of sets for a given subject
	 * @param string $mappedSubject The subject to get sets for
	 * @param integer $year The year group to return sets for
	 * @param string $cohortId The id of the cohort to fetch sets for
	 * @return array an array of sets for the given year group
	 */
	public static function getSubjectSetsForYearGroup($mappedSubject,$year,$cohortId)
	{

		$sql="SELECT DISTINCT(set_code) AS set_code
		FROM setdata INNER JOIN pupil using(cohort_id,pupil_id)
		WHERE setdata.mapped_subject = '$mappedSubject'
		AND pupil.cohort_id = :cohortId
		AND pupil.year =:year";
		
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':year', $year, PDO::PARAM_INT);
		$command->bindParam(':cohortId', $cohortId, PDO::PARAM_STR);
		return $command->queryColumn();
		
	}
	
	/**
	 * Returns an array of pupils belonging to a specific set in a specific subject
	 * @param string $mappedSubject The subject to get sets for
	 * @param string $set The set code
	 * @param string $cohortId The id of the cohort to fetch sets for
	 * @return array An array of pupils in a specific set and year group
	 */
	public static function getPupilsInFilteredSet($mappedSubject,$set,$cohortId)
	{
		
		$sql="SELECT pupil_id, surname AS Surname, forename AS Forename
		FROM pupil INNER JOIN setdata USING (cohort_id,pupil_id)
		WHERE setdata.mapped_subject = '$mappedSubject'
		AND setdata.set_code=:set
		AND pupil.cohort_id = :cohortId
		ORDER BY surname, forename";
		
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':set', $set, PDO::PARAM_STR);
		$command->bindParam(':cohortId', $cohortId, PDO::PARAM_STR);
		return $command->queryAll(); 
	}
	
	/**
	 * Returns an array of KS4 qualifications
	 * @return array
	 */
	public static function getKs4Qualifications()
	{
		$sql="SELECT * FROM lookup.ks4pointscore
		ORDER by qualification, score DESC";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryAll(); 
	}
	
	/**
	 * Returns an array details for the provided qualification
	 * @param string $qualification The qualification to fetch results for
	 * @return array
	 */
	public static function getAcceptedResults($qualification)
	{
		$sql="SELECT * FROM lookup.ks4pointscore WHERE qualification=:qualification ORDER BY result";
		$command=Yii::app()->db->cache(1000)->createCommand($sql);
		$command->bindParam(':qualification',$qualification, PDO::PARAM_STR);
		return $command->queryAll();
	}
	

	/*
	 * Renders the CViewGrid sets column in _grid
	 */
	public function renderSetsColumn($data,$row)
	{
		$url=Yii::app()->controller->createUrl("sets",array('id'=>$data->id));
		$text = (in_array($data->id,$this->excludedSubjectIds)) ? 'Filtered' : 'All';

		return CHtml::link($text,$url,array('class'=>'filter-column'));
	}
	
	/**
	 * Returns an array of mapped_subjects that are on PTA but not available on the users MIS. I basically
	 * cross checks the subjectmapping subjects against the setdata mapped subjects. When the building of core
	 * data is done it loads the setdata based up the MISs current live state.
	 * @return mixed
	 */
	public static function getMissingSubjects()
	{
		$defaultCohort = Yii::app()->controller->schoolSetUp['defaultCohort'];
		$sql="SELECT t1.mapped_subject
				FROM subjectmapping t1
				WHERE NOT EXISTS(SELECT * FROM setdata t2 WHERE t1.mapped_subject = t2.mapped_subject AND t1.cohort_id = t2.cohort_id)
				AND t1.cohort_id='$defaultCohort'";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryColumn();
	}
}