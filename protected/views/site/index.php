<?php 

$this->breadcrumbs=array(
	'',
);
?>

<div class="row">
	<div class="span12 intro-wrapper" style="position:relative;">
		<img class="pull-right" src="/images/busybirds-color.png">
	</div>
	<div class="intro">
		<h1>Welcome to Pupil Tracking Analytics</h1>
	<div class="btn-toolbar">
<?php
if($this->schoolSetUp['defaultCohort']){
	 if(FieldMapping::getSetUpIsComplete("target")){
			$this->widget('bootstrap.widgets.TbButton', array(
    		'label'=>'KS4 Summary',
			'encodeLabel'=>false,
		    'type'=>'primary',
		    'size'=>'large',
			'url'=>'/ks4',
			));

			$this->widget('bootstrap.widgets.TbButton', array(
    		'label'=>'KS4 Breakdown',
			'encodeLabel'=>false,
		    'type'=>'primary',
		    'size'=>'large',
			'url'=>'/ks4/breakdown',
			));

			$this->widget('bootstrap.widgets.TbButton', array(
    		'label'=>'KS4 Subject',
			'encodeLabel'=>false,
		    'type'=>'primary',
		    'size'=>'large',
			'url'=>'/ks4/subject',
			));

		
	}
	else{
		$this->widget('bootstrap.widgets.TbButton', array(
    		'label'=>'Get Started',
			'encodeLabel'=>false,
		    'type'=>'primary',
		    'size'=>'large',
			'url'=>'/setup',
			));
	}
}
else{
		$this->widget('bootstrap.widgets.TbButton', array(
		'label'=>'Get Started',
		'encodeLabel'=>false,
	    'type'=>'primary',
	    'size'=>'large',
		'url'=>'/setup',
		));

}?>
</div>

<div class="alert alert-info" style="margin-top:20px;">
<p><strong>Tip.</strong> Look out for the <i class="icon-play-circle"></i> <a href='#' class='start-tour'><strong>Take a Guided Tour</strong></a>
link at the base of some pages. It will help you get started.</p>
</div>

</div>
</div>
<?$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'first',
        'title'       => 'I\'m a guided tour link',
        'description' => 'Look out for me at the base of some pages. I will take you on a step by step tour and help you get started.',
        'attachTo'    => '.start-tour',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);?>

<?php Yii::app()->clientScript->registerScript('home-eguiders', "
//Event to start eguiders tour
$('.start-tour').click(function(){
    guiders.show('first');
  });
");

