<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
	'type'=>'horizontal',
	'inlineErrors'=>false,
    'focus'=>array($model,'currentPassword'),
)); 

	//Get a list of safe attributes to control form display.
	$safeAttributes = $model->getSafeAttributeNames();?>
	<?php if(in_array('currentPassword',$safeAttributes)):?>
	<?php echo $form->passwordFieldRow($model,'currentPassword',array(
	'class'=>'span5',
	'maxlength'=>128,
	'title'=>'Enter your current password',
	)); ?>
	<?php endif;?>
	
	<?php if(in_array('newPassword',$safeAttributes)):?>
	<?php echo $form->passwordFieldRow($model,'newPassword',array(
	'class'=>'span5',
	'maxlength'=>128,
	'title'=>'Enter a new password',
	)); ?>
	<?php endif;?>
	
	<?php if(in_array('repeatPassword',$safeAttributes)):?>
	<?php echo $form->passwordFieldRow($model,'repeatPassword',array(
	'class'=>'span5',
	'maxlength'=>128,
	'title'=>'Repeat the new password',
	)); ?>
	<?php endif;?>
	
	<?php if(in_array('email',$safeAttributes)):?>
	<?php echo $form->textFieldRow($model,'email',array(
	'class'=>'span5',
	'maxlength'=>128,
	'title'=>'A valid email address')); ?>
	<?php endif;?>
	
	<?php if(in_array('username',$safeAttributes)):?>
	<?php echo $form->textFieldRow($model,'username',array(
	'class'=>'span5',
	'maxlength'=>128,
	'title'=>'A unique username. Lowercase, alphanumeric and underscores only')); ?>
	<?php endif;?>
	
	<?php if(in_array('captchaCode',$safeAttributes)):?>
	<div class="controls">
	<?php $this->widget('CCaptcha'); ?>
	</div>
	<?php echo $form->textFieldRow($model,'captchaCode',array(
	'class'=>'span5',
	'title'=>'Enter the letters in the image above')); ?>
	<?php endif;?>
	
	<?php if(in_array('agree',$safeAttributes)):?>
	<?php echo $form->checkboxRow($model, 'agree',array(
			'hint'=>'By checking this box you are confirming that we can invoice your school for £499 + vat.')); ?>
	<?php endif;?>	

	<div class="form-actions">
	<?php 
	$buttonText=($model->scenario=='recovery') ? 'Send' : 'Save';
	$btnClass = 'btn-primary';
	if($model->scenario=='upgradeAccount'){
	$buttonText = 'Upgrade to Premium';
	$btnClass = 'btn-success';
	}
	echo CHtml::submitButton($buttonText,array('class'=>"btn $btnClass")); 
	?>
	</div>

<?php $this->endWidget(); ?>

<?php Yii::app()->clientScript->registerScript('tooltip', "
var options={placement:'right'};
jQuery('input,select').tooltip(options);
");?>
