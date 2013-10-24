<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('cohort_id')); ?>:</b>
	<?php echo CHtml::encode($data->cohort_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dcp_no')); ?>:</b>
	<?php echo CHtml::encode($data->dcp_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mapped_field')); ?>:</b>
	<?php echo CHtml::encode($data->mapped_field); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('year_group')); ?>:</b>
	<?php echo CHtml::encode($data->year_group); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('key_stage')); ?>:</b>
	<?php echo CHtml::encode($data->key_stage); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
	<?php echo CHtml::encode($data->date); ?>
	<br />


</div>