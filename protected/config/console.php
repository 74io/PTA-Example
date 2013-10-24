<?php
/**
 * This is the config file for the console application that can be accessed via
 * /protected yiic <command> (php yiic <command> on linux) or when using the cron via yiic.php <command>
 */

//Get the db credentials
require(dirname(__FILE__).'/db.php');

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Cron',

   'preload'=>array('log'),
 
    'import'=>array(
        'application.components.*',
        'application.components.console.*',
		'application.components.interfaces.*',
    ),
	// application components
	'components'=>array(
			'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=admin',
			'emulatePrepare' => true,
			'username' => $dbUsername,
			'password' => $dbPassword,
			'charset' => 'utf8',
			//'enableParamLogging'=>true,
		   //'enableProfiling'=>true,
		   'schemaCachingDuration'=>3600,
    		'enableProfiling'=>true,
		   //'tablePrefix' => 'demo_'
		),
		
	//COMPONENT  - eventLog (Using PtEventLog)
	'eventLog'=>array(
            'class'=>'application.components.PtEventLog',
	        ),
	 //COMPONENT  - common (Using PtCommon)
	 /*
	'dbHelper'=>array(
            'class'=>'application.components.console.PtDbHelper',
	        ),*/
		
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
			'enabled'=>false,
						),
			array(
				'class'=>'CFileLogRoute',
				'levels'=>'error',//error, warning, trace
			 	'logFile'=>'console.log', 
				 //'categories'=>'system.*',
				),
			array(
            'class'=>'CProfileLogRoute',
            'levels'=>'profile',
            'enabled'=>false,   
        		),
			),
		)
	),
);