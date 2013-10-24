<?php
//Set section titles
$this->sectionTitle="Login";
$this->sectionSubTitle="to Pupil Tracking Analytics";
$this->breadcrumbs=array(
	'Login',
);
?>
<div class="row">
<div class="form busy-birds">
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'login-form',
    'type'=>'horizontal',
    'inlineErrors'=>false,
)); ?>


	<?php echo $form->textFieldRow($model,'username',array('class'=>'span3')); ?>
    <?php echo $form->passwordFieldRow($model,'password',array('class'=>'span3')); ?>
    <?php echo $form->checkBoxRow($model,'rememberMe'); ?>
    <div class="controls">
    	<?php echo CHtml::link('Forgotten your login details?',array('/user/account/recovery'));?>
    </div>

	<div class="form-actions">
	<?php echo CHtml::submitButton('Login',array('class'=>'btn btn-primary small')); ?>

		<?php echo CHtml::link('Register',array('/user/register'));?>
	</div>


<?php $this->endWidget(); ?>

</div><!-- form -->
</div><!--End Row-->
