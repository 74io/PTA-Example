<?php $hasMisAccess = PtMisFactory::mis()->hasMisAccess();?>

<!--Begin Badges Grid-->
<?php $this->renderPartial('_badgesGrid',array(
	'dataProvider'=>$dataProvider['badges'], // Here we pass only one grid/dataprovider
	//'model'=>$model,
	//'component'=>$component,
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

<!--Begin filters grid-->
 <? $this->renderPartial('_filtersGrid',array(
	'dataProvider'=>$dataProvider['filters'], // Here we pass only one grid/dataprovider
	//'model'=>$model,
	'component'=>$component,
));?>

<!--End Filters Grid-->