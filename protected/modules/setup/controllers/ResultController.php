<?php
/**
 * Results controller
 * @TODO Note. That if results that are mapped to same subject are imported twice they will be counted twice. For example if the subject English (En)
 * was on the system and then someone created the subject English Lit that was also mapped to 'En' and then imported data for 'English' and 'English Lit'
 * then data for 'En' would be duplicated. The danger of this happening could be fixed by removing duplicates after the import of a CSV file.
 */
class ResultController extends Controller
{
	/*
	 * @var array An array of CMenu items
	 */
	public $defaultAction='admin';
	
	public $menu=array(
	array('label'=>'Manage Results','url'=>array('/setup/result/admin')),
	array('label'=>'Import Results','url'=>array('/setup/result/import')),
	);
 
	/*
	 * Override filters()
	 */
	public function filters()
	{
		// return the filter configuration for this controller
		return array(
				'accessControl',
            );	
	}

	/**
	 * @see CController::accessRules()
	 */
	public function accessRules()
	{   
	    return array(
	        array('allow',
	            'roles'=>array('admin','data manager'),
	        ),
	        array('deny'),//Deny all users
	    );
	}
	
	/**
	 * Maps actions to action classes
	 * @see CController::actions()
	 */
    public function actions()
    {
        return array(
            'upload'=>array(
                'class'=>'application.modules.setup.actions.ResultUploadAction',
                'path' =>Yii::app() -> getBasePath() . "/../../../uploads",
            ),
        );
    }
    
    /**
     * The import result set action
     */
	public function actionImport() {
	        $model = new ResultUploadForm;
	        $this -> render('import', array('model' => $model, ));
	    }

	    
    /**
     * Default action
     */
    public function actionAdmin()
    {
        $model=new Result('search');
		/* Removed here because of ERememberFiltersBehavior in model
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Result']))
			$model->attributes=$_GET['Result'];
	*/
        $this->render('admin',array(
            'model'=>$model,
        ));
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
        $model=Result::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
    
    
    /**
     * A special action used with editable to update attributes edited inline
     * @return void
     */
	public function actionUpdate()
	{
		    $es = new TbEditableSaver('Result');
		    try {
		        $es->update();
		    } catch(CException $e) {
		        echo CJSON::encode(array('success' => false, 'msg' => $e->getMessage()));
		        return;
		    }

		    Yii::app()->eventLog->log("success",PtEventLog::RESULT_1,"Result ID {$es->model->id} was updated.");
		    echo CJSON::encode(array('success' => true));
		    Yii::app()->end();
	}
	
    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            $model=$this->loadModel($id);
            ResultUploadForm::deleteResults($id);
            
            //Log the event
		    $message = $model->file_name." was deleted.";
			Yii::app()->eventLog->log("info",PtEventLog::IMPORT_1,$message);
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }
	
	    
	/**
	 * Note that this is a POST request, but with get vars attached. This deletes an entry through the
	 * import result set interface and not from the manage results. See actionDelete for this
	 * @param string $_method The method passed from the json
	 * @param string $file The full path to the file
	 * @param integer $resultId The result id in the resultmapping table
	 * @return void
	 */
	public function actionDeleteResult($_method,$file,$resultId,$name)
    {
    	if(Yii::app()->request->isPostRequest){
	            if( isset( $_method ) ) {
		            if( $_method == "delete" ) {
		                $success = is_file( $file ) && $file[0] !== '.' && unlink( $file );
		                ResultUploadForm::deleteResults($resultId);
		                $message = $name." was deleted.";
						Yii::app()->eventLog->log("info",PtEventLog::IMPORT_1,$message);
		                echo json_encode( $success );
		                yii::app()->end();
		            }
	        }
	    }
    }	        
}