<?php
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'pupil-subject-average-grid',
	'type'=>'bordered condensed',
	'template'=>'{summary}{items}',
	'ajaxUpdate'=>false,
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
		'name'=>'subject',
		'header'=>'Subject',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'set_code',
		'header'=>'Class',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'teacher',
		'header'=>'Teacher',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		'visible'=>$hasMisAccess,
		),
		array(
		'name'=>'no_gcses',
		'header'=>'No.',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		'type'=>'raw',
		),
		array(
		'name'=>'qualification',
		'header'=>'Qualification',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'dcp_result',
		'header'=>'DCP',
		//'cssClassExpression'=>array($component,'getCellCssDcpResult'),
		//'type'=>'raw',
		),

		array(
		'name'=>'dcp_standardised_points',
		'header'=>'DCP<br>Score',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		'type'=>'raw',
		),
		array(
		'name'=>'subject_aps',
		'header'=>'Subject<br>APS',
		//'cssClassExpression'=>array($component,'getCellCssResidualDiff'),
		'type'=>'raw',
		),
		array(
		'name'=>'subject_aps_diff',
		'header'=>'Diff',
		'cssClassExpression'=>array($component,'getCellCssSubjectAverageDiff'),
		'type'=>'raw',
		),







	),
)); ?>