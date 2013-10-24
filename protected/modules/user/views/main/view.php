<?php
//Section Titles
$this->sectionTitle="View User";
$this->sectionSubTitle="Viewing user #".$model->id;

$this->breadcrumbs=array(
	'Users'=>array('admin'),
	$model->id,
);

?>

<?php $this->widget('ext.bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'username',
		'email',
		'role',
	
	),
)); ?>
