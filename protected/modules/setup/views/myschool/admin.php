<?php
//Page titles
$this->sectionTitle="My School";
$this->sectionSubTitle="Tell us about your school";
//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('index'),
	'My School'=>array('admin'),
	'Manage',
);?>
<?php if($this->schoolSetUp===null):?>

<div class="alert alert-block">
<h4>Setup Not Complete</h4>
<p>You cannot continue with setup until this page has been completed.</p>
</div>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'modal',
    'htmlOptions'=>array('width'=>'600px',
	'max-height'=>'500px'),
/*
    'events'=>array(
        'show'=>"js:function() { console.log('modal show.'); }",
        'shown'=>"js:function() { console.log('modal shown.'); }",
        'hide'=>"js:function() { console.log('modal hide.'); }",
        'hidden'=>"js:function() { console.log('modal hidden.'); }",
    ),
    */
)); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><?php echo $this->sectionSubTitle?></h4>
</div>


<?php $this->actionMisForm(); ?>



<?php $this->endWidget();?>

<?php Yii::app()->clientScript->registerScript("modal-script","
$('#modal').modal('show');
");?>
<?php else:?>
<?php $this->renderPartial('/_menus/setup');
	$this->actionMisForm();
	?>
<?php endif;?>








