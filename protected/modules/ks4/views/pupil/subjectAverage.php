<!-- Begin Title-->
<p>
<i class="icon-info-sign pull-left icon-border"></i> DCP - <?php echo $dataProvider['title']->rawData[0]['compareName'].' <small class="small muted">['.
$dataProvider['title']->rawData[0]['compareDate'].']</small>'?>
</p>
<!-- End Title -->

<?php $hasMisAccess = PtMisFactory::mis()->hasMisAccess();?>

<!--Begin Badges Grid-->
<?php $this->renderPartial('_badgesGrid',array(
	'dataProvider'=>$dataProvider['badges'], // Here we pass only one grid/dataprovider
	//'model'=>$model,
	//'component'=>$component,
	//'activeTab'=>$activeTab,
));?>
<br>
<!--End Badges Grid -->

<!--Begin Attendance Grid-->
<?php if($hasMisAccess){
 $this->renderPartial('_attendanceGrid',array(
	'dataProvider'=>$dataProvider['results'], // Here we pass only one grid/dataprovider
	//'model'=>$model,
	'component'=>$component,
));
}?>
<!--End Attendance Grid -->

<!--Begin Results Grid-->
<?php $this->renderPartial('_subjectAverageGrid',array(
	'dataProvider'=>$dataProvider['results'], // Here we pass only one grid/dataprovider
	'hasMisAccess'=>$hasMisAccess,
	'component'=>$component,
	//'activeTab'=>$activeTab,
));?>
<!--End Results Grid -->

<!--Begin Subject Average Chart -->
<?php $this->renderPartial('_subjectAverageChart',array(
	'dataProvider'=>$dataProvider['results'], // Here we pass only one grid/dataprovider
	//'model'=>$model,
	//'component'=>$component,
));?>
<!--End Subject Average Chart -->

<div id="subject-average-chart"></div>