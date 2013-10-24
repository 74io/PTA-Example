<?php
//Page titles
$this->sectionTitle="Create Cohort";
$this->sectionSubTitle="Create a new cohort";
$this->portletTitle = "Cohorts";
//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'Cohorts'=>array('admin'),
	'Create',
);?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>