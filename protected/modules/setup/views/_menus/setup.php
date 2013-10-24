<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // tabs or pills, defaults to tabs
	'encodeLabel'=>false,
    'items'=>array(
        array('label'=>'<span class="btn btn-small btn-success" style="line-height:8px;">Setup</span>', 'url'=>array('/setup')),
        array('label'=>'My School', 'url'=>array('/setup/myschool/admin')),
        array('label'=>'Cohorts', 'url'=>array('/setup/cohort/admin')),
        array('label'=>'Filters', 'url'=>array('/setup/indicator/admin')),
        array('label'=>'KS Data', 'url'=>array('/setup/keystage/admin')),
        array('label'=>'Build Core data', 'url'=>array('/setup/build/admin')),
        array('label'=>'Subjects', 'url'=>array('/setup/subject/admin')),
        array('label'=>'DCPs', 'url'=>array('/setup/dcp/admin')),
        array('label'=>'Targets', 'url'=>array('/setup/target/admin')),


   /*
        array('label'=>'More', 'items'=>array(
            array('label'=>'Secondary link', 'url'=>'#'),
            array('label'=>'Something else here', 'url'=>'#'),
            '---',
            array('label'=>'Another link', 'url'=>'#'),
        )),*/
    ),
)); ?>