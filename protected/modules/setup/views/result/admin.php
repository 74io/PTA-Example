<?php 
//Page Titles
$this->sectionTitle="Manage Results";
$this->sectionSubTitle="Manage imported result sets";
//Page bread crumbs
$this->breadcrumbs=array(
	'Results'=>array('admin'),
	'Manage',
);?>

<p>Import result sets to your system. Once imported you can create DCPs and targets based on these result sets.</p>

<!-- Start external filter in header -->
<div class="form-actions external-filter-container">
<?php echo CHtml::activeLabel($model, "cohort_id",array("style"=>"display:inline; padding-right:10px; font-weight:bold;"));?>
<?php echo CHtml::activeDropDownList($model, "cohort_id", Yii::app()->common->cohortsDropDown,array(
										'id'=>'cohort-external-filter',
										'class'=>'external-filter',
										'title'=>'Filter by cohort.',
										));?>
</div>
<!-- End external filter in header -->

<!-- Start Render grid -->
<?php $this->renderPartial('_grid',array('model'=>$model));?>
<!-- End Render grid -->

<!-- Start render help -->
<?php $this->renderPartial('_help');?>
<!-- End render help -->


<?php Yii::app()->clientScript->registerScript('cohort-external-filter', "
$('.external-filter').change(function(){
        		data1=$(this).serialize();
        		data2=$('#subject-grid input,select').serialize();
        		data=data1+'&'+data2;
        $.fn.yiiGridView.update('resultmapping-grid', {
                data: data,
        });
        return false;
});
");