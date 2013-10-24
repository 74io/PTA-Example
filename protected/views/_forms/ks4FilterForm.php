<a href='#' id='pin-filter' class='pull-right' rel='tooltip' title='Pin/Unpin Filter'><i class="icon-pushpin"></i></a>
<div class='filter-form'>
<?php
//Note. If you don't use $this->route for the form action the $_GET vars just get re-appeneded to the URL.
echo CHtml::beginForm('/'.$this->route,'get',array('id'=>'filter-form'));
echo CHtml::activeLabel($model, "gender");
echo CHtml::activeDropDownList($model, "gender", Yii::app()->common->getPupilFilterDropDown('gender',$model->cohortId),array(
			'multiple'=>'multiple',
			'title'=>'Gender',
			'rel'=>'tooltip',
			//'prompt'=>'',
		 	'class'=>'input-small multiselect',
			));
echo CHtml::activeLabel($model, "ethnicity");
echo CHtml::activeDropDownList($model, "ethnicity", Yii::app()->common->getPupilFilterDropDown('ethnicity',$model->cohortId),array(
			'multiple'=>'multiple',
			'title'=>'Ethnicity',
			'rel'=>'tooltip',
			//'prompt'=>'',
		 	'class'=>'input-small multiselect',
			));
echo CHtml::activeLabel($model, "sen_code");			
echo CHtml::activeDropDownList($model, "sen_code", Yii::app()->common->getPupilFilterDropDown('sen_code',$model->cohortId),array(
			'multiple'=>'multiple',
			'title'=>'SEN Code',
			'rel'=>'tooltip',
			//'prompt'=>'',
		 	'class'=>'input-small multiselect',
			));
echo CHtml::activeLabel($model, "fsm");			
echo CHtml::activeDropDownList($model, "fsm", Yii::app()->common->getPupilFilterDropDown('fsm',$model->cohortId),array(
			'multiple'=>'multiple',
			'title'=>'Free School Meals',
			'rel'=>'tooltip',
			//'prompt'=>'',
		 	'class'=>'input-small multiselect',
			));
echo CHtml::activeLabel($model, "gifted");			
echo CHtml::activeDropDownList($model, "gifted", Yii::app()->common->getPupilFilterDropDown('gifted',$model->cohortId),array(
			'multiple'=>'multiple',
			'title'=>'Gifted',
			'rel'=>'tooltip',
			//'prompt'=>'',
		 	'class'=>'input-small multiselect',
			));
echo CHtml::activeLabel($model, "cla");			
echo CHtml::activeDropDownList($model, "cla", Yii::app()->common->getPupilFilterDropDown('cla',$model->cohortId),array(
			'multiple'=>'multiple',
			'title'=>'Child Looked After',
			'rel'=>'tooltip',
			//'prompt'=>'',
		 	'class'=>'input-small multiselect',
			));
			
echo CHtml::activeLabel($model, "eal");			
echo CHtml::activeDropDownList($model, "eal", Yii::app()->common->getPupilFilterDropDown('eal',$model->cohortId),array(
	        'multiple'=>'multiple',
			'title'=>'English as an Additional Language',
			'rel'=>'tooltip',
			//'prompt'=>'',
		 	'class'=>'input-small multiselect',
			));
echo CHtml::activeLabel($model, "pupil_premium");			
echo CHtml::activeDropDownList($model, "pupil_premium", Yii::app()->common->getPupilFilterDropDown('pupil_premium',$model->cohortId),array(
	        'multiple'=>'multiple',
			'title'=>'Pupil Premium',
			'rel'=>'tooltip',
			//'prompt'=>'',
		 	'class'=>'input-small multiselect',
			));
echo CHtml::hiddenField("Ks4FF[oldYearGroup]",$model->yearGroup);
echo CHtml::hiddenField("Ks4FF[oldCohortId]",$model->cohortId);
?>
<div>	
<br>	
<?php echo CHtml::submitButton('Apply Filter',array('class'=>'btn btn-small btn-primary',
								'id'=>'submit-button',
										//'submit'=>'',
										));?>
<br><br>
<? 
echo CHtml::link('Reset Filter','/'.$this->route);
?>
</div>	
<?php 										
echo CHtml::endForm();
?>
</div>
<!-- See theme column1 for bootstrap affix inline javascript -->
<?php

Yii::app()->clientScript->registerScriptFile("/js/multiselect/bootstrap-multiselect.js"); 
Yii::app()->clientScript->registerScriptFile("/js/ismobile.js"); 

Yii::app()->clientScript->registerScript('filter-form-script', "
if(!isMobile.any()){


$('.multiselect').multiselect({
  buttonClass: 'btn btn-mini',
  buttonWidth: '100px',
  maxHeight: '300',
  buttonText: function(options) {
    if (options.length == 0) {
      return ' <b class=\'caret\'></b>';
    }
    else if (options.length > 5) {
      return options.length + ' ...  <b class=\'caret\'></b>';
    }
    else {
      var selected = '';
      options.each(function() {
        selected += $(this).text() + '<br>  ';
      });
      return selected.substr(0, selected.length -2) + ' <b class=\'caret\'></b>';
    }
  },
  onChange: function(element, checked) {
  		var el=element.context;
  		if($(el).find('option:selected').length>0){
		$(el).next().find('button').addClass('btn-info');
		}
		else {
		$(el).next().find('button').removeClass('btn-info');	
		}
		checkFilterHeight();
      },
});

//Colour the checkboxes
$('.filter-form select.multiselect').each(function (){
  if($(this).find('option:selected').length>0){
	$(this).next().find('button').addClass('btn-info');
}

});
}
//Set the affix on the filter
var stickyFilter  = $('.sticky-ks4filter');
if(stickyFilter.length){
stickyFilter.affix({
offset: { top: stickyFilter.offset().top-40}
});

$('#pin-filter').on('click',function(){
	stickyFilter.toggleClass('on');
	$(this).toggleClass('pin-off');
	return false;
})
}


");

