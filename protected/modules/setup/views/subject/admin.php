<?php
//Page Titles
$this->sectionTitle="Manage Subjects";
$this->sectionSubTitle="Control how subjects effect reports";
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'Subjects'=>array('admin'),
	'Manage',
);

//Output setup tabs
$this->renderPartial('/_menus/setup');

//Is setup complete. Cache the value
$setUpIsComplete = $model->setUpIsComplete;?>


<?php if(!$setUpIsComplete):?>
<div class="alert alert-block">
<h4 class="alert-heading">Subject Setup is Incomplete</h4>
<p>You must create at least 1 subject for the cohort <strong><?php echo $this->schoolSetUp['defaultCohort'];?></strong></p>
</div>
<div class="alert alert-info">
<h4 class="alert-heading">Creating Subjects:</h4>
<p>You can automatically create subjects for each key stage for cohort <strong><?php echo $this->schoolSetUp['defaultCohort'];?></strong> and edit them appropriately. Alternatively you can create them manually using the <strong>'Create Subject'</strong> link on the right.
<strong>Note</strong>, once you start creating subjects manually the automatic option will no longer be available.</p>
<?php echo CHtml::link('<strong>OK</strong>, Create subjects automatically','autoCreateSubjects',array('class'=>'btn'));?>
</div>
<?php endif;?>

<!-- Start external filter in header -->
<div class="form-actions external-filter-container">
<?php echo CHtml::activeLabel($model, "cohort_id",array("style"=>"display:inline; padding-right:10px; font-weight:bold;"));?>
<?php echo CHtml::activeDropDownList($model, "cohort_id", Yii::app()->common->cohortsDropDown,array(
										'id'=>'cohort-external-filter2',
										'class'=>'external-filter',
										//'prompt'=>'',
										'title'=>'Filter by cohort',
										));?>
<?php echo CHtml::activeLabel($model, "key_stage",array("style"=>"display:inline; padding:0 10px; font-weight:bold;"));?>
<?php echo CHtml::activeDropDownList($model, "key_stage", Yii::app()->common->keyStagesDropDown,array(
										'id'=>'cohort-external-filter1',
										'class'=>'external-filter input-mini',
										//'prompt'=>'',
										'title'=>'Filter by key stage',
										));?>
</div>
<!-- End external filter in header -->



<!-- Start render the grid -->										
<div id="subject-grid-container">			
<?php $this->renderPartial('/subject/_grid',array(
			'model'=>$model,
		));?>
</div>
<!-- End render the grid -->

<!-- Start render help -->
<?php $this->renderPartial('_qualificationsHelp',array(
			'model'=>$model,
			'setUpIsComplete'=>$setUpIsComplete,
		));?>
<!-- End render help -->


<!--Start guided tour-->
<?php if($setUpIsComplete){

//Create a new subject guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'first',
        'next'        => 'second',
        'title'       => 'Create a New Subject',
        'buttons'     => array(
            array('name'=>'Next')
        ),
        'description' => 'Click here whenever you need to create a new subject.',
        'attachTo'    => '#create',
        'position'    => 'left',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Subject name guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'second',
        'next'        => 'third',
        'title'       => 'Edit Subject Name',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("first");}'),
                array('name'=>'Next')
            ),
        'description' => 'Click the <span class="editable">underlined</span> text to edit the subject name. 
        The subject name is the full and unique name for a mapped subject. It is possible to map the same subject code
