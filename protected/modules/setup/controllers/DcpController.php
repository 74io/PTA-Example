<?php
Yii::import('application.modules.setup.controllers.PtFieldMappingController');
class DcpController extends PtFieldMappingController
{
	public $portletTitle = "DCPs";
	
	public $menu=array(
	array('label'=>'Manage DCPs','url'=>array('/setup/dcp/admin')),
	array('label'=>'Create DCP','url'=>array('/setup/dcp/create'),'linkOptions'=>array('id'=>'create')),
		);
		


	
}
