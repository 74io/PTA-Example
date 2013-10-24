<?php
//Page titles
if((Yii::app()->controller->id=="dcp")){
$this->sectionTitle="Create DCP";	
$this->sectionSubTitle="Create a new data collection point";	
}else{
$this->sectionTitle="Create Target";	
$this->sectionSubTitle="Create a new target";	
}

//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	$this->controllerSectionTitle.'s'=>array('admin'),
	'Create',
);?>

<?php if($displayResultInfo):?>
<?php echo $this->renderPartial('/dcp/_resultsInfo', array('model'=>$model)); ?>
<?php endif;?>


<?php echo $this->renderPartial('/dcp/_form', array('model'=>$model)); ?>