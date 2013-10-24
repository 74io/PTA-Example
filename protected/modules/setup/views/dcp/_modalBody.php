<?php 

echo " I am modal body - ".$id; 
$this->widget('zii.widgets.jui.CJuiTabs', array(
	'id'=>'explore-tabs',
    'tabs'=>array(
        'StaticTab 1'=>'Content for tab 1',
        'StaticTab 2'=>array('content'=>'Content for tab 2', 'id'=>'tab2'),
        // panel 3 contains the content rendered by a partial view
        // 'AjaxTab'=>array('ajax'=>$ajaxUrl),
    ),
    // additional javascript options for the tabs plugin
    'options'=>array(
        //'collapsible'=>true,
    ),
));
?>

