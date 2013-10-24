<?php
class Ks4Filter extends CFilter
{

    protected function preFilter($filterChain)
    {
    		$this->buildCoreData();
			$filterChain->run();
			
		
    }
 
    protected function postFilter($filterChain)
    {
        // logic being applied after the action is executed
    }
    
	
	/**
	 * Builds the core data if it has not already been built
 	*/	
	public function buildCoreData()
	{
		if(!Yii::app()->build->coreDataBuiltToday)
		{
			if(!Yii::app()->build->getBuilding(PtEventLog::BUILD_4))
			{
				if(!Yii::app()->build->buildCoreData())
					Yii::app()->user->setFlash("warning","<strong>Warning!</strong> Core data could not be built. This could be because setup is incomplete.
						Please check the ".CHtml::link('log','/event')." for details");

			
			}
		}
	}
}