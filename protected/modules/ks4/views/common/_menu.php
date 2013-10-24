<?php 

$schoolActive=false;

switch($this->id)
{
    case('summary'):
    case('breakdown'):
    $schoolActive=true;
    break;
}

$this->widget('bootstrap.widgets.TbMenu', array(
    'id'=>'report-menu',
    'type'=>'tabs', // tabs or pills, defaults to tabs
	//'encodeLabel'=>false,
    'items'=>array(
        
        array('label' => 'School', 'active'=>$schoolActive, 'items' => array(
            array('label' => 'Summary', 'url'=>array('/ks4/summary/admin')),
            array('label' => 'Breakdown', 'url'=>array('/ks4/breakdown/headlines'))
        )),
        array('label'=>'Subject', 'url'=>array('/ks4/subject/admin')),

    ),
)); ?>