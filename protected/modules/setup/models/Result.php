<?php

/** 
 * This is the model class for table "resultmapping". 
 * 
 * The followings are the available columns in table 'resultmapping': 
 * @property integer $id
 * @property string $cohort_id
 * @property integer $user_id
 * @property string $name
 * @property string $file_name
 * @property string $description
 * @property integer $num_records
 * @property string $date_time
 */ 
class Result extends CActiveRecord
{ 
	public $username;
	public $oldName;
	
    /** 
     * Returns the static model of the specified AR class. 
     * @param string $className active record class name. 
     * @return Result the static model class 
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
        return 'resultmapping'; 
    } 

    /** 
     * @return array validation rules for model attributes. 
     */ 
    public function rules() 
    { 
   		$this->oldName=$this->name;
        // NOTE: you should only define rules for those attributes that 
        // will receive user inputs. 
        return array( 
        	array('name', 'filter', 'filter'=>'trim'),
            //array('user_id, num_records', 'numerical', 'integerOnly'=>true),
            array('name','required'),
            array('name', 'length', 'max'=>50),
			array('name','unique',
			'attributeName'=>'name',
			'criteria'=>array('condition'=>'cohort_id =:cohortId',
				'params'=>array(':cohortId'=>$this->cohort_id)),
			'message'=>'Result name already exists.'),
            array('name','filter','filter'=>array('PtFilter','stripSpecialCharsRelaxed')),
            array('description','filter','filter'=>array('PtFilter','stripHtml')),
            //array('file_name', 'length', 'max'=>255),
            //array('description, date_time', 'safe'),
            // The following rule is used by search(). 
            // Please remove those attributes that should not be searched. 

            array('id, cohort_id, user_id, username, name, file_name, description, num_records, date_time', 'safe', 'on'=>'search'),
            array('name, description','safe'),
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
            'user'=>array(self::BELONGS_TO, 'User', array('user_id'=>'id'),
            'joinType'=>'INNER JOIN',
		    'select'=>'user.username',
		    //'together'=>true,
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
            'user_id' => 'User',
            'username' => 'User',
            'name' => 'Result Name',
            'file_name' => 'Filename',
            'description' => 'Description',
            'num_records' => 'No. Records',
            'date_time' => 'Date/Time',
        ); 
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
	 * Is fired after the query is ran by AR, but before the property is returned
	 */
	protected function afterFind()
	{
		parent::afterFind();
		$this->date_time = Yii::app()->dateFormatter->format('dd-MM-yyyy HH:mm:ss',$this->date_time);
		
	}

    /** 
     * Retrieves a list of models based on the current search/filter conditions. 
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions. 
     */ 
    public function search() 
    { 
        // Warning: Please modify the following code to remove attributes that 
        // should not be searched. 

        $criteria=new CDbCriteria; 

		$criteria->with = array('user');
		$criteria->compare('t.id',$this->id);
        $criteria->compare('cohort_id',$this->cohort_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('user.username',$this->username,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('file_name',$this->file_name,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('num_records',$this->num_records);
        $criteria->compare('date_time',$this->date_time,true);
        
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'sort'=>array(
				'defaultOrder'=>'t.id DESC',
		        'attributes'=>array(
		            'username'=>array(
		                'asc'=>'user.username',
		                'desc'=>'user.username DESC',
		            ),
		            '*',
		        ),
		    ),
		)); 
    } 
    
    /**
     * @see CActiveRecord::afterSave()
     */
    public function afterSave()
    {
    	FieldMapping::updateMappedFieldName($this);
    }
    
    /**
     * Returns a list of result set field names for the current cohort
     * 
     */
    public static function getResultSetFieldNames()
    {
    	$cohort = Cohort::getCurrentCohort();
    	$sql="SELECT name FROM resultmapping WHERE cohort_id=:cohortId";
    	$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':cohortId', $cohort['id'], PDO::PARAM_STR);
		return $command->queryColumn();
    }
    
    /**
     * Returns the entire result set row based upon its field name
     * @return mixed
     */
    public static function getResultSetFromFieldName($fieldName)
    {
    	$cohort = Cohort::getCurrentCohort();
    	$sql="SELECT * FROM resultmapping WHERE cohort_id=:cohortId AND name=:name";
    	$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':cohortId', $cohort['id'], PDO::PARAM_STR);
		$command->bindParam(':name', $fieldName, PDO::PARAM_STR);
		return $command->queryRow();
    }
    
    /**
     * Returns 
     */
    public static function getNumResults()
    {
    	$cohort = Cohort::getCurrentCohort();
    	$sql="SELECT COUNT(*) FROM resultmapping WHERE cohort_id=:cohortId";
    	$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':cohortId', $cohort['id'], PDO::PARAM_STR);
		return $command->queryScalar();
    		
    }
}