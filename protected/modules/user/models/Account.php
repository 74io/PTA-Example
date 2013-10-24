<?php
class Account extends CActiveRecord
{
	/**
	 * The following are the available columns in table 'user':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $role
	 * @var string $salt
	 * @var string $email
	 * @var string $active
	 * @var string $activation_id
	 * @var string account_created
	 */
	public $currentPassword;
	public $newPassword;
	public $hiddenPassword;
	public $repeatPassword;
	public $previousEmail;
	public $previousUsername;
	public $captchaCode;
	public $agree;

	

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
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
		return 'user';
	}
	
	/**
	 * Attach behaviors
	 */
	public function behaviors()
	{
		return array('superAccount'=>array(
    		'class'=>'application.components.PtSuperAccountBehavior',
				),
			);
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(

			/**
			 * EMAIL
			 */
			array('email, currentPassword','required','on'=>'updateEmail'),
			array('email','required','on'=>'recovery'),
			array('email','email','on'=>'recovery, updateEmail'),
			
			//Validate that email is unique, but allow current email
			array('email','unique',
			'attributeName'=>'email',
			'criteria'=>array('condition'=>'email !=:previousEmail',
				'params'=>array(':previousEmail'=>$this->previousEmail)),
			'message'=>'Email already exists.',
			'on'=>'updateEmail'),
			
			//Validate that email exists in the users table
			array('email','exist',
			'attributeName'=>'email',
			'allowEmpty'=>false,
			'message'=>'{attribute} does not exist on this system.',
			'on'=>'recovery'),
			
			/**
			 * USERNAME
			 */
			array('username,currentPassword','required','on'=>'updateUsername'),
			array('username', 'match', 'pattern'=>'/^([a-z0-9_])+$/','on'=>'updateUsername'),
			array('username','length','min'=>6, 'max'=>128, 'on'=>'updateUsername'),
			array('username','unique',
			'attributeName'=>'username',
			'criteria'=>array('condition'=>'email !=:previousUsername',
				'params'=>array(':previousUsername'=>$this->previousUsername)),
			'message'=>'Username already exists.',
			'on'=>'updateUsername'),
	
			/**
			 * PASSWORD
			 */
			array('currentPassword','required','on'=>'updatePassword'),
			array('newPassword, repeatPassword','required','on'=>'updatePassword,reset'),
			array('currentPassword','validateCurrentPassword','on'=>'updatePassword,updateUsername,updateEmail'),
			array('newPassword', 'length', 'max'=>128, 'on'=>'updatePassword,reset'),
			array('newPassword','ext.validators.EPasswordStrength', 'min'=>8, 'on'=>'updatePassword,reset'),
			array('repeatPassword','compare', 
					'compareAttribute'=>'newPassword',
					'on'=>'updatePassword,reset',
					'message'=>'Passwords do not match.'),
			array('captchaCode','captcha','on'=>'recovery'),
			
			/**
			 * AGREE
			 */
			array('agree', 'compare', 'compareValue' => 1, 'message'=>'Please check the box if you agree.','on'=>'upgradeAccount'),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$emailLabel = $this->scenario=='recovery' ? 'Email' : 'New Email';
		 
		return array(
			'username' => 'New Username',
			'email' => $emailLabel,
			'captchaCode'=>'Verification Code',
			'agree'=>'I Agree',
		);
	}
	
	/**
	 * Validate Password applied in rules above
	 */
	public function validateCurrentPassword($attributes,$params)
	{
		$user= new User;
		$bool=$user->hashPassword($this->currentPassword,$this->salt)===$this->hiddenPassword;
		if(!$bool)
		$this->addError('currentPassword','Current password is incorrect.');
		
	}
	
	/*
	 * Acts on the user object before it is saved
	 */
	protected function beforeSave()
	{
	    if(parent::beforeSave())
	    {
	    	//Scenario specific overrides
	    	switch($this->scenario)
	    	{
	    		case('updatePassword'):
	    		$user=new User;
	    		$this->salt=$user->generateSalt();
	        	$this->password = $user->hashPassword($this->newPassword,$this->salt);		
	    		break;
	    		case('recovery'):
	    		$this->recovery_id = uniqid('',true);
	    		break;
	    		case('reset'):
	    		$user=new User;
	    		$this->salt=$user->generateSalt();
	        	$this->password = $user->hashPassword($this->newPassword,$this->salt);	
	        	$this->recovery_id='';//Reset the id so it can't be used again
	    		break;

	    	}
	    
	    return true;
	   }
	}
	
	/*
	 * Acts on the user object after it is saved
	 */
	protected function afterSave()
	{
		switch($this->scenario)
		{
			case('updatePassword'):
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_4,'User '.$this->username.' password updated.');
			break;
			
			case('updateEmail'):
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_5,'User '.$this->username.' email updated.');
			break;
			
			case('updateUsername'):
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_6,'User '.$this->username.' username updated.');
			break;
			
			case('recovery'):
			$this->sendRecoveryEmail();
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_9,'User '.$this->username.' requested a password reset email.');
			break;
			
			case('upgradeAccount'):
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_2,'User '.$this->username.' upgraded their PTA account.');
			break;
			
			case('reset'):
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_9,'User '.$this->username.' reset their password.');
			break;
			
		}
	    parent::aftersave();
	}
	
	
	/**
	 * Sends a password recovery email to the user
	 */
	public function sendRecoveryEmail()
	{
		$link = Yii::app()->request->hostInfo.'/user/account/reset?rid='.$this->recovery_id.'&uid='.$this->id;
		$body = "Hello ".$this->username;
		$body .= "\nYou requested this password reset link for username: ".$this->username;
		$body .="\nClick on the one time link below to reset your password:";
		$body .="\n".$link;

		Yii::app()->mailer->AddAddress($this->email);
		Yii::app()->mailer->Subject = 'Pupil Tracking Analytics Password Reset';
		Yii::app()->mailer->Body = $body;
		Yii::app()->mailer->Send();
		
	}
	
}