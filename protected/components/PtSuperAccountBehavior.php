<?php
class PtSuperAccountBehavior extends CActiveRecordBehavior{
	
	private $_accountType;
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecordBehavior::afterSave()
	 */
	public function afterSave($event){

		if($this->owner instanceof Account){
		switch($this->owner->scenario)
			{
				case('upgradeAccount'):
				$this->updateConfigFile();
				$this->updateSuperAccountType();
				$this->sendUpgradeAccountEmail();
				break;
				case('updateEmail'):
				$this->updateSuperAccountEmail();
				break;
			}
		}
		
		if($this->owner instanceof User){
		switch($this->owner->scenario)
			{
				case('update'):
				$this->updateSuperAccountEmail();
				break;
			}
		}
		
		
	}
	/**
	 * Returns an account type
	 * @param integer $type The type of account 0=Free, 1=Premium, 2=Unlimited etc...
	 * @return integer
	 */
	public function getSuperAccountWording()
	{
		switch($this->superAccountType){
			case 0:
				$accountType = "free";
			break;
			case 1:
				$accountType = "premium";
			break;
			case 2:
				$accountType = "unlimited";
			break;
			default:
				$accountType = "free";
		}
		return $accountType;
	}
	
	/**
	 * Returns the appropraite account wording for the account
	 * @return string
	 */
	public function getSuperAccountType()
	{
		if($this->_accountType!=null)
		return $this->_accountType;
		
		$sql="SELECT type FROM admin.account WHERE email=:email AND subdomain=:subdomain";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":email",$this->owner->email,PDO::PARAM_STR);
		$command->bindParam(":subdomain",Yii::app()->params['dbName'],PDO::PARAM_STR);
		return $this->_accountType =  $command->queryScalar();
	}
	
	
    /**
     * Updates the super users account type in the admin.account table
     * @return void
     */
    public function updateSuperAccountType()
    {
    	$sql="UPDATE admin.account SET type=:type, last_updated=NOW() WHERE email=:email AND subdomain=:subdomain";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":email",$this->owner->email,PDO::PARAM_STR);
		$command->bindParam(":subdomain",Yii::app()->params['dbName'],PDO::PARAM_STR);
		$command->bindValue(":type",1,PDO::PARAM_INT);
		$command->execute();
    }
    
    /**
     * Updates the super users account email in the admin.account table
     * @return void
     */
    public function updateSuperAccountEmail()
    {
    	if($this->owner->role=='super'){
    		if($this->owner->previousEmail!=$this->owner->email){
		$sql="UPDATE admin.account SET email=:email WHERE subdomain=:subdomain AND email=:previousEmail";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":email",$this->owner->email,PDO::PARAM_STR);
		$command->bindParam(":subdomain",Yii::app()->params['dbName'],PDO::PARAM_STR);
		$command->bindParam(":previousEmail",$this->owner->previousEmail,PDO::PARAM_STR);
		$command->execute();
    		}
    	}
    }
    
    /**
     * Creates the schools config file. This method makes a copy of _config_template.tpl and creates
     * the schools config file with the necessay account limits
     * @return void
     */
    public function updateConfigFile()
    {
    	$configPath = Yii::getPathOfAlias('application.config.clients')."/".Yii::app()->params['dbName'].'.php';
    	if($file = Yii::app()->file->set($configPath, false)){
    		$contents = $file->contents;
    		//Upgrade to premium. will need to redo for unlimted.
    		$contents  = str_replace("'totalUsers'=>1","'totalUsers'=>0",$contents);
    		$contents =  str_replace("'totalDcps'=>2","'totalDcps'=>10",$contents);
    		$file->contents=$contents;
    	}
    }

    /**
	 * Sends an email to the user informing them that they have chosen to upgrade their account to premium
	 */
	public function sendUpgradeAccountEmail()
	{
		//Get the username of the user
		$user=User::model()->findByPk(Yii::app()->user->id);		
		
		$body = "Hello ".$user->username;
		$body .= "\nYou just upgraded your Pupil Tracking Analytics to Premium. This will trigger an invoice to be sent to your school.";
		$body .="\nIf for any reason this was not your intention then please contact us and we can alter your account appropriately.";
		//Closing
		$body .="\nKind regards";
		$body .="\nPupil Tracking Limited";

		//Send email to user
		Yii::app()->mailer->AddAddress($user->email,$user->username);
		Yii::app()->mailer->Subject = 'Pupil Tracking Analytics Account Upgrade';
		Yii::app()->mailer->Body = $body;
		Yii::app()->mailer->Send();
		
		//Email us
		Yii::app()->mailer->AddAddress(Yii::app()->mailer->from);
		Yii::app()->mailer->Subject = 'PTA account upgrade!';
		Yii::app()->mailer->Body = $user->email." just upgraded their account to premium.\n\n".$body;
		Yii::app()->mailer->Send();
	}
}