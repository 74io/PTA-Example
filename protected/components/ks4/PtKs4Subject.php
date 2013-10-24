<?php
class PtKs4Subject extends PtKs4{

	/**
	 * Creates a temporary ks4 subject master table
	 * @return void
	 */
	public function createKs4MasterTempTable()
		{
			$connection = Yii::app()->db;
			$t = $connection->setTmpTableName('ks4subjectmaster');
			
			$sql="CREATE TABLE $t (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `cohort_id` varchar(20) DEFAULT NULL,
			  `fieldmapping_id` int(11) DEFAULT NULL,
			  `subjectmapping_id` int(11) DEFAULT NULL,
		  	  `subject` varchar(50) DEFAULT NULL,
			  `astar_a` int(3) DEFAULT NULL,
			  `astar_c` int(3) DEFAULT NULL,
			  `astar_g` int(3) DEFAULT NULL,
			  `fail` int(11) DEFAULT NULL,
		  	  `average_point_score` decimal(4,2) DEFAULT '0',
			  `total` int(11) DEFAULT NULL,
			  `qualification` varchar(255) DEFAULT NULL,
  			  `volume` decimal(2,1) DEFAULT NULL,
  			  `equivalent` tinyint(1) DEFAULT NULL,
			  PRIMARY KEY (`id`),
	  		  KEY `idx_fieldmapping_type` (`fieldmapping_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
			
			$command=$connection->createCommand($sql);
			$command->execute();

			$this->_ks4MasterTempTableBuilt=true;
			
		}

	/**
	 * Populates the ks4 subject master temp table
	 * @param  int $fieldMappingId The fieldmapping id
	 * @param  string $type Either equivalent or volume
	 * @return void
	 */
	public function buildKs4Master($fieldMappingId,$type="volume")
		{
			if($this->_ks4MasterTempTableBuilt===null)
			$this->createKs4MasterTempTable();
			
			if($this->_ks4MasterBuilt[$fieldMappingId][$type])
			return $this->_ks4MasterBuilt[$fieldMappingId][$type];
			
			$connection  = Yii::app()->db;
			$t=$connection->tmpTable['ks4subjectmaster'];
			
			$sql="
			INSERT INTO $t (cohort_id,fieldmapping_id,subjectmapping_id,subject,astar_a,astar_c,astar_g,fail,average_point_score,total, qualification, volume, equivalent)
			SELECT t1.cohort_id, 
			t1.fieldmapping_id,
			t2.id,
			t2.subject, 
			SUM(astar_a*t2.$type) AS astar_a, 
			SUM(astar_c*t2.$type) AS astar_c,
			SUM(astar_g*t2.$type) AS astar_g,
			SUM(standardised_points=0),
			SUM(standardised_points*t2.$type)/COUNT(*),
			COUNT(*),
			t2.qualification,
			t2.volume,
			t2.equivalent
			FROM ks4meta AS t1 INNER JOIN (subjectmapping AS t2 ,setdata AS t3)
			ON (t1.cohort_id = t3.cohort_id
			AND t1.pupil_id = t3.pupil_id 
			AND t1.cohort_id = t2.cohort_id 
			AND t2.cohort_id = t3.cohort_id
			AND t1.subjectmapping_id=t2.id 
			AND t3.mapped_subject = t2.mapped_subject
			)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.pupil_id=t1.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.set_code=t3.set_code) 
			AND t2.include='1'
			AND t1.fieldmapping_id='$fieldMappingId'
			";

			if($this->filteredPupilsInClause)
			$sql.=" AND t1.pupil_id IN ($this->filteredPupilsInClause)";
			$sql.=" GROUP BY t2.subject";
			
			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			//$command->bindParam(":type",$type,PDO::PARAM_STR);
			$command->execute();
			
			$this->_ks4MasterBuilt[$fieldMappingId][$type]=true;

		}

		/**
		 * Updates the master if the type is volume to set all subjects with a volume of 0.5 to 0. 
		 * @return void
		 */
		public function updateKs4Master()
		{
			$connection  = Yii::app()->db;
			$t=$connection->tmpTable['ks4subjectmaster'];

			$sql="UPDATE $t AS t1 INNER JOIN subjectmapping AS t2 
			ON(t1.subjectmapping_id=t2.id)
			SET astar_a=0, astar_c=0, astar_g=0
			WHERE t2.volume='0.5'";
			$connection->createCommand($sql)->execute();
		}

		/**
		 * Returns an array of all the subject stats from the temporary table
		 * @param int $fieldMappingId The field mapping id
		 * @return array
		 */
		public function getSubjectSummary($fieldMappingId)
		{
			$connection  = Yii::app()->db;
			$t=$connection->tmpTable['ks4subjectmaster'];

			$sql="SELECT * FROM $t WHERE fieldmapping_id=:fieldMappingId ORDER BY subject";

			$command=$connection->createCommand($sql);
			$command->bindParam(":fieldMappingId",$fieldMappingId,PDO::PARAM_INT);
			$rows= $command->queryAll();//Returns an empty array if no results are returned

			if(count($rows)){
				foreach($rows as $key=>$value){
					$array[$rows[$key]['subject']]=$rows[$key];
				}
				return $array;
			}

			return array();	
		}

		

		/**
		 * Returns an array of group results for pupils achieving A*-A, A*-C or A*-G. Note that the astar_a field is passed as a param in
		 * $this->model->groupArg[1]
		 * @return array
		 */
		public function getDcpGroup()
		{
			
			//var_dump($this->model);
			//exit;
			//
			//Code without teacher and attendance data
			/*
			$sql="SELECT 
			t2.pupil_id,
			t2.surname AS Surname,
			t2.forename AS Forename,
			t1.result AS Result,
			t3.set_code AS Class 
			FROM ks4meta AS t1 
			INNER JOIN pupil AS t2 USING(cohort_id,pupil_id)
			INNER JOIN setdata AS t3 USING(cohort_id, pupil_id)
			INNER JOIN subjectmapping AS t4 ON(t3.mapped_subject = t4.mapped_subject AND t1.subjectmapping_id=t4.id)
			WHERE NOT EXISTS (SELECT * FROM excludedpupils AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.pupil_id=t1.pupil_id)
			AND NOT EXISTS  (SELECT * FROM excludedsets AS t WHERE t.subjectmapping_id = t1.subjectmapping_id AND t.set_code=t3.set_code) 
			AND t1.cohort_id=:cohortId
			AND t1.fieldmapping_id=:fieldMappingId
			AND t1.subjectmapping_id=:subjectMappingId
			AND t1.".$this->model->groupArg[1]."='{$this->model->groupAchiever}'";
			if($this->filteredPupilsInClause)
			$sql.=" AND t1.pupil_id IN ($this->filteredPupilsInClause)";
			$sql.=" GROUP BY t1.pupil_id ORDER BY t2.surname, t2.forename";
			*/
			
		
			//Fetch the DPC results
			$sql="SELECT 
			t2.pupil_id,
			t2.surname,
			t2.forename,
			t2.year,
			t2.form,
			t2.dob,
			t1.result AS dcp_result,
			t1.standardised_points AS dcp_standardised_points,
			t3.set_code,
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
			AND t1.fieldmapping_id=:fieldMappingId
			AND t1.subjectmapping_id=:subjectMappingId
			AND t1.".$this->model->groupArg[1]."='{$this->model->groupAchiever}'";
			if($this->filteredPupilsInClause)
			$sql.=" AND t1.pupil_id IN ($this->filteredPupilsInClause)";
			$sql.=" GROUP BY t1.pupil_id ORDER BY t3.set_code, t2.surname, t2.forename";
			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
			$command->bindParam(":fieldMappingId",$this->model->compare,PDO::PARAM_INT);
			$command->bindParam(":subjectMappingId",$this->model->groupArg[0],PDO::PARAM_INT);
			$compareRows= $command->queryAll();//Returns empty array if no records are found

			//Fetch the target results
			$sql="SELECT 
			t1.pupil_id,
			t1.result,
			t1.standardised_points
			FROM ks4meta AS t1 
			WHERE t1.cohort_id=:cohortId
			AND t1.fieldmapping_id=:fieldMappingId
			AND t1.subjectmapping_id=:subjectMappingId";
				
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(":cohortId",$this->model->cohortId,PDO::PARAM_STR);
			$command->bindParam(":fieldMappingId",$this->model->compareTo,PDO::PARAM_INT);
			$command->bindParam(":subjectMappingId",$this->model->groupArg[0],PDO::PARAM_INT);
			$compareToRows = $command->queryAll();

			//Convert array to use pupil_id as key
			foreach($compareToRows as $key=>$value){
				$compareToData[$value['pupil_id']]=$value;
			}

			//Insert target(compareTo result into original(compareRows) array)
			foreach($compareRows as $key=>$value)
			{
				//print_r($value);
				//echo '<br>'.$compareToData[$value['pupil_id']]['result'];
				//exit;
				//$value['target_result']=$compareToData[$value['pupil_id']]['result'];
				$compareRows[$key]['target_result']=$compareToData[$value['pupil_id']]['result'];
				$compareRows[$key]['target_standardised_points']=$compareToData[$value['pupil_id']]['standardised_points'];
			}

			return $compareRows;
		}

}