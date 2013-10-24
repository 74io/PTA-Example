<?php // $gridId, $dataProvider and $component are available to this view
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>$gridId,
	'template'=>'{items}',
	'nullDisplay'=>'0',
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
			'header'=>'% DCP Pupils',
			'cssClassExpression'=>array($component,'getCellCss'),
			'htmlOptions'=>array('width'=>'80px'),
			'type'=>'raw'

		),
		array(
			'name'=>'col3',
			'header'=>'',
			'htmlOptions'=>array('width'=>'80px'),
			'type'=>'raw',
			'cssClassExpression'=>array($component,'getCellCss'),
		),
		array(
			'name'=>'col4',
			'header'=>'% Target Pupils',
			'htmlOptions'=>array('width'=>'80px'),
			'type'=>'raw',
		),
		array(
			'name'=>'col5',
			'header'=>'',
			'type'=>'raw',
			'htmlOptions'=>array('width'=>'80px'),
		),

	),
)); ?>