<?php 
class WebUser extends CWebUser
{
	private $_role;
	public $loginRequiredAjaxResponse="<strong>Oops!</strong> Either you have logged out or your session has expired.";
    /**
     * Overrides a Yii method that is used for roles in controllers (accessRules).
     *
     * @param mixed $operation Name of the operation required (here, a role). If an array of roles
     * can is provided all will be checked
     * @param mixed $params (opt) Parameters for this operation, usually the object to access.
     * @return bool Permission granted?
     */
    public function checkAccess($operation, $params=array())
    {
    	//the user is not identified and thus has no rights
        if (empty($this->id)) {
            return false;
        }
        
        // super user has access to everything
        if ($this->role === 'super') {
            return true; 
        }
        
        // allow access if the operation request is the current user's role
        if(is_array($operation)){
        	foreach($operation as $value)
        	{
        		if($value === $this->role)
        		return true;
        	}
        }
        else{
        return ($operation === $this->role);
        }
        
        return false;
    }
    
    /**
     * Fetches the role of the current user from the DB
     */
    public function getRole()
    { 
    	if($this->_role!=null)
    	return $this->_role;
    	
    	$sql="SELECT role FROM user WHERE id=:userId";
    	$command=Yii::app()->db->cache(1000)->createCommand($sql);
    	$command->bindParam(':userId', $this->id, PDO::PARAM_INT);
    	return $this->_role = $command->queryScalar();

    }
}