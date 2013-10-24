<?php
class Ks4FF extends CFormModel
{
	public $pupilId; //Used in pupilController
	public $yearGroup='11';
	public $oldYearGroup='';
	public $gender='';
	public $ethnicity='';
	public $sen_code='';
	public $fsm='';
	public $gifted='';
	public $cla='';
	public $eal='';
	public $pupil_premium='';
	public $cohortId;
	public $oldCohortId;
	public $compare;
	public $compareTo;
	public $activeTab;
	public $mode='volume';
	
	//Vars used for displaying achievers and non achievers
	public $params; //The params var becomes the properties below
	public $groupFieldMappingId;
	public $groupMethod;
	public $groupArg=array();
	public $groupAchiever;
	public $opId; //The id of the option selected in a drop down button. 0 indexed.

	//Cached properties
	protected $_fieldMappingDefaults;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mode','validateMode'),
			array('cohort,yearGroup','validateDefaults'),
			array('params','validateParams'),
			//array('compare, compareTo','required'),
			array('compare, compareTo,oldYearGroup,yearGroup, gender,
			ethnicity,sen_code,fsm,gifted,cla,eal,pupil_premium,cohortId,oldCohortId,activeTab,mode,params,groupAchiever,pupilId, opId', 'safe'),
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
    public function attributeLabels()
    {
    	return array('sen_code'=>'SEN Code',
    				'fsm'=>'FSM',
    				'gifted'=>'Gifted',
    				'cla'=>'CLA',
    				'eal'=>'EAL',
    				'yearGroup'=>'Year',
    				'cohortId'=>'Cohort',
    				'compare'=>'Compare',
    				'compareTo'=>'To',
    				);
    }
    
    /**
     * Validates the default options
 	 * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function validateDefaults($attribute,$params)
    {
			$this->cohortId=($this->cohortId===null) ? Yii::app()->controller->schoolSetUp['defaultCohort'] : $this->cohortId;
			$this->oldCohortId=($this->cohortId===null) ? Yii::app()->controller->schoolSetUp['defaultCohort'] : $this->oldCohortId;
			
			$defaults=$this->fieldMappingDefaults;
			$this->compare=($this->compare===null) ? $defaults['dcp'] : $this->compare;
			$this->compareTo=($this->compareTo===null) ? $defaults['target'] : $this->compareTo;
			
			if($this->yearGroup!=$this->oldYearGroup)
			{
				$this->compare=$defaults['dcp'];
				$this->compareTo=$defaults['target'];
			}	
			
			
			if($this->cohortId!=$this->oldCohortId)
			{
				$this->compare=$defaults['dcp'];
				$this->compareTo=$defaults['target'];
			}
			
			if($this->activeTab===null)
			$this->activeTab='0';
			
			//var_dump($this->attributes);
    }
    
    /**
     * Returns default DCP and target for a specified cohort and year group
     */
    public function getFieldMappingDefaults()
    {
    	if($this->_fieldMappingDefaults!==null)
    	return $this->_fieldMappingDefaults;
    	else
    	return $this->_fieldMappingDefaults = FieldMapping::getFieldMappingDefaults($this->cohortId, $this->yearGroup);
    }
    
    /**
     * Sets the default mode. Validates the mode attribute. This is used in queries to name fields like subjectmapping.$type.
     */
    public function validateMode($attribute,$params)
    {
    	if($this->cohortId===null) 
    	$this->mode='equivalent';
    }
    
    /**
     * Converts the params string to model properties
     */
    public function validateParams()
    {
    	//Could use a scenario here but this makes things simpler in this instance
    	if(isset($this->params)){
    	
    	//method|fieldMappingId|param1|param2 
		$param =  explode('|',$this->params);
		$this->groupMethod= $param[0];
		$this->groupFieldMappingId = $param[1];
		$this->groupArg[] = $param[2];
		$this->groupArg[] =$param[3];
		$this->groupArg[] = $param[4];
		}
	}
    
    
}