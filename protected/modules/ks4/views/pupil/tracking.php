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

<!--Begin Subject Point Score Chart -->
<?php 

$allSubjectPointScoreChart=$this->renderPartial('_allSubjectPointScoreChart',array(
	'dataProvider'=>$dataProvider['allSubjectPointScores'], // Here we pass only one grid/dataprovider
	//'model'=>$model,
	//'component'=>$component,
),true);
?>
<!--End Subject Point Score Chart -->

<!--Begin Subject APS Chart -->
<?php 
$allSubjectApsChart=$this->renderPartial('_allSubjectApsChart',array(
	'dataProvider'=>$dataProvider['allSubjectAps'], // Here we pass only one grid/dataprovider
  'title'=>$dataProvider['title']->rawData[0]['compareToName'].' ('.$dataProvider['title']->rawData[0]['compareToDate'].')',
  'target'=>(float)$dataProvider['summary']->rawData[1]['aps'],
	//'model'=>$model,
	//'component'=>$component,
),true);

?>
<!--End Subject APS Chart -->

<? //echo $allSubjectPointScoreChart;?>

<?php $this->widget('bootstrap.widgets.TbTabs', array(
  'id'=>'tracking-tabs',
  'type'=>'tabs', // 'tabs' or 'pills'
  'placement'=>'left',
  'tabs'=>array(
    array('label'=>'Chart 1', 'content'=>$allSubjectPointScoreChart, 'active'=>true),
    array('label'=>'Chart 2', 'content'=>"$allSubjectApsChart"),
  ),
));
?>
