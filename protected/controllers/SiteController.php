<?php

class SiteController extends Controller
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{

		// render home page
		$this->render('index',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;
		
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()){
				$this->redirect(Yii::app()->user->returnUrl);
			}
		}
		
		// display the login form
		$this->render('login',array('model'=>$model));
	}
	
	

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	/**
	 * This is the default action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest){
	    		echo $error['message'];
	    	}
	    	else
	        	$this->render('error', $error);
	    }
	}
	
	/**
	 * A maintenabce page to be displayed when the site is down
	 * The iste is down if $maintenanceMode=true in config/main.php
	 */
	public function actionMaintenance()
	{
		$this->layout="//layouts/maintenance";
		$this->render('maintenance', $error);
	}
	
	/**
	 * About page
	 */
	public function actionAbout()
	{
		$this->render('about');
	}

	/**
	 * Tutorials page
	 */
	public function actionTutorials()
	{
		$this->render('tutorials');
	}

	
	

}