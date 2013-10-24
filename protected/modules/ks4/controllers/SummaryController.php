<?php
class SummaryController extends Controller
{
	public $defaultAction='admin';
	
	/*
	 * Override filters()
	 */
	public function filters()
	{
		// return the filter configuration for this controller
		return array(
				'accessControl',
				array('application.filters.Ks4Filter',
            ));	
	}
	
	/**
	 * @see CController::accessRules()
	 */
	public function accessRules()
	{   
	    return array(
	        array('allow',
	            'users'=>array('@'),//All authenticated users
	        ),
	        array('deny'),//Deny all users
	    );
	}
	

	/**
	 * The main action. This displays a KS4 Summary
	 */
	public function actionAdmin()
	{
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Ks4FF'])){
			$model->attributes=$_GET['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4Grid($model);
		}
		
		$grids=$component->grid;
		//build a data provider from each grid
		foreach($grids as $key=>$value){
			$dataProvider[$key]=new CArrayDataProvider($grids[$key], array(
			'pagination'=>array(
        	'pageSize'=>50,
    		),
		));
		}

		
		//Handle the active tab
		$activeTab[$model->activeTab]=true;


		$this->render('admin',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
			'component'=>$component,
			'activeTab'=>$activeTab,
		));	
		
	}

	/**
	 * Outputs a grid containing a list of pupils that match specific criteria
	 */
	public function actionGroup()
	{
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Ks4FF'])){
			$model->attributes=$_GET['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4GroupGrid($model);
		}
		
		$grid=$component->grid;
		$dataProvider=new CArrayDataProvider($grid, array(
			'pagination'=>array(
        	'pageSize'=>500,
    		),
		));
		
		$this->renderPartial('/common/_groupGrid',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
			'component'=>$component,
		));
		
		Yii::app()->end();
	}
	
}