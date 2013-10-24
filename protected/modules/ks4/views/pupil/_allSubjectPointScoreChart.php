<div id="all-subject-point-scores-chart" ></div>

<?php
//print_r($dataProvider->rawData);
//exit;
//

foreach($dataProvider->rawData as $key=>$value)
{
$dcps[]=$value['mapped_alias']." ".$value['date'];
$subjectMappindIds[]=$value['subjectmapping_id'];
}

$dcps=array_unique($dcps);
$dcps = array_values($dcps);
$subjectMappindIds = array_unique($subjectMappindIds);

foreach($subjectMappindIds as $subjectMappindId){
  $data=array();
    foreach($dataProvider->rawData as $key=>$value)
    {
      if($value['subjectmapping_id']==$subjectMappindId){
          $data[]=(float)number_format($value['standardised_points'],2);
          $subject = $value['subject'];
        }
      }
           $series[]=array('name'=>$subject, 'data'=>$data);
}


	
 $this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
 	  'credits' => array('enabled' => false),
  	  'theme' => 'pta',
      'chart'=> array(
            'renderTo'=>'all-subject-point-scores-chart',
			       'type'=>'line',
            // 'width'=>800,
      //'marginRight'=>130,
      //'marginBotton'=>25,
        ),
      'title' => array('text' => 'Subject Standardised Point Scores Across All Data Collection Points (DCPs)',
        ),
      //'subtitle' => array('text' => 'This pupil is',),

      'xAxis'=>array(
                     'categories'=>$dcps,
                     'labels'=>array('rotation'=>-45,
                     'align'=>'right',
                     'style'=>array('fontSize'=>'12px')
                     ),
                ),
       'yAxis'=>array(
                     'min'=>0,
                     'max'=>70,
                     'title'=>array('text'=>'Standardised Point Score'),
                      ),

       'legend'=>array(
          'layout'=> 'vertical',
					'backgroundColor'=> '#FFFFFF',
					'align'=> 'right',
					'verticalAlign'=> 'top',
					'x'=> 0,
					'y'=>20,
          'borderWidth'=>0,
					//'floating'=> false,
					//'shadow'=> true  
                     ),
        'tooltip'=>array(
					'formatter'=>'js:function() {
				return ""+
					this.x+"<br>"+this.series.name +": "+ this.y;}'
					),

		'plotOptions'=>array(
					'line'=>array(
					'pointPadding'=> 0.2,
					'borderWidth'=>0, 
          'dataLabels'=> array('enabled'=>true),
          //'enableMouseTracking'=>false,
					),
         // 'line'=>array('dataLabels'=> array('enabled'=>true))   
			),
		'series' => $series,      					
)
)
);?>
<p>
This chart uses standardised point scores to allow subjects to be compared. Use the legend on the right to show/hide subjects.
</p>