<?php
//Page titles
if((Yii::app()->controller->id=="dcp")){
$this->sectionTitle="Update DCP";	
$this->sectionSubTitle="Update Data Collection Point #".$model->id;
}else{
$this->sectionTitle="Update Target";	
$this->sectionSubTitle="Update Target #".$model->id;	
}

$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	$this->controllerSectionTitle.'s'=>array('admin'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);?>

<?php echo $this->renderPartial('/dcp/_form',array('model'=>$model)); ?>