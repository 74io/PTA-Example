<?php
class PtKs4SubjectGrid extends PtKs4Subject
{

	public $qs;
	
	/**
	 * Class constructor
	 */
	public function __construct($model)
	{
		parent::__construct();
		$this->model=$model;
		//$this->qs=urldecode(Yii::app()->request->queryString);

		
	}
	
	public function init()
	{
	
	}
	/**
	 * Returns array containing all the grid data.
	 * Note. We first check the dataCache for a grid. If a grid is present we pass the cohortTotal
	 * to our _cohortTotal cached var. We do this because the charts call $this->cohortTotal.
	 * @return array
	 */
	public function getGrid()
	{

		//Check to see if a grid exists in the cache
		if($grid=Yii::app()->dataCache->getDataCache($this->attributesForCaching,$this->model->cohortId,4,'ks4Subject')){
		return $grid;
		}
		
		//If not then we need to check some params
		
		if($this->filteredPupilsInClause===null){
			$grid['subject']=array();
	
			//cache the empty grid
			Yii::app()->dataCache->setDataCache($this->attributesForCaching,$grid,$this->model->cohortId,4,'ks4Subject');
			return $grid;
		}

		
		/**
		 * Use this to test caching. PHP crc32 id the same as mysql crc32
		 * $checksum = crc32(serialize($this->model->attributes));
		 * echo $checksum= sprintf("%u", $checksum);//Get unsigned integer
		 */
		

		//Build the ks4SubjectMaster table 
		$this->buildKs4Master($this->model->compare,$this->model->mode);
		$this->buildKs4Master($this->model->compareTo,$this->model->mode);
		if($this->model->mode=='volume'){
		$this->updateKs4Master();//Remove results for subjects with a point score of 0.5
		}
	
		$grid['subject']=$this->subjectGrid;

		Yii::app()->dataCache->setDataCache($this->attributesForCaching,$grid,$this->model->cohortId,4,'ks4Subject');
		
		// Uncomment when we want temp tables dropped
		$connection = Yii::app()->db;
		$connection->dropTmpTables();
		
		return $grid;
	}

	/**
	 * [getSubjectGrid description]
	 * @return [type] [description]
	 */
	public function getSubjectGrid()
	{
		$compare = $this->getSubjectSummary($this->model->compare);
		$compareTo = $this->getSubjectSummary($this->model->compareTo);

		$params=array();

		foreach($compare as $key=>$value)
		{
			/*
			if($compare[$key][$this->model->mode]==0){
				continue;
			}*/

			$grid[] = array(//DCPs
							'col1'=>'<span rel="tooltip" 
							title="Qual: '.$compare[$key]['qualification'].'<br>Worth: '.$compare[$key][$this->model->mode].' GCSEs"
							>'.$compare[$key]['subject'].'</span>',
							'col2'=>($compare[$key][$this->model->mode]==0) ? 0 : number_format(($compare[$key]['astar_a']/$compare[$key]['total'])*100/$compare[$key][$this->model->mode],2),
							'col3'=>$this->getButton($compare[$key]['astar_a'],
									array(
										'groupMethod'=>'getDcp',
										'groupFieldMappingId'=>$this->model->compare,
										'arg0'=>$compare[$key]['subjectmapping_id'],
										'arg1'=>'astar_a'
									)),
							'col4'=>($compare[$key][$this->model->mode]==0) ? 0 : number_format(($compare[$key]['astar_c']/$compare[$key]['total'])*100/$compare[$key][$this->model->mode],2),
							'col5'=>$this->getButton($compare[$key]['astar_c'],
									array(
										'groupMethod'=>'getDcp',
										'groupFieldMappingId'=>$this->model->compare,
										'arg0'=>$compare[$key]['subjectmapping_id'],
										'arg1'=>'astar_c'
									)),
							'col6'=>($compare[$key][$this->model->mode]==0) ? 0 : number_format(($compare[$key]['astar_g']/$compare[$key]['total'])*100/$compare[$key][$this->model->mode],2),
							'col7'=>$this->getButton($compare[$key]['astar_g'],
									array(
										'groupMethod'=>'getDcp',
										'groupFieldMappingId'=>$this->model->compare,
										'arg0'=>$compare[$key]['subjectmapping_id'],
										'arg1'=>'astar_g'
									)),
							'col8'=>$this->getButton($compare[$key]['fail'],
									array(
										'groupMethod'=>'getDcp',
										'groupFieldMappingId'=>$this->model->compare,
										'arg0'=>$compare[$key]['subjectmapping_id'],
										'arg1'=>'standardised_points'
									),0),
							'col9'=>$compare[$key]['average_point_score'],
							// Targets
							'col10'=>$compareTo[$key]['astar_a'],
							'col11'=>($compare[$key][$this->model->mode]==0) ? 0 : number_format(($compareTo[$key]['astar_a']/$compare[$key]['total'])*100/$compare[$key][$this->model->mode],2),
							'col12'=>($compare[$key][$this->model->mode]==0) ? 0 : number_format(($compareTo[$key]['astar_c']/$compare[$key]['total'])*100/$compare[$key][$this->model->mode],2),
							'col13'=>$compareTo[$key]['astar_c'],
							'col14'=>$compareTo[$key]['astar_g'],
							'col15'=>($compare[$key][$this->model->mode]==0) ? 0 : number_format(($compareTo[$key]['astar_g']/$compare[$key]['total'])*100/$compare[$key][$this->model->mode],2),
							'col16'=>$compareTo[$key]['average_point_score'],
							//Total
							'col17'=>$compare[$key]['total'],
							'col18'=>$compare[$key]['subject'],//Raw subject text for chart
				);
		}
		return $grid;
	}


	/**
	 * Formats the astar_a grades in the grid
	 */
	public function getCellCssAstarToA($row,$data)
	{
		
		if($data['col2']>$data['col11'])
		return "green";
		
		if($data['col2']==$data['col11'])
		return "amber";
		
		if($data['col2']<$data['col11'])
		return "red";
	
	}
	 /**	 
	 * Formats the astar_c grades in the grid
	 */
	public function getCellCssAstarToC($row,$data)
	{
		
		if($data['col4']>$data['col12'])
		return "green";
		
		if($data['col4']==$data['col12'])
		return "amber";
		
		if($data['col4']<$data['col12'])
		return "red";
	
	}

	/**
	 * Formats the astar_c grades in the grid
	 */
	public function getCellCssAstarToG($row,$data)
	{
		
		if($data['col6']>$data['col15'])
		return "green";
		
		if($data['col6']==$data['col15'])
		return "amber";
		
		if($data['col6']<$data['col15'])
		return "red";
	
	}

	/**
	 * Formats the astar_c grades in the grid
	 */
	public function getCellCssAps($row,$data)
	{
		
		if($data['col9']>$data['col16'])
		return "green";
		
		if($data['col9']==$data['col16'])
		return "amber";
		
		if($data['col9']<$data['col16'])
		return "red";
	}

}