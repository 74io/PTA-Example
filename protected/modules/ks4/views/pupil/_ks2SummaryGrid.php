<?php
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'pupil-ks2-grid',
	'type'=>'striped condensed',
	'template'=>'{items}',
	'ajaxUpdate'=>false,
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
		'name'=>'ks2_english_level',
		'header'=>'KS2 English Level',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'ks2_maths_level',
		'header'=>'KS2 Maths Level',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'ks2_science_level',
		'header'=>'KS2 Science Level',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'ks2_english_ps',
		'header'=>'KS2 English Point Score',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'ks2_maths_ps',
		'header'=>'KS2 Maths Point Score',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'ks2_science_ps',
		'header'=>'KS2 Science Point Score',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'ks2_aps',
		'header'=>'KS2 Average Point Score',
		//'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		
	),
)); ?>
