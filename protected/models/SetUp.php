<?php
class SetUp extends CFormModel
{
	public $name;
    public $mis;
    public $ks3YearGroups=array();
    public $ks4YearGroups=array();
    public $ks5YearGroups=array();
    public $ptpDbName;
    public $ptpSchoolId;
    public $defaultCohort;
    
    public function rules()
    {
    	$misSystems = Yii::app()->common->misSystems;
    	
        return array(
            array('mis, name, ptpDbName, ptpSchoolId,'.$this->safeKeyStageAttributes, 'required'),
            array($this->safeKeyStageAttributes,'validateYearGroups'),
            array('name','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
            array('mis','in','range'=>$misSystems),
             array('ptpSchoolId','validatePtpSchoolId'),
             array('ptpDbName','safe')
   
        );
    }
    
    public function attributeLabels()
    {
    	return array('name'=>'School Name',
    				'mis'=>'MIS',
    	    		'ks3YearGroups'=>'KS3 Year Groups',
    				'ks4YearGroups'=>'KS4 Year Groups',
    	    		'ks5YearGroups'=>'KS5 Year Groups',
    				'ptpDbName'=>'PTP Database Name',
    				'ptpSchoolId'=>'PTP School ID');
    }
    
    
	/**
	 * Validates that the school id is valid
	 */
    public function validatePtpSchoolId($attribute,$params)
    {
    	if($this->mis=='PTP'){
    		//We cant call Yii::app()->common here because the mis has not yet been saved
    		if(!PtPtp::schoolIsValid($this->ptpDbName,$this->ptpSchoolId))
    		$this->addError("ptpSchoolId","Your PTP DB name or school ID is incorrect.");
    	}
    }
    
    /**
     * Validates that the year groups provided at each key stage do not overlap
     */
    public function validateYearGroups($attribute,$params)
    {
    	$yearGroups = array_merge($this->ks3YearGroups,$this->ks4YearGroups ,$this->ks5YearGroups );
    	
    	if(count($yearGroups) != count(array_unique($yearGroups)))
    	$this->addError($attribute,'Year groups overlap key stages.');
    	
    	if(count($this->ks3YearGroups)>3)
    	$this->addError('ks3YearGroups','Only 3 year groups per key stage are allowed.');
    	
    	if(count($this->ks4YearGroups)>3)
    	$this->addError('ks4YearGroups','Only 3 year groups per key stage are allowed.');
    	
    	if(count($this->ks5YearGroups)>3)
    	$this->addError('ks5YearGroups','Only 3 year groups per key stage are allowed.');
    	
    }
    
    /**
     * Returns a string of safe attributes to only display key stage drop downs for
     * those who are allowed to access them
     * @return string
     */
    public function getSafeKeyStageAttributes()
    {
    	$allowedKeyStages = Yii::app()->common->keyStages;

    	foreach($allowedKeyStages as $keyStage){
    		$stages[]='ks'.$keyStage.'YearGroups';
    	}
    	return implode(',',$stages);
    }
    
    
    /*
     * Load the settings and assign settings to class properties
     */
    public function load()
    {
    	$settings=Yii::app()->settings->get("schoolSetUp");
    	if($settings){
	    	foreach($settings as $key=>$value){
	    		$this->$key=$value;
	    	}
    	}
    }
    
    public function beforeValidate()
    {
    	if($this->mis!="PTP"){
    	$this->ptpDbName="N/A";
    	$this->ptpSchoolId="N/A";
    	}
    	
    	return true;
    }
    
    /*
     * Save the settings
     */
    public function save()
    {
	    Yii::app()->settings->set("schoolSetUp",$this->attributes);
    }
 
}