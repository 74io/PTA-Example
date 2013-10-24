<?php
$this->sectionTitle = "View Result Set";
$this->sectionSubTitle="Result Set #".$model->id;
//Page bread crumbs
$this->breadcrumbs=array(
	'Results'=>array('admin'),
	'View',
);?>

<?php $this->widget('PtEditableDetailView',array( 
    'data'=>$model, 
    'url' => $this->createUrl('result/update'),
    'attributes'=>array( 
        'cohort_id',
        'id',
        'num_records',
        'name',
        'file_name',
        'user.username',
        'date_time',
        'description',
    ), 
)); ?> 

<!-- Start render help -->
<?php $this->renderPartial('_help');?>
<!-- End render help -->

