<?php
$data1 = array(
         			 (float)$dataProvider->rawData[0]['col2'],
                     (float)$dataProvider->rawData[1]['col2'],
                     (float)$dataProvider->rawData[2]['col2'],
                     (float)$dataProvider->rawData[3]['col2'],
                     (float)$dataProvider->rawData[4]['col2'],
                     (float)$dataProvider->rawData[5]['col2'],
                     (float)$dataProvider->rawData[6]['col2'],
                     (float)$dataProvider->rawData[7]['col2'],
                     (float)$dataProvider->rawData[8]['col2'],
                     (float)$dataProvider->rawData[9]['col2'],
                     (float)$dataProvider->rawData[10]['col2'],
                     (float)$dataProvider->rawData[11]['col2']
         				);
         				
$data2 = array(
         			 (float)$dataProvider->rawData[0]['col4'],
                     (float)$dataProvider->rawData[1]['col4'],
                     (float)$dataProvider->rawData[2]['col4'],
                     (float)$dataProvider->rawData[3]['col4'],
                     (float)$dataProvider->rawData[4]['col4'],
                     (float)$dataProvider->rawData[5]['col4'],
                     (float)$dataProvider->rawData[6]['col4'],
                     (float)$dataProvider->rawData[7]['col4'],
                     (float)$dataProvider->rawData[8]['col4'],
                     (float)$dataProvider->rawData[9]['col4'],
                     (float)$dataProvider->rawData[10]['col4'],
                     (float)$dataProvider->rawData[11]['col4']
         				);
         				
 $this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
 	  'credits' => array('enabled' => false),
 	  'theme' => 'pta',
      'chart'=> array(
            'renderTo'=>'astartoc-chart',
			'type'=>'bar'
        ),
      'title' => array('text' => 'Grades in the Range A* - G',
        ),
      'subtitle' => array('text' => 'Click on the legend to show/hide bars',
        ),
      'xAxis'=>array(
                     'categories'=>array(
                     $dataProvider->rawData[0]['col1'],
                     $dataProvider->rawData[1]['col1'],
                     $dataProvider->rawData[2]['col1'],
                     $dataProvider->rawData[3]['col1'],
                     $dataProvider->rawData[4]['col1'],
                     $dataProvider->rawData[5]['col1'],
                     $dataProvider->rawData[6]['col1'],
                     $dataProvider->rawData[7]['col1'],
                     $dataProvider->rawData[8]['col1'],
                     $dataProvider->rawData[9]['col1'],
                     $dataProvider->rawData[10]['col1'],
                     $dataProvider->rawData[11]['col1']
                     )),
       'yAxis'=>array(
                     'min'=>0,
                     'max'=>100,
                     'title'=>array('text'=>'% Grades in the range A*-G')
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
 
?>

 