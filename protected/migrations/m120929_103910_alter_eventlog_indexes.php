<?php

class m120929_103910_alter_eventlog_indexes extends CDbMigration
{
	public function up()
	{
		Yii::app()->dbHelper->schoolDbs;
	}

	public function down()
	{
		echo "m120929_103910_alter_eventlog_indexes does not support migration down.\n";
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