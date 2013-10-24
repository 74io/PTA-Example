<?php $hasMisAccess = PtMisFactory::mis()->hasMisAccess();?>
<!--Begin Title Grid-->
<?php $this->renderPartial('_titleGrid',array(
	'dataProvider'=>$dataProvider['title'], // Here we pass only one grid/dataprovider
	//'model'=>$model,
	//'component'=>$component,
));?>
<!--End Title Grid-->


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

<!--Begin Summary Grid-->
<div id='ks4pupil-summary-container'>
<?php $this->renderPartial('_summaryGrid',array(
	'dataProvider'=>$dataProvider['summary'], // Here we pass only one grid/dataprovider
	//'model'=>$model,
	'component'=>$component,
));?>
</div>
<!--End Summary Grid -->


<!--Begin Results Grid-->
<?php $this->renderPartial('_resultsGrid',array(
	'dataProvider'=>$dataProvider['results'], // Here we pass only one grid/dataprovider
	'hasMisAccess'=>$hasMisAccess,
	'component'=>$component,
	'model'=>$model,
));?>
<!--End Resuts Grid -->







<?php
//Here we store _summary in the views file, but publish it to the assets folder
/*
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish($this->viewPath.'/_pupil.js',false, -1, YII_DEBUG),
    CClientScript::POS_END
);*/

?>