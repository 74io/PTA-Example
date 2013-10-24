<?php
/**
 * This class is executed on the command line using the command 'php yiic sims load'
 * It will fetch any SIMS schools that have not yet been synced and load the data from the XML file into the database
 */
class SimsCommand extends CConsoleCommand
{
	/*
	public function run($args)
    {
        echo 'hello yii friends';
    }
    */
   public $schoolId;
	
	public function actionHelp()
	{
		echo "Help with my command";
	}
    
	/**
	 * Loads SIMS XML files into the correct database
	 * @return void
	 */
	public function actionLoad()
	{
		//$logger=Yii::getLogger();
		//var_dump($logger->profilingResults);
		//echo $logger->memoryUsage;
	
		
		if($school=$this->schoolForSyncing){
		//Fire off one loader for each school db
		//$time_start = microtime(true);
		$loader= new PtSimsLoader($school);
		$loader->load();
		//$time_end = microtime(true);
		//$time= $time_end - $time_start;	
		//echo "Done in - ".$time;
		$this->markSchoolAsProcessed();
		}
		
		//echo memory_get_peak_usage()."\n";
		//echo memory_get_usage();
		
	}
	/**
	 * Gets the next school for syncing
	 * @return string
	 */
	public function getSchoolForSyncing()
	{
		$sql="SELECT * FROM admin.sync_log WHERE app='PTA' AND processed='0' LIMIT 1";
		$command=Yii::app()->db->createCommand($sql);
		$row = $command->queryRow(); //Returns false if no rows are found

		if($row){
		$this->schoolId= $row['schoolID'];
		$sql="SELECT school_name FROM admin.sec_schools WHERE schoolID='{$this->schoolId}'";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryScalar();
		}
		else{
			return false;
		}
	}

	/**
	 * Marks a school as processed
	 * @return void
	 */
	public function markSchoolAsProcessed()
	{
		$sql="UPDATE admin.sync_log SET processed='1' WHERE schoolID='{$this->schoolId}' AND app='PTA'";
		Yii::app()->db->createCommand($sql)->execute();
	}
}
?>