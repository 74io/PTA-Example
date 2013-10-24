<?php
//Page titles
$this->sectionTitle="Import Results";
$this->sectionSubTitle="Import a new result set";
//Page bread crumbs
$this->breadcrumbs=array(
	'Results'=>array('admin'),
	'Import',
);?>

<p>Download our report definition to easily export data from SIMS in the correct format.
See the help section below for full details.</p>

<?php $this->widget('ext.xupload.XUpload', array(
                    'url' => Yii::app()->createUrl("setup/result/upload"),
                    'model' => $model,
                    'attribute' => 'file',
                    'multiple' => false,
					'formView'=>'application.modules.setup.views.result.form',
					'uploadView'=>'application.modules.setup.views.result.upload',
					'downloadView'=>'application.modules.setup.views.result.download',
					'options'=>array(
						'maxNumberOfFiles'=>1,
						//'acceptFileTypes'=>'/(\.|\/)(gif|jpe?g|png)$/i',
						//'previewSourceFileTypes'=>'/(\.|\/)(gif|jpe?g|png)$/i',
						//'previewMaxHeight'=>80,
						//'autoUpload'=>true,
						//'filesContainer'=>'#file-container',
					/*	
					'submit' => "js:function (e, data) {
                    	//data.formData = $('form').serializeArray();
                    	return true;
                	}",*/
					),
));
?>



<!-- Start render help -->
<?php $this->renderPartial('_importHelp');?>
<!-- End render help -->



<?php 
/* Yii::app()->clientScript->registerScript('tooltip', "
$.widget.bridge('boottooltip', $.tooltip);
$('input, textarea').boottooltip({placement:'right'});
");*/?>







 





