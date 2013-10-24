<?php
//Page titles
$this->sectionTitle="Create User";
$this->sectionSubTitle="Create a new user";
$this->portletTitle = "Users";
//Page bread crumbs
$this->breadcrumbs=array(
	'Users'=>array('admin'),
	'Create',
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php echo $this->renderPartial('_help'); ?>