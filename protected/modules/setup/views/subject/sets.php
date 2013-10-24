<?php

/**
 * 	$yearGroups, $dataProvider, $filteredSets and $model are available to this view
 */
//Page Titles
$this->sectionTitle=$model->subject;
$this->sectionSubTitle="Filter Classes and Pupils";
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	'Subjects'=>array('admin'),
	'Filter Classes and Pupils',
);?>
<p>Filter classes from subjects or individual pupils from classes. Saving is done instantly upon checking/unchecking. 
	Pupils highlighted in <span class='already-excluded'>blue</span> have already been excluded from sets mapped to the same subject. When you are finished click Done.</p>
<!-- Exclude sets content -->
<div class="row">
<div class="span4">
<h2 class="span4"><small>Exclude Entire Classes:</small></h2>

<!-- Start outputting check boxes -->
<?php
$count=1;
foreach($yearGroups as $year){
	$content = $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider[$year],
	'itemsTagName'=>'ul',
	'itemsCssClass'=>'unstyled',
	'itemView'=>'_list',
	'template'=>'{items}',
	'viewData' => array( 'id' => $model->id, 
	'filteredSets'=>$filteredSets, 
	'keyStage'=>$model->key_stage,
	'cohortId'=>$model->cohort_id), 
	),true); 
	$boolean  = ($count==1) ? true : false;
	$yearPillsForSets[]=array("label"=>"Year $year", "content"=>$content,'active'=>$boolean);
	$count++;
}

?>

<!-- End outputting check boxes -->
<?php $this->widget('bootstrap.widgets.TbTabs', array(
	'id'=>'year-pill',//Sets the href
    'type'=>'pills', // '', 'tabs', 'pills' (or 'list')
    'tabs'=>$yearPillsForSets,
	'htmlOptions'=>array('class'=>'span3',
	'id'=>'exclude-sets-nav-pills'),
	
)); ?>

<div class="form-actions span3">
<?php echo CHtml::link("Done","admin",array("class"=>"btn btn-primary"))?>
</div>


</div><!-- End span4 -->

<!-- Exclude Pupils from sets content -->
<h2 class="span4"><small>Exclude Pupils from Classes:</small></h2>

<?php
foreach($yearGroups as $year){
	$yearPillsForPupils[]=array("label"=>"Year $year", "content"=>"","linkOptions"=>array("data-year"=>$year,"data-id"=>$model->id));
}
?>

<?php $this->widget('bootstrap.widgets.TbTabs', array(
	'id'=>'pill',//Sets the href
    'type'=>'pills', // '', 'tabs', 'pills' (or 'list')
    'tabs'=>$yearPillsForPupils,
	'htmlOptions'=>array('class'=>'span6','id'=>'exclude-pupils-nav-pills'),//Sets the div html options
)); ?>

</div><!-- End row -->

<?php Yii::app()->clientScript->registerScript('sets-script', '
var previousYear;
var previousPillContent;
$("div#exclude-pupils-nav-pills").on("click", ".nav-pills a", function (event) {
  var pill = event.target // activated pill
  var year = $(pill).data("year");
	if(previousYear==year)
	return false;
  var id = $(pill).data("id");
  var div_pill_content = $(pill).attr("href");
  $(previousPillContent).empty();
  $.get("ajaxLoadYearGroupPill", {year:year, id:id},function(data) {
  $(div_pill_content).html(data).hide().fadeIn();
	});
	previousYear = year;
	previousPillContent=div_pill_content;
  });
    //Trigger the first pills content
   $("a[href=#pill_tab_1]").trigger("click");
',CClientScript::POS_READY);?>

<?php Yii::app()->clientScript->registerScript('_pill-script','
var previousSet;
var previousTabContent;
$("div#exclude-pupils-nav-pills").on("click", ".nav-tabs a", function (event) {
  var tab = event.target // activated tab
  var set = $(tab).data("set");
	if(previousSet==set)
	return false;
  var id = $(tab).data("id");
  var div_tab_content = $(tab).attr("href");
  $(previousTabContent).empty();
  $.get("ajaxLoadSetTab", {set:set, id:id},function(data) {
  $(div_tab_content).html(data).fadeIn();
	});
	previousSet = set;
	previousTabContent=div_tab_content;
  });

',CClientScript::POS_READY);


Yii::app()->clientScript->registerScript('subject-grid-script', '
	function updateExcludedPupil(el){
		var subject_id=$(el).data("subject-id");
		var pupil_id=$(el).attr("id");
		var set=$(el).data("set");
		var keyStage=$(el).data("key-stage");
		var cohortId=$(el).data("cohort-id");
		var checked = ($(el).is(":checked")) ? 1 : 0;
		
		$.ajax({
		  url: "ajaxUpdateExcludedPupil",
		  type: "POST",
		  data: {pupil_id : pupil_id, subject_id:subject_id, 
		  set:set, 
		  checked:checked, 
		  keyStage:keyStage,
		  cohortId:cohortId},
		});
		
		//Handle sets tab class
		if($("#pupil-grid .exclude-checkbox:checked").length!=0){
		var tab = $("#set-nav-tabs ul li > a:contains("+set+")");
		$(tab).addClass("filtered-tab");
		
		}
		else{
		$("#set-nav-tabs ul li > a:contains("+set+")").removeClass("filtered-tab");
		}
		
		if(checked){
		$(el).closest("tr").addClass("excluded");
		}
		else{
		$(el).closest("tr").removeClass("excluded");
		}

		return false;
	}
',CClientScript::POS_HEAD);?>

<!-- Exclude Sets Scripts -->
<?php Yii::app()->clientScript->registerScript('exclude-sets-script', '
$("#exclude-sets-nav-pills .nav-pills a").click(function(event){
  var tab = event.target;
  var href = $(tab).attr("href");
  var pill = href.replace("year-","");
 $("a[href="+pill+"]").trigger("click");
 
});
',CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('updateExcludedSet', '
	function updateExcludedSet(el){
	var set = $(el).data("set");
	var subject_id = $(el).data("id");
	var keyStage=$(el).data("key-stage");
	var cohortId=$(el).data("cohort-id");
	var checked = ($(el).is(":checked")) ? 1 : 0;
	var tab = $("#set-nav-tabs ul li > a:contains("+set+")");
	
	if(checked){
	$(el).closest("label").addClass("excluded");
	$(tab).addClass("excluded");
	$($(tab).attr("href")+" table").addClass("filtered-set");
	}
	else{
	$(el).closest("label").removeClass("excluded");
	$("#set-nav-tabs ul li > a:contains("+set+")").removeClass("excluded");
	$($(tab).attr("href")+" table").removeClass("filtered-set");
	}
	
	$.ajax({
	url: "ajaxUpdateExcludedSet",
	type: "POST",
	data: {subject_id:subject_id, set:set, checked:checked,keyStage:keyStage, cohortId:cohortId},
	});
		
		return false;
	}
',CClientScript::POS_HEAD);?>




