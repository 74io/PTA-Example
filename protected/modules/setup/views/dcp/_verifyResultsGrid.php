<p class="lead">The following pupils in this result set are in classes for subjects but currently have empty results:</p>
<?php 
	//Note that headerHtmlOptions have been set here because we clone thead user jquery and need the sizes
	$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'missing-results-grid',
	'type'=>'striped condensed bordered',
	'emptyText'=>'No Pupils Found',
	//'template'=>'{items}',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name'=>'pupil_id',
			'header'=>'Pupil ID',
			'headerHtmlOptions'=>array('width'=>'10%'),
		),
		array(
			'name'=>'surname',
			'header'=>'Surname',
		),
		array(
			'name'=>'forename',
			'header'=>'Forename',
		),
		array(
			'name'=>'year',
			'header'=>'Year',
		),
		array(
			'name'=>'subject',
			'header'=>'Subject',
		),
		array(
			'name'=>'set_code',
			'header'=>'Class',
		),
		array(
			'name'=>'result',
			'header'=>'Result',
		),

	),
));


?>