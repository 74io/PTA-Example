<?php
//Page titles
$this->sectionTitle="Register";
$this->sectionSubTitle="Register to use Pupil Tracking Analytics";
//Page bread crumbs
$this->breadcrumbs=array(
	'Register',
);
?>

<?php echo $this->renderPartial('/main/_form', array('model'=>$model)); ?>