<?php
/**
 * $model, $dataProvider, $set, $itemsCssClass are available to this view
 */
$this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'pupil-grid',
    'dataProvider'=>$dataProvider,
	'template'=>'{summary}{items}',
	'itemsCssClass'=>$itemsCssClass,
	'type'=>'striped',
	'rowCssClassExpression'=>'$data[\'css\']',
	'columns'=>array(
	
	'Surname',
	'Forename',
	array(
			//'name'=>'exclude',
			'header'=>'Exclude',
			'value'=>'CHtml::checkBox("exclude[]", $data[\'exclude\'],
				array("id"=>$data[pupil_id],
					"title"=>$data[pupil_id],
					"data-subject-id"=>"'.$model->id.'",
					"data-set"=>"'.$set.'",
					"data-key-stage"=>"'.$model->key_stage.'",
					"data-cohort-id"=>"'.$model->cohort_id.'",
					"class"=>"exclude-checkbox",
					"onclick"=>"js:updateExcludedPupil(this);"))',
	
			'type'=>'raw',//Required to output as HTML i.e. not HTML encoded
			//'filter'=>Yii::app()->common->yearGroupsDropDown,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'class' => 'CDataColumn',
			'htmlOptions'=>array('width'=>'10px'),
		),
		),
));
