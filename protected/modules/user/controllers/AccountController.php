<?php
class AccountController extends Controller
{
	
	public function filters()
	{
	    return array(
	        'accessControl', // perform access control for CRUD operations
	    );
	}
	
	/**
	 * @see CController::accessRules()
	 * Note this can be overridden in individual controllers to allow other roles
	 */
	public function accessRules()
	{   
	    return array(
	        array('allow',
                'actions'=>array('recovery','captcha','reset'),
                'users'=>array('*'),//Allow both anonymous and authenticated users
            ),
	        array('allow',
	        	//'actions'=>array('admin'),No actions specified means all actions
	             'users'=>array('@'),//All authenticated users
	        ),
	        array('deny'),//Deny all users access to everything
	    );
	}
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}
	
	/**
	 * Action index
	 */
	public function actionIndex()
	{
			$this->render('index',array(
			'model'=>$this->loadModel(Yii::app()->user->id),
		));
	}
	
	/**
	 * Action to update password
	 */
	public function actionUpdatePassword()
	{
		$model=$this->loadModel(Yii::app()->user->id);
		$model->scenario='updatePassword';
		$model->hiddenPassword = $model->password;
		
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['Account']))
		{
			$model->attributes=$_POST['Account'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> Your password has been
				updated.");
				$this->redirect(array('index',));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'title'=>'Update Your Password',
		));	
	}
	
	/**
	 * Action to update email
	 */
	public function actionUpdateEmail()
	{
		$model=$this->loadModel(Yii::app()->user->id);
		$model->scenario='updateEmail';
		$model->hiddenPassword = $model->password;
		$model->previousEmail = $model->email;
		
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['Account']))
		{
			$model->attributes=$_POST['Account'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> Your email has been
				updated.");
				$this->redirect(array('index',));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'title'=>'Update Your Email',
		));	
	}
	
	/**
	 * Action to update username
	 */
	public function actionUpdateUsername()
	{
		$model=$this->loadModel(Yii::app()->user->id);
		$model->scenario='updateUsername';
		$model->hiddenPassword = $model->password;
		$model->previousUsername  = $model->username;
		
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['Account']))
		{
			$model->attributes=$_POST['Account'];
			if($model->save()){
				Yii::app()->user->setFlash('success','<strong>Success!</strong> Your username has been
				updated. <strong>IMPORTANT.</strong> You need to log out and log back in before this new username will
				be reflected on the system.');
				$this->redirect(array('index',));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'title'=>'Update Your Username',
		));	
	}
	
	/**
	 * Action to allow users to upgrade their account type. e.g. from FREE to PREMIUM
	 */
	public function actionUpgradeAccount()
	{
		$model=$this->loadModel(Yii::app()->user->id);
		$model->scenario='upgradeAccount';

		if(isset($_POST['Account']))
		{
			$model->attributes=$_POST['Account'];
			if($model->save()){
				Yii::app()->user->setFlash('success','<strong>Success!</strong> Your account has been upgraded.');
				$this->redirect(array('index',));
			}
		}

		$this->render('upgradeAccount',array(
			'model'=>$model,
		));
		
	}
	
	/**
	 * Action recovery
	 * Helps an anonymous user recover their username and password
	 */
	public function actionRecovery()
	{
		$model=new Account('recovery');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Account']))
		{
			$model->attributes=$_POST['Account'];
			if($model->validate()){
				$model=$this->loadModelEmail($model->email);
				$model->scenario='recovery';
				$model->save(false);
				Yii::app()->user->setFlash('success','<strong>Success!</strong> An email containing a password reset link has been sent
				to <strong>'.$model->email.'</strong>'.Yii::app()->common->emailAdvice);
				$this->redirect(array('/site/login'));
			}
		}

		$this->render('recovery',array(
			'model'=>$model,
		));
		
	}
	
	/**
	 * Action to reset a users password
	 * @param string $rid The recovery_id
	 * @param integer $uid the user id
	 */
	public function actionReset($rid,$uid)
	{
		$model= $this->loadModelRecoveryId($rid,$uid);

		if($model===null){
		Yii::app()->user->setFlash('warning','<strong>Warning!</strong> The reset link is invalid. Either this link has been used
		before or the URL has been truncated.');
		$this->redirect(array('/site/login',));
		}
		else{
		$model->scenario = 'reset';	
		if(isset($_POST['Account']))
		{
			$model->attributes=$_POST['Account'];
			if($model->save()){
				Yii::app()->user->setFlash('success','<strong>Success!</strong> Your password has been
				updated.');
				$this->redirect(array('/site/login',));
			}
		}

		$this->render('reset',array(
			'model'=>$model,
		));
			
			
			
		}
	}
	
	/**
	 * Tries to load a model using the recovery_id and user id
	 * @return object
	 */
	public function loadModelRecoveryId($rid,$uid)
	{
		return Account::model()->find('recovery_id=? AND id=?',array($rid,$uid));
		
	}
	
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModelEmail($email)
	{
		 $model=Account::model()->find('LOWER(email)=?',array($email));
		if($model===null)
			throw new CHttpException(404,'Error with the database query');
		return $model;
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Account::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'Error with the database query');
		return $model;
	}
	
}