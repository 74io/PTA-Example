<?php
//Page Titles
$this->sectionTitle="View ".$this->controllerSectionTitle;
$this->sectionSubTitle=$this->controllerSectionTitle." #".$model->id;
//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'DCPs'=>array('admin'),
	$model->id,
);?>

<?php $this->widget('ext.bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'cohort_id',
		'year_group',
		'mapped_field',
		'mapped_alias',
		'date',
		array(     
            'label'=>'Last Built',
            'type'=>'raw',
            'value'=>$model->renderLastBuiltColumn($model->last_built,$row), 
        ),
	),
)); ?>

<div class="form-actions">

<?php echo CHTML::button('Edit',array('submit'=>array('update','id'=>$model->id),
						'class'=>'btn'));?>
						
						
<?php echo CHTML::button('Delete',array('submit'=>array('delete','id'=>$model->id),
						'confirm'=>'Are you sure you want to delete this item?',
						'class'=>'btn btn-primary'));?>
</div>




