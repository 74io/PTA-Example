<?php

class LogController extends Controller
{
	public $defaultAction='admin';
	public $menu=array(
	array('label'=>'Manage Events','url'=>array('/event/log/admin')),
);

	/**
	 * @see CController::filters()
	 */
	public function filters()
	{
	    return array(
	        'accessControl', // perform access control for CRUD operations
	    );
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new EventLog('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['EventLog']))
			$model->attributes=$_GET['EventLog'];

		if($_GET['ajax']){
		$this->renderPartial('admin',array(
			'model'=>$model,
		));
		}
		else{
		$this->render('admin',array(
			'model'=>$model,
		));
		}
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=EventLog::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
