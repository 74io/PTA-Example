<?php


// change the following paths if necessary
$yii=dirname(__FILE__).'/../../framework/yii.php';

if($_SERVER['SERVER_PORT']==443)
$mode="prod";

if($_SERVER['SERVER_PORT']==80)
$mode="prod";

if($mode=='prod'){
error_reporting(0);
$config=dirname(__FILE__).'/protected/config/prod.php';	
}
elseif($mode=='dev'){
$config=dirname(__FILE__).'/protected/config/dev.php';
defined('YII_DEBUG') or define('YII_DEBUG',true);	
}

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();

