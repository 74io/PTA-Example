<?php

class m121115_172542_schema_upgrade_to_0_9_2 extends CDbMigration
{
	public function up()
	{
		$dbs=$this->getDbs();
		foreach($dbs as $db){
		
		/**
		 * Schema changes to ks4meta
		 */
		// Drop columns
		$this->execute('ALTER TABLE '.$db.'.ks4meta DROP COLUMN a_a');
		$this->execute('ALTER TABLE '.$db.'.ks4meta DROP COLUMN a_c');
		$this->execute('ALTER TABLE '.$db.'.ks4meta DROP COLUMN a_g');
		
		//Add columns
		$this->execute('ALTER TABLE '.$db.'.ks4meta ADD COLUMN result varchar(50) DEFAULT NULL');
		
		//Add indexes
		$this->execute('ALTER TABLE '.$db.'.ks4meta ADD INDEX idx_cohort_result_fieldmapping_id (cohort_id,result,fieldmapping_id)');


		/**
		 * Schema changes for eventlog
		 */
		
		//Add columns
		$this->execute('ALTER TABLE '.$db.'.eventlog ADD COLUMN date DATE DEFAULT NULL');
		
		//Drop indexes
		$this->execute('ALTER TABLE '.$db.'.eventlog DROP INDEX category');
		$this->execute('ALTER TABLE '.$db.'.eventlog DROP INDEX level');
		$this->execute('ALTER TABLE '.$db.'.eventlog DROP INDEX key_stage');
		
		//Add new Indexes
		$this->execute('ALTER TABLE '.$db.'.eventlog ADD INDEX idx_cat_level_ks (category, level, key_stage)');
		$this->execute('ALTER TABLE '.$db.'.eventlog ADD INDEX idx_date_cat_level (date, category, level)');
		
		/**
		 * Schema changes for pupil
		 */
		
		//Add new columns
		$this->execute('ALTER TABLE '.$db.'.pupil ADD COLUMN post_code VARCHAR(10) DEFAULT NULL AFTER eal');
		
		
		/**
		 * Schema changes for setdata
		 */
		//Add new columns
		$this->execute('ALTER TABLE '.$db.'.setdata ADD COLUMN teacher_id INTEGER(11) DEFAULT NULL');
		
		/**
		 * Changes to fieldmapping
		 */
		$this->execute('ALTER TABLE '.$db.'.fieldmapping MODIFY COLUMN mapped_field VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL');
		
		/**
		 * Build tables
		 */
		
		$this->execute("CREATE TABLE $db.mis_attendance (
					  id int(11) NOT NULL AUTO_INCREMENT,
					  upn varchar(15) DEFAULT NULL,
					  adno varchar(10) DEFAULT NULL,
					  possible_marks int(3) DEFAULT NULL,
					  present_marks int(3) DEFAULT NULL,
					  approved_ed_activity int(3) DEFAULT NULL,
					  authorised_absences int(3) DEFAULT NULL,
					  unauthorised_absences int(3) DEFAULT NULL,
					  present_plus_aea int(3) DEFAULT NULL,
					  unexplained_absences int(3) DEFAULT NULL,
					  late_before_reg int(3) DEFAULT NULL,
					  late_after_reg int(3) DEFAULT NULL,
					  late_both int(3) DEFAULT NULL,
					  missing_marks int(3) DEFAULT NULL,
					  attendance_not_required int(3) DEFAULT NULL,
					  date datetime DEFAULT NULL,
					  PRIMARY KEY (id),
					  KEY upn (upn)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		
		$this->execute("CREATE TABLE $db.mis_classes (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  upn varchar(15) DEFAULT NULL,
				  adno varchar(10) DEFAULT NULL,
				  subject_code varchar(20) DEFAULT NULL,
				  subject varchar(50) DEFAULT NULL,
				  class varchar(10) DEFAULT NULL,
				  teacher_id int(10) DEFAULT NULL,
				  PRIMARY KEY (id)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		
		
		$this->execute("CREATE TABLE $db.mis_general (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  upn varchar(15) DEFAULT NULL,
			  ethnicity varchar(10) DEFAULT NULL,
			  sen_code varchar(10) DEFAULT NULL,
			  fsm varchar(10) DEFAULT NULL,
			  gifted varchar(10) DEFAULT NULL,
			  cla varchar(10) DEFAULT NULL,
			  uln varchar(14) DEFAULT NULL,
			  eal varchar(30) DEFAULT NULL,
			  post_code varchar(10) DEFAULT NULL,
			  PRIMARY KEY (id),
			  KEY upn (upn)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		
		$this->execute("CREATE TABLE $db.mis_ks2 (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  upn varchar(15) DEFAULT NULL,
			  adno varchar(10) DEFAULT NULL,
			  field varchar(50) DEFAULT NULL,
			  result char(1) DEFAULT NULL,
			  PRIMARY KEY (id),
			  KEY field (field)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		
		$this->execute("CREATE TABLE $db.mis_pupils (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  upn varchar(15) DEFAULT NULL,
			  adno varchar(10) DEFAULT NULL,
			  legal_surname varchar(20) DEFAULT NULL,
			  surname varchar(20) DEFAULT NULL,
			  legal_forename varchar(20) DEFAULT NULL,
			  forename varchar(20) DEFAULT NULL,
			  year tinyint(2) DEFAULT NULL,
			  form varchar(10) DEFAULT NULL,
			  dob date DEFAULT NULL,
			  gender varchar(10) DEFAULT NULL,
			  PRIMARY KEY (id),
			  KEY upn (upn)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		
		$this->execute("CREATE TABLE $db.mis_teachers (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  initials varchar(10) DEFAULT NULL,
			  title varchar(10) DEFAULT NULL,
			  surname varchar(30) DEFAULT NULL,
			  forename varchar(30) DEFAULT NULL,
			  teacher_id int(11) DEFAULT NULL,
			  PRIMARY KEY (id),
			  KEY teacher_id (teacher_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		
		$this->execute("CREATE TABLE $db.resultdata (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  resultmapping_id int(11) DEFAULT NULL,
			  pupil_id varchar(15) DEFAULT NULL,
			  mapped_subject varchar(20) DEFAULT NULL,
			  result varchar(50) DEFAULT NULL,
			  PRIMARY KEY (id),
			  KEY resultmapping_id (resultmapping_id),
			  KEY pupil_id (pupil_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
			
		$this->execute("CREATE TABLE $db.resultmapping (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  cohort_id varchar(10) DEFAULT NULL,
		  user_id int(11) DEFAULT NULL,
		  name varchar(50) DEFAULT NULL,
		  file_name varchar(255) DEFAULT NULL,
		  description text,
		  num_records int(11) DEFAULT NULL,
		  date_time datetime DEFAULT NULL,
		  PRIMARY KEY (id),
		  KEY cohort_id (cohort_id,name)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		}//end foreach
		
		
	}

	public function down()
	{
		echo "m121115_172542_schema_upgrade_to_0_9_2 does not support migration down.\n";
		return false;
	}
	
	public function getDbs()
	{
		return array("aylestonepta",
					"bromleyprspta",
					"nhh4aspta",
					"pcscpta",
					"ptademolive",
					"stpaulschspta",
					"wtcpta",
					"sjppta",
					"ludlowpta",
					"longdendalepta");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}