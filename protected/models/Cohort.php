<?php

/**
 * This is the model class for table "cohort".
 *
 * The followings are the available columns in table 'cohort':
 * @property string $id
 * @property string $term_start_date
 * @property string $term_end_date
 */
class Cohort extends CActiveRecord implements iPtSetUp
{
	/*
	 * The followings are the available columns in table 'cohort':
	 * @property string $id
	 * @property string $term_start_date
	 * @property string $term_end_date
	 * @property integer $default
	 */
	
	public $itemCount;
	public static $_currentCohort;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Cohort the static model class
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
		return 'cohort';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'length', 'max'=>10),
			array('term_start_date, term_end_date', 'required'),
			array('term_start_date, term_end_date', 'type', 'type'=>'date', 'dateFormat'=>'dd-MM-yyyy'),
			array('term_start_date','validateStartDate'),
			array('id','validateId','on'=>'create'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, term_start_date, term_end_date, default', 'safe', 'on'=>'search'),
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Cohort ID',
			'term_start_date' => 'Term Start Date',
			'term_end_date' => 'Term End Date',
			'default' => 'Make this the default cohort',
		);
	}
	
	/*
	 * Validates that the start date is not greater than the end date i.e. it is before it.
	 */
	public function validateStartDate($attribute,$params)
	{
		if((strtotime('today')< strtotime($this->term_start_date)))
		$this->addError('term_start_date','You cannot create a cohort in the future.');
		
		if((strtotime('today')> strtotime($this->term_end_date)))
		$this->addError('term_end_date','You cannot create a cohort in the past.');
		
		if(strtotime($this->term_start_date) > strtotime($this->term_end_date))
		$this->addError('term_start_date','Start date must be before end date.');

		$thisYear = $this->yearOfDate(strtotime('today'));
		$startYear = $this->yearOfDate($this->term_start_date);
		$endYear = $this->yearOfDate($this->term_end_date);
		
		if($startYear==$endYear)
		$this->addError('term_end_date','Start and end dates should not be in the same year.');
		
		if($endYear-$startYear>1)
		$this->addError('term_end_date','Start and end dates should only be 1 year apart.');
		
		if($this->startDateOverlaps)
		$this->addError('term_start_date','The start date overlaps another cohort.');
		
	}
	
	/*
	 * Validates the cohort id
	 */
	public function validateId($attribute,$params)
	{
		$startYear = $this->yearOfDate($this->term_start_date);
		$endYear = $this->yearOfDate($this->term_end_date);
		$this->id=$startYear."-".$endYear;
		$id=self::model()->findByPk($this->id);
		if($id!==null){
		$this->addError('term_start_date','There is already a cohort starting and ending in these years.');
		}
	}
	
	/*
	 * Short cut helper funtion to get e.g. 2011 from 01-07-2011
	 */
	private function yearOfDate($date)
	{
		return  Yii::app()->dateFormatter->format('yyyy',$date);
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id,true);
		$criteria->compare('term_start_date',$this->term_start_date,true);
		$criteria->compare('term_end_date',$this->term_end_date,true);

		$dataProvider= new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
		
		$this->itemCount=$dataProvider->totalItemCount;
		return $dataProvider;
	}
	
	/*
	 * Is fired before the record is saved to the database
	 */
	public function beforeSave()
	{
		if(parent::beforeSave()){
		$this->term_start_date = Yii::app()->dateFormatter->format('yyyy-MM-dd',$this->term_start_date);
		$this->term_end_date = Yii::app()->dateFormatter->format('yyyy-MM-dd',$this->term_end_date);
		$startYear = $this->yearOfDate($this->term_start_date);
		$endYear = $this->yearOfDate($this->term_end_date);
		$this->id=$startYear."-".$endYear;

		$this->clearDefaults();
		$this->default=1;

		return true;
		}

	}
	
	/*
	 * Is fired after the query is ran by AR, but before the property is returned
	 */
	public function afterFind()
	{
		parent::afterFind();
		$this->term_start_date = Yii::app()->dateFormatter->format('dd-MM-yyyy',$this->term_start_date);
		$this->term_end_date = Yii::app()->dateFormatter->format('dd-MM-yyyy',$this->term_end_date);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::afterSave()
	 * Note. This does not seem to fire when db profiling is enabled in config/dev.php?
	 */
	public function afterSave()
	{
		parent::afterSave();
		if($this->default==1)
		Yii::app()->settings->set("schoolSetUp","defaultCohort",$this->id);
	}
	
	/*
	 * Sets all the defualt cohorts to 0
	 */
	private function clearDefaults()
	{
		//Delete all default cohorts so that only one at a time can be saved.
		$sql="UPDATE cohort SET `default`=:default";
		$connection=Yii::app()->db;   // assuming you have configured a "db" connection
		$command=$connection->createCommand($sql);
		$command->bindValue(":default",0,PDO::PARAM_INT);
		//We can use numRows later to tell if there is no default cohort!!! or NOT???
		$numRows = $command->execute(); 
	}
	
	/**
	 * Returns all rows and columns from the cohort table
	 * @return array
	 */
	public static function getCohorts($id=null)
	{
		$sql="SELECT * FROM cohort";
		if($id!==null)
		$sql.=" WHERE id!='$id'";
		$command=Yii::app()->db->createCommand($sql);
		$rows=$command->queryAll();

		return $rows;	
	}
	
	/**
	 * Returns an array of cohort Ids e.g. 2011-2012, 2012-2013 etc..
	 */
	public static function getCohortIds()
	{
		$sql="SELECT DISTINCT(id) FROM cohort";
		$command=Yii::app()->db->createCommand($sql);
		$column = $command->queryColumn();

		return $column;
	}
	
	/**
	 * Returns the cohort that todays date currently belongs to
	 * @return string
	 */
	public static function getCurrentCohort()
	{
		if(self::$_currentCohort!==null)
		return self::$_currentCohort;
		
		$cohorts = self::getCohorts();
		$today = strtotime('today');
		foreach($cohorts as $cohort){
			if(($today>= strtotime($cohort['term_start_date'])) && ($today<= strtotime($cohort['term_end_date']))){
				return self::$_currentCohort=$cohort;
			}
		}
	}
	
	/**
	 * Returns true if a specified date falls between the start date and end date of the current cohort
	 * @param string $date The date to check
	 */
	public static function dateIsInCurrentCohort($date)
	{
		$cohort = self::getCurrentCohort();
		if(($date>= strtotime($cohort['term_start_date'])) && ($date<= strtotime($cohort['term_end_date'])))
		return true;
		else
		return false;
	}
	
	/**
	 * Returns true if the cohort provided is in the past
	 * @param string $cohortId the cohort to check
	 * @return boolean
	 */
	public static function getCohortInPast($cohortId)
	{
		$currentCohort=self::getCurrentCohort();

		$dateNow = substr($currentCohort['id'], -4);
		$date =  substr($cohortId, -4);
		
		if($date<$dateNow)
		{
			return true;
		}
		else{
			return false;
		}
	}
	
	
	/**
	 * Returns true if the start date overlaps another cohort
	 */
	public function getStartDateOverlaps()
	{
		$cohorts=$this->getCohorts($this->id);
		foreach($cohorts as $cohort)
		{
				if((strtotime($this->term_start_date)>= strtotime($cohort['term_start_date'])) && (strtotime($this->term_start_date)<= strtotime($cohort['term_end_date']))){
					return true;
			}
		}
		return false;
	}
	
	/**
	 * @return bool Whether or not the setup is complete or not
	 */
	public static function getSetUpIsComplete()
	{
		$sql="SELECT term_end_date FROM cohort WHERE `default`=:default";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindValue(":default",1,PDO::PARAM_INT);
		$row=$command->queryRow();
		if((strtotime($row['term_end_date']))< strtotime('today')){
			Yii::app()->settings->set("schoolSetUp","defaultCohort",null);
			return false;
		}

		return true;
	}
	
}