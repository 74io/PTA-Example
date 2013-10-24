<?php
$this->sectionTitle="Tutorials";
$this->sectionSubTitle="Watch tutorial screen casts";
$this->breadcrumbs=array(
  'Tutorials',
);?>

<p>The screen casts are designed to be watched in <strong>HD</strong> and <strong>full screen mode.</strong></p>

<div class="span6">

<div class="video-wrapper">
	<a id="importing-results"></a>
	<h3>Importing Results</h3>
	<p>SIMS users. Learn how to import results to analytics.</p>
	<div class="js-video">
	<iframe src="https://player.vimeo.com/video/70112230" width="500" height="281" frameborder="0" 
	webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	</div>
</div>

<div class="video-wrapper">
	<a id="exporting-results-from-sims"></a>
	<h3>Exporting Results From SIMS.Net</h3>
	<p>SIMS users. Learn how to export results from SIMS using our report definition.</p>
	<div class="js-video">
	<iframe src="https://player.vimeo.com/video/70054430" width="500" height="281" frameborder="0" 
	webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	</div>
</div>

</div><!--End span6-->


<?php
Yii::app()->clientScript->registerScriptFile("/js/jquery.fitvids.js");
Yii::app()->clientScript->registerScript('fitvids-script', '
$(".js-video").fitVids();
',CClientScript::POS_END);
?>


