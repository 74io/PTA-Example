<?php $this->widget('bootstrap.widgets.TbGridView',array( 
    'id'=>'resultmapping-grid', 
	'beforeAjaxUpdate'=>'function(id,options){ options["data"]+="&"+$(".external-filter").serialize(); }',
    'dataProvider'=>$model->search(), 
	'type'=>'striped',
    'filter'=>$model, 
    'columns'=>array( 
		array(
			'name'=>'id',
			//'value'=>'$data->cohort_id',
			'htmlOptions'=>array('width'=>'40px'),
		),
		array(
			'name'=>'num_records',
			'htmlOptions'=>array('width'=>'40px'),
		),
        array(
           'class' => 'PtEditableColumn',
           'name' => 'name',
           //'headerHtmlOptions' => array('style' => 'width: 110px'),
           'editable' => array(
                  'url'        => $this->createUrl('result/update'),
                  //'placement'  => 'right',
                  'inputclass' => 'span3',
              ),
          //'htmlOptions'=>array('max-width'=>'50px'),               
        ),        
        //'name',
        'file_name',
        array(
			'name'=>'username',
			//'value'=>'$data->user->username',
			'value'=>'$data->user->username',
			//'type'=>'raw',
			//'filter'=>true,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'htmlOptions'=>array('width'=>'50px'),
		),
		array(
			//'class' =>'application.components.PtDateColumn',
			'name'=>'date_time',
			//'format'=>'datetime',
			'filter'=>false,

		),
        
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view} {delete}',//Use template to only display specific buttons
			'htmlOptions'=>array('style'=>'width: 50px;','class'=>'edit-column'),
			
		), 
    ), 
)); ?> 