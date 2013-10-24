<?php
//Page titles
$this->sectionTitle="Update Cohort ID:".$model->id;
$this->portletTitle = "Cohorts";
//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'Cohorts'=>array('admin'),
	$model->id=>array('update','id'=>$model->id),
	'Update',
);
?>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>