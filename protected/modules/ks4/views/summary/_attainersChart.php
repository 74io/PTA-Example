<?php
$this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
	  'credits' => array('enabled' => false),
 	  'theme' => 'pta',
      'chart'=> array(
            'renderTo'=>'attainers-chart',
			'type'=>'pie',
        ),
      'title' => array('text' => 'Low, Middle and High Attainers'),
        
       'tooltip'=>array(
                'formatter'=>'js:function() { return "<b>"+ this.point.name +"</b>: "+ Highcharts.numberFormat(this.percentage, 2, ".") +" %"; }'

                ),
       'plotOptions'=>array(
            'pie'=>array(
                'allowPointSelect'=> true,
                'cursor'=>'pointer',
                'dataLabels'=>array(
                    'enabled'=> true,
					'style'=>array('fontSize'=>'15px'),
                                   )
                        )
                 ),
		'series' => array(
         				array('type'=>'pie', 'name' => 'Attainers', 'data' =>array( 
         				array('Low',(int)$component->getNumber($dataProvider->rawData[0]['col2'])),
         				array('Middle',(int)$component->getNumber($dataProvider->rawData[1]['col2'])),
         				array('High',(int)$component->getNumber($dataProvider->rawData[2]['col2'])),
         				array('No Prior Attainment',(int)$component->getNumber($dataProvider->rawData[3]['col2']))
         					),
         				)

   					)
 
   )
));
 
?>