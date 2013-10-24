<?php

/**
 * This is the model class for table "dcp".
 *
 * The followings are the available columns in table 'dcp':
 * @property integer $id
 * @property string $cohort_id
 * @property string $mapped_field
 * @property string $mapped_alias
 * @property integer $year_group
 * @property string $date
 * @property integer default
 */
class FieldMapping extends CActiveRecord implements iPtSetUp
{
	public $_mis;
	public $old_mapped_field;
	public $buildData=false;
	private $_missingFromResultSet;
	private $_triggerMissingPupilsEvent;
	
	public static $_fieldMappingsForYearGroup;


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Dcp the static model class
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
		return 'fieldmapping';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		    array('mapped_alias', 'filter', 'filter'=>'trim'),
			array('cohort_id, year_group, mapped_field, date', 'required'),
			array('date', 'type', 'type'=>'date', 'dateFormat'=>'dd-MM-yyyy'),
			array('year_group', 'numerical', 'integerOnly'=>true),
			array('year_group','validateDcpTotal','on'=>'create'),
			array('cohort_id', 'length', 'max'=>10),
			array('cohort_id', 'validateCohortId'),
			array('mapped_field', 'length', 'max'=>50),
			array('mapped_field', 'validateField'),
			array('mapped_field','triggerBuild','on'=>'create,update,build'),
			array('mapped_alias','required','on'=>'editable'),
			array('mapped_alias', 'length', 'max'=>50),
			array('default','validateDefault'),
			array('date', 'validateDate'),
			array('id, cohort_id, mapped_field, year_group', 'safe', 'on'=>'search'),
			array('default','safe'),
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
	
