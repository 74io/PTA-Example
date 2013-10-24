<?php
//Create a new subject guider	
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'first',
        'next'        => 'second',
        'title'       => 'Select Data to View',
        'buttons'     => array(
            array('name'=>'Next')
        ),
        'description' => 'You can compare any data collection point (DCP) to any target, a DCP to a DCP or a target to a target.
                          You can also select the year group and cohort you wish to view data for. When selecting a mode \'Pre 2014\'
                          uses the subject\'s volume indicator (ie worth x GCSEs) and \'2014 Onwards\' uses the subject\'s equivalence (ie the subject is worth 1 GCSE or not). 
                          Discounting is also applied in this mode.
                          <br><br>The DCP and target selected as the defaults during setup will be the defaults automatically selected here for each year group.',
        'attachTo'    => '.form-actions',
        'position'    => 'bottom',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'second',
        'next'        => 'third',
        'title'       => 'Filter',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("first");}'),
                array('name'=>'Next')
            ),
        'description' => 'Use the filter to narrow your selection. You can check multiple items within each filter.',
        'attachTo'    => '.sticky-ks4filter',
        'position'    => 'left',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'third',
        'title'       => 'Sub Reports',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("second");}'),
                array('name'=>'Close')
            ),

        'description' => 'Use the pills to navigate between sub reports. Clicking on the total buttons will reveal the pupils belonging to that group. The active button
                         is highlighted with a blue background.
                          <br><br>Below each sub report is a chart displaying a visualisation of the data currently being viewed.',
        'attachTo'    => '#atoc',
        'position'    => '11',
        'xButton'     => true,
        'autoFocus'      => true
    )
);