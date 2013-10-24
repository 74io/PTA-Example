<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 * PUT BACK TO  SBaseController for role based access control
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column2';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	/*
	 * @var string the title of a section. This is displayed on the top of each page and 
	 * can be different from the page title
	 */
	public $sectionTitle="";
	
	/*
	 * @var string the title of a section. This is displayed on the top of each page and 
	 * can be different from the page title
	 */
	public $sectionSubTitle="";
	
	/*
	 * @var string the title of the portlet (CPortlet)
	 * can be different from the page title
	 */
	public $portletTitle="";
	
	/*
	 * @var array The schoolset up
	 */
	private $_schoolSetUp;
	
	
	public function getSchoolSetUp()
	{
		if($this->_schoolSetUp!==null)
		return $this->_schoolSetUp;

		return $this->_schoolSetUp=Yii::app()->settings->get("schoolSetUp");		
	}
	
	
	  
	/*
	 * This is called by jQuery's .ajaxError in main.php to display an alert
	 */
	public function ajaxBootAlert(){
		Yii::app()->user->setFlash('error','<strong>Oops!</strong> There has been a problem with your request. We have been notified.');
		ob_start();
		$this->widget('bootstrap.widgets.TbAlert');
		$alert = ob_get_contents();
		ob_end_clean();
		return $alert;
	}
	
	/**
	 * @see CController::accessRules()
	 * Note this can be overridden in individual controllers to allow other roles
	 */
	public function accessRules()
	{   
	    return array(
	        array('allow',
	        	//'actions'=>array('admin'),No actions specified means all actions
	            'roles'=>array('admin'),
	        ),
	        array('deny'),//Deny all users
	    );
	}
	
	/**
	 * This should can be called when rendering ajax content e.g. a CGridView in a model for example. It prevents
	 * jquery etc being re-loaded and thus enables ajax paging/sorting etc inside the modal without
	 * ruiningthe page beneath.
	 * Note that this should be called before rendering a partial like so:
	 * 		$this->cleanForPartial();
	 *   	$this->renderPartial('_myView',array(
	 *	'dataProvider'=>$dataProvider),false,true);
	 * @return void
	 */
	public function cleanForPartial()
	{
		//JS
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
    	Yii::app()->clientScript->scriptMap['bootstrap.js'] = false;
    	Yii::app()->clientScript->scriptMap['bootstrap.min.js'] = false;
    	Yii::app()->clientScript->scriptMap['bootstrap.bootbox.min.js'] = false;
    	Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;
    	//Highcharts js
    	Yii::app()->clientScript->scriptMap['highcharts.js'] = false;
      	Yii::app()->clientScript->scriptMap['highcharts.src.js'] = false;

    	Yii::app()->clientScript->scriptMap['exporting.js'] = false;
    	Yii::app()->clientScript->scriptMap['exporting.src.js'] = false;
		Yii::app()->clientScript->scriptMap['pta.js'] = false;


    	//CSS
		Yii::app()->clientScript->scriptMap['bootstrap.min.css'] = false;
		Yii::app()->clientScript->scriptMap['bootstrap.css'] = false;
		Yii::app()->clientScript->scriptMap['bootstrap-responsive.min.css'] = false;	
		Yii::app()->clientScript->scriptMap['bootstrap-responsive.css'] = false;
    	Yii::app()->clientScript->scriptMap['bootstrap-yii.css'] = false;



    	//Reload our theme css so that it overrides the rest. Note that a partial is not rendered through main.php
    	////Note that we added the code above to not re-include all css. Thus the line below is no longer required
    	//Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/css/screen.css');
	}
	
	/**
	 * @return string the page title. Defaults to the controller name and the action name.
	 */
	/*
	public function getPageTitle()
	{
		if($this->_pageTitle!==null)
			return $this->_pageTitle;
		else
		{
			$name=ucfirst(basename($this->getId()));
			if($this->getAction()!==null && strcasecmp($this->getAction()->getId(),$this->defaultAction))
				return $this->_pageTitle=Yii::app()->name.' - '.ucfirst($this->getAction()->getId()).' '.$name;
			else
				return $this->_pageTitle=Yii::app()->name.' - '.$name;
		}
		
	}*/
	

}//End Controller class