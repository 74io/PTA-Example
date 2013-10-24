<?php

class EventTestingController extends Controller
{
	public $sectionTitle="Event Testing";
	public $componentObject;
	
	public function actionAdmin()
	{
		$this->componentObject = new EventTestingComponent;
	
		//$event = new CEvent($this);
		$this->random1();
		//ATTATCHING HANDLERS
		//Attach the event handler like this if it is a method of an object. In this case the component object
		//$componentObject->onBeginAction= array($componentObject, "myBeginActionMethod");
		//Or like this if it is a static method of EventTestingComponent or any other class
		//$componentObject->onBeginAction= array('EventTestingComponent', "myBeginActionMethod2");
		//Or like this if it is a global function...
		//$componentObject->onBeginAction= "myBeginActionMethod3";

		
		//RAISING THE EVENT
		//Raise the event like this...
	    //$component->raiseEvent('onBeginAction', $event);
	    //OR
	    //Like this... if the onBeginAction Method defined in EventTestingComponent contains the raiseEvent method
	    $this->componentObject->onBeginAction(new CEvent($this));

	}
	
	public function random1(){
		//Subscribe to the components onBeginAction event
		$this->componentObject->onBeginAction= array($this->componentObject, "myBeginActionMethod");
	}

	public function actionCreate()
	{
		$this->render('create');
	}

	public function actionUpdate()
	{
		$this->render('update');
	}

}
	//Global function
	function myBeginActionMethod3($event)
	{
		echo "<pre>";
		print_r($event);
		echo "</pre>";
		echo "myBeginActionMethod3 has been called!";
	}