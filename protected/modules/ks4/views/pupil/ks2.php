<?php $hasMisAccess = PtMisFactory::mis()->hasMisAccess();?>

<!--Begin Title Grid-->
<?php $this->renderPartial('_titleGrid',array(
	'dataProvider'=>$dataProvider['title'], // Here we pass only one grid/dataprovider
));?>
<!--End Title Grid-->

<!--Begin Badges Grid-->
<?php $this->renderPartial('_badgesGrid',array(
	'dataProvider'=>$dataProvider['badges'], 
	//'model'=>$model,
	//'component'=>$component,
));?>
<br>
<!--End Badges Grid -->

<!--Begin Attendance Grid-->
<?php if($hasMisAccess){
 $this->renderPartial('_attendanceGrid',array(
	'dataProvider'=>$dataProvider['results'],
	//'model'=>$model,
	'component'=>$component,
));
}?>
<!--End Attendance Grid -->

<!--Begin KS2 Summary Grid-->
<?php $this->renderPartial('_ks2SummaryGrid',array(
	'dataProvider'=>$dataProvider['ks2Summary'], 
	//'model'=>$model,
	//'component'=>$component,
));?>
<br>
<!--End KS2 Summary Grid -->


<!--Begin KS2 Grid-->
<?php $this->renderPartial('_ks2Grid',array(
	'dataProvider'=>$dataProvider['ks2'], 
	//'model'=>$model,
	'component'=>$component,
));?>
<br>
<!--End KS2 Grid -->

<!--Begin KS2 Subjects Grid-->
<?php $this->renderPartial('_ks2SubjectsGrid',array(
	'dataProvider'=>$dataProvider['ks2Subjects'],
	//'model'=>$model,
	'component'=>$component,
	'hasMisAccess'=>$hasMisAccess,
));?>
<br>
<!--End KS2 Subjects Grid -->


