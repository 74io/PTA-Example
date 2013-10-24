<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'cohort-form',
    'type'=>'horizontal',
	'inlineErrors'=>false,
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>
	<?php if($model->isNewRecord){
		$disabled=false;
	}
	else{
		$disabled = $model->default ? false : true;
	} ?>

	<?php echo $form->datepickerRow($model, 'term_start_date',
        array('title'=>'The date that the last term in your school ends',
        	  'options'=>array('format'=>'dd-mm-yyyy',
        	  				   'autoclose'=>true),
        	  )); ?>

	
	<?php echo $form->datepickerRow($model, 'term_end_date',
        array('title'=>'The date that the last term in your school ends',
        	  'options'=>array('format'=>'dd-mm-yyyy',
        	  				   'autoclose'=>true),
        	  )); ?>
	  
	<div class="form-actions">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

<?php Yii::app()->clientScript->registerScript('tooltip', "
var options={placement:'right'};
$('input').tooltip(options);
");


