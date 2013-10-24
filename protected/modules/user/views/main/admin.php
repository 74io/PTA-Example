<?php
//Page titles
$this->sectionTitle="Users";
$this->sectionSubTitle="Create, update and delete users";
$this->portletTitle = "Users";
//Page bread crumbs
$this->breadcrumbs=array(
	'Users'=>array('admin'),
	'Manage',
);?>

<p>Although it is possible to create new users manually, a much more efficient way is to allow users to
 register themselves. See the 'Manage Registration Code' link on the right for more details.</p>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-grid',
	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	//'htmlOptions'=>array('class'=>'items'),
	'columns'=>array(
		array(
			'name'=>'id',
			//'value'=>'$data->role',
			'htmlOptions'=>array("width"=>"25px"),

		),
		array(
		'name'=>'username'),
		'email',
		array(
			'name'=>'role',
			//'value'=>'$data->role',
			'filter'=>Yii::app()->common->rolesDropDown,
			//'headerHtmlOptions'=>array("class"=>"role-column"),
			'sortable'=>true,
		),
		
		array(
			'name'=>'account_created',
			'filter'=>false,
			//'value'=>'$data->role',
			'htmlOptions'=>array("width"=>"130px"),
			'sortable'=>true,
		),
		
		array(
			'header'=>'Active',
			'name'=>'active',
			'filter'=>false,
			'value'=>array($model,'renderActiveColumn'), 
			'type'=>'raw',
			'htmlOptions'=>array("width"=>"50px"),
			'sortable'=>true,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update} {delete}',
            'htmlOptions'=>array('style'=>'width: 50px','class'=>'edit-column'),
			//'template'=>'{update}'//Use template to only display specific buttons
			
		),
	),

)); 

/*
Yii::app()->clientScript->registerScript('delete-all-script', "
$('#delete-all').live('click', (function(){
	var ids = $.fn.yiiGridView.getSelection('user-grid');
	if(ids.length==0){
	alert('You need to select some users.');
	return false;
	}
	else{
		var data = {'ids':ids,'ajax':true};
		$.post('deleteAll',
		data,
		function(response) {
	  	$.fn.yiiGridView.update('user-grid');
		}).fail(function() {
			$.fn.yiiGridView.update('user-grid'); 
			});
	}
}));
");
*/
?>
