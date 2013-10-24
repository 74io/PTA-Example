<?php

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'pupil-summary-grid',
	'type'=>'condensed',
	'template'=>'{items}',
	'ajaxUpdate'=>false,
	'dataProvider'=>$dataProvider,
	'columns'=>array(
			array(
		'name'=>'title',
		'header'=>'',
		'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
		array(
		'name'=>'astar_a_percentage',
		'header'=>'% A*-A',
		'cssClassExpression'=>array($component,'getCellCssAstarToA'),
		//'type'=>'raw',
		),
		array(
		'name'=>'astar_a',
		'header'=>'',
		//'type'=>'raw',
		),
		array(
		'name'=>'astar_c_percentage',
		'header'=>'% <span class="label">A*-C</span>',
		'cssClassExpression'=>array($component,'getCellCssAstarToC'),
		//'type'=>'raw',
		),
		array(
		'name'=>'astar_c',
		'header'=>'',
		//'cssClassExpression'=>array($component,'getCellCssResult'),
		'type'=>'raw',
		),
		array(
		'name'=>'astar_g_percentage',
		'header'=>'% A*-G',
		'cssClassExpression'=>array($component,'getCellCssAstarToG'),
		//'type'=>'raw',
		),
		array(
		'name'=>'astar_g',
		'header'=>'',
		//'cssClassExpression'=>array($component,'getCellCssResult'),
		//'type'=>'raw',
		),

		array(
		'name'=>'aps',
		'header'=>'APS',
		'cssClassExpression'=>array($component,'getCellCssAps'),
		//'type'=>'raw',
		),
		array(
		'name'=>'total_points',
		'header'=>'Total Points',
		'cssClassExpression'=>array($component,'getCellCssTotalPoints'),
		//'type'=>'raw',
		),
		array(
		'name'=>'capped8',
		'header'=>'Capped 8',
		'cssClassExpression'=>array($component,'getCellCssCapped8'),
		//'type'=>'raw',
		),
		array(
		'name'=>'entries',
		'header'=>'No. Entries',
		//'cssClassExpression'=>array($component,'getCellCssCapped8'),
		//'type'=>'raw',
		),


	),
)); ?>