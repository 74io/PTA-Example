<?php

class CohortController extends Controller
{
	/*
	 * @var string the title of the CPortlet menu portlet
	 */
	public $portletTitle = "Cohorts";
	
	
	/*
	 * @var array An array of CMenu items
	 */
	public $menu=array(
	array('label'=>'Manage Cohorts','url'=>array('/setup/cohort/admin')),
	array('label'=>'Create Cohort','url'=>array('/setup/cohort/create'),'linkOptions'=>array('id'=>'create')),
	);
	
	/*
	 * Override filters()
	 */
	public function filters()
	{
		// return the filter configuration for this controller
		return array(
				'accessControl',
             	array('application.filters.SetUpFilter',
                'url'=>$this->createUrl('cohort/admin'),
				'schoolSetUp'=>$this->schoolSetUp,
            ));	
	}
	
	/**
	 * @see CController::accessRules()
	 */
	public function accessRules()
	{   
	    return array(
	        array('allow',
	            'roles'=>array('admin','data manager'),
	        ),
	        array('deny'),//Deny all users
	    );
	}
	
	/**
	 * Action to create a new cohort
	 */
	public function actionCreate()
	{
		$model=new Cohort('create');

		if(isset($_POST['Cohort']))
		{
			$model->attributes=$_POST['Cohort'];
			if($model->save())
			{
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> A new cohort has been created.");
				$this->redirect(array('admin'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Action to update a cohort
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		
		$model=$this->loadModel($id);
		$model->scenario='update';

		if(isset($_POST['Cohort']))
		{
			$model->attributes=$_POST['Cohort'];
			if($model->save())
			{
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> Cohort ID:".$model->id." has been updated.");
				$this->redirect(array('admin'));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Action Admin.
	 */
	public function actionAdmin()
	{
		//If the schoolName is not set it means that other setup components cannot be completed so we must redirect
		$model=new Cohort('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Cohort']))
			$model->attributes=$_GET['Cohort'];

		if($_GET['ajax']){
		$this->renderPartial('/cohort/_grid',array(
			'model'=>$model,
		));
		}
		else{
		$this->render('/cohort/admin',array(
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
		$model=Cohort::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='cohort-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	

}
