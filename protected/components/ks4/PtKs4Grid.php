<?php
class PtKs4Grid extends PtKs4
{

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
	 * Returns array containing all the grid data.
	 * Note. We first check the dataCache for a grid. If a grid is present we pass the cohortTotal
	 * to our _cohortTotal cached var. We do this because the charts call $this->cohortTotal.
	 * @return array
	 */
	public function getGrid()
	{
		//Check to see if a grid exists in the cache
		if($grid=Yii::app()->dataCache->getDataCache($this->attributesForCaching,$this->model->cohortId,4,'ks4Summary')){
		//@TODO do we actually need the line below? what does it actually do?
		$this->_cohortTotal=($grid['cohort'][0]['col2']===null) ? 0 : $grid['cohort'][0]['col2'];
		return $grid;
		}
		
		//If not then we need to check some params
		//@TODO do we need all these checks?
		if($this->filteredPupilsInClause===null || $this->cohortTotal===null || $this->cohortTotal==0){
			$grid['cohort']=array();
			$grid['levelsProgress']=array();
			$grid['astartoc']=array();
			$grid['incEnglishMaths']=array();
			$grid['attainers']=array();
			$grid['headlines']=array();
			//cache the empty grid
			Yii::app()->dataCache->setDataCache($this->attributesForCaching,$grid,$this->model->cohortId,4,'ks4Summary');
			return $grid;
		}
		
		/**
		 * Use this to test caching. PHP crc32 id the same as mysql crc32
		 * $checksum = crc32(serialize($this->model->attributes));
		 * echo $checksum= sprintf("%u", $checksum);//Get unsigned integer
		 */
		

		//Build the ks4master table 
		$this->buildKs4Master($this->model->compare,$this->model->mode);
		$this->buildKs4Master($this->model->compareTo,$this->model->mode);
	

		$grid['cohort']=$this->cohortGrid;
		$grid['levelsProgress']=$this->levelsProgressGrid;
		$grid['astartoc']=$this->astarToCGrid;
		$grid['incEnglishMaths']=$this->englishMathsGrid;
		$grid['attainers']=$this->attainersGrid;
		$grid['headlines']=$this->headlineGrid;
		
			  
		Yii::app()->dataCache->setDataCache($this->attributesForCaching,$grid,$this->model->cohortId,4,'ks4Summary');
		
		// Uncomment when we want temp tables dropped
		$connection = Yii::app()->db;
		$connection->dropTmpTables();
		
		return $grid;
	}
	
