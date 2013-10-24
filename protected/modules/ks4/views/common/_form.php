<div id="cohort-container">
	<div class="form-actions">
		<div class="row">
			<div class="span12">
	<?php echo CHtml::activeLabel($model, "compare");?>
	<?php echo CHtml::activeDropDownList($model, "compare", FieldMapping::getFieldMappingsForYearGroupDropDown($model->cohortId,
	array($model->yearGroup)),array(
				'title'=>'Compare',
				'rel'=>'tooltip',
			 	'class'=>'span4',
				));?>
				
	<?php echo CHtml::activeLabel($model, "compareTo");?>
	<?php echo CHtml::activeDropDownList($model, "compareTo", FieldMapping::getFieldMappingsForYearGroupDropDown($model->cohortId,
	array($model->yearGroup)),array(
				'title'=>'Compare To',
				'rel'=>'tooltip',
			 	'class'=>'span4',
				));?>
		</div>
		<div class="span12">

	<?php echo CHtml::activeLabel($model, "yearGroup");?>
	<?php echo CHtml::activeDropDownList($model, "yearGroup", Yii::app()->common->getYearGroupsForKeyStageDropDown(4),array(
											'id'=>'year-group-external-filter',
											'class'=>'input-mini',
											));?>
	<?php echo CHtml::activeLabel($model, "cohortId");?>
	<?php echo CHtml::activeDropDownList($model, "cohortId", Yii::app()->common->cohortsDropDown,array(
											'id'=>'cohort-external-filter',
											'class'=>'span2',
											));?>
	<?php echo CHtml::activeLabel($model, "mode");?>
	<?php echo CHtml::activeDropDownList($model, "mode", Yii::app()->common->mode ,array(
											'id'=>'mode-external-filter',
											'class'=>'span4',
										));?>
			</div>
		</div>									
	</div>
</div>