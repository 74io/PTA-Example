<?php
class UserModule extends CWebModule
{
  public $defaultController = 'Main';
  
  /**
   * Use this section to initalize module models and components
   * @see CModule::init()
   */
  public function init()
  {
     parent::init();
	//We need to read the module models folder here as well as the application models folder
     $this->setImport(array( 
                           'user.models.*',    
                     ));
  } 

}