	/**
	 * 
	 */
	public function getLevelsProgressGrid()
	{
		//Run levels progress dependencies
		$this->updateKs4MasterEnglishPointScore($this->model->compare,$this->model->mode);
		$this->updateKs4MasterEnglishPointScore($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->compare,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterEnglishLevelsProgress();
		$this->updateKs4MasterMathsLevelsProgress();
		
		$this->getAttainersLevelsProgress();
		$this->getLevelsProgress();
		
		
		
    	$title = "English 3 Levels Progress";
		$numPupilsCompare = (int)$this->_levelsProgress[$this->model->compare]['english_lp3'];
		$params['groupMethod']='getLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_lp3';//Column name
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_levelsProgress[$this->model->compareTo]['english_lp3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);	
					
    	$title = "Maths 3 Levels Progress";
		$numPupilsCompare = (int)$this->_levelsProgress[$this->model->compare]['maths_lp3'];
		$params['groupMethod']='getLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_lp3';//Column name
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_levelsProgress[$this->model->compareTo]['maths_lp3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);	
					
    	$title = "English 4 Levels Progress";
		$numPupilsCompare = (int)$this->_levelsProgress[$this->model->compare]['english_lp4'];
		$params['groupMethod']='getLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_lp4';//Column name
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_levelsProgress[$this->model->compareTo]['english_lp4'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);

					
    	$title = "Maths 4 Levels Progress";
		$numPupilsCompare = (int)$this->_levelsProgress[$this->model->compare]['maths_lp4'];
		$params['groupMethod']='getLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_lp4';//Column name
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_levelsProgress[$this->model->compareTo]['maths_lp4'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
				
		/**
		 * Low, Middle and High Attainers
		 */	
    	$title = "English 3 Levels Progress Low Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['1']['english_lp3'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_lp3';//Column name
		$params['arg1']=1;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['1']['english_lp3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "English 3 Levels Progress Middle Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['2']['english_lp3'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_lp3';//Column name
		$params['arg1']=2;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['2']['english_lp3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "English 3 Levels Progress High Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['3']['english_lp3'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_lp3';//Column name
		$params['arg1']=3;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['3']['english_lp3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "Maths 3 Levels Progress Low Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['1']['maths_lp3'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_lp3';//Column name
		$params['arg1']=1;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['1']['maths_lp3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "Maths 3 Levels Progress Middle Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['2']['maths_lp3'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_lp3';//Column name
		$params['arg1']=2;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['2']['maths_lp3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "Maths 3 Levels Progress High Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['3']['maths_lp3'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_lp3';//Column name
		$params['arg1']=3;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['3']['maths_lp3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "English 4 Levels Progress Low Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['1']['english_lp4'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_lp4';//Column name
		$params['arg1']=1;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['1']['english_lp4'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "English 4 Levels Progress Middle Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['2']['english_lp4'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_lp4';//Column name
		$params['arg1']=2;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['2']['english_lp4'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "English 4 Levels Progress High Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['3']['english_lp4'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_lp4';//Column name
		$params['arg1']=3;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['3']['english_lp4'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "Maths 4 Levels Progress Low Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['1']['maths_lp4'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_lp4';//Column name
		$params['arg1']=1;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['1']['maths_lp4'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "Maths 4 Levels Progress Middle Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['2']['maths_lp4'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_lp4';//Column name
		$params['arg1']=2;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['2']['maths_lp4'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
    	$title = "Maths 4 Levels Progress High Attainers";
		$numPupilsCompare = (int)$this->_attainersLevelsProgress[$this->model->compare]['3']['maths_lp4'];
		$params['groupMethod']='getAttainersLevelsProgress';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_lp4';//Column name
		$params['arg1']=3;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainersLevelsProgress[$this->model->compareTo]['3']['maths_lp4'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
			 		  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);			
						
		return $grid;
	}
	
	
	/**
	 * Returns an array which can be used to make a data provider for ArrayDataProvider.
	 * Note the link will contain data in the format method|fieldMappingId|param1|param2 e.g.  getAstarToC|17|astar_a|1
	 * @return array
	 */
	public function getAstarToCGrid()
	{
		$this->getAstarToC();
		$params['groupMethod'] = 'getAstarToC';//The same for all rows but can be overridden if necessary
    
    	$title = "1 x A*-A";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_a1'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_a';//Column name
		$params['arg1']=1; //Number of astar_a to lookup
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_a1'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);	
						
					
		$title = "1 x A*-C";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_c1'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_c';//Column name
		$params['arg1']=1; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_c1'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);

		$title = "1 x A*-G";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_g1'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_g';//Column name
		$params['arg1']=1; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_g1'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		$title = "3 x A*-A";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_a3'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_a';//Column name
		$params['arg1']=3; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_a3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		$title = "3 x A*-C";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_c3'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_c';//Column name
		$params['arg1']=3; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_c3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		$title = "3 x A*-G";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_g3'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_g';//Column name
		$params['arg1']=3; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_g3'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		$title = "5 x A*-A";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_a5'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_a';//Column name
		$params['arg1']=5; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_a5'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink =$this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);

		/**
		 * 5 x A* - C
		 */
		$title = "5 x A*-C";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_c5'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_c';//Column name
		$params['arg1']=5; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_c5'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=$this->_headlineRow['5AstarToC']=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		$title = "5 x A*-G";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_g5'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_g';//Column name
		$params['arg1']=5; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_g5'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
					
		$title = "8 x A*-A";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_a8'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_a';//Column name
		$params['arg1']=8; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_a8'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		$title = "8 x A*-C";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_c8'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_c';//Column name
		$params['arg1']=8; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_c8'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					  
					);
					
		$title = "8 x A*-G";
		$numPupilsCompare = (int)$this->_aStarToC[$this->model->compare]['astar_g8'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='astar_g';//Column name
		$params['arg1']=8; 
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = (int)$this->_aStarToC[$this->model->compareTo]['astar_g8'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		
		return $grid;
		
	}
	
	
	/**
	 * Returns an array containing overall cohort data
	 * @return array
	 */
	public function getCohortGrid()
	{

		//Run attainers dependencies.
		$this->updateKs4MasterKs2AveragePointScore();
		$this->updateKs4MasterKs2Attainers();

		$grid[]=array("col1"=>"Cohort",
						"col2"=>$this->cohortTotal,
						"col4"=>$this->cohortTotal);

		$ks2Aps = $this->getKs2AverageTotalPoints($this->model->compare);
		$grid[]=array("col1"=>"KS2 Average Point Score",
					  "col2"=>$ks2Aps,
					  "col4"=>$ks2Aps
					  );
		
		$grid[]=array("col1"=>"KS4 Average Point Score",
					  "col2"=>$this->getAverageTotalPoints($this->model->compare,$this->model->mode),
					  "col4"=>$this->getAverageTotalPoints($this->model->compareTo,$this->model->mode));
		/*
		$grid[]=array("col1"=>"Average Total Points (with equivalences)",
					  "col2"=>$this->getAverageTotalPoints($this->model->compare,'equivalent'),
					  "col4"=>$this->getAverageTotalPoints($this->model->compareTo,'equivalent'));
					  */
		
		$this->getCappedPointScore($this->model->compare,$this->model->mode);
		$this->getCappedPointScore($this->model->compareTo,$this->model->mode);
		$grid[]=array("col1"=>"Average Capped 8",
					  "col2"=>(int)($this->_cappedTotal[$this->model->compare]/$this->_cohortTotal),
					  "col4"=>(int)($this->_cappedTotal[$this->model->compareTo]/$this->_cohortTotal),
					  );
		$grid[]=array("col1"=>"Average Capped 8 inc Eng & Maths bonus",
					  "col2"=>(int)($this->_cappedTotalEngMathsBonus[$this->model->compare]/$this->_cohortTotal),
					  "col4"=>(int)($this->_cappedTotalEngMathsBonus[$this->model->compareTo]/$this->_cohortTotal),
					  );



		return $grid;
		
	}
	
	/**
	 * 
	 */
	public function getEnglishMathsGrid()
	{
		//Update date the master table for maths and English
		$this->updateKs4MasterMaths($this->model->compare,$this->model->mode);
		$this->updateKs4MasterMaths($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->compare,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->compareTo,$this->model->mode);
		
		/**
		 * Display cached 5 x A*-C
		 */
		$grid[]=$this->_headlineRow['5AstarToC'];

		/**
		 * Get No pupils with 1xA*C in English
		 */
		$title = "1 x A*-C English";
		$params['groupMethod'] = 'get1AstarToC';
		$numPupilsCompare =$this->getEnglish1AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='english_astar_c';
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = $this->getEnglish1AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);	
					
		/**
		 * Get No pupils with 1xA*C in Maths
		 */
		$title = "1 x A*-C Maths";
		$params['groupMethod'] = 'get1AstarToC';
		$numPupilsCompare = $this->getMaths1AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']='maths_astar_c';
		$compareLink = $this->getDropDownButton($numPupilsCompare,$params,3);
									
		$numPupilsCompareTo = $this->getMaths1AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getDropDownButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);	
		
		/**
		 * Get No pupils with 5xA*-C including English & Maths
		 */
		$title = "5 x A*-C<br>inc English & Maths";
		$params['groupMethod'] = 'getIncEnglishMaths5AstarToC';
		$numPupilsCompare = $this->getIncEnglishMaths5AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = $this->getIncEnglishMaths5AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=$this->_headlineRow['5AstarToCIncEnglishMaths']=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
		);
					
		/**
		 * Get 5 x A*-C exc English & Maths
		 */	
		$title = "5 x A*-C<br>exc English & Maths";
		$params['groupMethod'] = 'getExcEnglishMaths5AstarToC';
		$numPupilsCompare = $this->getExcEnglishMaths5AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$compareLink =  $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = $this->getExcEnglishMaths5AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink =  $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		/**
		 * Get 5 x A*-C inc English only
		 */	
		$title = "5 x A*-C<br>inc English only<br>(no Maths)";
		$params['groupMethod'] = 'getIncEnglishOnly5AstarToC';
		$numPupilsCompare =$this->getIncEnglishOnly5AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$compareLink =  $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = $this->getIncEnglishOnly5AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink =  $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		/**
		 * Get 5 x A*-C inc Maths only (no English)
		 */	
		$title = "5 x A*-C<br>inc Maths only<br>(no English)";
		$params['groupMethod'] = 'getIncMathsOnly5AstarToC';
		$numPupilsCompare = $this->getIncMathsOnly5AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$compareLink =  $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo =  $this->getIncMathsOnly5AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink =  $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
					
		/**
		 * Get 1 x A*-C in English only (no Maths) not 5 x A*-C
		 */	
		$title = "1 x A*-C<br>in English only<br>(no Maths) not 5 x A*-C";
		$params['groupMethod'] = 'getEnglishOnlyNoMathsNot5AstarToC';
		$numPupilsCompare = $this->getEnglishOnlyNoMathsNot5AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$compareLink =  $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = $this->getEnglishOnlyNoMathsNot5AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink =  $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		/**
		 * Get 1 x A*-C in Maths only (no English) not 5 x A*-C
		 */	
		$title = "1 x A*-C<br>in Maths only<br>(no English) not 5 x A*-C";
		$params['groupMethod'] = 'getMathsOnlyNoEnglishNot5AstarToC';
		$numPupilsCompare = $this->getMathsOnlyNoEnglishNot5AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$compareLink =  $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo =  $this->getMathsOnlyNoEnglishNot5AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink =  $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		/**
		 * Get 1 x A*-C in English & Maths not 5 x A*-C
		 */	
		$title = "1 x A*-C<br>in English & Maths<br>not 5 x A*-C";
		$params['groupMethod'] = 'getEnglishAndMathsNot5AstarToC';
		$numPupilsCompare = $this->getEnglishAndMathsNot5AstarToC($this->model->compare,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compare;
		$compareLink =  $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo =  $this->getEnglishAndMathsNot5AstarToC($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink =  $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		/**
		 * Get Attainers
		 */
		$this->getAttainers5AstarToCIncEnglishMaths();

		/**
		 * Get 5 x A*-C inc English & Maths Low Attainers
		 */
		$title = "5 x A*-C inc English & Maths Low Attainers";
		$params['groupMethod'] = 'getAttainers5AstarToCIncEnglishMaths';
		$numPupilsCompare = (int)$this->_attainers5AstarToCIncEnglishMaths[$this->model->compare]['1']['astar_c5'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']=1;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainers5AstarToCIncEnglishMaths[$this->model->compareTo]['1']['astar_c5'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=$this->_headlineRow['AstarToC5LowAttainers']=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);	
		/**
		 * Get 5 x A*-C inc English & Maths Middle Attainers
		 */			
		$title = "5 x A*-C inc English & Maths Middle Attainers";
		$params['groupMethod'] = 'getAttainers5AstarToCIncEnglishMaths';
		$numPupilsCompare = (int)$this->_attainers5AstarToCIncEnglishMaths[$this->model->compare]['2']['astar_c5'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']=2;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainers5AstarToCIncEnglishMaths[$this->model->compareTo]['2']['astar_c5'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=$this->_headlineRow['AstarToC5MiddleAttainers']=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);

		/**
		 * Get 5 x A*-C inc English & Maths High Attainers
		 */	
		$title = "5 x A*-C inc English & Maths High Attainers";
		$params['groupMethod'] = 'getAttainers5AstarToCIncEnglishMaths';
		$numPupilsCompare = (int)$this->_attainers5AstarToCIncEnglishMaths[$this->model->compare]['3']['astar_c5'];
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']=3;
		$compareLink =  $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainers5AstarToCIncEnglishMaths[$this->model->compareTo]['3']['astar_c5'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink =  $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=$this->_headlineRow['AstarToC5HighAttainers']=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
		
		return $grid;
		
	}
	
	/**
	 * Returns an array containing headline figures
	 */
	public function getHeadlineGrid()
	{ 		
		//Get cached A*-C rows
		$grid[]=$this->_headlineRow['5AstarToC'];
		$grid[]=$this->_headlineRow['5AstarToCIncEnglishMaths'];
	
		//Get cached 5 x A*-C for Low, Middle and High Attainers
		$grid[]=$this->_headlineRow['AstarToC5LowAttainers'];
		$grid[]=$this->_headlineRow['AstarToC5MiddleAttainers'];
		$grid[]=$this->_headlineRow['AstarToC5HighAttainers'];

					
		//Update remaining Ebacc tables
		$this->updateKs4MasterScienceEbacc($this->model->compare,$this->model->mode);
		$this->updateKs4MasterScienceEbacc($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterHumanity($this->model->compare,$this->model->mode);
		$this->updateKs4MasterHumanity($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterLang($this->model->compare,$this->model->mode);
		$this->updateKs4MasterLang($this->model->compareTo,$this->model->mode);

		$title = "1 x Ebacc";
		$numPupilsCompare =  $this->getEbacc($this->model->compare,$this->model->mode);
		$params['groupMethod']='getEbacc';
		$params['groupFieldMappingId'] = $this->model->compare;
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo =  $this->getEbacc($this->model->compareTo,$this->model->mode);
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);		
	return $grid;	
	}
	
	/**
	 * Returns an array of grid rows for low, middle and high attainers
	 * @return array
	 */
	public function getAttainersGrid()
	{
		
		$this->getAttainers();
		
		/**
		 * Low attainers
		 */
    	$title = "Low Attainers";
		$numPupilsCompare = (int)$this->_attainers[$this->model->compare]['low'];
		$params['groupMethod']='getAttainers';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']=1;//Column name
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainers[$this->model->compareTo]['low'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);	
	
		/**
		 * Middle attainers
		 */
    	$title = "Middle Attainers";
		$numPupilsCompare = (int)$this->_attainers[$this->model->compare]['middle'];
		$params['groupMethod']='getAttainers';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']=2;//Column name
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainers[$this->model->compareTo]['middle'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);	
		/**
		 * High attainers
		 */		
    	$title = "High Attainers";
		$numPupilsCompare = (int)$this->_attainers[$this->model->compare]['high'];
		$params['groupMethod']='getAttainers';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']=3;//Column name
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainers[$this->model->compareTo]['high'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);

		/**
		 * No Prior attainment
		 */		
    	$title = "No Prior Attainment";
		$numPupilsCompare = (int)$this->_attainers[$this->model->compare]['non'];
		$params['groupMethod']='getAttainers';
		$params['groupFieldMappingId'] = $this->model->compare;
		$params['arg0']=0;//Column name
		$compareLink = $this->getButton($numPupilsCompare,$params);
									
		$numPupilsCompareTo = (int)$this->_attainers[$this->model->compareTo]['non'];
		$params['groupFieldMappingId'] = $this->model->compareTo;
		$compareToLink = $this->getButton($numPupilsCompareTo,$params);
		
		$grid[]=array("col1"=>$title,
					  "col2"=>$this->getPercentage($numPupilsCompare),
					  "col3"=>$compareLink,
					  "col4"=>$this->getPercentage($numPupilsCompareTo),
					  "col5"=>$compareToLink,
					);
					
		//Get cached 5 x A*-C for Low, Middle and High Attainers
		$grid[]=$this->_headlineRow['AstarToC5LowAttainers'];
		$grid[]=$this->_headlineRow['AstarToC5MiddleAttainers'];
		$grid[]=$this->_headlineRow['AstarToC5HighAttainers'];
					
		return $grid;
		
	}
	
	
	/**
	 * Provides CSS for all other columns
	 */
	public function getCellCss($row,$data)
	{
		
		if($data['col2']>$data['col4'])
		return "green";
		
		if($data['col2']==$data['col4'])
		return "amber";
		
		if($data['col2']<$data['col4'])
		return "red";
	
	}
	
	/**
	 * Provides CSS for the cohort grid. Note that only rows 1,2,3 are formatted
	 */
	public function getCellCssCohort($row,$data)
	{
		if(in_array($row,array(2,3,4))){
		
		if($data['col2']>$data['col4'])
		return "green";
		
		if($data['col2']==$data['col4'])
		return "amber";
		
		if($data['col2']<$data['col4'])
		return "red";
		}
	
	}
	
}