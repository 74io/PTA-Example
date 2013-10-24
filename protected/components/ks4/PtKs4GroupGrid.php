<?php
class PtKs4GroupGrid extends PtKs4
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
		if($grid=Yii::app()->dataCache->getDataCache($this->attributesForCaching,$this->model->cohortId,4,'ks4Group'))
		return $grid;
		
		//We need to run this update for all groups
		$this->buildKs4Master($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterAttendance();
		
		$grid=$this->groupGrid;
		
		Yii::app()->dataCache->setDataCache($this->attributesForCaching,$grid,$this->model->cohortId,4,'ks4Group');
		
		$connection = Yii::app()->db;
		$connection->dropTmpTables();
		
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
	

	
	
}