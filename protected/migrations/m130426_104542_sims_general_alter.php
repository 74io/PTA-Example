<?php

class m130426_104542_sims_general_alter extends CDbMigration
{
	public function up()
	{
		$dbs = PtDbHelper::getDbs();

		foreach($dbs as $db)
		{
			$this->execute('ALTER TABLE '.$db.'.mis_general ADD COLUMN pupil_premium varchar(10) AFTER post_code');
			$this->execute('ALTER TABLE '.$db.'.pupil ADD COLUMN pupil_premium varchar(10) AFTER post_code');

		}
	}

	public function down()
	{
		echo "m130426_104542_sims_general_alter does not support migration down.\n";
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