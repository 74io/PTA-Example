<a style="display:block;" class="close" data-dismiss="alert" href="#" data-target=".drop-down">&times;</a><br>
<?php $hasMisAccess = PtMisFactory::mis()->hasMisAccess();?>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'group-grid',
	'type'=>'striped bordered condensed',
	'template'=>'{summary}{items}',
	'ajaxUpdate'=>false,
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name'=>'surname',
			'header'=>'Surname',
			'value'=>array($component,'renderSurnameColumn'), 
			'type'=>'raw',
			//'cssClassExpression'=>array($component,'getCellCss'),
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
			'name'=>'form',
			'header'=>'Form',
		),
		array(
			'name'=>'dob',
			'header'=>'DOB',
		),
		array(
			'name'=>'percentage_present',
			'header'=>'% Present',
			'visible'=>$hasMisAccess,
		),
		array(
			'name'=>'percentage_unauthorised_absences',
			'header'=>'% Unauthorised Absences',
			'visible'=>$hasMisAccess,
		),
		array(
			'name'=>'lates',
			'header'=>'No. Lates',
			'visible'=>$hasMisAccess,
		),
	),
)); ?>