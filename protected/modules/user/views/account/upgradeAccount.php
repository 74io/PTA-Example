<?php
//Page titles
$this->sectionTitle="Upgrade to Premium";

//Page breadcrumbs
$this->breadcrumbs=array(
	'My Account'=>array('index'),
	$title,
);
?>
<p>Upgrading your account to premuim will enable you to create unlimited users and up to 10 data collection
points and 10 targets per year group.

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>