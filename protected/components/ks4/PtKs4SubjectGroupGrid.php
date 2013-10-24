<?php
class PtKs4SubjectGroupGrid extends PtKs4Subject
{
	public $params;
	/**
	 * Class constructor
	 */
	public function __construct($model)
	{
		parent::__construct();
		$this->model=$model;
		
	}
	
	public function init()
	{
	
	}
	
	/**
	 * Returns the grid
	 * @return array
	 */
	public function getGrid()
	{
		if($grid=Yii::app()->dataCache->getDataCache($this->attributesForCaching,$this->model->cohortId,4,'ks4SubjectGroup'))
		return $grid;
		
		$grid=$this->groupGrid;
		
		Yii::app()->dataCache->setDataCache($this->attributesForCaching,$grid,$this->model->cohortId,4,'ks4SubjectGroup');
		
		//No tmp tables created to drop here
		return $grid;
	}
	
	
	/**
	 * Calls the approprite group method
	 */
	public function getGroupGrid()
	{	
		$method=$this->model->groupMethod.'Group';
		if(method_exists($this, $method)){
			$array = call_user_func(array($this, $method));
		}
		return $array;
	}

	/**
	 * Formats the DCP result in the subject group grid
	 */
	public function getCellCssResult($row,$data)
	{
		
		if($data['dcp_standardised_points']>$data['target_standardised_points'])
		return "green";
		
		if($data['dcp_standardised_points']==$data['target_standardised_points'])
		return "amber";
		
		if($data['dcp_standardised_points']<$data['target_standardised_points'])
		return "red";
	
	}

}