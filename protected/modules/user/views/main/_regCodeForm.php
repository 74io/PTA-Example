<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
	'type'=>'horizontal',
	'inlineErrors'=>false,
));?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->textFieldRow($model,'registrationCode',array(
	'class'=>'span5',
	'maxlength'=>128,
	'title'=>'A registration code. Min 8, Max 20 chars')); ?>


	<?php echo $form->datepickerRow($model, 'expiryDate',
        array('title'=>'The date that this registration code will expire',
        	  'class'=>'span5',
        	  'options'=>array('format'=>'dd-mm-yyyy',
        	  				   'autoclose'=>true),
        	  )); ?>


<div class="form-actions">
 <?php echo CHtml::htmlButton('Save', array('class'=>'btn btn-primary', 'type'=>'submit')); ?>
	</div>

<?php $this->endWidget(); ?>

<?php Yii::app()->clientScript->registerScript('tooltip', "
var options={placement:'right'};
jQuery('input').tooltip(options);
");?>
