<?php
class SetupModule extends CWebModule
{
  public $defaultController = 'Myschool';
  public $mySettings; //custom properties you can config in config/main.php
  
   /**
   * Use this section to initalize module models and components
   * @see CModule::init()
   */
  public function init()
  {
     parent::init();
	//We need to read the module models folder here as well as the application models folder
     $this->setImport(array( 
                           'setup.models.*',    
                     ));
  } 

}
