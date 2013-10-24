<!-- Begin Title-->
<blockquote>
<p>
<?php echo "Target - ".$title;?>
</p>
</blockquote>
<!-- End Title -->

<div id='all-subject-aps-chart'></div>
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
          $data[]=(float)number_format($value['subject_aps'],2);
          $subject = $value['subject'];
          $targetData[]=$target;
        }
      }
           $series[]=array('name'=>'DCP APS', 'data'=>$data);
           
}

//Add the target
$series[]=array('type'=>'line','name'=>'Target APS','data'=>$targetData);


$yAxisData = $data;
array_push($yAxisData,$target);

 $this->Widget('ext.highcharts.HighchartsWidget', array(
   'options'=>array(
 	  'credits' => array('enabled' => false),
  	  'theme' => 'pta',
      'chart'=> array(
            'renderTo'=>'all-subject-aps-chart',
			'type'=>'line',
      //'marginRight'=>130,
      //'marginBotton'=>25,
        ),
      'title' => array('text' => 'Comparison of all DCP APS to Current Target APS',
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
                     'min'=>min($yAxisData),
                     'max'=>max($yAxisData),
                     'title'=>array('text'=>'Average Point Score'),
                     /*
                     'plotBands'=>array(
                      'from'=>$target-2,
                      'to'=>$target,
                      'color'=>'#CCC',
                      'label'=>array('text'=>'Target '.$target),
                      )*/
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
  					),
  			),
		'series' => $series
)
)
);?>
<p>
This chart displays the average point score for each data collection point compared to the average point score for the current target.
</p>