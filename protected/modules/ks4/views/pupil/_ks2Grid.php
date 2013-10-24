<?php
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'pupil-ks2-grid',
	'type'=>'bordered condensed',
	'template'=>'{items}',
	'ajaxUpdate'=>false,
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
		'name'=>'title',
		'header'=>'',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		'type'=>'raw',
		),
		array(
		'name'=>'english_lp',
		'header'=>'English Levels Progress',
		'cssClassExpression'=>array($component,'getCellCssLevelsProgressEnglish'),
		//'type'=>'raw',
		),
		array(
		'name'=>'maths_lp',
		'header'=>'Maths Levels Progress',
		'cssClassExpression'=>array($component,'getCellCssLevelsProgressMaths'),
		//'type'=>'raw',
		),
		
	),
)); ?>

