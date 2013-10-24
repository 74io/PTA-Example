<?php $form=$this->beginWidget('ext.bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<?php echo $form->textFieldBlock($model,'id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldBlock($model,'username',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->textFieldBlock($model,'role',array('class'=>'span5','maxlength'=>30)); ?>

	<?php echo $form->textFieldBlock($model,'salt',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->textFieldBlock($model,'email',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->textAreaBlock($model,'profile',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>


	<div class="actions">
		<?php echo CHtml::submitButton('Search',array('class'=>'btn primary')); ?>
	</div>

<?php $this->endWidget(); ?>
