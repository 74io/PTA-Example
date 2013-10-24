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
        'next'        => 'forth',
        'title'       => 'Table Header',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("second");}'),
                array('name'=>'Next')
            ),

        'description' => 'The table displays A*-A, A*-C, A*-G, Average Point Score (APS) and Fails for the DCP and just the A*-C and APS for
                          the target. If you wish to view all data for the target then simply select it as a DCP above. The colours indicate whether
                            the percentage is:
                            <br>Green = Above the target percentage
                            <br>Amber = Equal to the target percentage
                            <br>Red = Below the target percentage',
        'attachTo'    => 'thead th#ks4subject-grid_c1',
        'position'    => '7',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'forth',
        'next'        => 'fifth',
        'title'       => 'Subject Title',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("third");}'),
                array('name'=>'Next')
            ),

        'description' => 'Hover your mouse over the subject name to reveal the qualification and number of GCSEs the subject is worth.<br><br>
                          Go on. Try it now.
                          <br><br>
                          When viewing the figures to the right. The amount of GCSEs each subject is worth is reflected in the totals with the exception
                          of \'Fail\' which merely states the number of pupils who are failing the subject.',
        'attachTo'    => 'tbody td span',
        'position'    => 'right',
        'xButton'     => true,
        'autoFocus'      => true
    )
);

$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'fifth',
       // 'next'        => 'sixth',
        'title'       => 'Total Buttons',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("forth");}'),
                array('name'=>'Close')
            ),

        'description' => 'Click the total buttons to reveal pupils and classes. The active button
                         is highlighted with a blue background.
                          <br><br>Go on. Try it now.
                          <br><br>The \'DCP Result\' traffic light colour indicates whether the pupil is:
                          <br>Green = Above target
                          <br>Amber = On target
                          <br>Red = Below target',
        'attachTo'    => '.group-link',
        'position'    => 'top',
        'xButton'     => true,
        'autoFocus'      => true
    )
);


/*
$this->widget('ext.eguiders.EGuider', array(
        'id'          => 'sixth',
        'title'       => '% A*-C vs Target',
        'buttons'     => array(
            array(
                'name'   => 'Previous',
                'onclick'=> 'js:function(){guiders.hideAll(); guiders.show("fifth");}'),
                array('name'=>'Close')
            ),
        'description' => 'If you want to visualise the best and least performing subjects in the A*-C range then use the chart below.
                          For example, if you want to focus on the best performing subjects for the current DCP then click on the target in the
                          legend on the right to hide it and look for the longest bars.<br><br>Go on. Try it now.',
        'attachTo'    => '#subject-chart',
        'position'    => 'top',
        'xButton'     => true,
        'autoFocus'      => true
    )
);
*/




