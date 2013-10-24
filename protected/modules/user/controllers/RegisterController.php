<?php
class RegisterController extends Controller
{
	
	/**
	 * @see CController::filters()
	 */
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
                'users'=>array('?'),//Allow  anonymous users only
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
		$model=new User('register');
		
			//Validate the number of users
		if(!$model->userTotalIsValid){
				Yii::app()->user->setFlash('warning',"<strong>Warning!</strong> Users cannot be added to a FREE account.");
				$this->redirect(array('/site/login'));
		}

		// Uncomment the following line if AJAX validation is needed
		 //$this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> An email containing an account activation
				link has been sent to <strong>".$model->email."</strong> You must click on the link within the email to activate your account."
				.Yii::app()->common->emailAdvice);
				$this->redirect(array('/site/login'));
			}
		}

		$this->render('register',array(
			'model'=>$model,
		));
		
		
	}
	
	/**
	 * Action to active an account
	 * @param string $acid The account activation id
	 * @param integer $uid the user id
	 * @return void
	 */
	public function actionActivate($acid,$uid)
	{
		$model=new User('register');
		if($model->activateAccount($acid,$uid)){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> Your account is now active. You may login below.");
				$this->redirect(array('/site/login'));
			
		}
		else{
				Yii::app()->user->setFlash('warning',"<strong>Warning!</strong> Either this account is already active or the URL you provided has been truncated.");
				$this->redirect(array('/site/login'));
		}
	}	
}