<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('cohort_id')); ?>:</b>
	<?php echo CHtml::encode($data->cohort_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('key_stage')); ?>:</b>
	<?php echo CHtml::encode($data->key_stage); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mapped_subject')); ?>:</b>
	<?php echo CHtml::encode($data->subject); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('subject')); ?>:</b>
	<?php echo CHtml::encode($data->subject_alias); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('qualification')); ?>:</b>
	<?php echo CHtml::encode($data->qualification); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('volume')); ?>:</b>
	<?php echo CHtml::encode($data->volume); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('include')); ?>:</b>
	<?php echo CHtml::encode($data->include); ?>
	<br />

	*/ ?>

</div>