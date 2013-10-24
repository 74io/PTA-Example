<?php
$this->sectionTitle="Update Subject";
$this->sectionSubTitle="Update subject #".$model->id;
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'Subjects'=>array('admin'),
	'Update',
);?>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>
<!-- Start render help -->
<?php $this->renderPartial('_qualificationsHelp',array(
			'model'=>$model,
		));?>
<!-- End render help -->