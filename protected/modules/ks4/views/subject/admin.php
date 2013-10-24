<?php
$this->sectionTitle="KS4 Subjects";
$this->sectionSubTitle="Subject level statistics";
$this->breadcrumbs=array(
	'KS4 Subjects'=>array('admin'),
	'Manage',
);?>

<?php $this->renderPartial('/common/_menu');?>

<!--Render the header form-->
<?php $this->renderPartial('/common/_form',array(
				'model'=>$model));
?>

<!--Render the side bar filter form -->
<?php $this->beginClip('ks4Filter');
$this->renderPartial('application.views._forms.ks4FilterForm',array(
					'model'=>$model,
					'form'=>$form)
);?>
<?php $this->endClip()?>

<!--Render the subject grid-->

<div id='ks4subject-container' class="ks4-container">
<?php $this->renderPartial('_subjectGrid',array(
				'gridId'=>'ks4subject-grid',
				'dataProvider'=>$dataProvider['subject'],
				'component'=>$component));
?>
<!-- End cohort grid -->
</div>

<!-- Render the subject chart-->
<?php 
$height =  (count($dataProvider['subject']->rawData)*28).'px' //Calculate dynamic hight of chart?>
<div id='subject-chart2' style="height:<?php echo $height?>;"></div>
<?php
if($height!=0){
	$this->renderPartial('_subjectChart2',array(
				'dataProvider'=>$dataProvider['subject'],
				'component'=>$component));
}?>

<div id='subject-chart' style="height:<?php echo $height?>;"></div>
<?php
if($height!=0){
	$this->renderPartial('_subjectChart',array(
				'dataProvider'=>$dataProvider['subject'],
				'component'=>$component));
}
?>
<!-- End subject chart -->

<i class="icon-play-circle"></i> <a href='#' class='start-tour'><strong>Take a Guided Tour</strong></a>

<!--Start eGuiders -->
<?php $this->renderPartial('_eGuiders');?>
<!-- End eGuiders -->

<!-- Start pupil model-->
<?php $this->renderPartial('/common/_pupilModal');?>
<!-- End pupil modal -->

<?php 
//Here we store _summary in the views file, but publish it to the assets folder
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish($this->module->viewPath.'/common/_common.js',false, -1, YII_DEBUG),
    CClientScript::POS_END
);

Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish($this->module->viewPath.'/common/_plugins.js',false, -1, YII_DEBUG),
    CClientScript::POS_END
);

Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish($this->viewPath.'/_subject.js',false, -1, YII_DEBUG),
    CClientScript::POS_END
);

?>