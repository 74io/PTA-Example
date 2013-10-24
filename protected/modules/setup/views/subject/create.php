<?php
//Page Titles
$this->sectionTitle="Create Subject";
$this->sectionSubTitle="Create a new subject";
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'Subjects'=>array('admin'),
	'Create',
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<!-- Start render help -->
<?php $this->renderPartial('_qualificationsHelp',array(
			'model'=>$model,
		));?>
<!-- End render help -->