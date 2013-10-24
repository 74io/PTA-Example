<li>
<?php $css = (in_array($data,$filteredSets)) ? "excluded" : "";?>
<label class="checkbox <?php echo $css;?>">
<?php
 echo CHtml::checkBox("set", (in_array($data,$filteredSets) ? true : false),
				array("data-set"=>$data,
					"data-id"=>$id,
					"data-key-stage"=>$keyStage,
					"data-cohort-id"=>$cohortId,
					"onclick"=>"updateExcludedSet(this);"))." ".$data;
?>
</label>
</li>
