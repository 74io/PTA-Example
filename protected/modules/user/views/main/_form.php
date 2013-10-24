<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
	'type'=>'horizontal',
	'inlineErrors'=>false,
)); 


//Get a list of safe attributes to control form display.
$safeAttributes = $model->getSafeAttributeNames();?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>
	
	<?php if(in_array('registrationCode',$safeAttributes)):?>
	<?php echo $form->passwordFieldRow($model,'registrationCode',array(
	'class'=>'span5',
	'maxlength'=>128,
	'title'=>'The registration code issued to you by your school')); ?>
	<?php endif;?>


	<?php //echo $form->errorSummary($model); ?>
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
		
	<?php if(in_array('password',$safeAttributes)):?>
	<?php echo $form->passwordFieldRow($model,'password',array('class'=>'span5','maxlength'=>128)); ?>
	<?php echo $form->passwordFieldRow($model,'repeatPassword',array('class'=>'span5','maxlength'=>128)); ?>
	<?php endif;?>

	<?php if(in_array('role',$safeAttributes)):?>
	<?php if(Yii::app()->user->role=='super' && $model->role=="super"){
		$roles=array('super'=>'super');
		$disabled=true;
	}
	else{
		$roles = Yii::app()->common->rolesDropDown;
		$disabled=false;
	}
	?>
	<?php echo $form->dropDownListRow($model, "role", $roles,array('class'=>'span5',
	'prompt'=>'',
	'title'=>'Select the role you want to give this user. See below for more details',
	'disabled'=>$disabled));?>
	<?php endif;?>
	
	
	<?php if(in_array('captchaCode',$safeAttributes)):?>
	<div class="controls">
	<?php $this->widget('CCaptcha'); ?>
	</div>
	<?php echo $form->textFieldRow($model,'captchaCode',array(
	'class'=>'span5',
	'title'=>'Enter the letters in the image above')); ?>
	<?php endif;?>

	<div class="form-actions">
	<?php $buttonText = ($model->scenario=='register') ? 'Register' : 'Create';?>
		<?php echo CHtml::submitButton($model->isNewRecord ? $buttonText : 'Save',array('class'=>'btn btn-primary')); ?>
		<?php if($model->scenario=="update"):?>
	<?php echo CHtml::link('Change Password',array('updatePassword','id'=>$model->id));?>
	<?php endif?>
	</div>

<?php $this->endWidget(); ?>

<?php Yii::app()->clientScript->registerScript('tooltip', "
var options={placement:'right'};
jQuery('input,select').tooltip(options);
");?>
