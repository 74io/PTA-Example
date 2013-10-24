<?php
/**
 * Detect server port and create appropriate db credentials
 */
//Running over SSL
if($_SERVER['SERVER_PORT']=='80' || $_SERVER['LOGNAME']=="Air"){
	$dbUsername = "root";
	$dbPassword = "secretpassword";

}
else{
	$dbUsername = "notroot";
	$dbPassword = "secretpassword";
}
?>
