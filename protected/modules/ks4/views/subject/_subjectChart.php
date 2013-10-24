<?php
//print_r($dataProvider->rawData);

foreach($dataProvider->rawData as $key=>$value)
{
$subjects[]=$value['col18'];
$data1[]=(float)$value['col4'];
$data2[]=(float)$value['col12'];
}
              
         				
 $this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
 	  'credits' => array('enabled' => false),
  	  'theme' => 'pta',
      'chart'=> array(
            'renderTo'=>'subject-chart',
			'type'=>'bar'
        ),
      'title' => array('text' => '% A* - C',
        ),
      'subtitle' => array('text' => 'Click on the legend to show/hide columns',
        ),

      'xAxis'=>array(
                     'categories'=>$subjects,
                     ),
       'yAxis'=>array(
                     'min'=>0,
                     'max'=>100,
                     'title'=>array('text'=>'% A*-C')
                     ),
       'legend'=>array(
             		'layout'=> 'vertical',
					'backgroundColor'=> '#FFFFFF',
					'align'=> 'right',
					'verticalAlign'=> 'top',
					'x'=> 0,
					'y'=>40,
					'floating'=> false,
					'shadow'=> true  
                     ),
        'tooltip'=>array(
					'formatter'=>'js:function() {
				return ""+
					this.x +": "+ this.y+"%";}'
					),

		'plotOptions'=>array(
					'column'=>array(
					'pointPadding'=> 0.2,
					'borderWidth'=>0
					)
			),
		'series' => array(
         				array('name' => 'DCP', 'data' => $data1),
         				array('name' => 'Target', 'data' => $data2),
   					)
)
)
);