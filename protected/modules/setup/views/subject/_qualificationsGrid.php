<?php 
	//Note that headerHtmlOptions have been set here because we clone thead user jquery and need the sizes
	$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'qualifications-grid',
	'type'=>'striped condensed',
	'template'=>'{items}',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name'=>'qualification',
			'header'=>'Qualification',
			'htmlOptions'=>array('width'=>'40%'),
			'headerHtmlOptions'=>array('width'=>'40%'),
		),
		array(
			'name'=>'result',
			'header'=>'Accepted Result',
			'htmlOptions'=>array('width'=>'20%'),
			'headerHtmlOptions'=>array('width'=>'20%'),
		),
		
		array(
			'name'=>'score',
			'header'=>'Point Score',
			'htmlOptions'=>array('width'=>'20%'),
			'headerHtmlOptions'=>array('width'=>'20%'),
		),
		
		array(
			'name'=>'capped_from',
			'header'=>'Capped From',
			'htmlOptions'=>array('width'=>'10%'),
			//'headerHtmlOptions'=>array('width'=>'10%'),
		),
		array(
			'name'=>'inclusion_2014',
			'header'=>'Included 2014',
			'htmlOptions'=>array('width'=>'10%'),
			//'headerHtmlOptions'=>array('width'=>'10%'),
		),

	),
));

?>