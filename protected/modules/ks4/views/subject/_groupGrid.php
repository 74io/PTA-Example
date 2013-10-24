<div id="group-grid">
<a style="display:block;" class="close" data-dismiss="alert" href="#" data-target=".drop-down">&times;</a><br>
<?php $hasMisAccess = PtMisFactory::mis()->hasMisAccess();?>


<?php
$columns=array(
	array(
		'name'=>'surname',
		'header'=>'Surname',
		'value'=>array($component,'renderSurnameColumn'), 
		'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
	array(
		'name'=>'forename',
		'header'=>'Forename',
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
	/*
		array(
		'name'=>'dob',
		'header'=>'DOB',
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),*/

	array(
		'name'=>'dcp_result',
		'header'=>'DCP Result',
		'cssClassExpression'=>array($component,'getCellCssResult'),
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
	array(
		'name'=>'dcp_standardised_points',
		'header'=>' DCP Points',
		'visible'=>false,
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
		array(
		'name'=>'target_result',
		'header'=>'Target Result',
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
	array(
		'name'=>'target_standardised_points',
		'header'=>'Target Points',
		'visible'=>false,
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),

	array(
		'name'=>'set_code',
		'header'=>'Class',
		'visible'=>false,
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
	array(
		'name'=>'teacher',
		'header'=>'Teacher',
		'visible'=>false,
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
	array(
		'name'=>'percentage_present',
		'header'=>'% Present',
		'visible'=>$hasMisAccess,
		'cssClassExpression'=>array($component,'getPercentagePresentCss'),
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
	array(
		'name'=>'percentage_unauthorised_absences',
		'header'=>'% Unauthorised Absences',
		'type'=>'raw',
		'visible'=>$hasMisAccess,
		//'htmlOptions'=>array('width'=>'20%'),
		),
	array(
		'name'=>'lates',
		'header'=>'No. Lates',
		'visible'=>$hasMisAccess,
		//'type'=>'raw',
		//'htmlOptions'=>array('width'=>'20%'),
		),
	);

//Generate a grid view for each class
foreach($dataProvider as $key=>$value)
{
	$teacher = ($value->rawData[0]['teacher']) ? '<span class="label label-inverse pull-right">'.$value->rawData[0]['teacher'].'</span>' : '';
	$grid[$key]=$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'class-'.$key,
	'type'=>'striped bordered condensed',
	'summaryText'=>'Displaying {start}-{end} of '.$numRows.' results.'.$teacher,
	'template'=>'{summary}{items}',
	'ajaxUpdate'=>false,
	'dataProvider'=>$value,
	'columns'=>$columns,
),true);
	
}

//Generate tabs
$active=true;
foreach($grid as $key=>$value)
{
	$tabs[]=array('label'=>$key, 'content'=>$value, 'active'=>$active);
	$active=false;
}
?>

<div id='subject-group-grid'>
<?php $this->widget('bootstrap.widgets.TbTabs', array(
	'type'=>'tabs', // 'tabs' or 'pills'
	'id'=>'set-tabs',
	'placement'=>'left',
	'tabs'=>$tabs
));?>
</div>
</div> <!--End Group Grid-->