<?php
class PtMis extends CComponent
{

	public $requiredIndicators=array('Gender','Ethnicity','SEN_Code','FSM','Gifted','CLA','EAL','Pupil_Premium');
	
	/**
	 * The current cohort id. This is the cohort that todays date belongs to
	 * @var string
	 */
	protected $currentCohortId;
	protected $_indicators;
	
	protected $_generalFields=array();
	
	
	/**
	 * Deletes data for from a table for the default cohort
	 * @param string $table The name of the table
	 * @return void
	 */
	protected function deleteCohort($table,$cohortId)
	{
		$sql = "DELETE FROM $table WHERE cohort_id='$cohortId'";
		$command = Yii::app()->db->createCommand( $sql );
		$command->execute();
	}
}
