<?php
class KeyStageController extends Controller
{
	public $portletTitle = "KS Data";
	
	/*
	 * Override filters()
	 */
	public function filters()
	{
		// return the filter configuration for this controller
		return array(
				'accessControl',
               array('application.filters.SetUpFilter',
                'url'=>$this->createUrl($this->id.'/admin'),
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
	 * Action admin
	 */
	public function actionAdmin()
	{
		$model=$this->loadModel();
		
		if(isset($_POST['KeyStage']))
		{
			$model->attributes=$_POST['KeyStage'];
			if($model->save())
			{
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> Key stage data has been updated.");
				$this->redirect(array('admin'));
			}
		}
		
		$rawData = Keystage::getKs2PointScores();
		$ks2dataProvider=new CArrayDataProvider($rawData,array(
		    'pagination'=>array(
        		'pageSize'=>300,
    			),
		));
		
		$this->render('admin',array('model'=>$model,
					'ks2dataProvider'=>$ks2dataProvider));
	}
	
	/*
	 * Loads the model
	 */
	public function loadModel()
	{
		$model = new KeyStage();
		$model->load();
		return $model;
	}



}