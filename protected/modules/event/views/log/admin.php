<?php
$this->sectionTitle="Event Log";
$this->sectionSubTitle="View system events and the users who created them";
$this->breadcrumbs=array(
	'Event Log'=>array('/event'),
	'Manage',
);

$datePicker = $this->widget('bootstrap.widgets.TbDatePicker', array(
			        'model'=>$model,
			        'attribute'=>'date_time',
			       // 'class'=>'span5',
        	  		'options'=>array('format'=>'dd-mm-yyyy',
        	  				   		'autoclose'=>true),
			  ),true);
			
?>
<?php $this->widget('ext.bootstrap.widgets.TbGridView',array(
	'id'=>'event-log-grid',
	'dataProvider'=>$model->search(),
	'type'=>'striped',
	'filter'=>$model,
 	'afterAjaxUpdate' => 'reinstallDatePicker', 
	'columns'=>array(
		'id',
		array(
			'name'=>'username',
			//'value'=>'$data->user->username',
			'value'=>array($model,'renderUserColumn'), 
			//'type'=>'raw',
			//'filter'=>true,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'htmlOptions'=>array('width'=>'50px'),
		),
		array(
			'name'=>'level',
			'value'=>array($model,'renderLevelColumn'), 
			'type'=>'raw',
			'filter'=>CHtml::listData(EventLog::model()->findAll(array(
    					'select'=>'t.level',
    					'distinct'=>true,)
		), 'level', 'level'),
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'htmlOptions'=>array('width'=>'50px'),
		),
		array(
			'name'=>'category',
			//'value'=>array($model,'renderLevelColumn'), 
			'type'=>'raw',
			'filter'=>CHtml::listData(EventLog::model()->findAll(array(
    					'select'=>'t.category',
    					'distinct'=>true,)), 'category', 'category'),
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'htmlOptions'=>array('width'=>'50px'),
		),
		array(
			'name'=>'message',
			'htmlOptions'=>array('width'=>'200px'),
		),
		array(
			'name'=>'date_time',
			//'value'=>array($model,'renderLevelColumn'), 
			'type'=>'raw',
			'filter'=>$datePicker,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			'htmlOptions'=>array('width'=>'80px'),
		),
		 
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
            'htmlOptions'=>array('style'=>'width: 50px','class'=>'edit-column'),
			'template'=>'{view}'//Use template to only display specific buttons
			
		),
	),
)); ?>

<?php 
//Note that it is necessary here to repass the date picker options
Yii::app()->clientScript->registerScript('re-install-date-picker', "
function reinstallDatePicker(id,data) {
jQuery('#EventLog_date_time').bdatepicker({'format':'dd-mm-yyyy','autoclose':true,'language':'en','weekStart':0});
}
");?>
