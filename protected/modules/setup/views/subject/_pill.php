<?php

/**
 * $sets, $filteredSets, $excludedSets, $cohortId and the Subject $model are passed to this view
 * Generate the tabs
 */

foreach($sets as $set)
{
	$class=(in_array($set,$filteredSets)) ? "filtered-tab" : "";
	$class.=(in_array($set,$excludedSets)) ? " excluded" : "";
	$tabs[]=array("label"=>$set,"content"=>"",
					"active"=>false,"linkOptions"=>array("class"=>"$class",
					"data-set"=>urlencode($set),
					"data-id"=>$model->id,
					"data-key-stage"=>$model->key_stage));
}
?>

<?php $this->widget('bootstrap.widgets.TbTabs', array(
	'id'=>'set-tab',
    'type'=>'tabs', // 'tabs' or 'pills'
    'tabs'=>$tabs,
    'htmlOptions'=>array('class'=>'tabs-left','id'=>'set-nav-tabs'),
)); ?>

<script type="text/javascript">
$(document).ready(function () {	
    //Trigger the first tabs content
   $("a[href=#set-tab_tab_1]").trigger("click");
});
</script>



