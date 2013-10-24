<?php

class BuildController extends Controller
{
	
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
		$this->render('admin',array(
					'coreDataLastBuilt'=> Yii::app()->build->coreDataLastBuilt,
					'setUpIsComplete'=>Yii::app()->build->setUpIsComplete,
		));
	}
	
	
	/**
	 * Action build core data
	 */
	public function actionBuildCoreData()
	{
			if(!Yii::app()->build->getBuilding(PtEventLog::BUILD_4))
			{
				if(Yii::app()->build->buildCoreData()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> The core data has been built.");
				}
				else{
				Yii::app()->user->setFlash('error',"The core data could not be built. Please check the log for details.");	
				}
			}
		$this->redirect(array('admin'));
	}
	

}