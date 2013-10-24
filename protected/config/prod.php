<?php

return CMap::mergeArray(
require(dirname(__FILE__).'/main.php'),
//Start components
array(
	'components'=>array(
	    'dataCache'=>array(
	        'toCache'=>true,//Set to true to cache
	        	),
			
	/**
	 * Logging is set to send the error in and email and log the full trace in file
	 * We can use the date/time information in the email to search the log file.
	 */		
	'log'=>array(
		'class'=>'CLogRouter',
		'routes'=>array(
			array(
			'class' => 'CEmailLogRoute',
			//'categories' => 'system.*',
			'levels' => 'error, warning',
			'emails' => array('roneill@pupiltracking.com'),
			'sentFrom' => 'log@pupiltracking.com',
			'subject' => 'Error at pta.pupiltracking.com',
			//'filter'=>'CLogFilter',//Adds environment vars to error email
			'enabled'=>true,
						),
			array(
				'class'=>'CFileLogRoute',
				'levels'=>'error, warning',//error, warning, trace
				 //'categories'=>'system.*',

				),
					),
				)
			),//End Components
			
),
require($clientDir)
);
