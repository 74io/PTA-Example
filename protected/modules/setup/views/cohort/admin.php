<?php
//Page Titles
$this->sectionTitle="Cohorts";
$this->sectionSubTitle="Define term start and end dates";
//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'Cohorts'=>array('admin'),
	'Manage',
);
//Output setup tabs
$this->renderPartial('/_menus/setup');
?>

<p>Every result set is attached to a cohort. The system is date aware and will only import data for a cohort
 where today's date is between the start and end dates of your cohort. Therefore you cannot create a cohort
  in the future nor in the past.</p>

<?php if(!$model->setUpIsComplete):?>
<div class="alert alert-block">
<h4 class="alert-heading">Cohort Setup is Incomplete</h4>
<p>You must create a new cohort before you can complete the setup. Click <strong>'Create Cohort'</strong> on
the right to create a new cohort.</p>
</div>
<?php $this->widget('ext.eguiders.EGuider', array(
        'id'          => 'create',
        //'next'        => 'second',
        'title'       => 'Create a New Cohort',
        //'buttons'     => array(array('name'=>'Next')),
        'description' => 'Click here to create a new cohort.',
        'attachTo'    => '#create',
        'position'    => 'left',
        //'overlay'     => true,
        //'xButton'     => true,
        // look here !! 'show' is true, so that means this guider will be
        // automatically displayed when the page loads
        'show'        => true,
        'autoFocus'      => true
    )
);?>
<?php endif;?>

<?php $this->renderPartial('_grid',array(
			'model'=>$model,
		));?>
		


