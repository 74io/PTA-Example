<?php

class User extends CActiveRecord
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
	 */
	public $previousUsername;
	public $previousEmail;
	public $itemCount;
	public $repeatPassword;
	public $registrationCode;
	public $captchaCode;
	
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
	 * Possible scenarios in this model are update, create, register, changePassword
	 */
	public function rules()
	{
		return array(
			//Required
			array('username, email, ', 'required','on'=>'update, create, register'),
			array('role', 'required','on'=>'update, create'),
			array('role','validateRole','on'=>'update, create'),
			array('email', 'required','on'=>'create,update,register'),
			
			array('captchaCode','captcha','on'=>'register'),
			array('registrationCode', 'required','on'=>'register'),
			array('registrationCode', 'validateRegistrationCode','on'=>'register'),

			//Username
			array('username','length','min'=>6, 'max'=>128, 'on'=>'create,update,register'),
			array('username','unique',
			'attributeName'=>'username',
			'message'=>'Username already exists.',
			'on'=>'create,register'),
			//Vaidate the username but allow the current username
			array('username','unique',
			'attributeName'=>'username',
			'criteria'=>array('condition'=>'email !=:previousUsername',
				'params'=>array(':previousUsername'=>$this->previousUsername)),
			'message'=>'Username already exists.',
			'on'=>'update'),
			array('username', 'match', 'pattern'=>'/^([a-z0-9_])+$/','on'=>'create,update,register'),
			array('username', 'validateNotSystem'),
			//Password
			array('password, repeatPassword','required', 'on'=>'create,changePassword, register'),
			array('password', 'length', 'max'=>128, 'on'=>'create,changePassword,changePasswordUser,register'),
			array('password','ext.validators.EPasswordStrength', 'min'=>8, 'on'=>'create,changePassword,register'),
			array('repeatPassword','compare', 
					'compareAttribute'=>'password',
					'on'=>'create, changePassword, register',
					'message'=>'Passwords do not match.'),
			//Email
			array('email', 'email','on'=>'create,update,register'),
			array('email','unique',
			'attributeName'=>'email',
			'message'=>'Email already exists.',
			'on'=>'create,register'),
			//Validate the email but allow the existing email
			array('email','unique',
			'attributeName'=>'email',
			'criteria'=>array('condition'=>'email !=:previousEmail',
				'params'=>array(':previousEmail'=>$this->previousEmail)),
			'message'=>'Email already exists.',
			'on'=>'update'),
						
						
			//array('email', 'validateEmail','on'=>'create,update,register'),
			array('id, role, email, username', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'captchaCode'=>'Verification Code',
		);
	}
	
	
	/**
	 * Validates the registration code for users who try to create an account
	 */
	public function validateRegistrationCode($attribute,$params)
	{
		$settings=Yii::app()->settings->get("Registration");
		
		//No registration code has been set 
		if($settings['registrationCode']===null || $settings['registrationCode']==null)
		$this->addError('registrationCode','Registration code is incorrect.');
		
		// Has the registration code exipred?
		if(strtotime($settings['expiryDate'])<strtotime('today'))
		$this->addError('registrationCode','The registration code has expired. You need a new code.');
		

		if($settings['registrationCode']!==$this->registrationCode)
		$this->addError('registrationCode','Registration code is incorrect.');
	}
	
	/**
	 * Validate the role. If the user is super then add super to the list of roles
	 * that is allowed to be created
	 */
	public function validateRole($attribute,$params)
	{
	
		$roles = Yii::app()->common->roles;
		
		//If creating a user then only allow current roles i.e. not super as only one super user should exist
		if($this->scenario=='create'){
		if(!in_array($this->role,$roles))
		$this->addError('role','Role is invalid.');
		return;
		}
		
		if(Yii::app()->user->role=='super'){
			$roles[]='super';
		}
		
		if(!in_array($this->role,$roles))
		$this->addError('role','Role is invalid.');
	}
	
	/**
	 * Validate the username to ensure that it is not the reserved 'system' user
	 */
	public function validateNotSystem($attribute,$params)
	{
		if(strtolower($this->username)=="system")
		$this->addError('username','System is a reserved username.');
		
	}

	
	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)
	{
		return $this->hashPassword($password,$this->salt)===$this->password;
	}
	

	/**
	 * Generates the password hash.
	 * @param string password
	 * @param string salt
	 * @return string hash
	 */
	public function hashPassword($password,$salt)
	{
		return md5($salt.$password);
	}

	/**
	 * Generates a salt that can be used to generate a password hash.
	 * @return string the salt
	 */
	public function generateSalt()
	{
		return uniqid('',true);
	}
	
	/*
	 * Acts on the user object before it is saved
	 */
	protected function beforeSave()
	{
	    if(parent::beforeSave())
	    {
	    	if($this->isNewRecord){
	    	$this->active=1;
	    	$this->activation_id = $this->generateSalt();
	    	$this->account_created = new CDbExpression('NOW()');
	    	$this->salt=$this->generateSalt();
	        $this->password = $this->hashPassword($this->password,$this->salt);
	        }
	    	
	    	//Scenario specific overrides
	    	switch($this->scenario)
	    	{
	    		case('register'):
	    			$this->active=0;
	    			$this->role='staff';	
	    		break;
	    		case('changePassword'):
	    		$this->salt=$this->generateSalt();
	        	$this->password = $this->hashPassword($this->password,$this->salt);	
	    		break;
	    	}
	    
	    return true;
	    
	   }
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
		
		switch($this->scenario)
		{
			case('create'):
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_1,'User '.$this->username.' was created.');
			break;
			
			case('update'):
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_2,'User '.$this->username.' was updated.');
			break;
			
			case('changePassword'):
			Yii::app()->eventLog->log( 'info', PtEventLog::USER_4,'User '.$this->username.' password updated.');
			break;
			
			case('register'):
			$this->sendRegistrationEmail();
	    	Yii::app()->eventLog->log( 'info', PtEventLog::USER_7,$this->email.' created an account.','',$this->id);
			break;
			
		}

	    parent::aftersave();

	}
	
	
	/**
	 * Called after the AR record has been deleted
	 * @see CActiveRecord::afterDelete()
	 */
	protected function afterDelete()
	{
		Yii::app()->eventLog->log( 'info', PtEventLog::USER_3,'User '.$this->username.' was deleted.');
		parent::afterDelete();
	}

	
	/*
	 * Is fired after the query is ran by AR, but before the property is returned
	 */
	protected function afterFind()
	{
		parent::afterFind();
		$this->account_created = Yii::app()->dateFormatter->format('dd-MM-yyyy HH:mm:ss',$this->account_created);
		
	}
	
	/**
	 * Sends an email to the user who has just registered to use PTA
	 */
	protected function sendRegistrationEmail()
	{
		$link = Yii::app()->request->hostInfo.'/user/register/activate?acid='.$this->activation_id.'&uid='.$this->id;
		$body = "Thank you for creating an account. Activate your account by clicking on the link below:";
		$body .="\n\n".$link;

		Yii::app()->mailer->AddAddress($this->email);
		Yii::app()->mailer->Subject = 'Pupil Tracking Analytics Account Activation';
		Yii::app()->mailer->Body = $body;
		Yii::app()->mailer->Send();
	}
	
	/**
	 * Activates a user account
	 * @param string $acid
	 * @param integer $uid
	 */
	public function activateAccount($acid,$uid)
	{
		$sql="UPDATE user SET active=1 WHERE activation_id=:acid AND id=:uid";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":acid",$acid,PDO::PARAM_STR);
		$command->bindParam(":uid",$uid,PDO::PARAM_INT);
		$rowCount =  $command->execute();

		if($rowCount)
	    Yii::app()->eventLog->log( 'info', PtEventLog::USER_8,'User activated their account.','',(int)$uid);
		return $rowCount;
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
		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('active',$this->active,true);

		$dataProvider= new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'pagination'=>array(
        		'pageSize'=>30,
    		),
		));
		//Pass CActiveDataProvider::itemCount to this model
		$this->itemCount=$dataProvider->itemCount;
		
		return $dataProvider;
	}
	
	/**
	 * Returns the total number of users on the system
	 * @return integer
	 */
	public function getUserTotal()
	{
		$sql="SELECT COUNT(*) FROM user";
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}
	
	/**
	 * Returns true if the number of users is valid
	 * @return boolean
	 */
	public function getUserTotalIsValid()
	{
		$totalUsersAllowed = Yii::app()->common->totalUsers;
		
		if($totalUsersAllowed==0)
		return true;
		
		if($this->userTotal >= $totalUsersAllowed)
		return false;
		else 
		return true;
	}
	
	/*
	 * Returns an array of roles in key=>value format
	 */
	public function getRoles()
	{
		$connection=Yii::app()->db;
		$sql="SELECT DISTINCT(role) FROM user";
		$command=$connection->createCommand($sql);
		$column=$command->queryColumn();
		return array_combine(array_values($column), $column);
	}
	
	
	/**
	 * Renders a column in CGridView or a row in CDetailView
	 */
	public function renderActiveColumn($data,$row)
	{
		$data = (is_object($data)) ? $data->active : $data;
		
		if($data==0){
		$html='<span class="label">No</span>';
		}
		elseif($data==1){
		$html='<span class="label label-success">Yes</span>';
		}
		
		return $html;
	}

}