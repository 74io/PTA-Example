<?php
class MainController extends Controller
{
	
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	
	public $defaultAction='admin';
	
	public $menu=array(
	array('label'=>'Manage Users','url'=>array('/user/main/admin')),
	array('label'=>'Create User','url'=>array('/user/main/create')),
	array('label'=>'Manage Registration Code','url'=>array('/user/main/regcode')),
	);
	
	
	public function filters()
	{
	    return array(
	        'accessControl', // perform access control for CRUD operations
	    );
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		
		$model=new User('create');
		
		//Validate the number of users
		if(!$model->userTotalIsValid){
				Yii::app()->user->setFlash('warning',"<strong>Warning!</strong> You cannot create any more users. You may need to upgrade your account.");
				$this->redirect(array('admin'));
		}

		// Uncomment the following line if AJAX validation is needed
		 //$this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> The new user <strong>".CHtml::encode($model->username)."</strong> has been created.");
				$this->redirect(array('admin'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$model->scenario='update';
		$model->previousUsername=$model->username;
		$model->previousEmail=$model->email;
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> User <strong>".$model->id."</strong> has been updated. 
				Note. Changes to roles will not take effect for up to 10 minutes.");
				$this->redirect(array('admin',));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	/*
	 * Action to allow admin to change a users password
	 */
	public function actionUpdatePassword($id)
	{
		$model=$this->loadModel($id);
		$model->scenario='changePassword';
		
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> The password for user <strong>".$id."</strong> has been
				updated.");
				$this->redirect(array('admin',));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model=$this->loadModel($id);
			if($model->role=='super')
			return; //You cannot delete the super user
			
			else
			$model->delete();
			
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	/*
	 * Action to delete multiple selected users
	 */
	public function actionDeleteAll()
	{
		
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$ids=$_POST['ids'];
			foreach($ids as $id){
				$this->loadModel($id)->delete();
			}
		
			Yii::app()->user->setFlash('success',"<strong>Success!</strong> The selected users have been deleted.");
			//echo $_POST['returnUrl'];
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		//echo YiiBase::getVersion();
		
		$model=new User('search'); // Using the search scenario
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];
			
		if(Yii::app()->request->isAjaxRequest){
		$this->renderPartial('admin',array(
			'model'=>$model,
		));
		}
		else{
			$this->render('admin',array(
			'model'=>$model,
		));	
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'Error with the database query');
		return $model;
	}
	
	
	public function actionRegCode()
	{
		$model=$this->loadRegCodeModel();
	    if(isset($_POST['RegCode']))
	    {
	        $model->attributes=$_POST['RegCode'];
	        if($model->validate())
	        {
	        	$model->save();
	            // form inputs are valid, do something here
	            Yii::app()->user->setFlash('success','<strong>Success!</strong> A new registration code has been created.
	            Users may now create an account using the registration code below. Any previous codes will no longer be valid.');
				$this->redirect('regcode');
			
	        }
	    }
	    $this->render('regCode',array('model'=>$model));
		
		
	}
	
	/*
	 * Loads the registration code model
	 */
	public function loadRegCodeModel()
	{
		$model = new RegCode();
		$model->load();
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
}
