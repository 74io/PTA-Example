<?php
class PtKs4Pupil extends PtKs4{

	protected $_noSubjects;

	protected $_dcpAps;
	protected $_targetAps;

	protected $_dcpTotal;
	protected $_targetTotal;

	protected $_ks4Master;
	protected $_entries;
	protected $_ks2SubjectsGrid;

	/**
	 * [getResults description]
	 * @return [type] [description]
	 */
	public function getResults()
	{
			$sql="SELECT 
			t2.pupil_id,
			t2.surname,
			t2.forename,
			t1.subjectmapping_id,
			t4.subject,
			t4.qualification,
			t4.discount_code,
			t4.type,
			t1.result AS dcp_result,
			t1.standardised_points AS dcp_standardised_points,
			t3.set_code,
			t4.".$this->model->mode.",
			ROUND((t5.present_marks + t5.approved_ed_activity)/t5.possible_marks*100,1) AS percentage_present,
			ROUND(t5.unauthorised_absences/t5.possible_marks*100,1) AS percentage_unauthorised_absences,
			t5.late_both AS lates,
			CONCAT(t6.title, ' ',t6.forename, ' ', t6.surname) AS teacher
			FROM ks4meta AS t1 
			INNER JOIN pupil AS t2 USING(cohort_id,pupil_id)
			INNER JOIN setdata AS t3 USING(cohort_id, pupil_id)
			INNER JOIN subjectmapping AS t4 ON(t3.mapped_subject = t4.mapped_subject AND t1.subjectmapping_id=t4.id)
			LEFT JOIN attendance t5 ON(t1.cohort_id = t5.cohort_id AND t1.pupil_id = t5.pupil_id)
			LEFT JOIN teacher AS t6 ON(t3.cohort_id=t6.cohort_id AND t3.teacher_id=t6.teacher_id)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.pupil_id=t1.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.set_code=t3.set_code) 
			AND t1.cohort_id=:cohortId
			AND t1.pupil_id=:pupilId
			AND t1.fieldmapping_id=:fieldMappingId
			AND t4.include=1
			ORDER BY t4.subject";

			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
			$command->bindParam(":fieldMappingId",$this->model->compare,PDO::PARAM_INT);
			$command->bindParam(":pupilId",$this->model->pupilId,PDO::PARAM_STR);
			$compareRows= $command->queryAll();//Returns empty array if no records are found

			//Fetch the target results
			$sql="SELECT 
			t1.pupil_id,
			t1.subjectmapping_id,
			t1.result,
			t1.standardised_points
			FROM ks4meta AS t1 
			WHERE t1.cohort_id=:cohortId
			AND t1.pupil_id=:pupilId
			AND t1.fieldmapping_id=:fieldMappingId";
				
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
			$command->bindParam(":fieldMappingId",$this->model->compareTo,PDO::PARAM_INT);
			$command->bindParam(":pupilId",$this->model->pupilId,PDO::PARAM_STR);
			$compareToRows = $command->queryAll();

			//Convert array to use fieldmapping id as key
			foreach($compareToRows as $key=>$value){
				$compareToData[$value['subjectmapping_id']]=$value;
			}

			//Insert target(compareTo result and subject average into original(compareRows) array)
			$subjectAverageAps = $this->subjectAps;
			foreach($compareRows as $key=>$value)
			{
				$compareRows[$key]['target_result']=$compareToData[$value['subjectmapping_id']]['result'];
				$compareRows[$key]['target_standardised_points']=$compareToData[$value['subjectmapping_id']]['standardised_points'];
				$compareRows[$key]['subject_aps']=$subjectAverageAps[$value['subjectmapping_id']]['subject_aps'];
			}

