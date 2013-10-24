<?php
$this->sectionTitle=$model->mapped_alias;
$this->sectionSubTitle="Pupils currently missing from this result set";

//Page breadcrumbs
$this->breadcrumbs=array(
	'Setup'=>array('/setup'),
	$this->controllerSectionTitle.'s'=>array('admin'),
	'Review Data Set',
);?>

<p>The pupils listed below are currently not part of this result set. This means that they exist on your MIS but do not
have results for this result set. See below for more details.</p>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'review-grid',
	'type'=>'striped condensed bordered',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		 array(
    			'name'=>'pupil_id',
    			'header'=>'Pupil ID',
  		),
		 array(
    			'name'=>'surname',
    			'header'=>'Surname',
  		),
  		array(
    			'name'=>'forename',
    			'header'=>'Forename',
  		),
		 array(
    			'name'=>'year',
    			'header'=>'Year',
  		),
		 array(
    			'name'=>'form',
    			'header'=>'Form',
  		),
	),
)); ?>


<h4><i class="icon-question-sign"></i> Possible reasons why pupils are missing?</h4>
<ul>
<li>They currently have no classes. Only pupils who have a class for a subject are included in result sets.
 Add classes to your MIS for these pupils and rebuild the result set if you want to include them. Alternatively you can just leave them as
  they are. They will not be included in any cohort totals or reports on the system</li>
<li>They arrived at the school after this result set's original build date. Re-importing/Rebuilding the result set should fix this</li>
</ul>