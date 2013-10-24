<?php
Yii::import('application.modules.setup.controllers.PtFieldMappingController');
class TargetController extends PtFieldMappingController
{
	public $portletTitle = "Targets";
	
	public $menu=array(
	array('label'=>'Manage Targets','url'=>array('/setup/target/admin')),
	array('label'=>'Create Target','url'=>array('/setup/target/create'),'linkOptions'=>array('id'=>'create')),
		);

}