to multiple subjects provided the subject name is different.',
        'attachTo'    => 'a[rel="Subject_subject"]',
        'position'    => 'bottomLeft',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Qualification guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'third',
        'next'        => 'fourth',
        'title'       => 'Edit Qualification',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("second");}'),
                array('name'=>'Next')
            ),
        'description' => 'Click the <span class="editable">underlined</span> text to edit the qualification.
        Be sure to take note of the accepted results for each qualification that you choose.',
        'attachTo'    => 'a[rel="Subject_qualification"]',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Discount Code guider  
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'fourth',
        'next'        => 'fifth',
        'title'       => 'Edit Discount Code',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("third");}'),
                array('name'=>'Next')
            ),
        'description' => 'Click the <span class="editable">underlined</span> text to edit the discount code.
        The codes can be either DfE codes or your own codes. As long as the codes are identical discounting will be applied.
        If the subject does not need discounting then leave the box empty. See the help section for more details.',
        'attachTo'    => 'a[rel="Subject_discount_code"]',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Volume guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'fifth',
        'next'        => 'sixth',
        'title'       => 'Edit Volume',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("fourth");}'),
                array('name'=>'Next')
            ),
         'description' => 'Click the <span class="editable">underlined</span> text to edit the volume.
         The volume column tells the system the amount of GCSE\'s a subject is worth. Volume is used then viewing reports in \'Pre 2014\' mode.',
        'attachTo'    => 'a[rel="Subject_volume"]',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Equivalent guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'sixth',
        'next'        => 'seventh',
        'title'       => 'Edit Equivalent',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("fifth");}'),
                array('name'=>'Next')
            ),
        'description' => 'Click the <span class="editable">underlined</span> text to edit the equivalent. 
        The equivalent column tells the system whether the subject is equivalent to a full GCSE(1) or not(0). 
        Equivalent is used then viewing reports in \'2014 Onwards\' mode.',
        'attachTo'    => 'a[rel="Subject_equivalent"]',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Type guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'seventh',
        'next'        => 'eighth',
        'title'       => 'Edit Type',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("sixth");}'),
                array('name'=>'Next')
            ),
        'description' => 'Click the <span class="editable">underlined</span> text to edit the type. 
        The type column is used when calculating English and Maths measures as well as the English Baccalaureate.
        This <strong>column must be completed</strong> before reports will display accurate results. See the help section
        for more details.
        ',
        'attachTo'    => 'a[rel="Subject_type"]',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Classes guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'eighth',
        'next'        => 'ninth',
        'title'       => 'Filter Classes',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("seventh");}'),
                array('name'=>'Next')
            ),
        'description' => 'You can click the link here to exclude entire classes or individual pupils from classes. 
        When this column displays \'All\' it means that all pupils and classes are included. When this column displays \'Filtered\'
         it means that at least one class or pupil has been excluded.
        ',
        'attachTo'    => '.filter-column',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Include guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'ninth',
        'next'        => 'tenth',
        'title'       => 'Edit Include',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("eighth");}'),
                array('name'=>'Next')
            ),
        'description' => 'Whether to include the subject in reports or not.',
        'attachTo'    => '.include-checkbox',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

//Filters guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'tenth',
        'next'        => 'eleventh',
        'title'       => 'Filters',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("ninth");}'),
                array('name'=>'Close')
            ),
        'description' => 'Use the filters here and above to control the displayed results.
        If you think that results are missing check that you do not have any filters applied.',
        'attachTo'    => '.filter-container',
        'position'    => 'bottomLeft',
        'xButton'     => true,
        'autoFocus'      => true
    )
);
}
?>




<?php Yii::app()->clientScript->registerScript('cohort-external-filter', "
$('.external-filter').change(function(){
        		data1=$(this).serialize();
        		data2=$('#subject-grid input,select').serialize();
        		data=data1+'&'+data2;
        $.fn.yiiGridView.update('subject-grid', {
                data: data,
        });
        return false;
});
");

Yii::app()->clientScript->registerScript('subject-grid-script', '
	function updateInclude(el){
		var id=$(el).data("include-id");
		var keyStage=$(el).data("key-stage");
		var cohortId=$(el).data("cohort-id");
		var val = ($(el).is(":checked")) ? 1 : 0;
		$.ajax({
		  url: "ajaxUpdateInclude",
		  type: "POST",
		  data: {id : id, value:val, keyStage:keyStage, cohortId:cohortId},
		});
	}
',CClientScript::POS_HEAD);?>



