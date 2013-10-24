<div class="form">
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'setup-mis-form',
    'type'=>'horizontal',
	'inlineErrors'=>false,
    'focus'=>array($model,'mis'),
/*
	'clientOptions'=>array(
		'validateOnChange'=>true,
		'validationDelay'=>1000),*/
	
));
$safeAttributes = $model->getSafeAttributeNames();
?> 
	<p class="help-block">Fields with <span class="required">*</span> are required.</p>
<?php echo $form->textFieldRow($model,'name',array('class'=>'span4',
			'title'=>'The name of your school as it will appear on the system')); ?>
<?php echo $form->dropDownListRow($model, "mis", Yii::app()->common->misSystemsDropDown,array('class'=>'span4', 
			'title'=>'The management information system that your school currently uses',
			'prompt'=>'',
			'onchange'=>'js:checkMis(this);'));?>
<div id="ptp-only" style="display:none;">
<?php echo $form->textFieldRow($model,'ptpDbName',array('class'=>'span4',
			'title'=>'The name of your PTP database')); ?>
<?php echo $form->textFieldRow($model,'ptpSchoolId',array('class'=>'span4',
			'title'=>'Your PTP school ID')); ?>
</div>
<?php if(in_array('ks3YearGroups',$safeAttributes)):?>
<?php echo $form->dropDownListRow($model, "ks3YearGroups", Yii::app()->common->systemYearGroupsDropDown,array('class'=>'span4',
			'title'=>'Select the year groups that belong to KS3 in your school. Use Ctrl+click to select multiple year groups',
			'rel'=>'tooltip',
			'multiple'=>true,
			));?>
<?php endif;?>

<?php if(in_array('ks4YearGroups',$safeAttributes)):?>
<?php echo $form->dropDownListRow($model, "ks4YearGroups", Yii::app()->common->systemYearGroupsDropDown,array('class'=>'span4',
			'title'=>'Select the year groups that belong to KS4 in your school. Use Ctrl+click to select multiple year groups',
			'rel'=>'tooltip',
			'multiple'=>true,
			));?>
<?php endif;?>

<?php if(in_array('ks5YearGroups',$safeAttributes)):?>		
<?php echo $form->dropDownListRow($model, "ks5YearGroups", Yii::app()->common->systemYearGroupsDropDown,array('class'=>'span4',
			'title'=>'Select the year groups that belong to KS5 in your school. Use Ctrl+click to select multiple year groups',
			'rel'=>'tooltip',
			'multiple'=>true,
			));?>
<?php endif;?>		

<div class="form-actions">
 <?php echo CHtml::htmlButton('Save', array('class'=>'btn btn-primary', 'type'=>'submit')); ?>
</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<?php Yii::app()->clientScript->registerScript('tooltip', "
var options={placement:'right'};
jQuery('input,select').tooltip(options);

if($('#SetUp_mis').val()=='PTP')
$('#ptp-only').slideDown();
");?>

<?php Yii::app()->clientScript->registerScript('ptp-only-script', "
function checkMis(el)
{
if($(el).val()=='PTP')
$('#ptp-only').slideDown();
else
$('#ptp-only').slideUp();
}
",CClientScript::POS_HEAD);?>