<?php
//print_r($dataProvider->rawData);

foreach($dataProvider->rawData as $key=>$value)
{
$subjects[]=$value['col18'];
$data1[]=(float)number_format($value['col4']-$value['col12'],2);
}

              
         				
 $this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
 	  'credits' => array('enabled' => false),
  	  'theme' => 'pta',
      'chart'=> array(
            'renderTo'=>'subject-chart2',
			'type'=>'bar'
        ),
      'title' => array('text' => 'Difference Between DCP % A* - C and Target % A* - C',
        ),
      'subtitle' => array('text' => 'Subjects on the left are failing to meet their target',),

      'xAxis'=>array(
                     'categories'=>$subjects,
                ),
       'yAxis'=>array(
                     'min'=>-100,
                     'max'=>100,
                     'title'=>array('text'=>'Difference (Subjects on the left are failing to meet their target)')
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
					this.x +": "+ this.y;}'
					),

		'plotOptions'=>array(
					'column'=>array(
					'pointPadding'=> 0.2,
					'borderWidth'=>0
					)
			),
		'series' => array(
         				array('name' => 'DCP', 'data' => $data1),
   					)
)
)
);