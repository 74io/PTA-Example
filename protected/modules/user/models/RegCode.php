<?php
class RegCode extends CFormModel
{
	public $registrationCode;
	public $expiryDate;
	
	
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// name, email, subject and body are required
			array('registrationCode, expiryDate', 'required'),
			array('registrationCode', 'length','min'=>8,'max'=>20),
			array('expiryDate', 'type', 'type'=>'date', 'dateFormat'=>'dd-MM-yyyy','message'=>'Date must be in the format dd-mm-yyyy'),
			array('expiryDate','validateExpiryDate'),
		);
	}
	
    public function attributeLabels()
    {
    	return array('registrationCode'=>'Registration Code',
    				'expiryDate'=>'Expiry Date',
    	    		);
    }
    
    public function validateExpiryDate()
    {
    	if((strtotime('today')> strtotime($this->expiryDate)))
		$this->addError('expiryDate','You cannot create an expiry date in the past.');
    	
    }
    
    /*
     * Load the settings
     */
    public function load()
    {
    	$settings=Yii::app()->settings->get("registration");
    	if($settings){//Assign settings to class properties
	    	foreach($settings as $key=>$value){
	    		$this->$key=$value;
	    	}
    	}

    }
    
    /*
     * Save the settings
     */
    public function save()
    {
	    Yii::app()->settings->set("registration",$this->attributes);
	    Yii::app()->eventLog->log( 'info', PtEventLog::USER_7,'Registration code was edited.');
    }
	
	
}