	/**
	 * Only limit the scope for dcp and target controllers. Return no scope if called from outside these controllers
	 * @return [type] [description]
	 */
	public function defaultScope()
    {

    	if(Yii::app()->controller->id=='dcp' || Yii::app()->controller->id=='target'){
        $array= array(
            'condition'=>"type='".Yii::app()->controller->id."'",
        	'order'=>'last_built DESC',
        );
    	}
    	else{
    		$array = array();
    	}

    	return $array;
    }
	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		
		return array(
			'id' => 'ID',
			'cohort_id' => 'Cohort',
			'mapped_field' => 'Result Set',
			'mapped_alias' => 'Name',
			'year_group' => 'Year Group',
			'date' => 'Date',
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
		$criteria->compare('mapped_field',$this->mapped_field,true);
		$criteria->compare('mapped_alias',$this->mapped_alias,true);
		$criteria->compare('year_group',$this->year_group);
		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'pagination'=>array(
       			 'pageSize'=>12,
    		),
		
		));
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
	
	/*
	 * Is fired before the record is saved to the database
	 */
	public function beforeSave()
	{
		if(parent::beforeSave()){
		$this->type=Yii::app()->controller->id;
		$this->date = Yii::app()->dateFormatter->format('yyyy-MM-dd',$this->date);
		$this->mapped_alias = ($this->mapped_alias=="") ? $this->mapped_field : $this->mapped_alias;
		
		if($this->buildData)
		$this->last_built= new CDbExpression('NOW()');
		else
		$this->last_built = Yii::app()->dateFormatter->format('yyyy-MM-dd HH:mm:ss',$this->last_built);
		
		//Override the above if it is a new record
		if($this->isNewRecord)
		$this->last_built= new CDbExpression('NOW()');
		
		return true;
		}
	}
	
	/**
	 * Flags the building of data. Called on create,update and build
	 */
	public function triggerBuild($attribute, $params)
	{
		$this->buildData = ($this->old_mapped_field != $this->mapped_field) ? true : false;
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
			if($this->buildData){
				$cohort= Cohort::getCurrentCohort();
					if($cohort['id']==$this->cohort_id){
					Yii::app()->build->buildSubjectDataForFieldMapping($this);
					Yii::app()->build->buildMetaData($this);
					Yii::app()->user->setFlash('success',"<strong>Success!</strong> ".$this->mapped_alias.' has been rebuilt.');
					}	
				else{
				Yii::app()->user->setFlash('error','<strong>Error!</strong> '.$this->mapped_field.' could not be rebuilt because it is not within the cohort <strong>'.$cohort['id'].'</strong>.');	
				}
			}
			//Clear the cache
			$keyStage = Yii::app()->common->getKeyStageForYearGroup($this->year_group);
			Yii::app()->dataCache->deleteDataCache($this->cohort_id,$keyStage);
			parent::afterSave();
	}
	
	/*
	 * Is fired after the query is ran by AR, but before the property is returned
	 */
	public function afterFind()
	{
		parent::afterFind();
		$this->date = Yii::app()->dateFormatter->format('dd-MM-yyyy',$this->date);
		$this->last_built = Yii::app()->dateFormatter->format('dd-MM-yyyy HH:mm:ss',$this->last_built);
	}
	
	/**
	 * Is fired after a record is deleted from the database
	 */
	public function afterDelete()
	{
		Yii::app()->build->deleteSubjectDataForFieldMapping($this);
		Yii::app()->build->deleteKs4Meta($this);
		//Further code here to delete field mapping ids from any table where it features
		Yii::app()->eventLog->log("info",PtEventLog::FIELDMAPPING_3,"Field mapping '{$this->mapped_alias}' ID $this->id has been deleted.");
		parent::afterDelete();
	}
	
	/**
	 * Validate the default DCP/Target
	 * This is only fired for the default cohort otherwise cohorts in the past will have their defaults cleared
	 */
	public function validateDefault($attribute, $params)
	{
		if($this->cohort_id==Yii::app()->controller->schoolSetUp['defaultCohort']){
			if($this->default==1){
			$sql="UPDATE fieldmapping SET `default`=:default WHERE cohort_id=:cohort_id AND year_group=:year_group
			AND type=:type";
			$connection=Yii::app()->db; 
			$command=$connection->createCommand($sql);
			$command->bindValue(":default",0,PDO::PARAM_INT);
			$command->bindParam(":cohort_id",$this->cohort_id,PDO::PARAM_STR);
			$command->bindParam(":year_group",$this->year_group,PDO::PARAM_INT);
			$command->bindParam(":type",Yii::app()->controller->id,PDO::PARAM_STR);
			//We can use numRows later to tell if there is no default cohort!!! or NOT???
			$numRows = $command->execute(); 
			}
		}
	}
	
	
	/*
	 * Validates that the date given is between the term start and end dates for this cohort
	 */
	public function validateDate($attribute,$params)
	{

		$sql="SELECT term_start_date, term_end_date FROM cohort WHERE id=:id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":id",$this->cohort_id,PDO::PARAM_STR);
		$row=$command->queryRow();
	
		$start = strtotime($row['term_start_date']);
		$end = strtotime($row['term_end_date']);
		$date =strtotime($this->date);
		
		if(($start <= $date) && ($end >= $date))
		return;
		
		$this->addError('date','The date is not between the start and end dates of this cohort.');
	}
	
	/**
	 * Validates that the chosen field is actually available on the MIS.
	 * This is belt and braces. The chances of the field not being available straight after selection is low
	 */
	public function validateField($attribute,$params)
	{
		
		if(!in_array($this->mapped_field,Yii::app()->common->fieldsToMapDropDown))
		{
			if(Yii::app()->controller->schoolSetUp['mis']=="PTP")
				$message = "is not currently available on your PTP system.";
			else
				$message = " does not exist as a result set for this cohort. 
				Please import a new result set with the same name or map this DCP/Target to an existing result set.";
			$this->addError('mapped_field',$this->mapped_field.' '.$message);
		};
		
	}
	
	/**
	 * Validates that cohort id does not occur in the past
	 */
	public function validateCohortId()
	{
		if(Cohort::getCohortInPast($this->cohort_id))
		{
			$this->addError('cohort_id','You can\'t map a result set for a cohort in the past.');
		}	
	}
	
	
	/**
	 * Returns the field mapping rows for a specific cohort
	 * @param string $cohortId
	 * @return array
	 */
	public static function getFieldMappingForCohort($cohortId)
	{
		$sql="SELECT * FROM fieldmapping WHERE cohort_id=:cohortId ORDER BY id";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":cohortId",$cohortId,PDO::PARAM_STR);
		$rows=$command->queryAll();
		
		return $rows;
	}
	/**
	 * Returns a list of field mappings for specific year groups and cohort.
	 * Note this returns an array which needs to be referenced e.g. $fieldMapping['id']
	 * therefore more fields can be added to this query/table
	 * @param string $cohortId The cohort ID, normally the current cohort
	 * @param array $yearGroups An array of year groups
	 * @return array
	 */
	public static function getFieldMappingsForYearGroup($cohortId,$yearGroups)
	{
		if(self::$_fieldMappingsForYearGroup)
		return self::$_fieldMappingsForYearGroup;
		
		$yearGroups = implode(",",$yearGroups);
		$sql="SELECT * FROM fieldmapping WHERE cohort_id=:cohortId AND year_group IN ($yearGroups) ORDER BY type";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":cohortId",$cohortId,PDO::PARAM_STR);
		$rows=$command->queryAll();
		
		return self::$_fieldMappingsForYearGroup=$rows;
	}
	
	/**
	 * Returns a key => value array to be used in a drop down
	 * @param string $cohortId the cohort e.g. 2011-2012
	 * @param array $yearGroups an array of year groups
	 * @return array
	 */
	public static function getFieldMappingsForYearGroupDropDown($cohortId,$yearGroups)
	{
		$fieldMappings=self::getFieldMappingsForYearGroup($cohortId,$yearGroups);
		if($fieldMappings){
			foreach($fieldMappings as $fieldMapping)
			{
				$type=($fieldMapping['type']=="dcp") ? "DCP" : "Target";
				$array[$fieldMapping['id']]=$type."-".$fieldMapping['mapped_alias'];
			}
		return $array;
		}
		
		return array();
		
	}
	
	/**
	 * Returns the default DCP and target for a specific year group
	 * @param string $cohortId
	 * @param integer $yearGroup
	 * @return array
	 */
	public static function getFieldMappingDefaults($cohortId,$yearGroup)
	{
		$fieldMappings=self::getFieldMappingsForYearGroup($cohortId,array($yearGroup));
		
		if($fieldMappings){
			foreach($fieldMappings as $fieldMapping)
			{
				if($fieldMapping['default']==1)
				{
					$array[$fieldMapping['type']]=$fieldMapping['id'];
				}
			}
			return $array;
		}
		
		return array();
	}
	
	/**
	 * Updates the name of a mapped field when a result set name is changed
	 * @param Result $object
	 * @return void
	 */
	public static function updateMappedFieldName($object)
	{
		$sql="UPDATE fieldmapping SET mapped_field=:name WHERE cohort_id=:cohortId AND mapped_field=:oldName";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":name",$object->name,PDO::PARAM_STR);
		$command->bindParam(":oldName",$object->oldName,PDO::PARAM_STR);
		$command->bindParam(":cohortId",$object->cohort_id,PDO::PARAM_STR);
		$command->execute();

	}
	
	/**
	 * Returns an array of pupils/fieldmapping_ids missing from a particular data set. Note that a pupil
	 * must appear in
	 * @return array
	 */
	public function getMissingFromResultSet($fieldMappingId=null)
	{
		
		if($this->_missingFromResultSet!==null)
		return $this->_missingFromResultSet;
		
		if($fieldMappingId!==null)
		$sql="SELECT t1.pupil_id, t1.surname, t1.forename, t1.year, t1.form";
		else
		$sql="SELECT derived.fieldmapping_id";
		
		$sql.=" FROM  pupil t1
				INNER JOIN ((
				SELECT DISTINCT fieldmapping_id
				FROM subjectdata
				WHERE fieldmapping_id IS NOT NULL
				AND cohort_id=:cohortId
				) AS derived , fieldmapping t4)

				ON(derived.fieldmapping_id=t4.id
				AND t1.year=t4.year_group)

				WHERE NOT EXISTS (
				SELECT *
				FROM subjectdata t2
				WHERE t2.cohort_id = t1.cohort_id
				AND t2.pupil_id = t1.pupil_id
				AND t2.fieldmapping_id = derived.fieldmapping_id
				)
				AND t1.cohort_id=:cohortId";
				if($fieldMappingId!==null)
				$sql.=" AND derived.fieldmapping_id=:fieldMappingId";
		
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":cohortId",$this->cohort_id,PDO::PARAM_STR);
		if($fieldMappingId!==null){
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		return $this->_missingFromResultSet = $command->queryAll();
		}
		else{
		$this->_missingFromResultSet = $command->queryColumn();//returns an empty array if empty so safe to use
		Yii::app()->eventLog->log( "success", PtEventLog::FIELDMAPPING_5, "Pupils missing from result set were fetched.");
		Yii::app()->settings->set('cache','missingPupilsFromResultSet',$this->_missingFromResultSet);
		return $this->_missingFromResultSet;
		
		}
	}
	
	/**
	 * Returns a list of field mapping Ids for a cohort and an array of year groups
	 * @param string $cohortId The cohort ID, normally the current cohort
	 * @param array $yearGroups An array of year groups
	 * @return array
	 */
	public static function getFieldMappingIdsForYearGroup($cohortId,$yearGroups)
	{
		$yearGroups = implode(",",$yearGroups);
		$sql="SELECT id FROM fieldmapping WHERE cohort_id='$cohortId' AND year_group IN ($yearGroups)";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryColumn();
	}
	
	/**
	 * Renders the data set column in _grid view
	 */
	public function renderMissingPupilsColumn($data,$row)
	{
		
		if($this->triggerMissingPupilsEvent){
		$missing = $this->missingFromResultSet;
		}
		else{
			$cache = Yii::app()->settings->get('cache','missingPupilsFromResultSet');
			$missing= ($cache==null) ? array() : $cache;
		}

		if(in_array($data->id,$missing))
		return CHtml::link('Possibly?',array('review','id'=>$data->id),array('title'=>'Review result set','rel'=>'tooltip'));
		else return "<span class='label'>No</span>";
		
	}
	
	/**
	 * Renders the buttons in the grid
	 */
	public function renderButton($data,$row)
	{	
		 Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup', array(
		'type' => 'primary',
		'size'=>'small',		 
		'buttons' => array(
		array('label' => 'Verify', 
			  'url' =>'#',
			  'htmlOptions'=>array('class'=>'verify',
			  		'title'=>'Verify or Rebuild result set',
					'rel'=>'tooltip',
					'data-id'=>$data->id,
					'data-field'=>$data->mapped_alias,
					'')),
		array('items' => array(
			array('label' => 'Rebuild', 'url' => array('buildSubjectData','id'=>$data->id),
				  'linkOptions'=>array(
					'class'=>'rebuild',
					)),
			/*		
			array('label' => 'Verify', 'url' => '#',
				  'linkOptions'=>array(
					'class'=>'verify',
					'data-id'=>$data->id,
					'data-field'=>$data->mapped_alias,
					)),*/
					
				)),
			),
		));
		
	}
	
	/**
	 * @return boolean
	 */
	public function getTriggerMissingPupilsEvent()
	{
		if($this->_triggerMissingPupilsEvent!==null)
		return $this->_triggerMissingPupilsEvent;
		
		$triggers = array (
		PtEventLog::FIELDMAPPING_4, 
		PtEventLog::SUBJECT_1,
		PtEventLog::BUILD_1 );
		
		return $this->_triggerMissingPupilsEvent = Yii::app()->eventLog->getTriggerEvent( PtEventLog::FIELDMAPPING_5, $triggers);
	}
	
	/**
	 * @return bool Whether or not the setup is complete or not
	 */
	public static function getSetUpIsComplete($type="dcp")
	{
		$yearGroups = Yii::app()->common->yearGroups;
		$yearGroupsString = implode(',',$yearGroups);

		$defaultCohort = Yii::app()->controller->schoolSetUp['defaultCohort'];
		
			$sql="SELECT COUNT(*) FROM fieldmapping WHERE year_group IN ($yearGroupsString) AND `type`=:type AND `default`=:default AND cohort_id=:cohort_id";
			$command=Yii::app()->db->createCommand($sql);
			$command->bindValue(":default",1,PDO::PARAM_INT);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->bindParam(":cohort_id",$defaultCohort,PDO::PARAM_STR);
			$value = $command->queryScalar();
			return ($value>=count($yearGroups)) ? true : false;
	}
	
	/**
	 * Returns the total number of DCPs or Targets on the system for a specific year group
	 * @return integer
	 */
	public function getDcpTotal()
	{
		$sql="SELECT COUNT(*) FROM fieldmapping WHERE cohort_id=:cohortId AND year_group=:year_group AND type=:type";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":type",Yii::app()->controller->id,PDO::PARAM_STR);
		$command->bindParam(":cohortId",$this->cohort_id,PDO::PARAM_STR);
		$command->bindParam(":year_group",$this->year_group,PDO::PARAM_INT);
		return $command->queryScalar();
	}
	
	/**
	 * Validates the dcps against the allowed number
	 * @return boolean
	 */
	public function validateDcpTotal($attribute,$params)
	{
		$totalDcpsAllowed = Yii::app()->common->totalDcps;
		
		if($this->dcpTotal >= $totalDcpsAllowed)
		$this->addError('year_group','DCP/Targets limit of '.$totalDcpsAllowed.' has been reached.<br> You may need to upgrade your account.');
		
	}
	
	/**
	 * Returns an array of pupils who are in a class, but do not have any results in the subjectdata table
	 * @param integer $fieldMappingId The fieldmapping id
	 * @return array
	 */
	public static function getVerifyResults($fieldMappingId)
	{
		$dcp=FieldMapping::model()->findByPk($fieldMappingId);
		
		$sql="SELECT t1.pupil_id, t1.surname, t1.forename, t1.year, t4.set_code, t3.mapped_subject, t3.subject, t2.result
			FROM pupil t1 INNER JOIN subjectdata t2 USING(cohort_id,pupil_id)
			INNER JOIN subjectmapping t3 ON(t3.cohort_id=t2.cohort_id AND t3.id=t2.subjectmapping_id)
			INNER JOIN setdata t4 ON(t4.cohort_id = t3.cohort_id AND t4.pupil_id = t2.pupil_id AND t4.mapped_subject = t3.mapped_subject)
			WHERE t1.cohort_id='{$dcp->cohort_id}'
			AND t2.fieldmapping_id='{$dcp->id}'
			AND (t2.result='' OR t2.result IS NULL)
			ORDER BY t1.surname,t1.forename";
		$command = Yii::app()->db->createCommand($sql);
		return $command->queryAll();
		
	}
	
	/**
	 * Returns an array of subjects that are on the system but not included in the result set
	 * @param integer $fieldMappingId The field mapping id
	 * @return array
	 */
	public static function getVerifySubjects($fieldMappingId)
	{
		$dcp=FieldMapping::model()->findByPk($fieldMappingId);
		$keyStage = Yii::app()->common->getKeyStageForYearGroup($dcp->year_group);
		
		$sql="SELECT mapped_subject, subject FROM subjectmapping t1 LEFT JOIN
				(SELECT subjectmapping_id AS id FROM subjectdata WHERE fieldmapping_id='{$dcp->id}') AS derived
				ON(t1.id = derived.id)
				WHERE t1.cohort_id='{$dcp->cohort_id}'
				AND t1.key_stage='$keyStage'
				AND derived.id IS NULL ORDER BY mapped_subject";
		$command = Yii::app()->db->createCommand($sql);
		return $command->queryAll();
	}
	
	/**
	 * Returns an array of pupils,subjects etc that have sets on the system but have no entries in the results set
	 * @param integer $fieldMappingId The field mapping id
	 * @return array
	 */
	public static function getVerifyPupils($fieldMappingId)
	{
		$dcp=FieldMapping::model()->findByPk($fieldMappingId);
		$sql="SELECT t1.pupil_id, t2.surname, t2.forename, t2.year, t1.set_code, t1.mapped_subject, t1.subject
				FROM setdata t1 INNER JOIN pupil t2 USING(cohort_id,pupil_id) 
				LEFT JOIN
				(
				SELECT t1.subjectmapping_id, t1.pupil_id, t2.mapped_subject, t1.fieldmapping_id, t2.id
				FROM subjectdata t1 INNER JOIN subjectmapping t2 ON(t1.subjectmapping_id = t2.id)
				WHERE t1.fieldmapping_id='{$dcp->id}'
				) AS derived
				ON(t1.mapped_subject=derived.mapped_subject 
				AND t1.pupil_id=derived.pupil_id)
				WHERE t2.cohort_id='{$dcp->cohort_id}'
				AND t2.`year`='{$dcp->year_group}'
				AND t1.mapped_subject IN(SELECT DISTINCT(mapped_subject) FROM subjectmapping WHERE cohort_id='{$dcp->cohort_id}')
				AND derived.subjectmapping_id IS NULL";
		$command = Yii::app()->db->createCommand($sql);
		return $command->queryAll();
		
	}
	
	/**
	 * Returns an array of pupils who currently have a fail for a qualification. E.g.
	 * if the qualification for a subject is BTEC and the results are in GCSE format e.g. A,B,C
	 * then the result will be a fail
	 * @param integer $fieldMappingId The field mapping id
	 * @return array
	 */
	public static function getVerifyFails($fieldMappingId)
	{
		$dcp=FieldMapping::model()->findByPk($fieldMappingId);
		$sql="SELECT t1.pupil_id, t2.surname, t2.`forename`, t2.year, 
			t3.mapped_subject,t3.subject, t3.qualification, t1.result FROM ks4meta t1 INNER JOIN pupil t2 USING(cohort_id,pupil_id)
			INNER JOIN subjectmapping t3 ON(t3.id = t1.subjectmapping_id)
			WHERE t1.cohort_id='{$dcp->cohort_id}'
			AND t1.fieldmapping_id='{$dcp->id}'
			AND t1.standardised_points='0'";
		$command = Yii::app()->db->createCommand($sql);
		return $command->queryAll();
	}
	
	
}