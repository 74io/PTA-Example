<?php
class SubjectController extends Controller
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

		if(isset($_GET['Ks4FF'])){
			$model->attributes=$_GET['Ks4FF'];	
		}
		
		//Here we can pass the attribtes off to any components
		if($model->validate()){
			$component = new PtKs4SubjectGrid($model);
		}

		$grids=$component->grid;
		
			foreach($grids as $key=>$value){
				$grid=($grids[$key]) ? $grids[$key] : array();//Ensure that grid is an array
				$dataProvider[$key]=new CArrayDataProvider($grid, array(
				'pagination'=>array(
	        	'pageSize'=>50,
	    		),
			));
			}
		
		$this->render('admin',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
			'component'=>$component,
			//'activeTab'=>$activeTab,
		));	

	}

	/**
	 * Group action. Outputs a grid containing a list of pupils that match specific criteria
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
			$component = new PtKs4SubjectGroupGrid($model);
		}
		
		//Fetch all rows
		$rows=$component->grid;
		if($rows){

		//Extract classes from rows array
		foreach($rows as $key=>$row)
		{
		$classes[]=$row['set_code'];
		}

		//Fetch only unique classes
		$classes=array_unique($classes);

		//Build new grid for each class/set
		foreach($classes as $classKey=>$class)
		{
			foreach($rows as $rowKey=>$row)
			{
				if($row['set_code']==$class)
				{
					$grid[$class][]=$row;

				}
			}
		}

		//Build a data provider for each grid
		foreach($grid as $key=>$value){
		$dataProvider[$key]=new CArrayDataProvider($grid[$key], array(
			'pagination'=>array(
        	'pageSize'=>500,
    		),
		));
		}

	}
	else{
			$dataProvider['No classes found']=new CArrayDataProvider(array(), array(
			'pagination'=>array(
        	'pageSize'=>500,
    		),
		));
	}
		
		$this->renderPartial('_groupGrid',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
			'component'=>$component,
			'numRows'=>count($rows),
		));
		Yii::app()->end();
	}
	
}