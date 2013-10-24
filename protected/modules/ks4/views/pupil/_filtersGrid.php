<?php $this->widget('ext.bootstrap.widgets.TbDetailView',array(
	'type'=>'bordered condensed',
	'data'=>$dataProvider->rawData,
	'attributes'=>array(
		array(     
            'label'=>'Gender',
            'value'=>$dataProvider->rawData['gender'], 
        ),
		array(     
            'label'=>'Ethnicity',
            'value'=>$dataProvider->rawData['ethnicity'], 
        ),
		array(     
            'label'=>'SEN Code',
            'value'=>$dataProvider->rawData['sen_code'], 
        ),
		array(     
            'label'=>'FSM',
            'value'=>$dataProvider->rawData['fsm'], 
        ),
		array(     
            'label'=>'Gifted',
            'value'=>$dataProvider->rawData['gifted'], 
        ),
		array(     
            'label'=>'CLA',
            'value'=>$dataProvider->rawData['cla'], 
        ),
		array(     
            'label'=>'EAL',
            'value'=>$dataProvider->rawData['eal'], 
        ),
        array(     
            'label'=>'Pupil Premium',
            'value'=>$dataProvider->rawData['pupil_premium'], 
        ),

	),
)); ?>