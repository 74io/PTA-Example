<?php
$this->sectionTitle="KS4 Summary";
$this->sectionSubTitle="School level statistics";
$this->breadcrumbs=array(
	'KS4 Summary'=>array('admin'),
	'Manage',
);?>

<?php $this->renderPartial('/common/_menu');?>

<?php $this->renderPartial('/common/_form',array(
				'model'=>$model));
?>

<?php $this->beginClip('ks4Filter');
$this->renderPartial('application.views._forms.ks4FilterForm',array(
					'model'=>$model,
					'form'=>$form)
);?>
<?php $this->endClip()?>

<div id='ks4summary-container' class="ks4-container">
<!-- Begin cohort grid -->		
<?php $this->renderPartial('_cohortGrid',array(
				'gridId'=>'ks4summary-grid-0',
				'dataProvider'=>$dataProvider['cohort'],
				'component'=>$component));
?>
<!-- End cohort grid -->	

<!-- Begin Headline Grid -->
<?php $tab[0]=$this->renderPartial('_grid',array(
				'gridId'=>'ks4summary-grid-1',
				'dataProvider'=>$dataProvider['headlines'],
				'component'=>$component),true);
	$tab[0].='<div id="headline-chart" class="chart-container"></div>';
	$this->renderPartial('_headlineChart',array('dataProvider'=>$dataProvider['headlines'],
	'component'=>$component));
?>
<!-- End Headline Grid -->

<!-- Begin A*-C Grid -->
<?php $tab[1]=$this->renderPartial('_grid',array(
				'gridId'=>'ks4summary-grid-2',
				'dataProvider'=>$dataProvider['astartoc'],
				'component'=>$component),true);

	$tab[1].='<div id="astartoc-chart" class="chart-container" style="height:2000px;"></div>';
	$this->renderPartial('_astartocChart',array('dataProvider'=>$dataProvider['astartoc']));
?>
<!-- End A*-C Grid -->

<!-- Begin Inc English, Maths -->
<?php $tab[2]=$this->renderPartial('_grid',array(
				'gridId'=>'ks4summary-grid-3',
				'dataProvider'=>$dataProvider['incEnglishMaths'],
				'component'=>$component),true);
	$tab[2].=$this->renderPartial('_engmathsChart',array(
				'dataProvider'=>$dataProvider['incEnglishMaths'],
				'component'=>$component),true);
?>
<!-- End  Inc English, Maths -->

<!-- Begin Attainers -->
<?php $tab[3]=$this->renderPartial('_grid',array(
				'gridId'=>'ks4summary-grid-4',
				'dataProvider'=>$dataProvider['attainers'],
				'component'=>$component),true);
	$tab[3].='<div id="attainers-chart" class="chart-container"></div>';
	$this->renderPartial('_attainersChart',array(
	'dataProvider'=>$dataProvider['attainers'],
	'component'=>$component));
?>
<!-- End Attainers  -->

<!-- Begin Levels Progress -->
<?php $tab[4]=$this->renderPartial('_grid',array(
				'gridId'=>'ks4summary-grid-5',
				'dataProvider'=>$dataProvider['levelsProgress'],
				'component'=>$component),true);
	$tab[4].='<div id="levels-progress-chart" class="chart-container"></div>';
	$this->renderPartial('_levelsProgressChart',array('dataProvider'=>$dataProvider['levelsProgress']));
?>
<!-- End Levels Progress  -->

<!-- Start tabs -->
<?php 
$this->widget('bootstrap.widgets.TbTabs', array(
    'type'=>'pills', // 'tabs' or 'pills'
	'id'=>'atoc',
	//'htmlOptions'=>array('style'=>'padding-top:20px'),
    'tabs'=>array(
				array('label'=>'Headlines','content'=>$tab[0],'active'=>$activeTab[0]),
				array('label'=>'A*-G','content'=>$tab[1],'active'=>$activeTab[1]),
				array('label'=>'English & Maths','content'=>$tab[2],'active'=>$activeTab[2]),
				array('label'=>'Attainers','content'=>$tab[3],'active'=>$activeTab[3]),
				array('label'=>'Levels Progress','content'=>$tab[4],'active'=>$activeTab[4]),
				)
));?>
<!-- End tabs -->
</div><!-- End ks4summary-container -->

<!-- Begin pupil modal-->
<?php $this->renderPartial('/common/_pupilModal');?>
<!-- End pupil modal -->

<!-- Begin help-->
<?php $this->renderPartial('_help');?>
<!-- End help -->

<?php $this->renderPartial('_eGuiders');?>

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
    Yii::app()->assetManager->publish($this->viewPath.'/_summary.js',false, -1, YII_DEBUG),
    CClientScript::POS_END
);

?>

