<?php $this->pageTitle=Yii::app()->name;
//Page titles
$this->sectionTitle="Setup";
$this->sectionSubTitle="Follow the steps below to setup your system";

//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup/'),
	'Manage',
);
$setup=array();
?>

<p>Follow the steps below completing one step at a time. You can return 
to this screen at any time to check your overall progress.</p>
<div id="setup-container">
<!-- Step 1 My School -->
<h4>Step 1</h4>
<?php if(Yii::app()->controller->schoolSetUp===null):?>
<?php $setup['step1']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">My School Setup is Incomplete</h4>
<p>Some basic information is required here.</p>
<?php echo CHtml::link('<strong>Set this up now</strong>',array('myschool/admin'),array('class'=>'btn'));?>
</div>
<?php else:?>
<?php $setup['step1']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">My School Setup is Complete!</h4>
<?php echo CHtml::link('<strong>Update</strong>',array('myschool/admin'),array('class'=>'btn'));?>
</div>

<!-- End Step 1 -->


<!-- Step 2 Cohorts -->
<h4>Step 2</h4>
<?php if(!Cohort::getSetUpIsComplete()):?>
<?php $setup['step2']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">Cohort Setup is Incomplete</h4>
<p>You must create a new cohort.</p>
<?php 
$disabled = ($setup['step1']) ? "" : "disabled";
echo CHtml::link('<strong>Set this up now</strong>',array('cohort/admin'),array('class'=>'btn '.$disabled));?>
</div>
<?php else:?>
<?php $setup['step2']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">Cohort Setup is Complete!</h4>
<?php echo CHtml::link('<strong>Update</strong>',array('cohort/admin'),array('class'=>'btn'));?>
</div>
<?php endif;?>
<!-- End Step 2-->


<!-- Step 3 Indicators -->
<h4>Step 3</h4>
<?php if(!Indicator::getSetUpIsComplete()):?>
<?php $setup['step3']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">Filter Setup is Incomplete</h4>
<p>You have filter fields missing from your system.</p>
<?php 
$disabled = ($setup['step2']) ? "" : "disabled";
echo CHtml::link('<strong>Set this up now</strong>',array('indicator/admin'),array('class'=>'btn '.$disabled));?>
</div>
<?php else:?>
<?php $setup['step3']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">Filter Setup is Complete!</h4>
<?php echo CHtml::link('<strong>Update</strong>',array('indicator/admin'),array('class'=>'btn'));?>
</div>
<?php endif;?>
<!-- End Step 3-->


<!-- Step 4 Key Stage -->
<h4>Step 4</h4>
<?php if(!KeyStage::getSetUpIsComplete()):?>
<?php $setup['step4']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">Key Stage Setup is Incomplete</h4>
<p>You must enter field mappings for KS2 English, Maths and Science.</p>
<?php 
$disabled = ($setup['step3']) ? "" : "disabled";
echo CHtml::link('<strong>Set this up now</strong>',array('keystage/admin'),array('class'=>'btn '.$disabled));?>
</div>
<?php else:?>
<?php $setup['step4']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">Key Stage Setup is Complete!</h4>
<?php echo CHtml::link('<strong>Update</strong>',array('keystage/admin'),array('class'=>'btn'));?>
</div>
<?php endif;?>
<!-- End Step 4-->




<!-- Step 5 Build Core Data -->
<h4>Step 5</h4>
<?php if(!Yii::app()->build->setUpIsComplete):?>
<?php $setup['step5']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">Core data has not yet been built</h4>
<p>You need to build the core data.</p>
<?php 
$disabled = ($setup['step4']) ? "" : "disabled";
echo CHtml::link('<strong>Set this up now</strong>',array('build/admin'),array('class'=>'btn '.$disabled));?>
</div>
<?php else:?>
<?php $setup['step5']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">Core Data Setup is Complete!</h4>
<?php echo CHtml::link('<strong>Update</strong>',array('build/admin'),array('class'=>'btn'));?>
</div>
<?php endif;?>
<!-- End Step 3-->


<!-- Step 6 Subjects -->
<h4>Step 6</h4>
<?php if(!Subject::getSetUpIsComplete()):?>
<?php $setup['step6']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">Subject Setup is Incomplete</h4>
<p>You must create at least 1 subject for the default cohort.</p>
<?php 
$disabled = ($setup['step5']) ? "" : "disabled";
echo CHtml::link('<strong>Set this up now</strong>',array('subject/admin'),array('class'=>'btn '.$disabled));?>
</div>
<?php else:?>
<?php $setup['step6']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">Subject Setup is Complete!</h4>
<?php echo CHtml::link('<strong>Update</strong>',array('subject/admin'),array('class'=>'btn'));?>
</div>
<?php endif;?>
<!-- End Step 6-->


<!-- Step 7 DCPs -->
<h4>Step 7</h4>
<?php if(!FieldMapping::getSetUpIsComplete("dcp")):?>
<?php $setup['step7']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">DCP Setup is Incomplete</h4>
<p>You must create at least 1 data collection point (DCP) for each year group in the default cohort.</p>
<?php 
$disabled = ($setup['step6']) ? "" : "disabled";
echo CHtml::link('<strong>Set this up now</strong>',array('dcp/admin'),array('class'=>'btn '.$disabled));?>
</div>
<?php else:?>
<?php $setup['step7']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">DCP Setup is Complete!</h4>
<?php echo CHtml::link('<strong>Update</strong>',array('dcp/admin'),array('class'=>'btn'));?>
</div>
<?php endif;?>
<!-- End Step 7-->


<!-- Step 8 Targets -->
<h4>Step 8</h4>
<?php if(!FieldMapping::getSetUpIsComplete("target")):?>
<?php $setup['step8']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">Target Setup is Incomplete</h4>
<p>You must create at least 1 target for each year group in the default cohort.</p>
<?php 
$disabled = ($setup['step7']) ? "" : "disabled";
echo CHtml::link('<strong>Set this up now</strong>',array('target/admin'),array('class'=>'btn '.$disabled));?>
</div>
<?php else:?>
<?php $setup['step8']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">Target Setup is Complete!</h4>
<?php echo CHtml::link('<strong>Update</strong>',array('target/admin'),array('class'=>'btn'));?>
</div>
<?php endif;?>
<!-- End Step 8-->


<!-- View Reports-->
<h4>Step 9</h4>
<?php if(!FieldMapping::getSetUpIsComplete("target")):?>
<?php $setup['step9']=false;?>
<div class="alert alert-warning">
<h4 class="alert-heading">View Reports</h4>
<p>Once setup is complete you can view reports.</p>
<?php 
$disabled = ($setup['step8']) ? "" : "disabled";
echo CHtml::link('<strong>View</strong>',array('/ks4'),array('class'=>'btn '.$disabled));?>
</div>
<?php else:?>
<?php $setup['step9']=true;?>
<div class="alert alert-success">
<h4 class="alert-heading">View Reports</h4>
<?php echo CHtml::link('<strong>View</strong>',array('/ks4'),array('class'=>'btn'));?>
</div>
<?php endif;?>
<!-- End Step 9-->

<?php endif;?>
</div>


