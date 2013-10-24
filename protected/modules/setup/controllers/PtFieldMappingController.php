<?php
class PtFieldMappingController extends Controller
{
	
	/**
	 * @see CController::filters()
	 */
	public function filters()
	{
		// return the filter configuration for this controller
		return array(
				'accessControl',
                array('application.filters.SetUpFilter',
                'url'=>$this->createUrl($this->id.'/admin'),
				'schoolSetUp'=>$this->schoolSetUp,
            ));	
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
	
	public function getControllerSectionTitle()
	{
		return ($this->id=="dcp") ? "DCP" : "Target";
		
	}
	

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new FieldMapping('create');
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$displayResultInfo=false;
		if($this->schoolSetUp['mis']!="PTP")
		$displayResultInfo = !Result::getNumResults();

		if(isset($_POST['FieldMapping']))
		{
			$model->attributes=$_POST['FieldMapping'];
			if($model->save()){
				Yii::app()->eventLog->log("success",PtEventLog::FIELDMAPPING_1,"Field mapping ID {$model->id} was created.");
				Yii::app()->user->setFlash('success','<strong>Success!</strong> A new '.$this->controllerSectionTitle.' has been created.');
				if($_POST['add'])
				$this->redirect(array('create'));
				$this->redirect(array('admin'));
		
			}
		}

		$this->render('/dcp/create',array(
			'model'=>$model,
			'displayResultInfo'=>$displayResultInfo,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$model->scenario="update";
		$model->old_mapped_field = $model->mapped_field;

		if(isset($_POST['FieldMapping']))
		{
			$model->attributes=$_POST['FieldMapping'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> ".$model->mapped_alias .' has been updated.');
				Yii::app()->eventLog->log("success",PtEventLog::FIELDMAPPING_2,"Field mapping ID {$model->id} was updated.");
				$this->redirect(array('admin'));
		
			}
		}

		$this->render('/dcp/update',array(
			'model'=>$model,
		));
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
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	/**
	 * Action review
	 * @param integer $id
	 */
	public function actionReview($id)
	{
		$model=$this->loadModel($id);
		$array = $model->getMissingFromResultSet($id);
		
		$dataProvider=new CArrayDataProvider($array, array(
			'pagination'=>array(
        	'pageSize'=>10,
    		),
    		 'sort'=>array(
        		'attributes'=>array(
             	'pupil_id','surname', 'forename', 'year','form'
        	),
        ),
		));
		
		$this->render('/dcp/review',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new FieldMapping('search');
		/* Removed here because of ERememberFiltersBehavior in model
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Dcp']))
			$model->attributes=$_GET['Dcp'];
	*/
		$displayResultInfo=false;
		if($this->schoolSetUp['mis']!="PTP")
		$displayResultInfo = !Result::getNumResults();
		
		
		if($_GET['ajax']){
		$this->renderPartial('/dcp/_grid',array(
			'model'=>$model,
		));
		}
		else{
		$this->render('/dcp/admin',array(
			'model'=>$model,
			'displayResultInfo' =>$displayResultInfo,
		));
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=FieldMapping::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='dcp-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Rebuilds the subject data for a DCP/Target
	 * @param integer $id The FieldMapping id
	 */
	public function actionBuildSubjectData($id)
	{
		$model=$this->loadModel($id);
		$model->scenario="build";
		if($model->save()){
			}
			else{
				$attributeNames = $model->attributeNames();
				foreach($attributeNames as $attribute)
				{
					$error=$model->getError($attribute);
					if($error!==null)
					$errors[] = $error;
				}
				$errors=implode("<br>",$errors);
				Yii::app()->user->setFlash('error','<strong>Error!</strong> '.$errors);
			}
			
			$this->redirect(array('admin'));
	}
	
    /**
     * A special action used with editable to update attributes edited inline
     * @return void
     */
	public function actionEditableUpdate()
	{
		    $es = new TbEditableSaver('FieldMapping');
		    try {
		        $es->update();
		    } catch(CException $e) {
		        echo CJSON::encode(array('success' => false, 'msg' => $e->getMessage()));
		        return;
		    }
		    
		    Yii::app()->eventLog->log("success",PtEventLog::FIELDMAPPING_2,"{$es->attribute} was updated for field mapping ID {$es->model->id}.");
		    echo CJSON::encode(array('success' => true));
		    Yii::app()->end();
	}
	
	/**
	 * Ajax action for verifying DCP results
	 * @param integer $id The dcp id
	 */
	public function actionVerifyResults($id)
	{
		$rawData = FieldMapping::getVerifyResults($id);
		$dataProvider=new CArrayDataProvider($rawData,array(
		    'pagination'=>array(
        		//'pageSize'=>20,
    			),
    		'sort'=>array(
        		'attributes'=>array(
             	'pupil_id','surname','subject','set_code','result'),
        ),
		));
		$this->cleanForPartial();
  		$this->renderPartial('/dcp/_verifyResultsGrid',array(
			'dataProvider'=>$dataProvider),false,true);
		Yii::app()->end();
	}
	
	/**
	 * Ajax action for verifying DCP subjects
	 * @param integer $id The dcp id
	 */
	public function actionVerifySubjects($id)
	{
		$rawData = FieldMapping::getVerifySubjects($id);
		$dataProvider=new CArrayDataProvider($rawData,array(
		    'pagination'=>array(
        		//'pageSize'=>20,
    			),
    		'sort'=>array(
        		'attributes'=>array(
             	'mapped_subject','subject'),
        ),
		));
		$this->cleanForPartial();
  		$this->renderPartial('/dcp/_verifySubjectsGrid',array(
			'dataProvider'=>$dataProvider),false,true);
		Yii::app()->end();
	}
	
	/**
	 * Ajax action for verifying DCP pupils
	 * @param integer $id The dcp id
	 */
	public function actionVerifyPupils($id)
	{
		$rawData = FieldMapping::getVerifyPupils($id);
		$dataProvider=new CArrayDataProvider($rawData,array(
		    'pagination'=>array(
        		//'pageSize'=>20,
    			),
    		'sort'=>array(
        		'attributes'=>array(
             	'pupil_id','surname','subject','set_code'),
        ),
		));
		$this->cleanForPartial();
  		$this->renderPartial('/dcp/_verifyPupilsGrid',array(
			'dataProvider'=>$dataProvider),false,true);
		Yii::app()->end();
	}
	
	
	/**
	 * Ajax action for verifying DCP fails
	 * @param integer $id The dcp id
	 */
	public function actionVerifyFails($id)
	{
		$rawData = FieldMapping::getVerifyFails($id);
		$dataProvider=new CArrayDataProvider($rawData,array(
		    'pagination'=>array(
        		//'pageSize'=>7,
    			),
    		'sort'=>array(
        		'attributes'=>array(
             	'pupil_id','surname','subject','qualification','result'),
        ),
		));
		$this->cleanForPartial();
  		$this->renderPartial('/dcp/_verifyFailsGrid',array(
			'dataProvider'=>$dataProvider),false,true);
		Yii::app()->end();
	}
	
	public function renderFailsSubjectColumn($data,$row)
	{
		return $data['mapped_subject']." (<small>".$data['subject'].")</small>";
	}
	
	public function renderFailsQualificationColumn($data,$row)
	{
		return '<small>'.$data['qualification'].'</small>';
	}
}