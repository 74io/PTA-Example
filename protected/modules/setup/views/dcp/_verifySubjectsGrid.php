<p class="lead">The following subjects listed for the appropriate key stage are not contained within this result set. 
This could be because no one takes these subjects:</p>
<?php 
	//Note that headerHtmlOptions have been set here because we clone thead user jquery and need the sizes
	$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'missing-subject-grid',
	'type'=>'striped condensed bordered',
	'emptyText'=>'No Subjects Found',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name'=>'mapped_subject',
			'header'=>'Mapped Subject',
			'htmlOptions'=>array('width'=>'10%'),
		),
		array(
			'name'=>'subject',
			'header'=>'Subject Name',
			'htmlOptions'=>array('width'=>'10%'),
		),


	),
));


?>