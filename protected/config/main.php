<?php
$maintenanceMode=false;

//Get the db credentials
require(dirname(__FILE__).'/db.php');
/**
 * Detect db name from subdomain
 */
$domain = explode(".",$_SERVER['SERVER_NAME']);
$client = trim(strtolower($domain[0]));//Must also be name of db
$clientDir = dirname(__FILE__).'/clients/'.$client.'.php';//Client directory

//Override to allow for testing
if($client=="greenabbeyptalive"){
	$maintenanceMode=false;
}

if(!file_exists($clientDir)) {
    die("Sorry, the client <strong>".$client."</strong> is not setup or is invalid.");
}

return array(
	'id'=>crc32($client),
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Pupil Tracking Analytics',
	'theme'=>'bootstrap',
	'language'=>'en',

	//Put the site into maintenance mode by setting the var at the top of the page to true
	'catchAllRequest'=>$maintenanceMode
        ? array('site/maintenance') : null,


	// preloading 'log' component
	'preload'=>array('log',
					 'bootstrap'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.models.ks4.*',
		'application.components.*',
		'application.components.core.*',
		'application.components.ks4.*',
		'application.components.interfaces.*',
		'application.components.filters.*',
		'application.components.widgets.*',
		'ext.CmsSettings.CmsSettings',
		'ext.bootstrap.widgets.TbEditableSaver', //Used alot
		//'ext.yiidebugtb.*', //Remove in production version
	),
	
	//BEGIN MODULES
	'modules'=>array(
		'setup'=>array(
			'mySettings'=>12345,
		),
		
		'user',
		'event',
		'ks4',
				
	),

	//BEGIN APPLICATION COMPONENTS
	'components'=>array(
		//COMPONENT - User (CWebUser)
		'user'=>array(
			// enable cookie-based authentication
			'class'=>'WebUser',
			'allowAutoLogin'=>true,
			'loginUrl'=>array('site/login'),
			
	
			//By specifying your own class here you can extent the parent (e.g. CWebUser) and add your own methods
			//'class'=>'PtWebUser'
		),
		
		//COMPONENT - Request (Using CHttpRequest)
		/*
		'request'=>array(
            'enableCsrfValidation'=>true,
			'enableCookieValidation'=>true,
        ),*/
        
		//COMPONENT - Caching (Using CApcCache)
		'cache'=>array(
            'class'=>'system.caching.CApcCache'),
		
		//COMPONENT - Settings (Using CmsSettings)
		'settings'=>array(
	        'class'             => 'CmsSettings',
	        'cacheComponentId'  => 'cache',
	        'cacheId'           => $client,//db/subdomain used as cache id
	        'cacheTime'         => 84000,
	        'tableName'     	=> 'settings',
	        'dbComponentId'     => 'db',
	        'createTable'       => false,
	        'dbEngine'      	=> 'InnoDB',
	        ),
	        
	   	//COMPONENT  - common (Using PtCommon)
		'common'=>array(
            'class'=>'application.components.PtCommon',
	        'totalUsers'=>0, //Number of users. 0 = unlimited
	        'totalDcps'=>10, //Number of DCPs/Targets
	        'systemKeyStages'=>array(4),
	        'systemYearGroups'=>array(7,8,9,10,11,12,13),
	        'volumeIndicators' => array('1.0','2.0','3.0','4.0','5.0','0.5'),
	        'equivalents' => array(1,0),//!Important they need to be in this order for json encoding
	        'mode' => array('equivalent'=>'2014 Onwards - Using subject equivalent & discounting','volume'=>'Pre 2014 - Using subject volume, no discounting'),
	        'subjectTypes'=>array('None',
	        					'English',
	        					'EngLit',
	        					'EngLang',
	        					'Maths',
	        					'Additional Science',
	        					'Core Science',
	        					'Biology',
	        					'Chemistry',
	        					'Physics',
	        					'Humanity',
	        					'AFL',
	        					'MFL'),
	        'misSystems'=>array('PTP','SIMS'),
	        'roles'=>array('admin','data manager','staff'),
	        'truthyValues'=>array('t','true','y','yes','1'),
	        'falsyValues'=>array('f','false','n','no','0'),
	        'maleValues'=>array('m','male','b','boy'),
	        'femaleValues'=>array('f','female','g','girl'),
	        'senValues'=>array('a','p','s'),
	        'senWithoutStatementValues'=>array('a','p'),
	        'ealValues'=>array('english',''),
	        'ethnicMinorityValues' =>array('WBRI','WENG')
	        		),
	        		
	    //COMPONENT  - eventLog (Using PtEventLog)
	    'eventLog'=>array(
            'class'=>'application.components.PtEventLog',
	        ),
	        
	    //COMPONENT  - build (Using PtBuild)
	    'build'=>array(
            'class'=>'application.components.PtBuild',
	        ),
	        
	    //COMPONENT  - dataCache (Using PtDataCache)
	    'dataCache'=>array(
            'class'=>'application.components.PtDataCache',
	        'toCache'=>true,//Set to true to cache
	        ),
	     //COMPONENT  - mailer (Using extension Emailer)   
	    'mailer' => array(
      		'class' => 'application.extensions.mailer.EMailer',
      		'pathViews' => 'application.views.email',
      		'pathLayouts' => 'application.views.email.layouts',
	        'fromName'=>'Pupil Tracking Limited',
	        'from'=>'noreply@pupiltracking.com',
   			),

		//COMPONENT - URL Manager (CUrlManager)
		'urlManager'=>array(
            'urlFormat'=>'path',
        	'showScriptName'=>false,
			'caseSensitive'=>false,//Note. Controllers need to be named MyschoolController and view folders must be in lower case
			'rules'=>array(
				//'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				//'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
			),
		),
		
		//COMPONENT - Client Script (CClientScript)
		/*
		'clientScript'=>array(
			'scriptMap'=>array(
			'jquery.js'=>true,
			),
		),
		*/
		
		//COMPONENT - Bootstrap (3rd Party Extension)
		'bootstrap' => array(
	    	'class' => 'ext.bootstrap.components.Bootstrap',
	    	'responsiveCss' => true,
		),

		//COMPONENT - Database Connection (CDbConnection)
		'db'=>array(
			'class'=>'PtDbConnection',
			'tmpDb' =>'tmp_table', // the temporary table database name
			'connectionString' => 'mysql:host=localhost;dbname='.$client,
			'emulatePrepare' => true,
			'username' => $dbUsername,
			'password' => $dbPassword,
			'charset' => 'utf8',
			'enableParamLogging'=>true,
		   	'enableProfiling'=>false,
		   	'schemaCachingDuration'=>3600,
		   //'tablePrefix' => 'demo_'
		),

		//COMPONENT - Error Handler (CErrorHandler)
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
        
       //COMPONENT - file (using CFile)
    	'file'=>array(
        'class'=>'ext.file.CFile',
    	),
       

	),//END APPLICATION COMPONENTS

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'mailProgram'=>($_SERVER['SERVER_PORT']=="443") ? "sendmail" : "mail",
		'dbName'=>$client,//A convenience param for getting the db name
	),
);
		
		
