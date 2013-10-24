<?php
$this->widget('bootstrap.widgets.TbMenu', array(
    'id'=>'report-menu',
    'type'=>'pills', // tabs or pills, defaults to tabs
	//'encodeLabel'=>false,
    'items'=>array(
        
        array('label'=>'Headlines', 'url'=>array('/ks4/breakdown/headlines')),
        array('label'=>'Attainers', 'url'=>array('/ks4/breakdown/attainers')),
        array('label'=>'SEN', 'url'=>array('/ks4/breakdown/sen')),
    ),
)); ?>