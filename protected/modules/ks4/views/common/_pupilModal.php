<div id="modal" class="modal hide" style="outline:none;" tabindex="-1" aria-hidden="false">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="modal-label">Explore</h3>
  </div>
  <div class="modal-body" style="min-height:700px; overflow-y:visible;">

<?php $this->widget('bootstrap.widgets.TbTabs', array(
	'type'=>'pills', // 'tabs' or 'pills'
	'id'=>'pupil-pills',
	//'placement'=>'left',
	'tabs'=>array(
		array('label'=>'Summary', 'id'=>'results', 'content'=>'Loading...'),
		array('label'=>'Subject Averages', 'id'=>'subjectaverage', 'content'=>'Loading...'),
		array('label'=>'KS2 & Levels Progress', 'id'=>'ks2', 'content'=>'Loading...'),
		array('label'=>'Tracking', 'id'=>'tracking','content'=>'Loading...'),
		array('label'=>'Filters', 'id'=>'filters','content'=>'Loading...'),
	),
));?>

</div>
</div>