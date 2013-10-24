<?php
class PupilController extends Controller
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
	 * Results Action
	 */
	public function actionResults()
	{
		//Here we must manually reconstrict the normal $_GET array as they have been passed via the jQuery data API and it removes all camel casing
		$array= $this->normaliseModel();		
		
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($array['Ks4FF'])){
			$model->attributes=$array['Ks4FF'];	
		}

		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4PupilGrid($model);
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

		$this->cleanForPartial();
		$this->renderPartial('admin',array(
			'dataProvider'=>$dataProvider, // Here we pass only one grid/dataprovider
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		),false,true);	

		Yii::app()->end();
	}

	/**
	 * Subject Average Action
	 */
	public function actionSubjectAverage()
	{
		
		$array= $this->normaliseModel();
		
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($array['Ks4FF'])){
			$model->attributes=$array['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4PupilGrid($model);
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

		$this->cleanForPartial();
		$this->renderPartial('subjectAverage',array(
			'dataProvider'=>$dataProvider, // Here we pass only one grid/dataprovider
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		),false,true);	

		Yii::app()->end();


	}

	/**
	 * KS2 Action
	 */
	public function actionKs2()
	{
		$array= $this->normaliseModel();
		
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($array['Ks4FF'])){
			$model->attributes=$array['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4PupilGrid($model);
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

		$this->cleanForPartial();
		$this->renderPartial('ks2',array(
			'dataProvider'=>$dataProvider, // Here we pass only one grid/dataprovider
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		),false,true);	

		Yii::app()->end();
	}

	/**
	 * Tracking Action
	 */
	public function actionTracking()
	{
		$array= $this->normaliseModel();
		
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($array['Ks4FF'])){
			$model->attributes=$array['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4PupilGrid($model);
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

		$this->cleanForPartial();
		$this->renderPartial('tracking',array(
			'dataProvider'=>$dataProvider, // Here we pass only one grid/dataprovider
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		),false,true);	

		Yii::app()->end();
	}

	/**
	 * Filters Action
	 */
	public function actionFilters()
	{
		$array= $this->normaliseModel();
		
		$model=new Ks4FF();

		//$model->unsetAttributes();  // clear any default values
		if(isset($array['Ks4FF'])){
			$model->attributes=$array['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4PupilGrid($model);
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

		$this->cleanForPartial();
		$this->renderPartial('filters',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		),false,true);	

		Yii::app()->end();
	}



	/**
	 * Here we must manually reconstrict the normal $_GET array as they have been passed via the jQuery data API and it removes all camel casing
	 * There is possibly a better way of doing this.
	 * @return array
	 */
	private function normaliseModel()
	{
		if(isset($_GET)){
		$array['Ks4FF']['pupilId'] = $_GET['pupilid'];
		$array['Ks4FF']['cohortId'] = $_GET['cohortid'];
		$array['Ks4FF']['oldCohortId'] = $_GET['oldcohortid'];
		$array['Ks4FF']['compare'] = $_GET['compare'];
		$array['Ks4FF']['compareTo'] = $_GET['compareto'];
		$array['Ks4FF']['mode'] = $_GET['mode'];
		$array['Ks4FF']['yearGroup'] = $_GET['yeargroup'];
		$array['Ks4FF']['oldYearGroup'] = $_GET['oldyeargroup'];
		return $array;
		}
		return array();

	}


	
}