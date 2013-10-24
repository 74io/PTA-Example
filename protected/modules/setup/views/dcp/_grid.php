<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'dcp-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	//Note here that we have appeneded data to the original data options for the ajax request
	'beforeAjaxUpdate'=>'function(id,options){ options["data"]+="&"+$("#cohort-external-filter").serialize(); }',
	//'afterAjaxUpdate'=>'function(id,data){$(".alert").hide();}',
	'columns'=>array(
		  array(
    			'name'=>'id',
    			//'value'=>'asdf',
    			'htmlOptions'=>array('width'=>'30px'),
		  		'filter' => false,
  		),
  		/*
		array(
			'name'=>'cohort_id',
			//'value'=>'2011-2012',
			'filter'=>Yii::app()->common->cohortsDropDown,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'htmlOptions'=>array('width'=>'100px'),
			'visible'=>false,
		),*/
		
		array(
			'name'=>'year_group',
			//'value'=>'$data->cohort_id',
			'filter'=>Yii::app()->common->yearGroupsDropDown,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			'htmlOptions'=>array('width'=>'80px'),
		),
        array(
           'class' => 'PtEditableColumn',
           'name' => 'mapped_alias',
           //'headerHtmlOptions' => array('style' => 'width: 110px'),
           'editable' => array(
                  'url'        => $this->createUrl($this->id.'/editableupdate'),
        		  'inputclass' => 'span3',
              )               
        ), 
       array( 
              'class' => 'PtEditableColumn',
              'name' => 'date',
         	//'type'=>'raw',
              //'headerHtmlOptions' => array('style' => 'width: 100px'),
              'editable' => array(
                  'type'          => 'date',
                  'format'    => 'dd-mm-yyyy',
                  'url'           => $this->createUrl($this->id.'/editableupdate'),
                 // 'placement'     => 'right',
              )
         ), 
		'mapped_field',//Result Set


		array(
			'name' => 'default',
			'header' => 'Default',
			'value' => array(Yii::app()->common,'gridDefaultColumn'),
			//'cssClassExpression' => "(\$data->default) ? 'default-cohort' :''",
			'type' => 'html',
			//'value'=>'$data->role',
			'filter'=>false,
			//'htmlOptions'=>array("class"=>"defualt-column"),
			'sortable'=>true,
		),
		
		array(
			//'class' =>'application.components.PtDateColumn',
			'name'=>'last_built',
			//'format'=>'datetime',
			'type'=>'raw',
			'filter'=>false,
			'htmlOptions'=>array('width'=>'80px'),
		),
		
		array(
			'header'=>'Requires Rebuild?',
			'value'=>array($model,'renderMissingPupilsColumn'), 
			'type'=>'raw',//Required to output as HTML i.e. not HTML encoded
			'filter'=>false,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'htmlOptions'=>array('width'=>'200px'),
			'sortable'=>true,
		),
		
		array(
			'header'=>'',
			'value'=>array($model,'renderButton'), 
			'type'=>'raw',
		),

		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
            'htmlOptions'=>array('style'=>'width: 50px'),
			'template'=>'{update} {delete}',//Use template to only display specific buttons
			'htmlOptions'=>array('class'=>'edit-column'),
			
		),
	),
)); ?>
