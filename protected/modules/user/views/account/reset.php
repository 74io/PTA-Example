<?php
//Page titles
$this->sectionTitle='Password Reset';
$this->sectionSubTitle='Reset the password for '.$model->username;

//Page breadcrumbs
$this->breadcrumbs=array(
	'Password Reset'
);
?>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>