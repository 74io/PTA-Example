<?php
class IndicatorController extends Controller
{
	
	/**
	 * @see CController::filters()
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
	 * Action Admin
	 */
	public function actionAdmin()
	{
		
		$rawData=Indicator::getIndicatorList();
		$dataProvider = new CArrayDataProvider($rawData);
		
		$this->render('admin',array(
		'dataProvider'=>$dataProvider,
		'complete'=>$rawData['complete'],
		));
	}
	
	/**
	 * Renders the present column
	 */
	public function renderPresentColumn($data,$row)
	{
		return ($data['present']==0) ? '<span class="label label-important">Missing</span>' : "<i class='icon-ok'></i>";
	}
	
	
	
}