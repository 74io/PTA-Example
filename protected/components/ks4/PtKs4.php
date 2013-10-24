<?php
class PtKs4 extends PtBuild
{
	public $model;
	public $qs; //Query String

	protected $_attributesForCaching;

	/**
	 * An array of cohort ids. If the current cohort id is found in this array then it will calculate
	 * the ks2 average based on English, Maths and Science. This may change to English and Maths 
	 * @var array
	 */
	public $ks2EngMathsSciCohorts=array('2011-2012',
	 '2012-2013','2013-2014','2014-2015','2015-2016','2016-2017','2017-2018','2018-2020');
	public $_cohortTotal;

	protected $_aStarToC=array();
	protected $_filteredPupilsInClause;
	protected $_ks4MasterBuilt=array();
	protected $_ks4MasterTempTableBuilt;
	protected $_ks4MetaTempTableBuilt;
	protected $_cappedPupil;
	protected $_cappedTotal=array();
	protected $_cappedTotalEngMathsBonus=array();
	//Cached grid results
	protected $_englishMathsCompare=array();
	protected $_englishMathsCompareTo=array();
	protected $_levelsProgress=array();
	protected $_attainersLevelsProgress=array();
	protected $_attainers=array();
	protected $_attainers5AstarToCIncEnglishMaths=array();
	//Cached query in clauses
	protected $_englishPupils=array();
	protected $_englishLitLangPupils=array();
	protected $_mathsPupils=array();
	protected $_scienceCoreAdditionalPupils=array();
	protected $_sciencePhChBiPupils=array();
	protected $_humanityPupils=array();
	protected $_langPupils=array();
	
