<?php
/**
 * This file should contain development settings only
 */
return CMap::mergeArray(
require(dirname(__FILE__).'/main.php'),
array(

	//BEGIN MODULES
	'modules'=>array(
		// uncomment the following to enable the Gii tool
		//MODULE - Gii
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'1234',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
	 		'generatorPaths'=>array(
            'bootstrap.gii',
			),
		),			
	),
	
	
	'components'=>array(
	
		//Override the db profile setting
		// Note. Enabling this prevented the CMS cache settings from working correctly no defaultCohort was defined
		
		'db'=>array(
		   	'enableProfiling'=>true,

		),
	        //COMPONENT  - Log (CLogRouter). Has no alias
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error',//error, warning, trace
				//Delete lines out below to restore normal application log
				 	//'categories'=>'system.db.*,system',
                   	//'logFile'=>'sql.log',
				),
				
				/*
				array(
		            'class'=>'CDbLogRoute',
		            'logTableName'=>'log',
				    'connectionID'=>'db', 
		            'categories'=>'pt.*',
		          ),
		          */
				/*
			array( // configuration for the toolbar
		          'class'=>'XWebDebugRouter',
		          'config'=>'alignLeft, opaque, runInDebug, fixedPos, collapsed, yamlStyle',
		          'levels'=>'error, warning, trace, profile, info',
		          'allowedIPs'=>array('127.0.0.1'),
		        ),*/
		        	
			
			array(
            'class'=>'CProfileLogRoute',
            'levels'=>'profile',
            'enabled'=>true,
            
        ),
        
			// uncomment the following to show log messages on web pages
        	array(
            'class'=>'CWebLogRoute',
            'levels'=>'error, trace', // Shows the binding param values, profile does not
            'enabled'=>false,
        	//'categories'=>'system.db.CDbCommand',
        	'showInFireBug'=>false,
        	),
        
				),
			),
		),

),require($clientDir)
);