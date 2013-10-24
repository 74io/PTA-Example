<?php
//Page Titles
$this->sectionTitle=$this->controllerSectionTitle."s";
$this->sectionSubTitle=($this->id=="dcp") ? "Data collection points" :"";
$this->portletTitle = $this->controllerSectionTitle."s";
//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	$this->controllerSectionTitle.'s'=>array('admin'),
	'Manage',
);


//Output setup tabs
$this->renderPartial('/_menus/setup');?>

<?php if($this->id=="dcp"):?>
<p>A data collection point (DCP) refers to each time you collect results from staff regarding the currently working at level/grade of pupils.
When viewing reports DCPs can be compared to targets as well as each other.</p>
<?php else:?>
<p> A target refers to results pupils should be achieving. When viewing reports targets can be compared to DCPs as well as each other.</p>
<?php endif;?>



<!-- Start Modal -->
<?php echo $this->renderPartial('/dcp/_modal'); ?>
<!-- End Modal -->

<?php if($displayResultInfo):?>
<?php echo $this->renderPartial('/dcp/_resultsInfo', array('model'=>$model)); ?>
<?php endif;?>

<?php if(!$model->getSetUpIsComplete(Yii::app()->controller->id)):?>
<div class="alert alert-block">
<h4 class="alert-heading"><?php echo $this->controllerSectionTitle;?> Setup is Incomplete</h4>
<p>You must create at least one <strong>default</strong> <?php echo $this->controllerSectionTitle;?> for each year group in the cohort <strong><?php echo $this->schoolSetUp['defaultCohort'];?></strong>. Click <strong>'Create <?php echo $this->controllerSectionTitle;?>'</strong>
 on the right to create new entries. Be sure to mark a <strong>default</strong> for each year group.</p>
</div>


<?php $this->widget('ext.eguiders.EGuider', array(
        'id'          => 'create',
        //'next'        => 'second',
        'title'       => 'Create a New '.$this->controllerSectionTitle,
        //'buttons'     => array(array('name'=>'Next')),
        'description' => 'Click here to create a new '.$this->controllerSectionTitle.'. Be sure to create a default for each year group.',
        'attachTo'    => '#create',
        'position'    => 'left',
        //'overlay'     => true,
        //'xButton'     => true,
        // look here !! 'show' is true, so that means this guider will be
        // automatically displayed when the page loads
        'show'        => true,
        'autoFocus'      => true
    )
);?>
<?php endif;?>


<div class="form-actions external-filter-container">
<?php echo CHtml::activeLabel($model, "cohort_id",array("style"=>"display:inline; padding-right:10px; font-weight:bold;"));?>
<?php echo CHtml::activeDropDownList($model, "cohort_id", Yii::app()->common->cohortsDropDown,array(
										'id'=>'cohort-external-filter',
										'class'=>'span2',
										//'prompt'=>'',
									
										));?>
</div>

<?php $this->renderPartial('/dcp/_grid',array(
			'model'=>$model,
));?>



<!-- Start render help -->
<?php $this->renderPartial('/dcp/_help',array(
			'model'=>$model,
		));?>
<!-- End render help -->

<?php 
Yii::app()->clientScript->registerScript('cohort-external-filter', "
$('#cohort-external-filter').change(function(){
        		data1=$(this).serialize();
        		data2=$('#dcp-grid input,select').serialize();
        		data=data1+'&'+data2;
        $.fn.yiiGridView.update('dcp-grid', {
                data: data,
        });
        return false;
});


$('.main').on('click', '.rebuild', function(event){
$(this).parent().parent().parent()
.find('.verify').attr('disabled',true).text('Building...')
.click(false)
.next().attr('disabled',true);
$(this).click(false);
});


$('.main').on('click', '.verify', function(event){
var data = $(this).data();
$('#verify-tabs').data({'id':data.id});
$('.modal-header h3').text(data.field);


$('#modal').modal().css({
       'width': function () { 
           return ($(document).width() * .8) + 'px';  
       },
       'height': function () { 
           return ($(window).height() * .8) + 'px';   
       },
       'margin-left': function () { 
           return -($(this).width() / 2); 
       },
       'margin-top': function () { 
           return -($(this).height() / 2);
       }
});


$('#verify-tabs a:first').trigger('click');
event.preventDefault();
});


$('.modal a[data-toggle=\"tab\"]').on('shown', function (e) {
  var data = $('#verify-tabs').data();
  var url =  e.target.hash.replace('#','');
 $.get(url+'?id='+data.id, function(response) {
 $(e.target.hash).html(response);
  });
})

$('body').on('hidden', '.modal', function () {
  $(this).removeData('modal');
  $('#verify-tabs li:first').removeClass('active');//Remove the active class so the trigger event will fire
});

");
?>
