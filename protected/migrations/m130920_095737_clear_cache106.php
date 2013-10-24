<?php

class m130920_095737_clear_cache106 extends CDbMigration
{
	public function up()
	{
		$dbs = PtDbHelper::getDbs();

		foreach($dbs as $db)
		{
			$this->execute("TRUNCATE ".$db.".datacache");
		}
	}

	public function down()
	{
		echo "m130920_095737_clear_cache106 does not support migration down.\n";
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