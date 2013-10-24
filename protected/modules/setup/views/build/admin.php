<?php
//Page Titles
$this->sectionTitle="Build Core Data";
$this->sectionSubTitle="Build pupils and classes";
//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'Build'=>array('admin'),
	'Manage',
);
//Output setup tabs
$this->renderPartial('/_menus/setup');
?>

<?php if(!$setUpIsComplete):?>
<div class="alert alert-block">
<h4 class="alert-heading">Core data has not yet been built.</h4>
<p>Click the link below to build the core data (pupils and classes).</p>
</div>
<?php $this->widget('ext.eguiders.EGuider', array(
        'id'          => 'create',
        //'next'        => 'second',
        'title'       => 'Build core data',
        //'buttons'     => array(array('name'=>'Next')),
        'description' => 'Click here to build pupils and classes. Once your system is setup the building of core data will happen automatically whenever a report is viewed.',
        'attachTo'    => '#buttonStateful',
        'position'    => 'right',
        //'overlay'     => true,
        //'xButton'     => true,
        // look here !! 'show' is true, so that means this guider will be
        // automatically displayed when the page loads
        'show'        => true,
        'autoFocus'   => true
    )
);?>
<?php endif;?>

<p>Once your system is setup the building of core data will happen automatically whenever a report is viewed.</p>

<?php if($setUpIsComplete):?>
<div class="alert alert-info">
<h4 class="alert-heading">Core Data</h4>
<p>The core data was last built at <strong><?php echo $coreDataLastBuilt;?></strong> for the cohort
<strong><?php echo $this->schoolSetUp['defaultCohort'];?></strong>. For full details check the <?php echo
CHtml::link('log',array('/event'));?>.</p>
</div>

<?php endif;?>


<div class='form-actions'>
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'type'=>'primary',
    'label'=>'Build Core Data',
    'loadingText'=>'Building...',
	'url'=>'buildCoreData',
    'htmlOptions'=>array('id'=>'buttonStateful'),
)); ?>
</div>

<?php 
Yii::app()->clientScript->registerScript('stateful', "
$('#buttonStateful').click(function() {
    var btn = $(this);
    btn.button('loading'); // call the loading function
    $(btn).click(false);
});

");
?>
		