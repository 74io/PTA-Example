<?php
//print_r($dataProvider->rawData);

foreach($dataProvider->rawData as $key=>$value)
{
$subjects[]=$value['subject'];
$data[] = (float)$value['subject_aps_diff'];
//$data2[] = (float)$value['subject_average_aps'];
}

$min = min($data);
$max = max($data);


	
 $this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
 	  'credits' => array('enabled' => false),
  	  'theme' => 'pta',
      'chart'=> array(
            'renderTo'=>'subject-average-chart',
			      'type'=>'bar'
        ),
      'title' => array('text' => 'Difference Between Subject Average APS and Pupil APS',
        ),
      //'subtitle' => array('text' => 'This pupil is',),

      'xAxis'=>array(
                     'categories'=>$subjects,
                ),
       'yAxis'=>array(
                     'min'=>$min,
                     'max'=>$max,
                     'title'=>array('text'=>'Difference')
                     ),
       'legend'=>array(
          'enabled'=>false,
          'layout'=> 'vertical',
					'backgroundColor'=> '#FFFFFF',
					'align'=> 'center',
					'verticalAlign'=> 'bottom',
					'x'=> 0,
					'y'=>40,
					'floating'=> false,
					'shadow'=> true  
                     ),
        'tooltip'=>array(
					'formatter'=>'js:function() {
				return ""+
					this.x +" Diff "+ this.y;}'
					),
/*
		'plotOptions'=>array(
					'column'=>array(
					'pointPadding'=> 0.2,
					'borderWidth'=>0, 
          'dataLabels'=> array('enabled'=>true),
					),
         // 'line'=>array('dataLabels'=> array('enabled'=>true))   
			),*/
		'series' => array(
         				array('name' => 'APS DIFF', 
                  'data' => $data,
                  /*
                  'dataLabels'=>array(
                    'enabled'=>true,
                    'color'=>'#FFF',
                    'align'=>'right',
                    'backgroundColor'=>'#666',
                    'x'=>30
                   )*/
                ),
                //array('name' => 'Subject APS', 'data' => $data2),
   					)
)
)
);