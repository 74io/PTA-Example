<p class="lead">The following pupils have results that are equal to a fail for the listed qualification:</p>
<?php 
	//Note that headerHtmlOptions have been set here because we clone thead user jquery and need the sizes
	$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'missing-fails-grid',
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
			//'value'=>array($this,'renderFailsSubjectColumn'), 
			//'type'=>'raw',
		),
		array(
			'name'=>'qualification',
			'header'=>'Qualification',
			//'value'=>array($this,'renderFailsQualificationColumn'),
			//'type'=>'raw'
		),
		array(
			'name'=>'result',
			'header'=>'Result',
		),
		
	),
));