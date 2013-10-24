<?php
class EventTestingComponent extends CComponent
{
	
	
	public function onBeginAction($event)
	{
		 $this->raiseEvent('onBeginAction',$event);
		
	}
	
	
	public function myBeginActionMethod($event)
	{
		echo "<pre>";
		print_r($event);
		echo "</pre>";
		echo "myBeginActionMethod has been called!";
	}
	
	public static function myBeginActionMethod2($event)
	{
		echo "<pre>";
		print_r($event);
		echo "</pre>";
		echo "myBeginActionMethod2 has been called!";
	}
	

}