	//Cached headline grid rows
	protected $_headlineRow=array();
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->qs=urldecode(Yii::app()->request->queryString);
	}
	
	public function init()
	{
		
	}
	
	/**
	 * Returns a line of SQL that limits pupils to the pupils filtered in the filter form
	 * @return string
	 */
	public function getFilteredPupilsInClause()
	{
		
		if($this->_filteredPupilsInClause!==null)
		return $this->_filteredPupilsInClause;
		
		$command=Yii::app()->db->createCommand();
		$command->select('pupil_id')->from('pupil');

		$command->where('year=:year',array(':year'=>$this->model->yearGroup));
		$command->andWhere('cohort_id=:cohort_id',array(':cohort_id'=>$this->model->cohortId));

	
		if($this->model->gender){
		$el = $this->model->gender; //Pass to el so blank still shows as selected on form
		$el[0] = (strtolower($el[0])=='blank') ? '' : $el[0];
		$command->andWhere(array('in','gender',$el));
		$param=true;
		}

		if($this->model->ethnicity){
		$el = $this->model->ethnicity;
		$el[0] = (strtolower($el[0])=='blank') ? '' : $el[0];
		$command->andWhere(array('in','ethnicity',$el));
		$param=true;
		}
		
		if($this->model->sen_code){
		$el = $this->model->sen_code;
		$el[0] = (strtolower($el[0])=='blank') ? '' : $el[0];
		$command->andWhere(array('in','sen_code',$el));
		$param=true;
		}
		
		if($this->model->fsm){
		$el = $this->model->fsm;
		$el[0] = (strtolower($el[0])=='blank') ? '' : $el[0];
		$command->andWhere(array('in','fsm',$el));
		$param=true;
		}
		
		if($this->model->gifted){
		$el = $this->model->gifted;
		$el[0] = (strtolower($el[0])=='blank') ? '' : $el[0];
		$command->andWhere(array('in','gifted',$el));
		$param=true;		
		}
		
		if($this->model->cla){
		$el = $this->model->cla;
		$el[0] = (strtolower($el[0])=='blank') ? '' : $el[0];
		$command->andWhere(array('in','cla',$el));
		$param=true;
		}
		
		if($this->model->eal){
		$el = $this->model->eal;
		$el[0] = (strtolower($el[0])=='blank') ? '' : $el[0];
		$command->andWhere(array('in','eal',$el));
		$param=true;
		}

		if($this->model->pupil_premium){
		$el = $this->model->pupil_premium;
		$el[0] = (strtolower($el[0])=='blank') ? '' : $el[0];
		$command->andWhere(array('in','pupil_premium',$el));
		$param=true;
		}
			
		if($param){
		$column= $command->queryColumn();
			if($column){
				return $this->_filteredPupilsInClause = "'".implode("','",$column)."'";
			}
			return;
		}
		
		return $this->_filteredPupilsInClause="";
	}
	

	
	/**
	 * Returns the number of pupils in a cohort for a specified year group.
	 * This is calculated from the number of distinct pupils who take a subject (have a set code)
	 * @return integer
	 */
	public function getCohortTotal()
	{
		if($this->_cohortTotal!==null)
		return $this->_cohortTotal;

		/**
		 * Begin test
		 * @todo  work out how we can eliminate this query by using the ks4master table instead
		 */
		/*
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];

		$sql="SELECT COUNT(DISTINCT(pupil_id)) FROM $t";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryScalar();
		*/
		/**
		 * End test
		 */

		
		$sql="SELECT COUNT(DISTINCT(setdata.pupil_id)) 
			FROM setdata 
			INNER JOIN (subjectmapping,pupil,ks4meta ) 
			ON (ks4meta.cohort_id = pupil.cohort_id
			AND ks4meta.pupil_id = pupil.pupil_id 
			AND ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND setdata.cohort_id = pupil.cohort_id
			AND setdata.pupil_id = pupil.pupil_id
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject) 
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id) 
			AND NOT EXISTS (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND pupil.cohort_id=:cohortId
			AND pupil.year=:yearGroup";
			if($this->filteredPupilsInClause)
			$sql.=" AND setdata.pupil_id IN ($this->filteredPupilsInClause)";

			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
			$command->bindParam(":yearGroup",$this->model->yearGroup,PDO::PARAM_INT);
			
			//echo $command->text;

			return $this->_cohortTotal = $command->queryScalar();
		
	}
	
	/**
	 * Returns the average total points for a specific field mapping id
	 * @param integer $fieldMappingId The ID of the field to calculate
	 * @param bool $equivalence Whether or not to use the equivalence column
	 */
	public function getAverageTotalPoints($fieldMappingId,$type='volume')
	{
		
			$sql="SELECT SUM(ks4meta.standardised_points*subjectmapping.$type)
			FROM ks4meta 
			INNER JOIN (subjectmapping,setdata ) 
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject) 
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id) 
			AND NOT EXISTS (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1' 
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
			 
			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);


			if($this->cohortTotal)
			return (int)($command->queryScalar()/$this->cohortTotal);
			
			return 0;
	}

	public function getKs2AverageTotalPoints($fieldMappingId)
	{
			$connection = Yii::app()->db;
			$t=$connection->tmpTable['ks4master'];

			$sql="SELECT SUM(ks2_average)
			FROM $t
			WHERE fieldmapping_id=:fieldMappingId";
			 
			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);


			if($this->cohortTotal)
			return number_format($command->queryScalar()/$this->cohortTotal,2);
			
			return 0;

	}
	

	/**
	 * Returns the sum of all the capped point scores
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId The field mapping Id
	 * @param string type Whether to use the volume indicator or the equivalent multiplier
	 * @return integer
	 */
	public function getCappedPointScore($fieldMappingId,$type='volume')
	{

			$array = array ();
			$allPupils = array ();

			$sql = "SELECT 
			ks4meta.pupil_id,
			subjectmapping.mapped_subject,
			$type AS multiplier, 
			type,
			ks4meta.standardised_points*subjectmapping.$type AS total_points,
			standardised_points
			FROM ks4meta INNER JOIN (subjectmapping,setdata )
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
			
			//$sql.=" ORDER BY ks4meta.pupil_id,standardised_points DESC";
			
			$command = Yii::app()->db->createCommand( $sql );
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			//$command->bindParam(":type",$type,PDO::PARAM_STR);
			$rows = $command->queryAll();
			
			
			
			//Restructure the rows with the pupil_id as the key
			foreach ( $rows as $row )
			{
				$pid = $row ['pupil_id'];
				if ($row ['pupil_id'] == $pid)
				{
					$allPupils [$pid] [] = $row;
				}
			}
			
			foreach ( $allPupils as $this->_cappedPupil )
			{
				$cumulative = 0;
				$total_points = 0;
				$t = 0;
				$log = false;

				$this->normaliseHigherScoring('English');
				$this->normaliseHigherScoring('Maths');
				$englishBonusScore = $this->englishBonusScore;
				usort($this->_cappedPupil, array($this, 'ptSort')); 
				
				
				foreach ( $this->_cappedPupil as $row )
				{
				//var_dump($pupils);
					//exit;
					//if($pupil['pupil_id']=="TMPDINA72005")
					//var_dump($pupil);
					
					$cumulative += $row ['multiplier'];
					$total_points += $row ['total_points'];
					if($row['type'=="Maths"])
					$mathsBonusScore = $row['standardised_points'];
					/*
					if($pupil['pupil_id']=="TMPDINA72005"){
					echo $cumulative."<br>";
					echo $total_points."<br>";
					}*/
					if (($cumulative - 8) >= 0)
					{
						$t = $row ['total_points'] * (($cumulative - 8) / $row ['multiplier']);
						$total_points -= $t;
						/*
						if($pupil['pupil_id']=="TMPDINA72005"){
						echo "cumulative-".$cumulative."<br>";
						echo $t."<br>";
						}*/
						
						$log = true;
						break;
					}
				
				}
				if ($log){
					$capped[$row['pupil_id']] = $total_points;
					$cappedEngMathsBonus[$row['pupil_id']] =  $total_points + ($mathsBonusScore+$englishBonusScore);
				}
					
			}
			
			if ($capped)
			{
				/*
				$sql = "INSERT INTO ks4capped (fieldmapping_id,scores)
				VALUES(
				'$fieldmapping_id',
				'" . serialize( $array ) . "'
				)";
				$command = Yii::app()->db->createCommand( $sql );
				$command->execute();
				*/
				//Caches the total so it can be called once built without having to be re selected from the DB
				//$this->_cappedPointScoreBuilt[$fieldmapping_id]=$array;
				$this->_cappedTotal[$fieldMappingId] = array_sum($capped);
				$this->_cappedTotalEngMathsBonus[$fieldMappingId] = array_sum($cappedEngMathsBonus);
				//$allPupils = array ();
			}
		return true;
	}
	
	/**
	 * A custom sort function to sort a multi dimentional array by a specific key
	 * @param array $a
	 * @param array $b
	 */
	public function  ptSort($a, $b)
	 { 
	 	return $a['standardised_points']<$b['standardised_points'];    
	 }
	 
	/**
	 * Returns the score that can be used in the English and Maths Bonus. See Statement of Intent 2011 page 6
	 * to reference what can count as English in performance tables
	 * @return integer
	 */
	public function getEnglishBonusScore()
	 {
	 	foreach($this->_cappedPupil as $key=>$row)
	 	{
	 		if($row['type']=="English"){
	 		$array[$key]=$row;
	 		$english['bool']=true;
	 		$english['key']=$key;
	 		}
	 		
	 	 	if($row['type']=="EngLang"){
	 		$array[$key]=$row;
	 		$engLang['key']=$key;
	 		$engLang['bool']=true;
	 		}
	 		
	 	 	if($row['type']=="EngLit"){
	 		$engLit['key']=$key;
	 		$engLit['bool']=true;
	 		}
	 	}
		if($array){
		 	if($engLang['bool'])
		 	{
		 		if($engLit['bool']===null){
		 			unset($array[$engLang['key']]);
		 			
		 		}
		 	}
		 	
		 	usort($array, array($this, 'ptSort'));
		 	return $array[0]['standardised_points'];
		}
		
		return 0;
	
	 }
	 
	 /**
	  * This method looks for all the subjects of type e.g. 'English' & works out which is the higher scoring
	  * and then unsets the others. This will take into account AS Level English which must be marked as
	  * type 'English' and not 'EngLang'.
	  * @param string $type The type of subject e.g. English or Maths
	  * @return void
	  */
	 public function normaliseHigherScoring($type)
	 {
	 	foreach($this->_cappedPupil as $key=>$row)
	 	{
	 		if($row['type']==$type){
	 			$array[$key]=$row;
	 		}
	 	}
	 	if($array){
	 	uasort($array, array($this, 'ptSort'));
	 	$count=0;
	 	foreach($array as $key=>$value){
	 		if($count>0)
	 		unset($this->_cappedPupil[$key]);
	 		$count++;
	 	}
	 	}
	 }
	 
	 /**
	  * Returns an array of pupils taking the subject type 'English' whos set and UPN have not been
	  * filtered.
	  * QUERY OPTIMISED 14/06/2012
	  * @param integer $fieldMappingId The field mapping id
	  * @return void
	  */
	 public function getEnglishPupils($fieldMappingId)
	 {
	 	if($this->_englishPupils[$fieldMappingId]!==null)
	 	return $this->_englishPupils[$fieldMappingId];
	 	
			//Get all the pupils who take English and have not been filtered
			$sql="SELECT ks4meta.pupil_id 			
			FROM ks4meta INNER JOIN (subjectmapping,setdata )
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND subjectmapping.type ='English'
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";

			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$this->_englishPupils[$fieldMappingId] = $command->queryColumn();
	 }
	 
	 /**
	  * Returns an array of pupils who actually have a grade for both EngLang and EngLit.
	  * If they do their EngLang can be used.
	  * QUERY OPTIMISED 14/06/2012
	  * @param integer $fieldMappingId The field mapping id
	  * @return void
	  */
	 public function getEnglishLitLangPupils($fieldMappingId)
	 {
	 		if($this->_englishLitLangPupils[$fieldMappingId]!==null)
	 		return $this->_englishLitLangPupils[$fieldMappingId];
	 		
			$sql="SELECT ks4meta.pupil_id
			FROM ks4meta INNER JOIN (subjectmapping,setdata )
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND subjectmapping.type IN ('EngLit','EngLang')
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
	        $sql.=" GROUP BY ks4meta.pupil_id HAVING COUNT(subjectmapping.type)=2";
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$this->_englishLitLangPupils[$fieldMappingId] = $command->queryColumn();
	 	
	 }
	 

	
	/**
	 * Updates the ks4master table to include all the pupils with an A*-C in English.
	 * Note. When calculating the ranges for English it will take into account a higher scoring English
	 * because it simply counts the range of grades for English and provides a boolean result. For example
	 * a pupil with a below C grade in GCSE English will still be counted in A*-C if they have an A*-C in AS Level
	 * English because both are marked as English
	 * 
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return void
	 */
	public function updateKs4MasterEnglish($fieldMappingId,$type='volume')
	{
		

			$connection = Yii::app()->db;
			$t=$connection->tmpTable['ks4master'];
		
			$this->getEnglishPupils($fieldMappingId);
			
			if($this->_englishPupils[$fieldMappingId]){
			$inClause="'".implode("','",$this->_englishPupils[$fieldMappingId])."'";
			$sql="UPDATE $t
			SET 
			$t.english_astar_a = IF((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type ='English'
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_a*subjectmapping.$type)>=1),1,0),
			
			$t.english_astar_c = IF((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type ='English'
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_c*subjectmapping.$type)>=1),1,0),
			
			$t.english_astar_g = IF((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type ='English'
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_g*subjectmapping.$type)>=1),1,0)
			WHERE $t.fieldmapping_id=:fieldMappingId
			AND $t.type=:type
			";
			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->execute();
			}
		
		
			//Update only for the pupils who have a grade for both subjects
			$this->getEnglishLitLangPupils($fieldMappingId);
			if($this->_englishLitLangPupils[$fieldMappingId]){
			$inClause = "'".implode("','",$this->_englishLitLangPupils[$fieldMappingId])."'";
			$sql="UPDATE $t INNER JOIN (ks4meta, subjectmapping)
			ON ( 
			$t.pupil_id = ks4meta.pupil_id
			AND ks4meta.subjectmapping_id= subjectmapping.id
			AND  ks4meta.fieldmapping_id = $t.fieldmapping_id
			)
			SET $t.english_astar_a = IF($t.english_astar_a =0,
			(IF((ks4meta.astar_a*subjectmapping.$type)>=1,1,0)),1),
			$t.english_astar_c = IF($t.english_astar_c =0,
			(IF((ks4meta.astar_c*subjectmapping.$type)>=1,1,0)),1),
			$t.english_astar_g = IF($t.english_astar_g =0,
			(IF((ks4meta.astar_g*subjectmapping.$type)>=1,1,0)),1)
			WHERE subjectmapping.type='EngLang'
			AND subjectmapping.include='1'
			AND ks4meta.pupil_id IN ($inClause)
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND $t.type=:type";
			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->execute();
			}	
			
	}
	
	 /**
	  * Returns an array of pupils taking the subject type 'Maths' whos set and UPN have not been
	  * filtered.
	  * QUERY OPTIMISED 14/06/2012
	  * @param integer $fieldMappingId The field mapping id
	  * @return void
	  */
	public function getMathsPupils($fieldMappingId)
	{
		 	if($this->_mathsPupils[$fieldMappingId]!==null)
	 		return $this->_mathsPupils[$fieldMappingId];
	
			$sql="SELECT ks4meta.pupil_id
			FROM ks4meta INNER JOIN (subjectmapping,setdata )
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND subjectmapping.type ='Maths'
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$this->_mathsPupils[$fieldMappingId] = $command->queryColumn();
	}
	
	/**
	 * Updates the ks4master table to include all the pupils with an A*-C in Maths
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return void
	 */
	public function updateKs4MasterMaths($fieldMappingId,$type='volume')
	{
		//This takes care of all subjects marked as English including higher scoring subjects
			
			$connection = Yii::app()->db;
			$t=$connection->tmpTable['ks4master'];
			
			$this->getMathsPupils($fieldMappingId);
			if($this->_mathsPupils[$fieldMappingId]){
			$inClause = "'".implode("','",$this->_mathsPupils[$fieldMappingId])."'";

			$sql="UPDATE $t
			SET 
			$t.maths_astar_a = IF((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type ='Maths'
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_a*subjectmapping.$type)>=1),1,0),
			
			$t.maths_astar_c = IF((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type ='Maths'
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_c*subjectmapping.$type)>=1),1,0),
			
			$t.maths_astar_g = IF((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type ='Maths'
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_g*subjectmapping.$type)>=1),1,0)
			WHERE $t.fieldmapping_id=:fieldMappingId
			AND $t.type=:type
			";
			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->execute();
			}
			
	}
	
	/**
	 * Returns an array of Core Science or Additional Science pupils who's set or UPN have not been filtered
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId The field mapping id
	 * @return void
	 */
	public function getScienceCoreAdditionalPupils($fieldMappingId)
	{
			if($this->_scienceCoreAdditionalPupils[$fieldMappingId]!==null)
	 		return $this->_scienceCoreAdditionalPupils[$fieldMappingId];
	 		
			$sql="SELECT ks4meta.pupil_id
			FROM ks4meta INNER JOIN (subjectmapping,setdata)
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND subjectmapping.type IN ('Core Science','Additional Science')
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$this->_scienceCoreAdditionalPupils[$fieldMappingId] = $command->queryColumn();
	}
	
	/**
	 * Returns an array of pupils who actually have a grade for Ph/Ch/Bi and who are or whos set's are not filtered
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId The field mapping id
	 * @return void
	 */
	public function getSciencePhChBiPupils($fieldMappingId)
	{
			if($this->_sciencePhChBiPupils[$fieldMappingId]!==null)
	 		return $this->_sciencePhChBiPupils[$fieldMappingId];

			$sql="SELECT ks4meta.pupil_id
			FROM ks4meta INNER JOIN (subjectmapping,setdata)
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND subjectmapping.type IN ('Physics','Chemistry','Biology')
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
	        $sql.=" GROUP BY ks4meta.pupil_id HAVING COUNT(subjectmapping.type)=3";
			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$this->_sciencePhChBiPupils[$fieldMappingId] = $command->queryColumn();
	}
	
	/**
	 * Updates the ks4master table for all pupils who have an A*-C in  science
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return void
	 */
	public function updateKs4MasterScienceEbacc($fieldMappingId,$type='volume')
	{
			$connection = Yii::app()->db;
			$t=$connection->tmpTable['ks4master'];

			$this->getScienceCoreAdditionalPupils($fieldMappingId);
			if($this->_scienceCoreAdditionalPupils[$fieldMappingId]){
			$inClause = "'".implode("','",$this->_scienceCoreAdditionalPupils[$fieldMappingId])."'";
			/*
			 * Update the ks4master table setting a 1 for any pupil who has a count of 2 for 
			 * an A*-C in both Core and Additional Science
			 */
			$sql="UPDATE $t
			SET $t.science_astar_c_ebacc = IF(((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type IN ('Core Science','Additional Science')
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_c*subjectmapping.$type)>=1)=2),1,0)
			WHERE $t.fieldmapping_id=:fieldMappingId
			AND $t.type=:type
			";
			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->execute();
			}
			
			
			/*
			 * Only for the pupils who have a 0. Update to a 1 if they take the 3 sciences (see above)
			 * and have an A*-C in 2 of them
			 */
			$this->getSciencePhChBiPupils($fieldMappingId);
			if($this->_sciencePhChBiPupils[$fieldMappingId]){
			$inClause = "'".implode("','",$this->_sciencePhChBiPupils[$fieldMappingId])."'";
			$sql="UPDATE $t
			SET 
			$t.science_astar_c_ebacc = IF(
			$t.science_astar_c_ebacc=0,
			(IF(
			(SELECT COUNT(*) FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type IN ('Physics','Chemistry','Biology')
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_c*subjectmapping.$type)>=1)>=2,1,0)),1)
			WHERE $t.fieldmapping_id=:fieldMappingId
			AND $t.type=:type
			";
			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->execute();
			}
		
	}
	
	
	/**
	 * Returns an array of MFL or AFL pupils who's set or UPN have not been filtered
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId The field mapping id
	 * @return void
	 */
	public function getLangPupils($fieldMappingId)
	{
			if($this->_langPupils[$fieldMappingId]!==null)
	 		return $this->_langPupils[$fieldMappingId];
		
			$sql="SELECT ks4meta.pupil_id 
			FROM ks4meta INNER JOIN (subjectmapping,setdata)
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND subjectmapping.type IN ('MFL','AFL')
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$this->_langPupils[$fieldMappingId] = $command->queryColumn();
		
		
	}
	
	/**
	 * Updates the ks4 summary table to indicate who has an A*-C in an MFL or AFL language
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return void
	 */
	public function updateKs4MasterLang($fieldMappingId,$type='volume')
	{
		
			$connection = Yii::app()->db;
			$t=$connection->tmpTable['ks4master'];
			
			$this->getLangPupils($fieldMappingId);
			if($this->_langPupils[$fieldMappingId]){
			$inClause = "'".implode("','",$this->_langPupils[$fieldMappingId])."'";
			
			$sql="UPDATE $t
			SET 
			$t.lang_astar_c = IF((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type IN ('MFL','AFL')
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_c*subjectmapping.$type)>=1),1,0)
			WHERE $t.fieldmapping_id=:fieldMappingId
			AND $t.type=:type
			";
			
			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->execute();
			}
		
	}
	
	/**
	 * Returns an array of Humanity pupils who's set or UPN have not been filtered
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId The field mapping id
	 * @return void
	 */
	public function getHumanityPupils($fieldMappingId)
	{
			if($this->_humanityPupils[$fieldMappingId]!==null)
	 		return $this->_humanityPupils[$fieldMappingId];

			$sql="SELECT DISTINCT(ks4meta.pupil_id)
			FROM ks4meta INNER JOIN (subjectmapping,setdata)
			ON (ks4meta.cohort_id = setdata.cohort_id
			AND ks4meta.pupil_id = setdata.pupil_id 
			AND ks4meta.subjectmapping_id=subjectmapping.id 
			AND setdata.mapped_subject = subjectmapping.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.pupil_id=ks4meta.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
			AND subjectmapping.include='1'
			AND subjectmapping.type ='Humanity'
			AND ks4meta.fieldmapping_id=:fieldMappingId";
			if($this->filteredPupilsInClause)
			$sql.=" AND ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$this->_humanityPupils[$fieldMappingId] = $command->queryColumn();
	}
	
	/**
	 * Updates the ks4 summary table to indicate who has an A*-C in a humanity e.g. History or Geography
	 * QUERY OPTIMISED 14/06/2012
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return void
	 */
	public function updateKs4MasterHumanity($fieldMappingId,$type='volume')
	{
			$connection = Yii::app()->db;
			$t=$connection->tmpTable['ks4master'];
		
			$this->getHumanityPupils($fieldMappingId);
			if($this->_humanityPupils[$fieldMappingId]){
			$inClause = "'".implode("','",$this->_humanityPupils[$fieldMappingId])."'";
			
			$sql="UPDATE $t
			SET 
			$t.humanity_astar_c = IF((
			SELECT COUNT(*)	FROM ks4meta,subjectmapping
			WHERE ks4meta.subjectmapping_id = subjectmapping.id
			AND subjectmapping.type ='Humanity'
			AND ks4meta.fieldmapping_id=:fieldMappingId
			AND ks4meta.pupil_id=$t.pupil_id
			AND ks4meta.pupil_id IN ($inClause)
			AND (ks4meta.astar_c*subjectmapping.$type)>=1),1,0)
			WHERE $t.fieldmapping_id=:fieldMappingId
			AND $t.type=:type
			";
			
			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->execute();
			}
		
	}
	
	/**
	 * Returns the number of pupils with 1xEbacc
	 * http://www.education.gov.uk/schools/teachingandlearning/qualifications/englishbac/a0075980/ebaccontentsfaqs
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getEbacc($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		COUNT(*)
		FROM $t
		WHERE 
		english_astar_c=1
		AND maths_astar_c=1
		AND science_astar_c_ebacc=1
		AND lang_astar_c=1
		AND humanity_astar_c=1
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
	
	}
	
	/**
	 * Returns all pupils achieving the Ebacc
	 */
	public function getEbaccGroup()
	{
		//Run Ebacc dependancies
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterScienceEbacc($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterHumanity($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterLang($this->model->groupFieldMappingId,$this->model->mode);
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.english_astar_c=1
		AND t1.maths_astar_c=1
		AND t1.science_astar_c_ebacc=1
		AND t1.lang_astar_c=1
		AND t1.humanity_astar_c=1";
		$command=$connection->createCommand($sql);
		return $command->queryAll();
	}
	
	/**
	 * Returns the number of pupils with 1xA*-C in English only
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getEnglish1AstarToC($fieldMappingId,$type='volume',$filter='')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		COUNT(*)
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE english_astar_c=1
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		if($filter)
		$sql.=" AND $filter";

		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
	}
	
	/**
	 * Returns all pupils with 1 x A*-C in English
	 */
	public function getEnglish1AstarToCGroup()
	{
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.english_astar_c=".$this->model->groupAchiever."
		ORDER BY surname, forename";
		$command=$connection->createCommand($sql);
		return $command->queryAll();
	}
	
	/**
	 * Returns the number of pupils with 1xA*-C in Maths
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getMaths1AstarToC($fieldMappingId,$type='volume',$filter='')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		COUNT(*)
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE maths_astar_c=1
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		if($filter)
		$sql.=" AND $filter";

		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
	}
	
	/**
	 * Returns all pupils with 1 x A*-C in Maths
	 */
	public function get1AstarToCGroup()
	{
		//Here we run both dependencies. Maybe room for optimisation?
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		$filter = $this->model->groupArg[2];

		if($this->model->opId==2)
		return $this->get1AstarToCGroupShouldAchievers();

		$criteria = $this->model->groupArg[0].'='.$this->model->groupAchiever;
		return $this->getGroupData($filter,$criteria,$this->model->groupFieldMappingId);

	}

	/**
	 * [get1AstarToCGroupShouldAchievers description]
	 * @return [type] [description]
	 */
	public function get1AstarToCGroupShouldAchievers()
	{
		$this->buildKs4Master($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterMaths($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->compareTo,$this->model->mode);
		$filter = $this->model->groupArg[2];

		//Get the current DCP non achievers
		$criteria = $this->model->groupArg[0].'=0';
		$nonAchieversDcp = $this->getGroupData($filter, $criteria, $this->model->compare);

		//Get the current target achievers
		$criteria = $this->model->groupArg[0].'=1';
		$achieversTarget = $this->getGroupData($filter, $criteria, $this->model->compareTo);

		//Extract Pupil id from target achievers array
		$pupilIds = $this->extractPupilIds($achieversTarget);

		//Build a new non achievers array containing just the non achievers who should be achieving when
		//compare to the current target
		foreach($nonAchieversDcp as $key=>$value){
			if(!in_array($nonAchieversDcp[$key]['pupil_id'],$pupilIds))
				unset($nonAchieversDcp[$key]);
		}
		return $nonAchieversDcp;

	}
	
	/**
	 * Returns the number of pupils with 5xA*-C including English and Maths
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @param string $filter A filter to limit results based upon the pupil table
	 * @return integer
	 */
	public function getIncEnglishMaths5AstarToC($fieldMappingId,$type='volume',$filter='')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		SUM(astar_c>=5) AS astar_c5
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE english_astar_c=1
		AND maths_astar_c=1
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		if($filter)
		$sql.=" AND $filter";

		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
	}
	
	/**
	 * Returns pupils with 5 x A*-C including English and Maths
	 */
	public function getIncEnglishMaths5AstarToCGroup()
	{
		//Run dependancies
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);

		$filter = $this->model->groupArg[2];
		$criteria = "english_astar_c=".$this->model->groupAchiever." 
		AND maths_astar_c=".$this->model->groupAchiever." 
		AND astar_c>=5";
		return $this->getGroupData($filter, $criteria, $this->model->groupFieldMappingId);

	}
	
	/**
	 * Returns the number of pupils with 5xA*-C excluding English and Maths
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getExcEnglishMaths5AstarToC($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		SUM(astar_c>=5) AS astar_c5
		FROM $t
		WHERE english_astar_c=0
		AND maths_astar_c=0
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
	}
	
	/**
	 * Returns pupils with 5 x A*-C exc English & Maths
	 */
	public function getExcEnglishMaths5AstarToCGroup()
	{
		
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		//$arg[0] = $this->model->groupArg[0];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.english_astar_c=0
		AND t1.maths_astar_c=0
		AND t1.astar_c>=5
		ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
	}
	
	/**
	 * Returns the number of pupils with 5xA*-C including only English
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getIncEnglishOnly5AstarToC($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		SUM(astar_c>=5) AS astar_c5
		FROM $t
		WHERE english_astar_c=1
		AND maths_astar_c=0
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
	}
	
	/**
	 * Returns pupils with 5 x A*-C inc English only (no maths)
	 */
	public function getIncEnglishOnly5AstarToCGroup()
	{
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.english_astar_c=1
		AND t1.maths_astar_c=0
		AND t1.astar_c>=5
		ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
	}
	
	/**
	 * Returns the number of pupils with 5xA*-C including only English
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getIncMathsOnly5AstarToC($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		SUM(astar_c>=5) AS astar_c5
		FROM $t
		WHERE english_astar_c=0
		AND maths_astar_c=1
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
	}
	
	/**
	 * Returns pupils with 5 x A*-C inc Maths only (no English)
	 */
	public function getIncMathsOnly5AstarToCGroup()
	{
		
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.english_astar_c=0
		AND t1.maths_astar_c=1
		AND t1.astar_c>=5
		ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
	}
	
	 /**
	 * Returns the number of pupils with an A*-C in English but not Maths who don't have 5 A*-Cs
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getEnglishOnlyNoMathsNot5AstarToC($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master']; 
		
		$sql="SELECT
		SUM(astar_c<5)
		FROM $t
		WHERE english_astar_c=1
		AND maths_astar_c=0
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
		
	}
	
	/**
	 * Returns pupil with 1 x A*-C in English only (no Maths) not 5 x A*-C
	 */
	public function getEnglishOnlyNoMathsNot5AstarToCGroup()
	{
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.english_astar_c=1
		AND t1.maths_astar_c=0
		AND t1.astar_c<5
		ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
		
	}
	
	 /**
	 * Returns the number of pupils with an A*-C in Maths but not Maths who don't have 5 A*-Cs
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getMathsOnlyNoEnglishNot5AstarToC($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		SUM(astar_c<5)
		FROM $t
		WHERE english_astar_c=0
		AND maths_astar_c=1
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
		
	}
	
	/**
	 * Returns pupils with 1 x A*-C in Maths only (no English) not 5 x A*-C
	 */
	public function getMathsOnlyNoEnglishNot5AstarToCGroup()
	{
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.english_astar_c=0
		AND t1.maths_astar_c=1
		AND t1.astar_c<5
		ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
	}
	
	 /**
	 * Returns the number of pupils with an A*-C in English and Maths who don't have 5 A*-Cs
	 * @param integer $fieldMappingId
	 * @param string $type The equivalence type/mode volume or equivalent
	 * @return string $type volume or equivalent
	 */
	public function getEnglishAndMathsNot5AstarToC($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT
		SUM(astar_c<5)
		FROM $t
		WHERE english_astar_c=1
		AND maths_astar_c=1
		AND fieldmapping_id=:fieldMappingId
		AND type=:type";
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		return (int)$command->queryScalar();
		
	}
	
	/**
	 * Returns pupils with 1 x A*-C in English & Maths not 5 x A*-C
	 */
	public function getEnglishAndMathsNot5AstarToCGroup()
	{
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.english_astar_c=1
		AND t1.maths_astar_c=1
		AND t1.astar_c<5
		ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
		
	}
	
	/**
	 * Returns an array of pupils with levels progress info
	 */
	public function getLevelsProgress($filter='')
	{	
		if(!$filter){
		if($this->_levelsProgress)
		return $this->_levelsProgress;
		}
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT fieldmapping_id,
				SUM(english_lp3) AS english_lp3,
				SUM(english_lp4) AS english_lp4,
				SUM(maths_lp3) AS maths_lp3,
				SUM(maths_lp4) AS maths_lp4
				FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)";
				if($filter)
				$sql.=" WHERE $filter";
				$sql.=" GROUP BY fieldmapping_id";

		$command=$connection->createCommand($sql);
		$rows= $command->queryAll();
		
		if($rows){
		return $this->_levelsProgress=$this->normaliseRows($rows);
		}
		else{
			return false;
		}
	}
	
	/**
	 * Returns pupils achieving either 3 or 4 levels progress in English and Maths
	 */
	public function getLevelsProgressGroup()
	{
		
		//If the 'Should achieve' button is clicked
		if($this->model->opId==2)
		return $this->getLevelsProgressGroupShouldAchievers();
		
		//Run levels progress dependencies
		$this->updateKs4MasterEnglishPointScore($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglishLevelsProgress();
		$this->updateKs4MasterMathsLevelsProgress();
		$filter = $this->model->groupArg[2];
		$criteria = $this->model->groupArg[0].'='.$this->model->groupAchiever;
		return $this->getGroupData($filter, $criteria, $this->model->groupFieldMappingId);
	}

	/**
	 * [getLevelsProgressGroupShouldAchievers description]
	 * @return [type] [description]
	 */
	public function getLevelsProgressGroupShouldAchievers()
	{
		$this->buildKs4Master($this->model->compareTo,$this->model->mode);
		//Run levels progress dependencies
		$this->updateKs4MasterEnglishPointScore($this->model->compare,$this->model->mode);
		$this->updateKs4MasterEnglishPointScore($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->compare,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterEnglishLevelsProgress();
		$this->updateKs4MasterMathsLevelsProgress();

		$filter = $this->model->groupArg[2];

		//Get the current DCP non achievers
		$criteria = $this->model->groupArg[0].'=0';
		$nonAchieversDcp = $this->getGroupData($filter, $criteria, $this->model->compare);

		//Get the current target achievers
		$criteria = $this->model->groupArg[0].'=1';
		$achieversTarget = $this->getGroupData($filter, $criteria, $this->model->compareTo);

		//Extract Pupil id from target achievers array
		$pupilIds = $this->extractPupilIds($achieversTarget);

		//Build a new non achievers array containing just the non achievers who should be achieving when
		//compare to the current target
		foreach($nonAchieversDcp as $key=>$value){
			if(!in_array($nonAchieversDcp[$key]['pupil_id'],$pupilIds))
				unset($nonAchieversDcp[$key]);
		}
		return $nonAchieversDcp;
	}

	
	/**
	 * This method returns a rows array from the database containing the fieldmapping_id with the fieldmapping_id
	 * as its key
	 * @param array $rows The rows returned from a DAO call to queryAll();
	 */
	public function normaliseRows($rows)
	{
		foreach($rows as $key=>$value){
			$array[$rows[$key]['fieldmapping_id']]=$rows[$key];
		}
		
		return $array;
		
	}
	
	/**
	 * Updates the english_score See updateKs4MasterMathsPointScore for full details. In addition the 
	 * method for English first calculates the point score for 'English'
	 * it then goes on to override this if a pupil's EngLang subject has a higher point score.
	 * @param integer $fieldMappingId The field mapping ID
	 * @param string $type The type either volume or equivalent
	 */
	public function updateKs4MasterEnglishPointScore($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$this->getEnglishPupils($fieldMappingId);
		
		if($this->_englishPupils[$fieldMappingId]){
		$inClause="'".implode("','",$this->_englishPupils[$fieldMappingId])."'";
		
		$sql="UPDATE $t AS t1,
		(SELECT ks4meta.pupil_id AS pupil_id,MAX(ks4meta.standardised_points) AS sp 
		FROM ks4meta INNER JOIN subjectmapping
		ON(ks4meta.subjectmapping_id = subjectmapping.id)
		WHERE subjectmapping.type='English'
		AND fieldmapping_id=:fieldMappingId
		AND ks4meta.pupil_id IN ($inClause)
		GROUP BY ks4meta.pupil_id) AS t2
		SET t1.english_score=t2.sp
		WHERE t1.pupil_id = t2.pupil_id
		AND t1.fieldmapping_id=:fieldMappingId
		AND t1.type=:type";
		
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		$command->execute();
		}
		
		$this->getEnglishLitLangPupils($fieldMappingId);
		
		if($this->_englishLitLangPupils[$fieldMappingId]){
		$inClause="'".implode("','",$this->_englishLitLangPupils[$fieldMappingId])."'";
		
		$sql="UPDATE $t AS t1,
		(SELECT ks4meta.pupil_id AS pupil_id,MAX(ks4meta.standardised_points) AS sp 
		FROM ks4meta INNER JOIN subjectmapping
		ON(ks4meta.subjectmapping_id = subjectmapping.id)
		WHERE subjectmapping.type='EngLang'
		AND fieldmapping_id=:fieldMappingId
		AND ks4meta.pupil_id IN ($inClause)
		GROUP BY ks4meta.pupil_id) AS t2
		SET t1.english_score=t2.sp
		WHERE t1.pupil_id = t2.pupil_id
		AND t1.fieldmapping_id=:fieldMappingId
		AND t1.type=:type
		AND t2.sp>t1.english_score";
		
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		$command->execute();
		}
	}
	
	/** 
	 * Updates columns to indicate whether or not each pupil has made 3 or 4 levels progress. The expected levels of progress
	 * (currently 3 2012) can be calcuated using a ks2 pointscore. If the difference between the KS2 PS and the
	 * KS4 PS is greater than or equal to 13 then they have achieved 3 levels progress. If greater or equal to
	 * 19 then they have achived 4 levels progress. In addition, given that a level 5 is the highest NCL 
	 * level at which a pupil can be assessed, all pupils attaining A*-B ie greater that 46 point score are
	 * deemed to have made 3 levels progress. We use 45 her to take into account a point score for D at AS level.
	 * Those above a point score of 52 have all made 4 levels progress. See equivalent Maths method as well
	 */
	public function updateKs4MasterEnglishLevelsProgress()
	{
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];

		/* Greater than 45 here to take into account a D at AS level. Also note that we cannot
		 * do the above 45 and above 52 override here because any pupils who's grades to not match
		 * the contents of the lookup.ks2pointscore table will not be included in the query and thus
		 * will not get the override when they need it.
		 */
		$sql="UPDATE $t AS t1 INNER JOIN (lookup.ks2pointscore AS t2, pupil)
		ON (t1.pupil_id = pupil.pupil_id
		AND pupil.ks2_english = t2.result)
		SET t1.english_lp3 = IF((t1.english_score-t2.score)>=13,1,0),
		t1.english_lp4 = IF((t1.english_score-t2.score)>=19,1,0)";
		
		$command=$connection->createCommand($sql);
		$command->execute();
		
		$sql="UPDATE $t AS t1 SET t1.english_lp3=IF(t1.english_score>=45,1,t1.english_lp3),
		t1.english_lp4 = IF(t1.english_score>=52,1,t1.english_lp4)";
		$command=$connection->createCommand($sql);
		$command->execute();
	}
	
	/**
	 * Updates the maths_score in the ks4master table. First it gets the maximum
	 * score a pupil has for a maths subject (this could come from an AS level).
	 * @param integer $fieldMappingId The field mapping ID
	 * @param string $type The type either volume or equivalent
	 */
	public function updateKs4MasterMathsPointScore($fieldMappingId,$type='volume')
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$this->getMathsPupils($fieldMappingId);
		
		if($this->_mathsPupils[$fieldMappingId]){
		$inClause="'".implode("','",$this->_mathsPupils[$fieldMappingId])."'";
		
		$sql="UPDATE $t AS t1,
		(SELECT ks4meta.pupil_id AS pupil_id,MAX(ks4meta.standardised_points) AS sp 
		FROM ks4meta INNER JOIN subjectmapping
		ON(ks4meta.subjectmapping_id = subjectmapping.id)
		WHERE subjectmapping.type='Maths'
		AND fieldmapping_id=:fieldMappingId
		AND ks4meta.pupil_id IN ($inClause)
		GROUP BY ks4meta.pupil_id) AS t2
		SET t1.maths_score=t2.sp
		WHERE t1.pupil_id = t2.pupil_id
		AND t1.fieldmapping_id=:fieldMappingId
		AND t1.type=:type";
		
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		$command->execute();
		}
	}
	
	/** 
	 * Updates columns to indicate whether or not each pupil has made 3 or 4 levels progress. The expected levels of progress
	 * (currently 3 2012) can be calcuated using a ks2 pointscore. If the difference between the KS2 PS and the
	 * KS4 PS is greater than or equal to 13 then they have achieved 3 levels progress. If greater or equal to
	 * 19 then they have achived 4 levels progress. In addition, given that a level 5 is the highest NCL 
	 * level at which a pupil can be assessed, all pupils attaining A*-B ie greater than 46 point score are
	 * deemed to have made 3 levels progress. We use 45 here to take into account a point score for D at AS level.
	 * We do this because a D is still worth 20% of the level 2 threshold.
	 * Those above a point score of 52 have all made 4 levels progress
	 */
	public function updateKs4MasterMathsLevelsProgress()
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		/* Greater than 45 here to take into account a D at AS level. Also note that we cannot
		 * do the above 45 and above 52 override here because any pupils who's grades to not match
		 * the contents of the lookup.ks2pointscore table will not be included in the query and thus
		 * will not get the override when they need it.
		 */
		$sql="UPDATE $t AS t1 INNER JOIN (lookup.ks2pointscore AS t2, pupil)
		ON (t1.pupil_id = pupil.pupil_id
		AND pupil.ks2_maths = t2.result)
		SET t1.maths_lp3 = IF((t1.maths_score-t2.score)>=13,1,0),
		t1.maths_lp4 = IF((t1.maths_score-t2.score)>=19,1,0)";
		
		$command=$connection->createCommand($sql);
		$command->execute();
		
		//Update for A*-B (3LP) and A*-A (4LP)
		$sql="UPDATE $t AS t1 SET t1.maths_lp3=IF(t1.maths_score>=45,1,t1.maths_lp3),
		t1.maths_lp4 = IF(t1.maths_score>=52,1,t1.maths_lp4)";
		$command=$connection->createCommand($sql);
		$command->execute();
		
	}

	/**
	 * Updates the KS4 master table with the vanilla KS2 levels. This is called by the KS4PupilGrid to add the actual KS2 levels for display
	 * It is NOT required or called for the summary whole school level stats
	 * @return void
	 */
	public function updateKs4MasterKs2Levels()
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];

		$sql="UPDATE $t INNER JOIN pupil USING(cohort_id,pupil_id) SET 
		ks2_english_level = ks2_english,
		ks2_maths_level = ks2_maths,
		ks2_science_level = ks2_science";
		$command=$connection->createCommand($sql);
		$command->execute();

	}

	/**
	 * Updates the attendance column in the KS4 master table so they can be called when groups are accessed.
	 * Note. This method is not a dependency for any summary reports.
	 * @return void
	 */
	public function updateKs4MasterAttendance()
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];

		$sql="UPDATE $t AS t1 LEFT JOIN attendance AS t2 USING(cohort_id,pupil_id) SET 
		percentage_present = 	ROUND((t2.present_marks + t2.approved_ed_activity)/t2.possible_marks*100,1),
		percentage_unauthorised_absences = ROUND(t2.unauthorised_absences/t2.possible_marks*100,1),
		lates = t2.late_both";
		$command=$connection->createCommand($sql);
		$command->execute();

	}

	
	/**
	 * Returns the number of low, middle and high attaining groups
	 */
	public function getAttainers()
	{	
		if($this->_attainers)
		return $this->_attainers;
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT fieldmapping_id, 
		SUM(ks2_attainer=0) AS non,
		SUM(ks2_attainer=1) AS low,
		SUM(ks2_attainer=2) AS middle,
		SUM(ks2_attainer=3) AS high
		FROM $t
		GROUP BY fieldmapping_id";
		$command=$connection->createCommand($sql);
		$rows=$command->queryAll();
	
		if($rows){
		foreach($rows as $key=>$value){
			$array[$rows[$key]['fieldmapping_id']]=$rows[$key];
		}
		return $this->_attainers=$array;
		}
		else{
			return false;
		}
	}
	
	/**
	 * [getAttainersGroup description]
	 * @return [type] [description]
	 */
	public function getAttainersGroup()
	{
		//Run levels progress dependencies
		$this->updateKs4MasterEnglishPointScore($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->groupFieldMappingId,$this->model->mode);
	
		//Run attainers dependencies.
		$this->updateKs4MasterKs2AveragePointScore();
		$this->updateKs4MasterKs2Attainers();
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$arg[0] = $this->model->groupArg[0];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE ks2_attainer=$arg[0]
		ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
	}

	
	/**
	 * Returns an array of pupil's English and Maths levels of progress keyed as fieldmapping_id and ks2 attainer
	 * Note attainers here are 1=low, 2=middle, 3=high
	 * @return mixed
	 */
	public function getAttainersLevelsProgress()
	{
		if($this->_attainersLevelsProgress)
		return $this->_attainersLevelsProgress;
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT fieldmapping_id, ks2_attainer, SUM(english_lp3) AS english_lp3, 
		SUM(english_lp4) AS english_lp4,
		SUM(maths_lp3) AS maths_lp3, 
		SUM(maths_lp4) AS maths_lp4
		FROM $t
		GROUP BY fieldmapping_id, ks2_attainer";
		$command=$connection->createCommand($sql);
		$rows=$command->queryAll();
	
		if($rows){
		foreach($rows as $key=>$value){
			$array[$rows[$key]['fieldmapping_id']][$rows[$key]['ks2_attainer']]=$rows[$key];
		}
		return $this->_attainersLevelsProgress=$array;
		}
		else{
			return false;
		}
	}
	
	/**
	 * 
	 */
	public function getAttainersLevelsProgressGroup()
	{
		//Run levels progress dependencies
		$this->updateKs4MasterEnglishPointScore($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglishLevelsProgress();
		$this->updateKs4MasterMathsLevelsProgress();
		
		//Run attainers dependencies.
		$this->updateKs4MasterKs2AveragePointScore();
		$this->updateKs4MasterKs2Attainers();

		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$arg[0] = $this->model->groupArg[0];
		$arg[1] = $this->model->groupArg[1];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE $arg[0]=".$this->model->groupAchiever."
		AND ks2_attainer=$arg[1]
		ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
		
	}
	
	/**
	 * Updates the ks4master table with the ks2 point score equivalent of the ks2 level
	 */
	public function updateKs4MasterKs2AveragePointScore()
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		//UPDATE English
		$sql="UPDATE $t AS t1 INNER JOIN (pupil AS t2, lookup.ks2pointscore AS t3)
		ON( t1.cohort_id = t2.cohort_id
		AND t1.pupil_id = t2.pupil_id
		AND t2.ks2_english = t3.result)
		SET t1.ks2_english_ps = t3.score
		";
		$command=$connection->createCommand($sql);
		$command->execute();
		
		//UPDATE Maths
		$sql="UPDATE $t AS t1 INNER JOIN (pupil AS t2, lookup.ks2pointscore AS t3)
		ON( t1.cohort_id = t2.cohort_id
		AND t1.pupil_id = t2.pupil_id
		AND t2.ks2_maths = t3.result)
		SET t1.ks2_maths_ps = t3.score
		";
		$command=$connection->createCommand($sql);
		$command->execute();
		
		//UPDATE Science
		$sql="UPDATE $t AS t1 INNER JOIN (pupil AS t2, lookup.ks2pointscore AS t3)
		ON(t1.cohort_id = t2.cohort_id
		AND t1.pupil_id = t2.pupil_id
		AND t2.ks2_science = t3.result)
		SET t1.ks2_science_ps = t3.score
		";
		$command=$connection->createCommand($sql);
		$command->execute();
		
	}
	
	/**
	 * Updates the ks4master table to indicate who is a 1=low, 2=middle and 3=high attainer
	 */
	public function updateKs4MasterKs2Attainers()
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="UPDATE $t
		SET ks2_average=(ks2_english_ps+ks2_maths_ps+ks2_science_ps)/3";
		$command=$connection->createCommand($sql);
		$command->execute();
		
		$sql="UPDATE $t
		SET ks2_attainer = IF((ks2_english_ps+ks2_maths_ps+ks2_science_ps)/3 BETWEEN 0.1 AND 23.99,1,
		IF( (ks2_english_ps+ks2_maths_ps+ks2_science_ps)/3 BETWEEN 24 AND 29.99,2,
		IF( (ks2_english_ps+ks2_maths_ps+ks2_science_ps)/3 >=30,3,0)))
		";
		$command=$connection->createCommand($sql);
		$command->execute(); 
	}

	/**
	 * Updates the ks4master table to indicate English attainers - 1=low, 2=middle and 3=high attainer
	 * relies on updateKs4MasterKs2AveragePointScore() as a dependancy
	 */
	public function updateKs4MasterKs2EnglishAttainers()
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
				
		$sql="UPDATE $t
		SET ks2_english_attainer = IF((ks2_english_ps) BETWEEN 0.1 AND 23.99,1,
		IF( (ks2_english_ps) BETWEEN 24 AND 29.99,2,
		IF( (ks2_english_ps) >=30,3,0)))
		";
		$command=$connection->createCommand($sql);
		$command->execute(); 
	}

	/**
	 * Updates the ks4master table to indicate Maths attainers - 1=low, 2=middle and 3=high attainer
	 * relies on updateKs4MasterKs2AveragePointScore() as a dependancy
	 */
	public function updateKs4MasterKs2MathsAttainers()
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
				
		$sql="UPDATE $t
		SET ks2_maths_attainer = IF((ks2_maths_ps) BETWEEN 0.1 AND 23.99,1,
		IF( (ks2_maths_ps) BETWEEN 24 AND 29.99,2,
		IF( (ks2_maths_ps) >=30,3,0)))
		";
		$command=$connection->createCommand($sql);
		$command->execute(); 
	}
	
	/**
	 * Returns an array of No A*-C type results
	 * @param integer $fieldMappingId The field mapping Id
	 * @return array
	 */
	public function getAstarToC($filter='')
	{
		if($this->_aStarToC)
		return $this->_aStarToC;
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT fieldmapping_id,
		SUM(astar_a>=1) AS astar_a1,
		SUM(astar_c>=1) AS astar_c1,
		SUM(astar_g>=1) AS astar_g1,
		SUM(astar_a>=3) AS astar_a3,
		SUM(astar_c>=3) AS astar_c3,
		SUM(astar_g>=3) AS astar_g3,
		SUM(astar_a>=5) AS astar_a5,
		SUM(astar_c>=5) AS astar_c5,
		SUM(astar_g>=5) AS astar_g5,
		SUM(astar_a>=8) AS astar_a8,
		SUM(astar_c>=8) AS astar_c8,
		SUM(astar_g>=8) AS astar_g8
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)";
		if($filter)
		$sql.=" WHERE $filter";
		$sql.=" GROUP BY fieldmapping_id";

		$command=$connection->createCommand($sql);
		$rows= $command->queryAll();

		if($rows){
		return $this->_aStarToC = $this->normaliseRows($rows);
		}
		else{
			return false;
		}
	}
	
	/**
	 * Returns an array of pupil details for
	 */
	
	/*
	public function getAstarToCGroup()
	{
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$arg[0]=$this->model->groupArg[0];
		$arg[1]=$this->model->groupArg[1];
		
		$op = ($this->model->groupAchiever) ? '>=' : '<';
		
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.$arg[0] $op $arg[1]
		ORDER BY surname, forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
	}*/

	/**
	 * Returns an array for pupils in A*-C ranges
	 * @return array
	 */
	public function getAstarToCGroup()
	{
		//If the 'Should achieve' button is clicked
		if($this->model->opId==2)
		return $this->getAstarToCGroupShouldAchievers();
		
	
		//The filter e.g. 'gender IN ('m','male') or false if not to be applied
		$op = ($this->model->groupAchiever) ? '>=' : '<';
		$criteria = $this->model->groupArg[0].' '.$op.' '.$this->model->groupArg[1];
		$filter = $this->model->groupArg[2];
		return $this->getGroupData($filter, $criteria, $this->model->groupFieldMappingId);
		
	}

	/**
	 * Returns an array of pupils who are currently not achieving, but who should
	 * be achieving according to their target
	 * @return array
	 */
	public function getAstarToCGroupShouldAchievers()
	{
		//Populate temp table with target data
		$this->buildKs4Master($this->model->compareTo,$this->model->mode);
		$filter = $this->model->groupArg[2];

		//Get the current DCP non achievers
		$op = '<';
		$criteria = $this->model->groupArg[0].' '.$op.' '.$this->model->groupArg[1];
		$nonAchieversDcp = $this->getGroupData($filter, $criteria, $this->model->compare);

		//Get the current target achievers
		$op = '>=';
		$criteria = $this->model->groupArg[0].' '.$op.' '.$this->model->groupArg[1];
		$achieversTarget = $this->getGroupData($filter, $criteria, $this->model->compareTo);

		//Extract Pupil id from target achievers array
		$pupilIds = $this->extractPupilIds($achieversTarget);

		//Build a new non achievers array containing just the non achievers who should be achieving when
		//compare to the current target
		foreach($nonAchieversDcp as $key=>$value){
			if(!in_array($nonAchieversDcp[$key]['pupil_id'],$pupilIds))
				unset($nonAchieversDcp[$key]);
		}
		return $nonAchieversDcp;
	}

	/**
	 * Returns an array of pupil_ids extracted from the array $array
	 * @param array $array The array to extract from
	 * @return array
	 */
	public function extractPupilIds($array)
	{
		foreach($array as $key=>$value){
			$pupilIds[] = $array[$key]['pupil_id'];
		}

		return ($pupilIds) ? $pupilIds : array();
	}


	
	/**
	 * Returns an array containing low, middle and high attainers with 5 x A*-C in English and Maths
	 */
	public function getAttainers5AstarToCIncEnglishMaths()
	{
		if($this->_attainers5AstarToCIncEnglishMaths)
		return $this->_attainers5AstarToCIncEnglishMaths;
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT fieldmapping_id,ks2_attainer,
		SUM(astar_c>=5) AS astar_c5
		FROM $t
		WHERE english_astar_c=1
		AND maths_astar_c=1
		GROUP BY fieldmapping_id, ks2_attainer";
		$command=$connection->createCommand($sql);
		$rows=$command->queryAll();
		
		if($rows){
		foreach($rows as $key=>$value){
			$array[$rows[$key]['fieldmapping_id']][$rows[$key]['ks2_attainer']]=$rows[$key];
		}
		return $this->_attainers5AstarToCIncEnglishMaths=$array;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Returns low(1), middle(2) or high(3) attaining pupils with 5 x A*-C including English and Maths
	 */
	public function getAttainers5AstarToCIncEnglishMathsGroup()
	{
		//Update English & Maths 
		$this->updateKs4MasterMaths($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->groupFieldMappingId,$this->model->mode);
		//Update point scores
		$this->updateKs4MasterEnglishPointScore($this->model->groupFieldMappingId,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->groupFieldMappingId,$this->model->mode);
		//Update point scores
		$this->updateKs4MasterEnglishLevelsProgress();
		$this->updateKs4MasterMathsLevelsProgress();
		$this->updateKs4MasterKs2AveragePointScore();
		//Update attainers
		$this->updateKs4MasterKs2Attainers();
		
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$arg[0] = $this->model->groupArg[0];
	
		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE t1.ks2_attainer = $arg[0]
		AND t1.english_astar_c=1
		AND t1.maths_astar_c=1
		AND t1.astar_c>=5
		ORDER BY surname, forename";
		
		$command=$connection->createCommand($sql);
		return $command->queryAll();
		
	}
	
	
	
	/**
	 * Builds the ks4master data for a specific field mapping Id
	 * QUERY OPTIMISED 14/06/2012
	 * The decision to to build a temp ks4meta table came after discounting was introduced. By creating a temporary one we are able to control
	 * which subjects are included in the A* to C count.
	 * @param integer $fieldMappingId
	 * @param string $type The multiplier type/mode either volume or equivalent
	 * @return void
	 */
	public function buildKs4Master($fieldMappingId,$type="volume")
	{
		if($this->_ks4MasterTempTableBuilt===null)
		$this->createKs4MasterTempTable();
		
		if($this->_ks4MasterBuilt[$fieldMappingId][$type])
		return $this->_ks4MasterBuilt[$fieldMappingId][$type];

		//Handle temporary ks4meta table
		if($this->_ks4MetaTempTableBuilt===null)
		$this->createKs4MetaTempTable();
		$this->buildKs4Meta($fieldMappingId);
		if($type=='equivalent')
		$this->discountKs4meta($fieldMappingId);
		
		$connection  = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		$ks4meta = $connection->tmpTable['ks4meta'];
		
		$sql="
		INSERT INTO $t (cohort_id,pupil_id,fieldmapping_id,astar_a,astar_c,astar_g,type)
		SELECT '{$this->model->cohortId}', $ks4meta.pupil_id, fieldmapping_id,
		SUM(astar_a*subjectmapping.$type) AS astar_a,
		SUM(astar_c*subjectmapping.$type) AS astar_c,
		SUM(astar_g*subjectmapping.$type) AS astar_g,
		:type
		FROM $ks4meta INNER JOIN (subjectmapping,setdata )
		ON ($ks4meta.cohort_id = setdata.cohort_id
		AND $ks4meta.pupil_id = setdata.pupil_id 
		AND $ks4meta.subjectmapping_id=subjectmapping.id 
		AND setdata.mapped_subject = subjectmapping.mapped_subject
		)
		WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = $ks4meta.subjectmapping_id AND t.pupil_id=$ks4meta.pupil_id)
		AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = $ks4meta.subjectmapping_id AND t.set_code=setdata.set_code) 
		AND subjectmapping.include='1'
		AND $ks4meta.fieldmapping_id=:fieldMappingId
		AND $ks4meta.include='1'";

		if($this->filteredPupilsInClause)
		$sql.=" AND $ks4meta.pupil_id IN ($this->filteredPupilsInClause)";
		$sql.=" GROUP BY $ks4meta.pupil_id";
		
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		$command->execute();
		
		$this->_ks4MasterBuilt[$fieldMappingId][$type]=true;

	}

	/**
	 * Creates the temporary KS4 meta table
	 * @return void
	 */
	public function createKs4MetaTempTable()
	{
		$connection = Yii::app()->db;
		$t = $connection->setTmpTableName('ks4meta');

		$sql="CREATE TABLE $t (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`cohort_id` varchar(10) DEFAULT NULL,
			`subjectmapping_id` int(11) DEFAULT NULL,
			`pupil_id` varchar(20) DEFAULT NULL,
			`fieldmapping_id` int(11) DEFAULT NULL,
			`astar_a` decimal(2,1) DEFAULT NULL,
			`astar_c` decimal(2,1) DEFAULT NULL,
			`astar_g` decimal(2,1) DEFAULT NULL,
			`result` varchar(50) DEFAULT NULL,
			`standardised_points` decimal(11,2) DEFAULT NULL,
			`include` tinyint(1) DEFAULT '1',
			PRIMARY KEY (`id`),
			KEY `idx_field_pupil` (`fieldmapping_id`,`pupil_id`),
			KEY `idx_cohort_pupil` (`cohort_id`,`pupil_id`),
			KEY `idx_cohort_result_fieldmapping_id` (`cohort_id`,`result`,`fieldmapping_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";

		$command=$connection->createCommand($sql);
		$command->execute();

		$this->_ks4MetaTempTableBuilt=true;
	}

	/**
	 * Builds the temporary ks4meat table
	 * @param  string $fieldMappingId The fieldmapping id
	 * @return void
	 */
	public function buildKs4Meta($fieldMappingId)
	{
		if($this->_ks4MetaTempTableBuilt===null)
		$this->createKs4MetaTempTable();
		
		$connection  = Yii::app()->db;
		$t=$connection->tmpTable['ks4meta'];

		$sql="INSERT INTO $t (id, cohort_id, subjectmapping_id,pupil_id,fieldmapping_id,astar_a,astar_c,astar_g,result,standardised_points) 
		SELECT id, cohort_id, subjectmapping_id,pupil_id,fieldmapping_id,astar_a,astar_c,astar_g,result,standardised_points
		FROM ks4meta WHERE ks4meta.fieldmapping_id=:fieldMappingId";

		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->execute();

	}

	/**
	 * The queries in this method first rest the include column to 0. We then apply discounting the entire table. A simple version of this query is documented 
	 * here: http://stackoverflow.com/questions/16719989/mysql-return-rows-that-have-no-associated-records-in-another-table-plus-max-th
	 * @param  string $fieldMappinId The fieldmapping id
	 * @return void
	 */
	public function discountKs4meta($fieldMappingId)
	{
		$connection  = Yii::app()->db;
		$t=$connection->tmpTable['ks4meta'];

		$sql="UPDATE $t SET include='0' AND $t.fieldmapping_id = :fieldMappingId";
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->execute();

		$sql="UPDATE $t INNER JOIN(

		SELECT t1.pupil_id, t1.fieldmapping_id, t1.subjectmapping_id, t1.standardised_points, t2.discount_code FROM $t AS t1 
		INNER JOIN subjectmapping AS t2 ON t2.id = t1.subjectmapping_id
		INNER JOIN (

			SELECT t1.pupil_id, t1.fieldmapping_id, t1.subjectmapping_id, t2.subject, MAX(t1.standardised_points) AS points, t2.discount_code
					FROM $t AS t1 INNER JOIN subjectmapping AS t2 ON t2.id = t1.subjectmapping_id
					WHERE t1.fieldmapping_id=:fieldMappingId
					AND t2.discount_code!=''
					GROUP BY t1.pupil_id,t2.discount_code

			) AS derived 
					ON t1.pupil_id = derived.pupil_id 
					AND t2.discount_code = derived.discount_code 
					AND t1.standardised_points = derived.points
					WHERE t1.fieldmapping_id=:fieldMappingId
					GROUP BY t1.pupil_id, t2.discount_code

			UNION ALL
			SELECT t1.pupil_id, t1.fieldmapping_id, t1.subjectmapping_id, t1.standardised_points, t2.discount_code
					FROM $t AS t1 INNER JOIN subjectmapping AS t2 ON t2.id = t1.subjectmapping_id
					WHERE t1.fieldmapping_id=:fieldMappingId
					AND t2.discount_code=''

			) AS derived2

				ON $t.pupil_id = derived2.pupil_id
				AND $t.fieldmapping_id = derived2.fieldmapping_id
				AND $t.subjectmapping_id = derived2.subjectmapping_id
				SET $t.include='1'";

		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		$command->execute();

	}

	
	
	/**
	 * Creates a temporary table to store the A*-C results
	 * @return void
	 */
	public function createKs4MasterTempTable()
	{
		$connection = Yii::app()->db;
		$t = $connection->setTmpTableName('ks4master');
		
		$sql="CREATE TABLE $t (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `cohort_id` varchar(20) DEFAULT NULL,
		  `pupil_id` varchar(20) DEFAULT NULL,
		  `fieldmapping_id` int(11) DEFAULT NULL,
		  `astar_a` tinyint(3) DEFAULT NULL,
		  `astar_c` tinyint(3) DEFAULT NULL,
		  `astar_g` tinyint(3) DEFAULT NULL,
		  `english_astar_a` tinyint(1) DEFAULT '0',
		  `english_astar_c` tinyint(1) DEFAULT '0',
		  `english_astar_g` tinyint(1) DEFAULT '0',
		  `maths_astar_a` tinyint(1) DEFAULT '0',
		  `maths_astar_c` tinyint(1) DEFAULT '0',
		  `maths_astar_g` tinyint(1) DEFAULT '0',
		  `science_astar_a` tinyint(1) DEFAULT '0',
		  `science_astar_c` tinyint(1) DEFAULT '0',
		  `science_astar_g` tinyint(1) DEFAULT '0',
		  `science_astar_c_ebacc` tinyint(1) DEFAULT '0',
		  `lang_astar_c` tinyint(1) DEFAULT '0',
		  `humanity_astar_c` tinyint(1) DEFAULT '0',
		  `english_score` decimal(4,2) DEFAULT '0',
		  `ks2_english_level` char(1) DEFAULT NULL,
	  	  `ks2_maths_level` char(1) DEFAULT NULL,
	  	  `ks2_science_level` char(1) DEFAULT NULL,
		  `english_lp3` tinyint(1) DEFAULT '0',
		  `english_lp4` tinyint(1) DEFAULT '0',
	  	  `maths_score` decimal(4,2) DEFAULT '0',
	  	  `maths_lp3` tinyint(1) DEFAULT '0',
	  	  `maths_lp4` tinyint(1) DEFAULT '0',
	  	  `ks2_english_ps` tinyint(2) DEFAULT '0',
	  	  `ks2_maths_ps` tinyint(2) DEFAULT '0',
	  	  `ks2_science_ps` tinyint(2) DEFAULT '0',
	  	  `ks2_average` decimal(4,2) DEFAULT '0',
	  	  `ks2_attainer` tinyint(1) DEFAULT '0',
	  	  `ks2_english_attainer` tinyint(1) DEFAULT '0',
	  	  `ks2_maths_attainer` tinyint(1) DEFAULT '0',
	  	  `percentage_present` decimal(4,1) DEFAULT '0',
	  	  `percentage_unauthorised_absences` decimal(4,1) DEFAULT '0',
	  	  `lates` int(3) DEFAULT NULL,
		  `type` varchar(20) DEFAULT NULL,
		  PRIMARY KEY (`id`),
  		  KEY `idx_fieldmapping_type` (`fieldmapping_id`,`type`),
  		  KEY `idx_pupil_fieldmapping_type` (`pupil_id`,`fieldmapping_id`,`type`),
  		  KEY `idx_fieldmapping_ks2_attainer` (`fieldmapping_id`,`ks2_attainer`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		
		$command=$connection->createCommand($sql);
		$command->execute();

		$this->_ks4MasterTempTableBuilt=true;
		
	}
	
	/**
	 * Returns a percentage of the cohort total to 2 decimal places
	 * @param integer $num Commonly the number of pupils
	 */
	public function getPercentage($num)
	{
		if(!$this->cohortTotal)
			return 0;
		
		$percentage =  number_format(($num/$this->cohortTotal)*100,2);
		return ($percentage==100.00) ? 100 : $percentage;
	}
	
	/**
	 * The opposite of getPercentage
	 * @param  float $percentage The percentage
	 * @return 	int
	 */
	public function getNumber($percentage)
	{
		return number_format(($percentage*$this->cohortTotal)/100);
	}

	/**
	 * Returns the percentage for a custom calculation i.e. not the cohort total
	 * @param  int $num The number to convert to a percentage
	 * @param  int $total The number to divide by
	 * @return float
	 */
	public function getCustomPercentage($num,$total)
	{
		//Guard against division by 0. Simply return 0
		if($total==0){
		return number_format(0,2);
		}
		else{
		return number_format(($num/$total)*100,2);
		}
	}
	
	/**
	 * This method normalises the model attribites that are used for caching
	 * It removes any attributes that do not uniquely describe the results being viewed.
	 * For example activeTab is need to control what is viewed, but does not effect that data
	 * returned and thus does not need caching.
	 * @return array
	 */
	protected function getAttributesForCaching()
	{
		if($this->_attributesForCaching!==null)
		return $this->_attributesForCaching;
		
		$this->_attributesForCaching = $this->model->attributes;
		unset($this->_attributesForCaching['oldYearGroup']);
		unset($this->_attributesForCaching['oldCohortId']);
		unset($this->_attributesForCaching['activeTab']);

		return $this->_attributesForCaching;
	}

	/*
	 * Renders the CViewGrid surname column in _groupGrid
	 */
	public function renderSurnameColumn($data,$row)
	{
	
		return CHtml::link($data['surname'],'#',array('class'=>'pupil',
			'id'=>$data['pupil_id'],
			'data-pupilid'=>$data['pupil_id'],
			'data-compare'=>$this->model->compare,
			'data-compareto'=>$this->model->compareTo,
			'data-cohortid'=>$this->model->cohortId,
			'data-oldcohortid'=>$this->model->oldCohortId,
			'data-mode'=>$this->model->mode,
			'data-yeargroup'=>$this->model->yearGroup,
			'data-oldyeargroup'=>$this->model->oldYearGroup,
			'data-surname'=>$data['surname'],
			'data-forename'=>$data['forename'],
			'data-form'=>$data['form'],
			'data-dob'=>$data['dob'],
			));
	}

	/**
	 * Returns a drop down button link
	 * @param string $num The number to display on the button
	 * @param array $params The query parameters
	 */
	public function getButton($num,$params,$groupAchiever=1,$size='')
	{
		//Guard against $num being false as retuned by some queries
		$num = (!$num) ? 0 : $num;

		return Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
		'label'=>$num,
		'type'=>'', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
		'size'=>$size, // '', 'large', 'small' or 'mini
		'url'=>$this->getQueryString($params,$groupAchiever),
		'htmlOptions'=>array('class'=>'group-link'),
		),true); 
	}

	/**
	 * Returns a drop down button link
	 * @param string $num The number to display on the button
	 * @param array $params The query parameters
	 * @param string $size The size of the button normal, small, large, mini
	 * @param int $noOptions The number of options to display in the dropdown list
	 * @return string
	 */
	public function getDropDownButton($num,$params,$noOptions=2,$size='')
	{
		//Guard against $num being false as retuned by some queries
		$num = (!$num) ? 0 : $num;

		$items[] = 
	                array(
	                'label'=>'Achievers', 
	                'url'=>$this->getQueryString($params,1,0), 
	                'linkOptions'=>array('class'=>'group-link'));
	    $items[] = 
	                array(
	                'label'=>'Non achievers', 
	                'url'=>$this->getQueryString($params,0,1),
	                'linkOptions'=>array('class'=>'group-link'));
            

		if($noOptions==3){
		$items[] =  array(
	                'label'=>'Should achievers', 
	                'url'=>$this->getQueryString($params,0,2),
	                'linkOptions'=>array('class'=>'group-link'));
		}

		return Yii::app()->controller->widget('bootstrap.widgets.TbButtonGroup', array(
        'type'=>'', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'size'=>$size,
        'buttons'=>array(
            array('label'=>$num, 'items'=>$items
            ),
        ),
    ),true);
	}

	/**
	 * A generic method to query the database based upon a specific filter and criteria
	 * @param string $filter The filter e.g. SEN pupils, boys etc...
	 * @param string $criteria The for the data e.g. astar_c=>5
	 * @param int $fieldMappingId The field mapping id
	 * @return array
	 */
	public function getGroupData($filter='', $criteria='', $fieldMappingId)
	{
		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];

		$sql="SELECT 
		pupil_id,
		surname, 
		forename,
		year, 
		form,
		dob,
		percentage_present,
		percentage_unauthorised_absences,
		lates
		FROM pupil INNER JOIN $t AS t1 USING(cohort_id,pupil_id)
		WHERE fieldmapping_id = :fieldMappingId";
		if($criteria)
		$sql.=" AND $criteria ";
		if($filter)
		$sql.=" AND $filter";
		$sql.=" ORDER BY pupil.surname, pupil.forename";
		
		$command=$connection->createCommand($sql);
		$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
		//echo $command->text;
		return $command->queryAll(); //Empty array returned if no rows found

	}
	
	
	/**
	 * Returns a parsed query string containing all the $_GET params necessary to create the request
	 * @param array $params An array of parameters
	 * @param int $groupAchiever Whether to get data for 1=achievers or 0=non achievers
	 * @param int $opid The id of the option selected in a drop down button. Always 0 for static button
	 * @return string
	 */
	public function getQueryString($params,$groupAchiever=1,$opid=0)
	{
		$paramsString = implode("|",$params);
		$className = get_class($this->model);
		$queryString = sprintf("%s%s=%s",$className,"[params]",$paramsString);
		$queryString .= sprintf("&%s%s=%s",$className,"[groupAchiever]",$groupAchiever);
		$queryString .= sprintf("&%s%s=%s",$className,"[opId]",$opid);
		
		if($this->qs){
		$queryString .='&'.$this->qs;
		}
		$url = Yii::app()->controller->createUrl('group');
		return $url.'?'.$queryString;
	}

	
	public function getPercentagePresentCss($row,$data){
		if($data['percentage_present']<85)
			return "red";
	}


}