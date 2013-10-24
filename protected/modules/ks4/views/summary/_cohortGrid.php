<?php // $gridId, $dataProvider and $component are available to this view
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>$gridId,
	'template'=>'{items}',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
			array(
			'name'=>'col1',
			'header'=>'',
			'type'=>'raw',
			'htmlOptions'=>array('width'=>'230px'),
		),
		array(
			'name'=>'col2',
			'header'=>'DCP Total',
			'cssClassExpression'=>array($component,'getCellCssCohort'),
			'htmlOptions'=>array('width'=>'80px'),
		),
		array(
			'name'=>'col3',
			'header'=>'',
			//'value'=>array($component,'getCellCss'), 
			//'type'=>'raw',
			//'cssClassExpression'=>'($data[col3]>$data[col5]) ? "green" : "red"',
			'htmlOptions'=>array('width'=>'80px'),
		),
		
		array(
			'name'=>'col4',
			'header'=>'Target Total',
			'htmlOptions'=>array('width'=>'80px'),
		),
		array(
			'name'=>'col5',
			'header'=>'',
			'htmlOptions'=>array('width'=>'80px'),
		),
	),
)); ?>