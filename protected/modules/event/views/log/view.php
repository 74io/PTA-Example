<?php
$this->sectionTitle = "View Event";
$this->sectionSubTitle="Event #".$model->id;
$this->breadcrumbs=array(
	'Event Log'=>array('index'),
	$model->id,
);

?>

<?php $this->widget('ext.bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		//'user.username',
		array(     
            'label'=>'Username',
            //'type'=>'raw',
            'value'=>$model->renderUserColumn($model->user->username,$row), 
        ),
		array(     
            'label'=>'Level',
            'type'=>'raw',
            'value'=>$model->renderLevelColumn($model->level,$row), 
        ),
		
		'category',
		'message',
		'date_time',
	),
)); ?>
