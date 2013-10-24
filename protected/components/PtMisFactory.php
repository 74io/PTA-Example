<?php
class PtMisFactory
{ 
    private static $instance; 

    private function __construct() {} 

    public static function mis() 
    { 
    	$mis=strtolower(Yii::app()->controller->schoolSetUp['mis']);
    	
    	switch($mis){
    		case("ptp"):
    			$class=PtPtp;
    		break;
    		 case("sims"):
    			$class=PtSims;
    		break;
    	}
    	
        if (!self::$instance) 
        { 
            self::$instance = new $class; 
        } 

        return self::$instance; 
    } 
} 