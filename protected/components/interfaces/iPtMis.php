<?php
/**
 * Iterface methods required for all MIS systems
 * @author Ryan
 *
 */
interface iPtMis
{
	/**
	 * Returns a list of valid indicator fields for the MIS
	 * @return array
	 */
	public function getIndicatorList();
	
	/**
	 * Returns general fields in a format for CHtml.dropDownList()
	 * This may differ depending on the MIS
	 * @return array
	 */
	public function getGeneralFieldsDropDown();
	
	/**
	 * Fetches the general fields from the database. The fields will differ depending on the MIS
	 * @return array
	 */
	public function getGeneralFields();
	
	
	/**
	 * Returns an array of fields containing DCPs or Targets. When using PTP these will come directly from
	 * shared fields on PTP. When using SIMS these will come from imported result set names.
	 * @return array
	 */
	public function getSharedFieldsDropDown();
	
	/**
	 *Returns an array of options to be used in CHtml.dropDownList()'s options property. For the case of
	 *SIMS this should return an empty array as there are no aliases
	 * @return array 
	 */
	public function getSharedFieldsDropDownOptions();
	
	/**
	 * Returns true if the ks2 fields in the settings are available on the MIS system
	 * @return bool
	 */
	public function getKs2FieldsAreValid();
	
	/**
	 * Builds the core data
	 * @param string $currentCohortId The id of the current cohort. I.e. the cohort that today's date
	 * currently belongs to
	 * @return bool
	 */
	public function buildCoreData($currentCohortId);
	
	/**
	 * Builds the pupil table
	 * @param string $currentCohortId See above
	 * @return bool
	 */
	public function buildPupils($currentCohortId);
	
	/**
	 * Builds the setdata table
	 * @param string $currentCohortId See above
	 * @return bool
	 */
	public function buildSets($currentCohortId);
	
	/**
	 * Builds the subject data for a specific subject
	 * @param Subject $object an active record object
	 * @param string $currentCohortId The current cohort id
	 */
	public function buildSubjectDataForSubject($object,$currentCohortId);
	
	/**
	 * Builds the result set for a specific mapped field. This is fired when a DCP or target is edited and the mapped field is changed
	 * @param FieldMapping $object An active record object
	 * @param string $currentCohortId The current cohort id
	 */
	public function buildSubjectDataForFieldMapping($object,$currentCohortId);

	/**
	 * Returns true if the system has access to other MIS data e.g. teacher, attendance
	 * @return boolean
	 */
	public function hasMisAccess();

}