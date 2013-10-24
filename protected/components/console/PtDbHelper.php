<?php
/*
 * Provides db helper methods for console applications
 */
class PtDbHelper extends CComponent
{
	
	public function init()
	{
		
	}
	/**
	 * Returns an array of file names on the protected/config/clients folder. These can be used to iterate
	 * over the school dbs
	 */
	public static function getDbs()
	{
		$sql="SELECT DISTINCT(subdomain) FROM admin.account";
		return Yii::app()->db->createCommand($sql)->queryColumn();

	}
}