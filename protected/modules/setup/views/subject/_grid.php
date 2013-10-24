<?php 
$templateHtml = '
				<form class="form-inline" autocomplete="off">
				       <div class="accepted-results alert alert-info"></div>
                       <div class="control-group">
                       &nbsp;<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i></button>&nbsp;<button type="button" class="btn editable-cancel"><i class="icon-ban-circle"></i></button>
                       <span class="help-block" style="clear: both"></span>
                       </div>
                       </form>';

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'subject-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	//'filter'=>($model->itemCount ? $model : null),
	'beforeAjaxUpdate'=>'function(id,options){ options["data"]+="&"+$(".external-filter").serialize(); }',
	//'afterAjaxUpdate'=>'function(id,data){$(".alert").hide();}',
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'id',
			'htmlOptions'=>array('width'=>'20px'),
		),
		array(
			'name'=>'mapped_subject',
			'filter'=>Yii::app()->common->getSubjectsDropDown($model->cohort_id),
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'htmlOptions'=>array('width'=>'100px'),
		),
		array(
           'class' => 'PtEditableColumn',
           'name' => 'subject',
		   'header' => 'Subject Name',
           'editable' => array(
                  'url'        => $this->createUrl('subject/editableupdate'),
                  'inputclass' => 'span3',
              )               
        ),
        array( 
              'class' => 'PtEditableColumn',
              'name' => 'qualification',
        	  'filter'=>Yii::app()->common->qualificationsDropDown,
              //'value' => CHtml::value($model, 'status.status_text'),
              //'headerHtmlOptions' => array('style' => 'width: 100px'),
              'editable' => array(
        		 'options' => array('formTemplate'=>$templateHtml),
                  'type'     => 'select',
                  'url'      => $this->createUrl('subject/editableupdate'),
                  'source'   => Yii::app()->common->qualificationsDropDownJson,
        		  'inputclass' => 'span3',
				  'onShown' => 'js: function(e, editable) {
					   getAcceptedResults(editable.value);
			
					   var select = editable.$input;
					   $(select).change(function(e){
					   		var qual = $(select).find("option:selected").text();
							getAcceptedResults(qual);
					   });
					   
					   function getAcceptedResults(qual)
					   {
					   		$.get("/setup/subject/getacceptedResults", { qualification: qual}, function(data) {
  							$(".accepted-results").html(data);
							});
					   }
					}'
				)        
              ),
		array(
           'class' => 'PtEditableColumn',
           'name' => 'discount_code',
           'filter'=>Yii::app()->common->getDiscountCodesDropDown($model->cohort_id),
		   //'header' => 'Discount Code',
           'editable' => array(
                  'url'        => $this->createUrl('subject/editableupdate'),
                  'inputclass' => 'span2',
              )               
        ),
        array( 
              'class' => 'PtEditableColumn',
              'name' => 'volume',
			  'filter'=>Yii::app()->common->volumeIndicatorsDropDown,
              'editable' => array(
                  'type'    => 'select',
                  'url'     => $this->createUrl('subject/editableupdate'),
                  'source'  => Yii::app()->common->volumeDropDownJson,
                  'inputclass' => 'span1',
              )
         ),
        array( 
              'class' => 'PtEditableColumn',
              'name' => 'equivalent',
			  'filter'=>Yii::app()->common->equivalentsDropDown,
              'editable' => array(
                  'type'    => 'select',
                  'url'     => $this->createUrl('subject/editableupdate'),
                  'source'  => Yii::app()->common->equivalentsDropDownJson,
                  'inputclass' => 'span1',
              )
         ),

        array( 
              'class' => 'PtEditableColumn',
              'name' => 'type',
			  'filter'=>Yii::app()->common->subjectTypesDropDown,
              'editable' => array(
                  'type'    => 'select',
                  'url'     => $this->createUrl('subject/editableupdate'),
                  'source'  => Yii::app()->common->subjectTypesDropDownJson,
                  'inputclass' => 'span2',
              )
         ),
		array(
			'header'=>'Classes',
			'value'=>array($model,'renderSetsColumn'), 
			'type'=>'raw',//Required to output as HTML i.e. not HTML encoded
			'filter'=>false,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			//'sortable'=>false,
			//'htmlOptions'=>array('width'=>'10px'),
		),
		array(
			'header'=>'Include',
			'value'=>'CHtml::checkBox("include[]", $data->include,
				array("id"=>"include-$data->id",
					"data-include-id"=>$data->id,
					"data-key-stage"=>$data->key_stage,
					"data-cohort-id"=>$data->cohort_id,
					"class"=>"include-checkbox",
					"onclick"=>"js:updateInclude(this);"))',
			'type'=>'raw',//Required to output as HTML i.e. not HTML encoded
			//'filter'=>Yii::app()->common->yearGroupsDropDown,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			'sortable'=>true,
			//'class' => 'CDataColumn',
			//'htmlOptions'=>array('width'=>'10px'),
		),	
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px;','class'=>'edit-column'),
			//'template'=>'{update}'//Use template to only display specific buttons
			
		),
	),
));


