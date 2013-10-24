<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'dcp-form',
	'type'=>'horizontal',
	'inlineErrors'=>false,
	'enableAjaxValidation'=>false,
)); ?>

	<?php $wording = ($this->id=="dcp") ? "DCP" : "Target";?>
	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php //echo $form->errorSummary($model); ?>
	
	<?php $defultCohort = $this->schoolSetUp['defaultCohort'];?>
	<?php
	if(!isset($_POST['FieldMapping']['cohort_id'])){
	$model->isNewRecord ? $model->cohort_id = $defultCohort : '';
	}
	?>
	<?php $disabled = $model->isNewRecord ? false : true; ?>
	
	<?php echo $form->dropDownListRow($model, "cohort_id", Yii::app()->common->cohortsDropDown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'The cohort that this '.$wording.' belongs to',
										'disabled'=>$disabled,
										));?>
										
	<?php echo $form->dropDownListRow($model, "year_group", Yii::app()->common->yearGroupsDropDown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'The year group this '.$wording.' belongs to',
										'disabled'=>$disabled,
										));?>
	
	
	<?php echo $form->dropDownListRow($model, "mapped_field", Yii::app()->common->fieldsToMapDropdown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'Where this '.$wording.' will take its results from',
										'options'=>Yii::app()->common->fieldsToMapDropDownOptions,
    									'onChange'=>'PtUpdateField();',
										));?>
									
	<?php echo $form->textFieldRow($model,'mapped_alias',array('class'=>'span5','title'=>'A name for this '.$wording.'. If left blank the result set name will be used')); ?>





	<?php echo $form->datepickerRow($model, 'date',
        array('title'=>'A point in time related to this '.$wording,
        	  'class'=>'span5',
        	  'options'=>array('format'=>'dd-mm-yyyy',
        	  				   'autoclose'=>true),
        	  )); ?>

	  
	  <?php echo $form->checkBoxRow($model,'default'); ?>

	<div class="form-actions">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn btn-primary')); ?>
		<?php if($model->isNewRecord):?>
		<?php echo CHtml::submitButton('Create and add',
										array('class'=>'btn btn-primary',
										'submit'=>'',
										'params'=>array('add'=>1))); ?>
		<?php endif;?>
	</div>

<?php $this->endWidget(); ?>

<?php Yii::app()->clientScript->registerScript('tooltip', "
var options={placement:'right'};
jQuery('input,select').tooltip(options);
");?>


<?php 
if($this->schoolSetUp['mis']=="PTP"){
Yii::app()->clientScript->registerScript('dcp-form-script', "
function PtUpdateField()
{
var text = $('#FieldMapping_mapped_field option:selected').data('field-alias');
$('#FieldMapping_mapped_alias').val(text);
}
",CClientScript::POS_HEAD);
}?>