			return $compareRows;
	}

	/**
	 * Returns an array containing ks4 Master rows for both dcp and target
	 * @param  integer $fieldMappingId The field mapping id
	 * @param  string $mode (Optional) The mode - volume or equivalent
	 * @return array
	 */
	public function getKs4Master()
	{
		if($this->_ks4Master!==null)
		return $this->_ks4Master;

		//Run ks2 and attainer dependencies
		$this->updateKs4MasterKs2AveragePointScore();
		$this->updateKs4MasterKs2Attainers();

		//Run Ebacc dependancies
		$this->updateKs4MasterMaths($this->model->compare,$this->model->mode);
		$this->updateKs4MasterEnglish($this->model->compare,$this->model->mode);
		$this->updateKs4MasterScienceEbacc($this->model->compare,$this->model->mode);
		$this->updateKs4MasterHumanity($this->model->compare,$this->model->mode);
		$this->updateKs4MasterLang($this->model->compare,$this->model->mode);

		//Run levels progress dependencies
		$this->updateKs4MasterEnglishPointScore($this->model->compare,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->compare,$this->model->mode);

		$this->updateKs4MasterEnglishPointScore($this->model->compareTo,$this->model->mode);
		$this->updateKs4MasterMathsPointScore($this->model->compareTo,$this->model->mode);

		$this->updateKs4MasterEnglishLevelsProgress();
		$this->updateKs4MasterMathsLevelsProgress();

		$connection = Yii::app()->db;
		$t=$connection->tmpTable['ks4master'];
		
		$sql="SELECT * FROM $t ";
		$command=$connection->createCommand($sql);
		$rows = $command->queryAll();

		//Convert array to use fieldmapping_id as key
		foreach($rows as $key=>$value){
			$array[$value['fieldmapping_id']]=$value;
		}
		return $this->_ks4Master = $array;
	}

	/**
	 * Returns an array of subject APS's for the current DCP
	 * @return array
	 */
	public function getSubjectAps()
	{
			$sql="SELECT 
			t1.subjectmapping_id,
			t1.fieldmapping_id,
			t4.subject,
			t4.qualification,
			SUM(t1.standardised_points)/COUNT(*) AS subject_aps

			FROM ks4meta AS t1 
			INNER JOIN pupil AS t2 USING(cohort_id,pupil_id)
			INNER JOIN setdata AS t3 USING(cohort_id, pupil_id)
			INNER JOIN subjectmapping AS t4 ON(t3.mapped_subject = t4.mapped_subject AND t1.subjectmapping_id=t4.id)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.pupil_id=t1.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.set_code=t3.set_code) 
			AND t1.cohort_id=:cohortId
			AND t1.fieldmapping_id=:fieldMappingId
			AND t4.include=1
			GROUP BY t4.subject";
						
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
			$command->bindParam(":fieldMappingId",$this->model->compare,PDO::PARAM_INT);
			$rows= $command->queryAll();//Returns empty array if no records are found

			//Convert array to use subjectmapping_id as key
			foreach($rows as $key=>$value){
			$array[$value['subjectmapping_id']]=$value;
			}
			return $array;
	}

	/**
	 * Returns an array of subject aps's for every subject for every DCP/Target
	 * @param string $type The type DCP or Target
	 * @param string $mode The mode equivalent or volume
	 * @return array
	 */
	public function getAllSubjectPointScores($type='dcp',$mode='volume')
	{
		$sql="SELECT 
			DATE_FORMAT(t5.date,'%d-%m-%Y') AS date,
			t5.mapped_alias,
			t4.subject,
			t4.id AS subjectmapping_id,
			t1.fieldmapping_id,
			t1.standardised_points
			FROM ks4meta AS t1 
			INNER JOIN pupil AS t2 USING(cohort_id,pupil_id)
			INNER JOIN setdata AS t3 USING(cohort_id, pupil_id)
			INNER JOIN subjectmapping AS t4 ON(t3.mapped_subject = t4.mapped_subject AND t1.subjectmapping_id=t4.id)
			INNER JOIN fieldmapping AS t5 ON(t1.cohort_id = t5.cohort_id AND t1.fieldmapping_id = t5.id)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.pupil_id=t1.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.set_code=t3.set_code) 
			AND t1.cohort_id=:cohortId
			AND t1.pupil_id=:pupilId			
			AND t5.type=:type
			AND t4.include=1
			GROUP BY t4.subject, t1.fieldmapping_id ORDER BY t5.date, t4.subject";

			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
			$command->bindParam(":pupilId",$this->model->pupilId,PDO::PARAM_STR);
			$command->bindParam(":type",$type,PDO::PARAM_STR);						
			return $command->queryAll();//Returns empty array if no records are found

	}

	/**
	 * [getAllSubjectAps description]
	 * @param  string $type [description]
	 * @param  string $mode [description]
	 * @return [type]       [description]
	 */
	public function getAllSubjectAps($type='dcp',$mode='volume')
	{
		$sql="SELECT 
			DATE_FORMAT(t5.date,'%d-%m-%Y') AS date,
			t5.mapped_alias,
			t1.fieldmapping_id,
			SUM(t1.standardised_points*t4.$mode)/COUNT(*) AS subject_aps
			FROM ks4meta AS t1 
			INNER JOIN pupil AS t2 USING(cohort_id,pupil_id)
			INNER JOIN setdata AS t3 USING(cohort_id, pupil_id)
			INNER JOIN subjectmapping AS t4 ON(t3.mapped_subject = t4.mapped_subject AND t1.subjectmapping_id=t4.id)
			INNER JOIN fieldmapping AS t5 ON(t1.cohort_id = t5.cohort_id AND t1.fieldmapping_id = t5.id)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.pupil_id=t1.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.set_code=t3.set_code) 
			AND t1.cohort_id=:cohortId
			AND t1.pupil_id=:pupilId
			AND t5.type=:type
			AND t4.include=1
			GROUP BY t1.fieldmapping_id ORDER BY t5.date";

			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
			$command->bindParam(":pupilId",$this->model->pupilId,PDO::PARAM_STR);
			$command->bindParam(":type",$type,PDO::PARAM_STR);
			return $command->queryAll();//Returns empty array if no records are found
	}


	/**
	 * Returns an array of all the available filters for a pupil. These can be used to check that the correct pupils have been filtered.
	 * @return array
	 */
	public function getFilters()
	{
		$sql="SELECT gender, ethnicity, sen_code, fsm, gifted, cla, eal, pupil_premium FROM pupil WHERE cohort_id=:cohortId AND pupil_id=:pupilId";
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
		$command->bindParam(":pupilId",$this->model->pupilId,PDO::PARAM_STR);
		return $command->queryRow();//Returns empty array if no records are found
	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssAstarToA($row,$data)
	{
		if($row==0){
		if($data['astar_a_percentage']>$data['target_astar_a_percentage'])
		return "green";
		
		if($data['astar_a_percentage']==$data['target_astar_a_percentage'])
		return "amber";
		
		if($data['astar_a_percentage']<$data['target_astar_a_percentage'])
		return "red";
		}

	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssAstarToC($row,$data)
	{

		if($row==0){
		if($data['astar_c_percentage']>$data['target_astar_c_percentage'])
		return "green";
		
		if($data['astar_c_percentage']==$data['target_astar_c_percentage'])
		return "amber";
		
		if($data['astar_c_percentage']<$data['target_astar_c_percentage'])
		return "red";
		}

	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssAstarToG($row,$data)
	{

		if($row==0){
		if($data['astar_g_percentage']>$data['target_astar_g_percentage'])
		return "green";
		
		if($data['astar_g_percentage']==$data['target_astar_g_percentage'])
		return "amber";
		
		if($data['astar_g_percentage']<$data['target_astar_g_percentage'])
		return "red";
		}

	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssAps($row,$data)
	{

		if($row==0){
		if($data['aps']>$data['target_aps'])
		return "green";
		
		if($data['aps']==$data['target_aps'])
		return "amber";
		
		if($data['aps']<$data['target_aps'])
		return "red";
		}

	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssTotalPoints($row,$data)
	{

		if($row==0){
		if($data['total_points']>$data['target_total_points'])
		return "green";
		
		if($data['total_points']==$data['target_total_points'])
		return "amber";
		
		if($data['total_points']<$data['target_total_points'])
		return "red";
		}

	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssCapped8($row,$data)
	{

		if($row==0){
		if($data['capped8']>$data['target_capped8'])
		return "green";
		
		if($data['capped8']==$data['target_capped8'])
		return "amber";
		
		if($data['capped8']<$data['target_capped8'])
		return "red";
		}

	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssDcpResult($row,$data)
	{

		if($data['dcp_standardised_points']>$data['target_standardised_points'])
		return "green";
		
		if($data['dcp_standardised_points']==$data['target_standardised_points'])
		return "amber";
		
		if($data['dcp_standardised_points']<$data['target_standardised_points'])
		return "red";
		
	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssResidualDiff($row,$data)
	{

		if($data['dcp_residual']>$data['target_residual'])
		return "green";
		
		if($data['dcp_residual']==$data['target_residual'])
		return "amber";
		
		if($data['dcp_residual']<$data['target_residual'])
		return "red";
		
	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssSubjectAverageDiff($row,$data)
	{

		if($data['dcp_standardised_points']>$data['subject_aps'])
		return "green";
		
		if($data['dcp_standardised_points']==$data['subject_aps'])
		return "amber";
		
		if($data['dcp_standardised_points']<$data['subject_aps'])
		return "red";
		
	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssLevelsProgressEnglish($row,$data)
	{
		if($row==0){
		if($data['english_lp']>$data['target_english_lp'])
		return "green";
		
		if($data['english_lp']==$data['target_english_lp'])
		return "amber";
		
		if($data['english_lp']<$data['target_english_lp'])
		return "red";
		}	
	}

	/**
	 * Renders a cell in CGridView
	 * @return string
	 */
	public function getCellCssLevelsProgressMaths($row,$data)
	{
		if($row==0){
		if($data['maths_lp']>$data['target_maths_lp'])
		return "green";
		
		if($data['maths_lp']==$data['target_maths_lp'])
		return "amber";
		
		if($data['maths_lp']<$data['target_maths_lp'])
		return "red";
		}	
	}

	/**
	 * Returns row css in CGridView
	 * @return string
	 */
	public function getRowCss($row,$data){
			if($this->model->mode=='equivalent'){
				if($data['dcp_discount'])
				return;
				if($data['discount_code']!='')
				return muted;
		}
	}

	/**
	 * Returns the value for a cell in CGridView. Note data and row are the other way around here.
	 * @return string
	 */
	public function getDcpDiscount($data,$row)
	{
		if($data['dcp_discount'] && $this->model->mode=='equivalent')
		return $data['dcp_standardised_points']." <i class='icon-ok-sign'></i>";
		else
		return $data['dcp_standardised_points'];


	}

	/**
	 * Returns the value for a cell in CGridView. Note data and row are the other way around here.
	 * @return string
	 */
	public function getTargetDiscount($data,$row)
	{
		if($data['target_discount'] && $this->model->mode=='equivalent'){
			if($data['target_discount'] && $data['dcp_discount']) 
				return $data['target_standardised_points']." <i class='icon-ok-sign'></i>";
				else
				return $data['target_standardised_points']." <i class='icon-ok-sign muted'></i>";
		}
		else{
		return $data['target_standardised_points'];
		}
	}

	
	/**
	 * Renders the value for the no gcses column
	 * @return string
	 */
	public function renderNoGCSEValue($data,$row){
		if($this->model->mode=='equivalent')
		return $data['no_gcses']." ".$data['discount_code'];
		else
		return $data['no_gcses'];
	}






}
