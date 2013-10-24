<div id="modal" class="modal hide fade" style="outline:none;" tabindex="-1" aria-hidden="false">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="modalLabel">Explore</h3>
  </div>
  <div class="modal-body" style="min-height:585px">
<?php $this->widget('bootstrap.widgets.TbTabs', array(
	'type'=>'tabs', // 'tabs' or 'pills'
	'id'=>'verify-tabs',
	'placement'=>'left',
	'tabs'=>array(
		array('label'=>'Missing Pupils', 'id'=>'verifyPupils', 'content'=>'Loading...'),
		array('label'=>'Missing Subjects', 'id'=>'verifySubjects', 'content'=>'Loading...'),
		array('label'=>'Missing Results', 'id'=>'verifyResults', 'content'=>'Loading...'),
		array('label'=>'Fail Results', 'id'=>'verifyFails','content'=>'Loading...'),
	),
));?>
</div>
</div>