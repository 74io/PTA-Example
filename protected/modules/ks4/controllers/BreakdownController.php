<?php
class BreakdownController extends Controller
{
	public $defaultAction='headlines';

	public $layout='//layouts/column1';
	
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
	 * Headlines Action
	 */
	public function actionHeadlines()
	{
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Ks4FF'])){
			$model->attributes=$_GET['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4BreakdownGrid($model);
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
		
		$this->render('view',array(
			'dataProvider'=>$dataProvider['headlines'],
			'grid'=>'_headlineGrid',
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		));	
	}

	/**
	 * Attainers Action
	 */
	public function actionAttainers()
	{
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Ks4FF'])){
			$model->attributes=$_GET['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4BreakdownAttainersGrid($model);
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
		
		$this->render('view',array(
			'dataProvider'=>$dataProvider['attainers'],
			'grid'=>'_attainersGrid',
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		));	
	}

	/**
	 * SEN Action
	 */
	public function actionSen()
	{
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Ks4FF'])){
			$model->attributes=$_GET['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4BreakdownSENGrid($model);
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
		
		$this->render('view',array(
			'dataProvider'=>$dataProvider['sen'],
			'grid'=>'_senGrid',
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		));	
	}

	/**
	 * Outputs a grid containing a list of pupils that match specific criteria
	 */
	public function actionGroup()
	{
		$model=new Ks4FF();
		if(isset($_GET['Ks4FF'])){
			$model->attributes=$_GET['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4BreakdownGroupGrid($model);
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