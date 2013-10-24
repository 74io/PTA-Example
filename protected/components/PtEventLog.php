<?php
class PtEventLog extends CApplicationComponent{
	
	public $user;
	
	//Constants are named according to their model/component counterparts.
	//They represent the categories of event available
	const BUILD_1 = "Build core data";
	const BUILD_2 = "Update core data";
	const BUILD_3 = "Delete core data";
	const BUILD_4 = "Building core data";
	const BUILD_5 = "Build KS4 meta data";
	const BUILD_6 = "Update KS4 meta data";
	const BUILD_7 = "Delete KS4 meta data";
	const BUILD_8 = "Building KS4 meta data";
	
	const SUBJECT_1 = "Subject create";
	const SUBJECT_2 = "Subject update";
	const SUBJECT_3 = "Subject delete";
	
	const FIELDMAPPING_1 = "Field mapping create";
	const FIELDMAPPING_2 = "Field mapping update";
	const FIELDMAPPING_3 = "Field mapping delete";
	const FIELDMAPPING_4 = "Field mapping build";
	const FIELDMAPPING_5 = "Field mapping query";
	
	const USER_1 = "User create";
	const USER_2 = "User update";
	const USER_3 = "User delete";
	const USER_4 = "User password updated";
	const USER_5 = "User email updated";
	const USER_6 = "User username updated";
	const USER_7 = "User registration";
	const USER_8 = "User activation";
	const USER_9 = "User reset";
	const USER_10 = "User Login";
	
	const CRON_1 ="Cron loaded xml";
	
	const IMPORT_1 = "File import";
	
	const RESULT_1 = "Result update";
	
	/**
	 * Class constructor. By calling this from the constructor it will only be executed once since it is
	 * instantiated as a singleton
	 */
	public function __construct()
	{
		//Check if the user method exists. If it is a console app it will not
		if(method_exists(Yii::app(), 'getUser')){
			$this->user=Yii::app()->user->id;
			$this->deleteLogs();
		}
		

	}
	
	/**
	 * Inserts a new event log record
	 * @param string $level Either info, error, warning, success
	 * @param string $category The event category (see class constants)
	 * @param string $message The event message
	 * @param integer $keystage The event keystage
	 * @param integer $object_id can be used to pass a useful id i.e. user id.
	 * @param string $db The name of a database. If set then the table name has the db name appended
	 * e.g. demo.eventlog. This is required when the log method is called from a console app
	 */
	public function log($level="info",$category="",$message="",$keyStage="",$object_id="",$db=null)
	{
		//If the user is 0 (annonymous) then use the object_id. In this instance the object_id will contain the
		// id of the user. I.e. when registering an account Yii::app()->user->id will be 0 but we can still get
		//the id of the created account and pass is via object id.
		if($this->user==0)
		$this->user=($object_id) ? $object_id : 0;
		
		
		if($db!==null)
		$table=$db.".eventlog";
		else
		$table = "eventlog";
		
    	//$now = new CDbExpression('NOW()'); 
		$sql="INSERT INTO $table (		
			id,
			user_id,
			level,
			category,
			message,
			object_id,
			key_stage,
			date_time,
			date
			) 
			VALUES(
			NULL,
			'$this->user',
			:level,
			:category,
			:message,
			:object_id,
			:key_stage,
			NOW(),
			CURDATE()
			)
			";
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(':level', $level, PDO::PARAM_STR);
			$command->bindParam(':category', $category, PDO::PARAM_STR);
			$command->bindParam(':message', $message, PDO::PARAM_STR);
			$command->bindParam(':object_id', $object_id, PDO::PARAM_INT);
			$command->bindParam(':key_stage', $keyStage, PDO::PARAM_INT);
            $command->execute();
            
            return Yii::app()->db->getLastInsertID();
	}
	
	/**
	 * Deletes an event from the eventlog table
	 * @param integer $id the eventlog id
	 * @return void
	 */
	public function deleteEvent($id)
	{
		$sql="DELETE FROM eventlog WHERE id='$id'";
		$command=Yii::app()->db->createCommand($sql);
		$command->execute();
	}
	
	/**
	 * Deletes event log entries over a year old
	 * @return void
	 */
	public function deleteLogs()
	{
		$sql="DELETE FROM eventlog WHERE (TO_DAYS(NOW())-TO_DAYS(date_time))='356'";
		$command=Yii::app()->db->createCommand($sql);
		$command->execute();
	}
	

	/**
	 * Returns the last event for a particular category and level
	 * @param string $category
	 * @param string $level
	 * @return mixed
	 */
	public function getLastEvent($category,$level="success")
	{
		$sql="SELECT * FROM eventlog WHERE category='$category'
		AND level='$level'
		ORDER BY id DESC LIMIT 1";
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryRow();
		
	}
	
	/**
	 * Returns an array of events that happen after a particular event
	 * @param integer $eventId
	 * @param integer $keystage The keystage to get the data for
	 * @return mixed
	 */
	public function getEventCategoriesAfter($eventId,$keyStage=null)
	{
		$sql="SELECT category FROM eventlog WHERE id>'$eventId'";
		if($keyStage!==null)
		{
			$sql.=" AND key_stage='$keyStage'";
		}
		$command=Yii::app()->db->createCommand($sql);
		return $command->queryColumn();  
	}
	
	/**
	 * Returns true if any of the event categories in triggers have happened after the event specified in category
	 * @param string $category The event category @see PtEventLog
	 * @param array $triggers An array of triggers which if found in the events will force method to return true
	 * @param integer $keyStage (Optional) The KS to limit the events to
	 * @return boolean
	 */
	public function getTriggerEvent($category, $triggers, $keyStage = null)
	{
		if (! $lastEvent = $this->getLastEvent( $category ))
			return true; //Return true if there are no records. I.e. it has never been built
		

		if ($events = $this->getEventCategoriesAfter( $lastEvent['id'], $keyStage))
		{
			foreach ( $triggers as $trigger )
			{
				if (in_array( $trigger, $events ))
					return true;
			}
		}
		return false;
	}
	
	

	
	
}