<?php
class PtKs4PupilGrid extends PtKs4Pupil
{

	/**
	 * Class constructor
	 */
	public function __construct($model)
	{
		parent::__construct();
		$this->model=$model;
		$this->_filteredPupilsInClause="'{$this->model->pupilId}'";//Limit all queries to a single pupil

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
		if($grid=Yii::app()->dataCache->getDataCache($this->attributesForCaching,$this->model->cohortId,4,'ks4Pupil')){
		return $grid;
		}
		
		//Build the ks4master table 
		$this->buildKs4Master($this->model->compare,$this->model->mode);
		$this->buildKs4Master($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterKs2Levels();

		$this->getCappedPointScore($this->model->compareTo,$this->model->mode);
		$this->getCappedPointScore($this->model->compare,$this->model->mode);

		$this->Ks4Master;//Makes $this->_ks4Master available to all methods


		$grid['results']=$this->resultsGrid;
		$grid['title']=$this->titleGrid;
		$grid['badges']=$this->badgesGrid;
		$grid['summary']=$this->summaryGrid;
		$grid['ks2Summary']=$this->ks2SummaryGrid;
		$grid['ks2']=$this->ks2Grid;
		$grid['ks2Subjects']=$this->ks2SubjectsGrid;
		$grid['allSubjectPointScores']=$this->allSubjectPointScoresGrid;
		$grid['allSubjectAps']=$this->allSubjectApsGrid;
		$grid['filters']=$this->filtersGrid;

		Yii::app()->dataCache->setDataCache($this->attributesForCaching,$grid,$this->model->cohortId,4,'ks4Pupil');
		
		// Uncomment when we want temp tables dropped
		$connection = Yii::app()->db;
		$connection->dropTmpTables();
		
		return $grid;
	}

	/**
	 * Returns the main results grid
	 * @return array
	 */
	public function getResultsGrid()
	{

		$results = $this->results;

		$this->_noSubjects = count($results);

		
		/**
		 * Extract Total Point Score, APS and entries
		 * Note that entries is all the volume indictors added together
		 */
		foreach($results as $key=>$result){
			$this->_dcpTotal+=($result['dcp_standardised_points']*$result[$this->model->mode]);
			$this->_targetTotal+=($result['target_standardised_points']*$result[$this->model->mode]);
			$this->_entries += $result[$this->model->mode];
			if($result['discount_code']!='')
			$discountCodes[] = $result['discount_code'];
		}


		//Check discount codes
		$dcpDiscountKeys=array();
		$targetDiscountKeys=array();

		if($discountCodes){
			$dcpScores=array();
			$targetScores=array();
			$discountCodes = array_unique($discountCodes);
			$dcpDiscountKeys=array();
			foreach($discountCodes as $code){
				foreach($results as $key=>$result){
					if($result['discount_code']!=''){
						if($result['discount_code']==$code){//e.g. if code == AAA
							$dcpScores[$code][$key]=$result['dcp_standardised_points'];
							$targetScores[$code][$key]=$result['target_standardised_points'];
						}
					}
				}
				$dcpDiscountKeys[] = array_search(max($dcpScores[$code]), $dcpScores[$code]);
				$targetDiscountKeys[] = array_search(max($targetScores[$code]), $targetScores[$code]);		
			}
		}

		//print_r($discountKeys);
		//exit;
		/*
		foreach($dcpScores as $score){
			$dcpDiscountKey[] = max($score);
		}*/


		$this->_dcpAps = number_format($this->_dcpTotal/$this->_noSubjects,2);
		$this->_targetAps = number_format($this->_targetTotal/$this->_noSubjects,2);


		/**
		 * Rebuild the grid
		 * Note that the subject residual is calculated by taking the APS and dividing by the number of entries (e.g. sum of volume indicators) This figure is then
		 * taken away from the subject point score. e.g. if APS is 348 and No. Entries is 9 then its 348/9=38.67 if the subject point score is 28 then its 28-38.67=-10.67
		 * however, the volume indicator must be taken into account e.g. if subject point score is 180 and subject is worth 4 GCSEs then we must do 180/4 - 38.67. Note
		 * that we get around the 180/4 below by only using the standardised point score
		 */
		foreach($results as $key=>$result){

			$dcpStandardisedPoints = number_format($result['dcp_standardised_points']*$result[$this->model->mode],2);

			$dcpResidual = number_format((($result[$this->model->mode]==0) ? 0 : $result['dcp_standardised_points'])-$this->_dcpTotal/$this->_entries,2);
			$targetResidual = number_format((($result[$this->model->mode]==0) ? 0 : $result['target_standardised_points'])-($this->_targetTotal/$this->_entries),2);
			$subjectAps = number_format($result['subject_aps']*$result[$this->model->mode],2);


			$grid[]=array(
						'subject'=>$result['subject'],
						'qualification'=>$result['qualification'],
						'discount_code'=>$result['discount_code'],
						'set_code'=>$result['set_code'],
						'teacher'=>$result['teacher'],
						'no_gcses'=>$result[$this->model->mode],
						'dcp_result'=>$result['dcp_result'],
						'dcp_standardised_points'=>$dcpStandardisedPoints,
						'target_result'=>$result['target_result'],
						'target_standardised_points'=>number_format($result['target_standardised_points']*$result[$this->model->mode],2),
						'diff'=>number_format(($result['dcp_standardised_points']*$result[$this->model->mode])-($result['target_standardised_points']*$result[$this->model->mode]),2),
						'dcp_residual'=>$dcpResidual,
						'target_residual'=>	$targetResidual,
						'residual_diff'=>number_format($dcpResidual-$targetResidual,2),
						'subject_aps'=>$subjectAps,
						'subject_aps_diff'=>number_format($dcpStandardisedPoints-$subjectAps,2),
						'percentage_present'=>$result['percentage_present'],
						'percentage_unauthorised_absences'=>$result['percentage_unauthorised_absences'],
						'lates'=>$result['lates'],
						'dcp_discount'=>(in_array($key, $dcpDiscountKeys)) ? 1 : 0,
						'target_discount'=>(in_array($key, $targetDiscountKeys)) ? 1 : 0,
						);

			//Construct a grid containing just the english and maths subjects
			if(in_array($result['type'],array('Maths','English','EngLit','EngLang'))){
			$this->_ks2SubjectsGrid[]=array(
						'subject'=>$result['subject'],
						'qualification'=>$result['qualification'],
						'set_code'=>$result['set_code'],
						'teacher'=>$result['teacher'],
						'no_gcses'=>$result[$this->model->mode],
						'dcp_result'=>$result['dcp_result'],
						'dcp_standardised_points'=>$dcpStandardisedPoints,
						'target_result'=>$result['target_result'],
						'target_standardised_points'=>number_format($result['target_standardised_points']*$result[$this->model->mode],2),
						'diff'=>number_format(($result['dcp_standardised_points']*$result[$this->model->mode])-($result['target_standardised_points']*$result[$this->model->mode]),2),
						//'dcp_residual'=>$dcpResidual,
						//'target_residual'=>	$targetResidual,
						//'residual_diff'=>number_format($dcpResidual-$targetResidual,2),
						'subject_aps'=>$subjectAps,
						'subject_aps_diff'=>number_format($dcpStandardisedPoints-$subjectAps,2),
						//'percentage_present'=>$result['percentage_present'],
						//'percentage_unauthorised_absences'=>$result['percentage_unauthorised_absences'],
						//'lates'=>$result['lates'],
						);
						}
		}
		return $grid;
	}

	/**
	 * Returns the title grid
	 * @return array
	 */
	public function getTitleGrid()
	{
		//Fetch field mapping objects
		$compare = FieldMapping::model()->findByPk($this->model->compare);
		$compareTo = FieldMapping::model()->findByPk($this->model->compareTo);

		$grid[]=array(
				'compareName'=>$compare->mapped_alias,
				'compareToName'=>$compareTo->mapped_alias,
				'compareDate'=>$compare->date,
				'compareToDate'=>$compareTo->date,
				);

		return $grid;
	}

	/**
	 * Returns the DCP badges grid. This is the first row from the ks4 master table
	 * @return array
	 */
	public function getBadgesGrid()
	{

		 return $this->_ks4Master[$this->model->compare];

	}

	/**
	 * Returns the levels progress grid
	 * @return array
	 */
	public function getKs2Grid()
	{
		$compare = $this->model->compare;
		$compareTo = $this->model->compareTo;

		$englishLpCompare[] = ($this->_ks4Master[$compare]['english_lp4']) ? 4 :0;
		$englishLpCompare[] = ($this->_ks4Master[$compare]['english_lp3']) ? 3 :0;
		$englishLpValueCompare = max($englishLpCompare);
		$englishLpValueCompare = (in_array($englishLpValueCompare,array(3,4))) ? $englishLpValueCompare : 'Expected progress not made';

		$englishLpCompareTo[] = ($this->_ks4Master[$compareTo]['english_lp4']) ? 4 :0;
		$englishLpCompareTo[] = ($this->_ks4Master[$compareTo]['english_lp3']) ? 3 :0;
		$englishLpValueCompareTo = max($englishLpCompareTo);
		$englishLpValueCompareTo = (in_array($englishLpValueCompareTo,array(3,4))) ? $englishLpValueCompareTo : 'Expected progress not made';

		$mathsLpCompare[] = ($this->_ks4Master[$compare]['maths_lp4']) ? 4 :0;
		$mathsLpCompare[] = ($this->_ks4Master[$compare]['maths_lp3']) ? 3 :0;
		$mathsLpValueCompare = max($mathsLpCompare);
		$mathsLpValueCompare = (in_array($mathsLpValueCompare,array(3,4))) ? $mathsLpValueCompare : 'Expected progress not made';

		$mathsLpCompareTo[] = ($this->_ks4Master[$compareTo]['maths_lp4']) ? 4 :0;
		$mathsLpCompareTo[] = ($this->_ks4Master[$compareTo]['maths_lp3']) ? 3 :0;
		$mathsLpValueCompareTo = max($mathsLpCompareTo);
		$mathsLpValueCompareTo = (in_array($mathsLpValueCompareTo,array(3,4))) ? $mathsLpValueCompareTo : 'Expected progress not made';


		$grid[]=array(
			'title'=>'<strong>DCP</strong>',
			'english_lp'=>$englishLpValueCompare,
			'target_english_lp'=>$englishLpValueCompareTo,
			'maths_lp'=>$mathsLpValueCompare,
			'target_maths_lp'=>$mathsLpValueCompareTo,
			);

		$grid[]=array(
			'title'=>'<strong>Target</strong>',
			'english_lp'=>$englishLpValueCompareTo,
			'maths_lp'=>$mathsLpValueCompareTo,
			);

		return $grid;
	}

	/**
	 * Returns the KS2 summary grid
	 * @return array
	 */
	public function getKs2SummaryGrid()
	{
		$compare = $this->model->compare;

		$grid[]= array(
			'ks2_english_level'=>$this->_ks4Master[$compare]['ks2_english_level'],
			'ks2_maths_level'=>$this->_ks4Master[$compare]['ks2_maths_level'],
			'ks2_science_level'=>$this->_ks4Master[$compare]['ks2_science_level'],
			'ks2_english_ps'=>$this->_ks4Master[$compare]['ks2_english_ps'],
			'ks2_maths_ps'=>$this->_ks4Master[$compare]['ks2_maths_ps'],
			'ks2_science_ps'=>$this->_ks4Master[$compare]['ks2_science_ps'],
			'ks2_aps'=>$this->_ks4Master[$compare]['ks2_average'],
			);

		return $grid;

	}

	/**
	 * Returns the pupil results summary grid
	 * @return array
	 */
	public function getSummaryGrid()
	{
		//Cache target calculations
		$targetAstarToAPercentage = number_format(($this->_ks4Master[$this->model->compareTo]['astar_a']/$this->_entries)*100,2);
		$targetAstarToCPercentage = number_format(($this->_ks4Master[$this->model->compareTo]['astar_c']/$this->_entries)*100,2);
		$targetAstarToGPercentage = number_format(($this->_ks4Master[$this->model->compareTo]['astar_g']/$this->_entries)*100,2);


		$grid[]=array('title'=>'<strong>DCP</strong>',
					  'astar_a_percentage'=>number_format(($this->_ks4Master[$this->model->compare]['astar_a']/$this->_entries)*100,2),
					  'astar_a'=>$this->_ks4Master[$this->model->compare]['astar_a'],
					  'target_astar_a_percentage'=>$targetAstarToAPercentage,
					  'astar_c'=>$this->_ks4Master[$this->model->compare]['astar_c'],
					  'astar_c_percentage'=>number_format(($this->_ks4Master[$this->model->compare]['astar_c']/$this->_entries)*100,2),
					  'target_astar_c_percentage'=>$targetAstarToCPercentage,
					  'astar_g'=>$this->_ks4Master[$this->model->compare]['astar_g'],
					  'astar_g_percentage'=>number_format(($this->_ks4Master[$this->model->compare]['astar_g']/$this->_entries)*100,2),
					  'target_astar_g_percentage'=>$targetAstarToGPercentage,
					  'aps'=>$this->_dcpAps,
					  'target_aps'=>$this->_targetAps,
					  'total_points'=>$this->_dcpTotal,
					  'target_total_points'=>$this->_targetTotal,
					  'capped8'=>$this->_cappedTotal[$this->model->compare],
					  'target_capped8'=>$this->_cappedTotal[$this->model->compareTo],
					  'entries'=>$this->_entries,
			);

		$grid[]=array('title'=>'<strong>Target</strong>',
					  'astar_a_percentage'=>$targetAstarToAPercentage,
					  'astar_a'=>$this->_ks4Master[$this->model->compareTo]['astar_a'],
					  'astar_c'=>$this->_ks4Master[$this->model->compareTo]['astar_c'],
					  'astar_c_percentage'=>$targetAstarToCPercentage,
					  'astar_g'=>$this->_ks4Master[$this->model->compareTo]['astar_g'],
					  'astar_g_percentage'=>$targetAstarToGPercentage,
					  'aps'=>$this->_targetAps,
					  'total_points'=>$this->_targetTotal,
					  'capped8'=>$this->_cappedTotal[$this->model->compareTo],
					  'entries'=>$this->_entries,
			);
		return $grid;
	}

	/**
	 * Returns a list of all English and Maths Subjects
	 * @return [type] [description]
	 */
	public function getKs2SubjectsGrid()
	{
		return $this->_ks2SubjectsGrid;
	}

	public function getAllSubjectPointScoresGrid()
	{
		return $this->getAllSubjectPointScores('dcp',$this->model->mode);
	}


	public function getAllSubjectApsGrid()
	{
		return $this->getAllSubjectAps('dcp',$this->model->mode);
	}

	public function getFiltersGrid()
	{
		return $this->filters;
	}





}
