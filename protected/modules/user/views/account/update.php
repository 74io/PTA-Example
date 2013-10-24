<?php
//Page titles
$this->sectionTitle=$title;

//Page breadcrumbs
$this->breadcrumbs=array(
	'My Account'=>array('index'),
	$title,
);
?>
<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>