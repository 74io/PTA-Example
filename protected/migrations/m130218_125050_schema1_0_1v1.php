<?php

class m130218_125050_schema1_0_1v1 extends CDbMigration
{
	public function up()
	{
		$dbs = PtDbHelper::getDbs();

		foreach($dbs as $db)
		{
			$this->execute('ALTER TABLE '.$db.'.setdata ADD INDEX set_code (set_code)');

			$this->execute('CREATE TABLE '.$db.'.teacher (
						  id int(11) NOT NULL AUTO_INCREMENT,
						  cohort_id varchar(20) DEFAULT NULL,
						  initials varchar(10) DEFAULT NULL,
						  title varchar(10) DEFAULT NULL,
						  surname varchar(30) DEFAULT NULL,
						  forename varchar(30) DEFAULT NULL,
						  teacher_id int(11) DEFAULT NULL,
						  PRIMARY KEY (id),
						  KEY teacher_id (teacher_id),
						  KEY idx_cohort_id_teacher_id (cohort_id,teacher_id)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8');

			$this->execute('CREATE TABLE '.$db.'.attendance (
						  id int(11) NOT NULL AUTO_INCREMENT,
						  cohort_id varchar(20) DEFAULT NULL,
						  pupil_id varchar(15) DEFAULT NULL,
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
						  KEY pupil_id (pupil_id),
						  KEY idx_cohort_id_pupil_id (cohort_id,pupil_id)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8');
		}
	}

	public function down()
	{
		echo "m130218_125050_schema1_0_1v1 does not support migration down.\n";
		return false;
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