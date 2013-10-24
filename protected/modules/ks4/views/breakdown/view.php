<?php
$this->sectionTitle="KS4 Breakdown";
$this->sectionSubTitle=ucfirst($this->action->id);
$this->breadcrumbs=array(
	'KS4 Breakdown'=>array($this->action->id),
	ucfirst($this->action->id)
);?>


<!--Begin top nav menu-->
<?php $this->renderPartial('/common/_menu');?>
<!--End Top Nav Menu-->

<!--Begin top form-->
<?php $this->renderPartial('/common/_form',array(
				'model'=>$model));
?>
<!--End top form-->

<!--Begin top nav sub menu pills-->
<?php $this->renderPartial('_menu');?>
<!--End top nav sub menu pills-->


<!--Begin Filter form. Hidden, but enables values to be passed back and forth between tabs-->
<div style="display:none;">
<?php 
$this->renderPartial('application.views._forms.ks4FilterForm',array(
					'model'=>$model,
					'form'=>$form)
);
?>
</div>
<!--End filter form-->


<!--Begin ks4 container-->
<div id='ks4breakdown-container' class="ks4-container">

<!-- Begin headlines grid -->	
<ul class="inline">
  <li><i class="icon-cog"></i> <a href="#" id="toggle-color">Hide colour coding</a></li>
  <li><i class="icon-cog"></i> <a href="#" id="toggle-targets">Show target %</a></li>
</ul>

<?php $this->renderPartial($grid,array(
				'gridId'=>'ks4breakdown-grid-0',
				'dataProvider'=>$dataProvider,
				'component'=>$component
				));
?>
<!-- End headlines grid -->
</div>
<!--End ks4-container-->

<!-- Start pupil model-->
<?php $this->renderPartial('/common/_pupilModal');?>
<!-- End pupil modal -->

<!--Include highcharts lib so pupil modal works. We need this because as yet this page does not have any charts-->
<div id="hidden-chart">
</div>

<?php 
//We need to include highcharts lib here as it is used by the pupil modal. Instantiating the lib will include the js files
 $this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
 	  'credits' => array('enabled' => false),
  	  'theme' => 'pta',
      'chart'=> array(
            'renderTo'=>'hidden-chart',
            'style'=>array('display'=>'none')
        ))));?>

<!--Begin Help-->
 <?php $this->renderPartial('_help');?>
 <!--End Help-->

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
    Yii::app()->assetManager->publish($this->viewPath.'/_breakdown.js',false, -1, YII_DEBUG),
    CClientScript::POS_END
);
?>