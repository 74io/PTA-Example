<?php
/**
 * Loads SIMS XML files into the database
 *
 */
class PtSimsLoader extends PtSims
{

	private $school;
	private $hash=array();

	
	/**
	 * Here we call the parent constructor passing in the db we wish to use
	 * @param string $db The name of the database. This is the same as the subdomain. However, because this
	 * is called from the console it needs to be explicitly set
	 * @return void
	 */
	public function __construct($db)
	{
		parent::__construct($db);
		
	}
	

	/**
	 * Load the xml files into the db
	 * The memory usage results below are from green_abbey. Peak memory usage was 15406824 14.69309MB
	 * bear in mind that when this script does nothing (loads no files) the memory usage is 2.3474MB
	 */
	public function load()
	{
		//Set max_allowed_packet=16M
		$sql="SET GLOBAL max_allowed_packet=16*1024*1024";
		Yii::app()->db->createCommand($sql)->execute();
		
		$this->school=$this->getSchool();
		//Load the XML
		$this->loadPupils();// 4249632 4.05276MB
		$this->loadClasses();// 14734296 14.05172MB
		$this->loadTeachers(); // 2508080 2.39189MB
		$this->loadGeneral(); // 3629512 3.46137MB
		$this->loadAttendance(); // 4682424 4.46551MB
		$this->loadKs2();
		//Log the hashes
		$this->logHash();
	}
	
	/**
	 * Load pupils
	 * Note we had to force the encoding of this file to UTF-8 as the pupil 'sian' (a with a hat)
	 * was causing issues. Although no
	 * @return void
	 */
	public function loadPupils()
	{
		$dataType = "pupils";
		if($this->requiresLoading($this->pupilsPathToFile, $dataType)){
			
			$sql="TRUNCATE $this->db.mis_pupils";
			Yii::app()->db->createCommand($sql)->execute();

			//$xml=simplexml_load_file($this->pupilsPathToFile);
			$content = utf8_encode(file_get_contents($this->pupilsPathToFile));
			$xml = simplexml_load_string($content);
				
			$startSql="INSERT INTO $this->db.mis_pupils (upn,adno,legal_surname,surname,legal_forename,forename,year,form,dob,gender) VALUES";
					foreach($xml->Record as $row){
						$upn = addslashes(trim($row->UPN));
						$adno = addslashes(trim($row->Adno));
						$legalSurname = addslashes(trim($row->Legal_Surname));
						$surname = addslashes(trim($row->Surname));
						$legalForename = addslashes(trim($row->Legal_Forename));
						$forename = addslashes(trim($row->Forename));

						$year = addslashes(trim($row->Year_taught_in_Code));
						$form = addslashes(trim($row->Reg_group));
						$form = str_replace($year,"",$form);
						//Handle removing of leading '0' from reg group (Oakwood Park Grammar has this problem)
    					if($form{0}=="0")
    					$form = str_replace("0","",$regGroup);
        				$dob = $this->isoToMysqlDate($row->DOB);
    					$dob = addslashes(trim($dob));
    					$gender = addslashes(trim($row->Gender));

    					//Only load if UPN exists
						if($row->UPN!=""){
						$sqlArray[]="
								('$upn',
								'$adno',
								'$legalSurname',
								'$surname',
								'$legalForename',
								'$forename',
								'$year',
								'$form',
								'$dob',
								'$gender'
								)";		
							}
						}
						
				
					$sql=implode(",",$sqlArray);
					$commitSql=$startSql.$sql;
					
					try{
						Yii::app()->db->createCommand($commitSql)->execute();
					}
					catch(Exception $e){
						$message="Problem loading $dataType - ".$e->errorInfo[2];
						Yii::app()->eventLog->log("error",PtEventLog::CRON_1,$message,"",0,$this->db);
						$this->hash[$dataType]="";//Clear the hash as the upload failed
						return false;
					}		
			
			//Log the event
			Yii::app()->eventLog->log("success",PtEventLog::CRON_1,$dataType,"",0,$this->db);
			
		}//End requies loading
		
	}
	
