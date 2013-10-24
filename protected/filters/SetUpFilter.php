<?php
class SetUpFilter extends CFilter
{
	public $url;
	public $schoolSetUp;
	
    protected function preFilter($filterChain)
    {
    		//Apply to all set up controllers. This redirects back to the school setup page if setup is not complete
    		if($this->schoolSetUp===null){
			$filterChain->controller->redirect(array('myschool/admin', 'previousUrl'=>$this->url));
			}
			
			if($filterChain->controller->id!="cohort"){//We cannot redirect if it is the cohort else infinite redired will occur
			if($filterChain->controller->schoolSetUp['defaultCohort']===null)
			$filterChain->controller->redirect(array('cohort/admin', 'previousUrl'=>$this->url));
			}
			
    		//Prevent KS2 data being completed before indicators
			if($filterChain->controller->id=="keystage"){
				if(!Indicator::getSetUpIsComplete()){
					$filterChain->controller->redirect(array('indicator/admin', 'previousUrl'=>$this->url));
				}
			}
			
            //Prevent build core data being completed before ks2
			if($filterChain->controller->id=="build"){
				if(!KeyStage::getSetUpIsComplete()){
					$filterChain->controller->redirect(array('keystage/admin', 'previousUrl'=>$this->url));
				}
			}
			
        	//Prevent subjects being created before the build has been completed
			if($filterChain->controller->id=="subject"){
				if(!Yii::app()->build->setUpIsComplete){
					$filterChain->controller->redirect(array('build/admin', 'previousUrl'=>$this->url));
				}
				
			}
			
			//Prevent DCPs and Targets from being created before subjects
			if($filterChain->controller->id=="dcp" || $filterChain->controller->id=="target"){
				if(!Subject::getSetUpIsComplete()){
					$filterChain->controller->redirect(array('subject/admin', 'previousUrl'=>$this->url));
				}
			}

			$filterChain->run();
		
    }
 
    protected function postFilter($filterChain)
    {
        // logic being applied after the action is executed
    }
}