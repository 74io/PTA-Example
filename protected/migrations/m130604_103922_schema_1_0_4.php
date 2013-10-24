<?php

class m130604_103922_schema_1_0_4 extends CDbMigration
{
	public function up()
	{
		$dbs = PtDbHelper::getDbs();

		foreach($dbs as $db)
		{
			$this->execute("ALTER TABLE ".$db.".subjectmapping ADD COLUMN discount_code varchar(10) NOT NULL DEFAULT '' AFTER type");
		}
	}

	public function down()
	{
		echo "m130604_103922_schema_1_0_4 does not support migration down.\n";
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