	/**
	 * Load classes
	 * @return void
	 */
	public function loadClasses()
	{
		$dataType="classes";
		if($this->requiresLoading($this->classesPathToFile, $dataType)){
			
			$sql="TRUNCATE $this->db.mis_classes";
			Yii::app()->db->createCommand($sql)->execute();

			$xml=simplexml_load_file($this->classesPathToFile);
				
			$startSql="INSERT INTO $this->db.mis_classes (upn,adno,subject_code,subject,class,teacher_id) VALUES";
					foreach($xml->Record as $row){
						$upn = addslashes(trim($row->UPN));
						$adno = addslashes(trim($row->Adno));
						$subject_code = addslashes(trim($row->Subject_code));
						$subject = addslashes(trim($row->Subject));
						$class = str_replace(array("&","\"","'","\\"),"",$row->Class);
						$class = addslashes(trim($class));
						$teacherId= addslashes(trim($row->Teacher_ID));
						
						//Only load if UPN exists
						if($row->UPN!=""){
						$sqlArray[]="('$upn',
								'$adno',
								'$subject_code',
								'$subject',
								'$class',
								'$teacherId')";		
							}
						}
				
					$sql=implode(",",$sqlArray);
					$commitSql=$startSql.$sql;
					
					try{
						Yii::app()->db->createCommand($commitSql)->execute();
					}
					catch(Exception $e){
						$message="Problem loading $dataType - ".$e->errorInfo[2];
						Yii::app()->eventLog->log("error",PtEventLog::CRON_1,$message,"",0,$this->db);
						$this->hash[$dataType]="";//Clear the hash as the upload failed
						return false;
					}		
			
			//Log the event
			Yii::app()->eventLog->log("success",PtEventLog::CRON_1,$dataType,"",0,$this->db);
			
		}//End requies loading
	}
	
	/**
	 * Load Teachers
	 * @return void
	 */
	public function loadTeachers()
	{
		$dataType = "teachers";
		if($this->requiresLoading($this->teachersPathToFile, $dataType)){
			
			$sql="TRUNCATE $this->db.mis_teachers";
			Yii::app()->db->createCommand($sql)->execute();

			$xml=simplexml_load_file($this->teachersPathToFile);
				
			$startSql="INSERT INTO $this->db.mis_teachers (initials,title,surname,forename,teacher_id) VALUES";
					foreach($xml->Record as $row){
						$initials = addslashes(trim($row->Initials));
						$title = addslashes(trim($row->Title));
						$surname = addslashes(trim($row->Preferred_Surname));
						$forename = addslashes(trim($row->Preferred_Forename));
						$teacherId= addslashes(trim($row->Teacher_ID));
						
						$sqlArray[]="('$initials',
								'$title',
								'$surname',
								'$forename',
								'$teacherId')";		
						}
						
						
				
					$sql=implode(",",$sqlArray);
					$commitSql=$startSql.$sql;
					
					try{
						Yii::app()->db->createCommand($commitSql)->execute();
					}
					catch(Exception $e){
						$message="Problem loading $dataType - ".$e->errorInfo[2];
						Yii::app()->eventLog->log("error",PtEventLog::CRON_1,$message,"",0,$this->db);
						$this->hash[$dataType]="";//Clear the hash as the upload failed
						return false;
					}		
			
			//Log the event
			Yii::app()->eventLog->log("success",PtEventLog::CRON_1,$dataType,"",0,$this->db);
			
		}//End requies loading
		
	}
	
	/**
	 * Load General
	 * @return void
	 */
	public function loadGeneral()
	{
		$dataType = "general";
		if($this->requiresLoading($this->generalPathToFile, $dataType)){
			
			$sql="TRUNCATE $this->db.mis_general";
			Yii::app()->db->createCommand($sql)->execute();

			$xml=simplexml_load_file($this->generalPathToFile);
				
			$startSql="INSERT INTO $this->db.mis_general (upn,ethnicity,sen_code,fsm,gifted,cla,uln,eal,post_code,pupil_premium)  VALUES";
					foreach($xml->Record as $row){
						$upn = addslashes(trim($row->UPN));
						$ethnicity = addslashes(trim($row->Ethnicity));
						$sen_code = addslashes(trim($row->SEN_Code));
						$fsm = addslashes(trim($row->FSM));
						$gifted= addslashes(trim($row->Gifted));
						$cla = addslashes(trim($row->CLA));
						$uln = addslashes(trim($row->ULN));
						$eal = addslashes(trim($row->EAL));
						$postCode = addslashes(trim($row->Postcode));
						$pupilPremium = addslashes(trim($row->Pupil_Premium));
						
						//Only load if UPN exists
						if($row->UPN!=""){
						$sqlArray[]="('$upn',
								'$ethnicity',
								'$sen_code',
								'$fsm',
								'$gifted',
								'$cla',
								'$uln',
								'$eal',
								'$postCode',
								'$pupilPremium')";		
							}
						}
						
						
					$sql=implode(",",$sqlArray);
					$commitSql=$startSql.$sql;
					
					try{
						Yii::app()->db->createCommand($commitSql)->execute();
					}
					catch(Exception $e){
						$message="Problem loading $dataType - ".$e->errorInfo[2];
						Yii::app()->eventLog->log("error",PtEventLog::CRON_1,$message,"",0,$this->db);
						$this->hash[$dataType]="";//Clear the hash as the upload failed
						return false;
					}		
			
			//Log the event
			Yii::app()->eventLog->log("success",PtEventLog::CRON_1,$dataType,"",0,$this->db);
			
		}
		
	}
	
	/**
	 * Load attendance
	 */
	public function loadAttendance()
	{
			$dataType = "attendance";
		if($this->requiresLoading($this->attendancePathToFile, $dataType)){
			
			$sql="TRUNCATE $this->db.mis_attendance";
			Yii::app()->db->createCommand($sql)->execute();

			$xml=simplexml_load_file($this->attendancePathToFile);
				
			$startSql="INSERT INTO $this->db.mis_attendance (
				upn,
			    adno,
			    possible_marks,
			    present_marks,
			    approved_ed_activity,
			    authorised_absences,
			    unauthorised_absences,
			    present_plus_aea,
			    unexplained_absences,
			    late_before_reg,
			    late_after_reg,
			    late_both,
			    missing_marks,
			    attendance_not_required,
			    date)  VALUES";
			
					foreach($xml->Record as $row){
						$upn = addslashes(trim($row->UPN));
						$adno = addslashes(trim($row->Adno));
						$possible_marks = addslashes(trim($row->Possible_marks));
						$present_marks = addslashes(trim($row->Present_marks));
						$approved_ed_activity= addslashes(trim($row->Approved_ed_activity));
						$authorised_absences = addslashes(trim($row->Authorised_absences));
						$unauthorised_absences = addslashes(trim($row->Unauthorised_absences));
						$present_plus_aea = addslashes(trim($row->Present_plus_aea));
						$unexplained_absences = addslashes(trim($row->Unexplained_absences));
						$late_before_reg = addslashes(trim($row->Late_before_reg));
						$late_after_reg = addslashes(trim($row->Late_after_reg));
						$late_both = addslashes(trim($row->Late_both));
						$missing_marks = addslashes(trim($row->Missing_marks));
						$attendance_not_required = addslashes(trim($row->Attendance_not_required));

						
						//Only load if UPN exists
						if($row->UPN!=""){
						$sqlArray[]="('$upn',
								'$adno',
								'$possible_marks',
								'$present_marks',
								'$approved_ed_activity',
								'$authorised_absences',
								'$unauthorised_absences',
								'$present_plus_aea',
								'$unexplained_absences',
								'$late_before_reg',
								'$late_after_reg',
								'$late_both',
								'$missing_marks',
								'$attendance_not_required',
								NOW()
								)";		
							}
						}
						
						
					$sql=implode(",",$sqlArray);
					$commitSql=$startSql.$sql;
					
					try{
						Yii::app()->db->createCommand($commitSql)->execute();
					}
					catch(Exception $e){
						$message="Problem loading $dataType - ".$e->errorInfo[2];
						Yii::app()->eventLog->log("error",PtEventLog::CRON_1,$message,"",0,$this->db);
						$this->hash[$dataType]="";//Clear the hash as the upload failed
						return false;
					}		
			
			//Log the event
			Yii::app()->eventLog->log("success",PtEventLog::CRON_1,$dataType,"",0,$this->db);
			
		}
		
	}
	
	/**
	 * Load KS2
	 * @return void
	 */
	public function loadKs2()
	{
		$dataType = "ks2";
		if($this->requiresLoading($this->ks2PathToFile, $dataType)){
			
			$sql="TRUNCATE $this->db.mis_ks2";
			Yii::app()->db->createCommand($sql)->execute();

			$xml=simplexml_load_file($this->ks2PathToFile);
				
			$startSql="INSERT INTO $this->db.mis_ks2 (upn,adno,field,result) VALUES";
					foreach($xml->Record as $row){
						$upn = addslashes(trim($row->UPN));
						$adno = addslashes(trim($row->Adno));
						$field = addslashes(trim($row->Field));
						$result = addslashes(trim($row->Result));

						//Only load if UPN exists
						if($row->UPN!=""){
						$sqlArray[]="('$upn',
								'$adno',
								'$field',
								'$result'
								)";		
							}
						}
						
						
					$sql=implode(",",$sqlArray);
					$commitSql=$startSql.$sql;
					
					try{
						Yii::app()->db->createCommand($commitSql)->execute();
					}
					catch(Exception $e){
						$message="Problem loading $dataType - ".$e->errorInfo[2];
						Yii::app()->eventLog->log("error",PtEventLog::CRON_1,$message,"",0,$this->db);
						$this->hash[$dataType]="";//Clear the hash as the upload failed
						return false;
					}		
			
			//Log the event
			Yii::app()->eventLog->log("success",PtEventLog::CRON_1,$dataType,"",0,$this->db);
			
		}
		
	}
	
	/**
	 * Returns an associatve array of details for a school
	 */
	public function getSchool()
	{
		$sql="SELECT * FROM sec_schools WHERE school_name=:school ";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(':school', $this->db,PDO::PARAM_STR);
		$row= $command->queryRow();
		$row['hash']=unserialize($row['hash']);
		return $row;
	}
	
	/**
	 * Returns true if the file requires loading. false if it doesn't
	 * @param string $file Path to the file
	 * @param string $type Either pupils, classes, teachers, general, attendance etc.
	 * @return bool
	 */
	public function requiresLoading($file,$type)
	{
		//Check if the file exists if not log the event
		if(!file_exists($file))
		return false;
		
		
		$this->hash[$type]=md5_file($file);
		if($this->school['hash'][$type]==$this->hash[$type])
		return false;
		
		
		return true;
	}
	
	/**
	 * Converts an ISO datetime format to a Mysql date format CCYY-MM-DD
	 * @param string $date An ISO date
	 * @return string
	 */
	public function isoToMysqlDate($date)
	{
        	$date=str_replace(array("-","T")," ",$date);
        	sscanf($date, '%s %s %s 00:00:00+01:00', $y, $m, $d);
        	return $y . "-" . $m . "-" . $d;
		
	}
	
	/**
	 * Updates the sec_schools table with the hashes of the previous files
	 */
	public function logHash()
	{
		$hash=serialize($this->hash);
		$sql="UPDATE admin.sec_schools SET hash='$hash', hash_logged=NOW() WHERE school_name='$this->db'";
		Yii::app()->db->createCommand($sql)->execute();
	}

	

	
	
}