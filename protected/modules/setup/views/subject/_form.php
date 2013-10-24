<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'subject-form',
	'type'=>'horizontal',
	'inlineErrors'=>false,
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

<?php //echo $form->errorSummary($model); ?>
<?php $defultCohort = $this->schoolSetUp['defaultCohort'];?>
	<?php
	if(!isset($_POST['Subject']['cohort_id'])){
	$model->isNewRecord ? $model->cohort_id = $defultCohort : '';
	}
	?>

<?php $disabled = $model->scenario=='update' ? true : false?>
<?php echo $form->dropDownListRow($model, "cohort_id", Yii::app()->common->cohortsDropDown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'The cohort that this subject belongs to',
										'disabled'=>$disabled,
										));?>

<?php echo $form->dropDownListRow($model, "key_stage", Yii::app()->common->keyStagesDropDown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'The key stage this subject belongs to',
										'disabled'=>$disabled,
										));?>
										
<?php echo $form->dropDownListRow($model, "mapped_subject", Yii::app()->common->getSubjectsDropdown($model->cohort_id),array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'The subject or subject code that this subject will be mapped to',
										'disabled'=>$disabled,
										'options'=>Yii::app()->common->subjectsDropDownOptions,
    									'onChange'=>'PtUpdateField();',
										));?>

<?php echo $form->textFieldRow($model,'subject',array(
										  'class'=>'span5',
										  'maxlength'=>50,
										  'title'=>'An unique name for this subject')); ?>

<?php echo $form->dropDownListRow($model, "qualification", Yii::app()->common->qualificationsDropDown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'The qualification e.g. GCSE, BTEC. Leave blank for KS3',
										));?>

<?php echo $form->dropDownListRow($model, "volume", Yii::app()->common->volumeIndicatorsDropDown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'How many GCSEs this qualification is worth',
										));?>
										
<?php echo $form->dropDownListRow($model, "equivalent", Yii::app()->common->equivalentsDropDown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'Whether this subject is equivalent to a full GCSE (1) or not (0)',
										));?>

<?php echo $form->dropDownListRow($model, "type", Yii::app()->common->subjectTypesDropDown,array(
										'class'=>'span5',
										'prompt'=>'',
										'title'=>'The type of subjects e.g. Humanity, MFL, Biology etc. Leave blank KS3',
										));?>
<?php echo $form->textFieldRow($model,'discount_code',array(
										  'class'=>'span5',
										  'maxlength'=>10,
										  'title'=>'An optional discount code for this subject')); ?>

<?php echo $form->checkBoxRow($model,'include'); ?>

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

<?php Yii::app()->clientScript->registerScript('subject-form-script', "
function PtUpdateField()
{
var text = $('#Subject_mapped_subject option:selected').data('field-alias');
$('#Subject_subject').val(text);
}
",CClientScript::POS_HEAD);?>

<?php Yii::app()->clientScript->registerScript('tooltip', "
var options={placement:'right'};
jQuery('input,select').tooltip(options);
");?>