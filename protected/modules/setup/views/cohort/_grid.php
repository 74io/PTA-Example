<?php
//Begin Widgets
$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'cohort-grid',
	'dataProvider'=>$model->search(),

	//'filter'=>$model
	//'rowCssClassExpression' => "(\$data->default) ? 'default-cohort' :''",
	'columns'=>array(
		'id:html:Cohort ID',
		array(
			'class' =>'application.components.PtDateColumn',
			'name'=>'term_start_date',
			'format'=>'date',
			'filter'=>false,

		),
		array(
			'class' =>'application.components.PtDateColumn',
			'name'=>'term_end_date',
			'format'=>'date',
			'filter'=>false,

		),
		array(
			'name' => 'default',
			'header' => 'Default Cohort',
			'value' => array(Yii::app()->common,'gridDefaultColumn'),
			//'cssClassExpression' => "(\$data->default) ? 'default-cohort' :''",
			'type' => 'html',
			//'value'=>'$data->role',
			//'filter'=>$model->something(),
			//'htmlOptions'=>array("class"=>"defualt-column"),
			'sortable'=>true,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
            'htmlOptions'=>array('style'=>'width: 50px;','class'=>'edit-column'),
			'template'=>'{update}'//Use template to only display specific buttons
			
		),
	),
)); ?>