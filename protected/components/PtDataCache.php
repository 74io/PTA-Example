<?php
class PtDataCache extends CApplicationComponent
{
	public $toCache; // Boolean, set in config file
	
	/**
	 * Saves the cached data to the database
	 * @param array $attributes An array of model attributes
	 * @param array $data The array to store
	 * @param integer $keyStage the key stage for reference when clearing cache
	 * @param string $category The cache category
	 */
	public function setDataCache($attributes,$data,$cohortId,$keyStage,$category)
	{
		if($this->toCache){
			$now = new CDbExpression('NOW()');
			$sql="INSERT INTO datacache (checksum,data,cohort_id,key_stage,category,datetime)
					VALUES(CRC32(:attributes),:data,'$cohortId','$keyStage','$category',$now)";
			
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(':data', serialize($data),PDO::PARAM_STR);
			$command->bindParam(':attributes',serialize($attributes) ,PDO::PARAM_STR);
			$command->execute();
		}
	}
	
	/**
	 * Gets the cached data from the database.
	 * @param array $attributes An array of model attributes
	 * @param integer $keyStage the key stage for reference when clearing cache
	 * @param string $category The cache category
	 */
	public function getDataCache($attributes,$cohortId,$keyStage,$category)
	{
		if($this->toCache){
			$sql="SELECT data FROM datacache WHERE checksum=CRC32(:attributes) 
			AND cohort_id='$cohortId'
			AND key_stage='$keyStage' 
			AND category='$category'";
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(':attributes', serialize($attributes),PDO::PARAM_STR);
			$row=$command->queryRow();//False returned of no result

			if($row)
			return unserialize($row['data']);
			
			return false;
		}
	}
	
	/**
	 * Clears the cache for a particular key stage and cohort
	 * @param integer $keystage The key stage to clear the data for
	 * @param string $cohortId The cohort to clear the data for
	 */
	public function deleteDataCache($cohortId,$keyStage=null)
	{

		$sql="DELETE FROM datacache WHERE cohort_id=:cohortId";
		if($keyStage)
		$sql.=" AND key_stage=:keyStage";
		
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(':cohortId', $cohortId,PDO::PARAM_STR);
		if($keyStage)
		$command->bindParam(':keyStage', $keyStage,PDO::PARAM_INT);
		
		$command->execute();
	}
	
	
}