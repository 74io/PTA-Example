<?php

class SubjectController extends Controller
{
	public $portletTitle = "Subjects";
	
	public $menu=array(
	array('label'=>'Manage Subjects','url'=>array('/setup/subject/admin')),
	array('label'=>'Create Subject','url'=>array('/setup/subject/create'),'linkOptions'=>array('id'=>'create')),
	);
	

	/*
	 * Override filters()
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
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Subject('create');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Subject']))
		{
			$model->attributes=$_POST['Subject'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> A new subject has been created.");
				if($_POST['add'])
				$this->redirect(array('create'));
				$this->redirect(array('admin'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
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
		$model->scenario='update';

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Subject']))
		{
			$model->attributes=$_POST['Subject'];
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Success!</strong> The subject has been updated.");
				$this->redirect(array('admin','id'=>$model->id));
			}
		}

		$this->render('update',array(
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
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Subject');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Subject('search');
		
		if($missingSubjects = Subject::getMissingSubjects()){
		$missingSubjects=implode(",",$missingSubjects);
		
		Yii::app()->user->setFlash("warning", "<strong>Warning!</strong> The subjects <strong> $missingSubjects</strong> exist on this system but do no longer exist on your MIS. To ensure
		the integrity of your reports it is advised that you delete these subjects.");
		}

		/* Removed here because of ERememberFiltersBehavior in model
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Subject']))
			$model->attributes=$_GET['Subject'];
			*/

		if($_GET['ajax']){
		$this->renderPartial('/subject/_grid',array(
			'model'=>$model,
		));
		}
		else{
		$this->render('/subject/admin',array(
			'model'=>$model,
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
		$model=Subject::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='subject-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Updates the subject include column. This action recieves id and value from $.ajax
	 * call within the _grid view 
	 */
	public function actionAjaxUpdateInclude()
	{
		Subject::updateInclude($_POST);
		Yii::app()->end();
	}
	
	/**
	 * Sets Action
	 */
	public function actionSets($id)
	{
			$model=$this->loadModel($id);
			$yearGroups=Yii::app()->common->getYearGroupsForKeyStage($model->key_stage);
			
			$setsWithYearGroup = Subject::getSubjectSetsForYearGroups($model->mapped_subject,$yearGroups,$model->cohort_id);

			if($setsWithYearGroup===null){
			Yii::app()->user->setFlash('warning',"There are no classes for ".$model->mapped_subject." at key stage ".$model->key_stage);	
			$this->redirect(array('admin'));
			
			}
			$filteredSets=Subject::getExcludedSets($model->id);
			
			foreach($yearGroups as $year){
			if($setsWithYearGroup[$year]===null)
			$setsWithYearGroup[$year]=array();
			$dataProvider[$year]=new CArrayDataProvider($setsWithYearGroup[$year],array(
		    'pagination'=>array(
        		'pageSize'=>50,
    			),	
		));
			}
			
			$this->render('sets',array(
			'yearGroups'=>$yearGroups,
			'filteredSets'=>$filteredSets,
			'dataProvider'=>$dataProvider,//As an array
			'model'=>$model,
		));
	}
	
	/**
	 * Loads the content for a specific year group pill
	 * @IDEA Add the isAjaxRequest and exception logic as a fliter?
	 */
	public function actionAjaxLoadYearGroupPill($year,$id)
	{
		if(Yii::app()->request->isAjaxRequest){
			$model=$this->loadModel($id);
			
			$sets=Subject::getSubjectSetsForYearGroup($model->mapped_subject,$year,$model->cohort_id);
	
			//If there are no sets we still need to create the tab in order for jQuery DOM events to fire
			if(empty($sets))
			$sets=array('No sets');
			
			$this->renderPartial('_pill',array(
			'filteredSets'=>Subject::getExcludedPupilsSets($model->id),
			'excludedSets'=>Subject::getExcludedSets($model->id),
			'sets'=>$sets,
			'model'=>$model
		));
		
		}
		else {
		throw new CHttpException(404,'The requested page does not exist.');
		}
		Yii::app()->end();
	}
	
	/**
	 * Loads the sets tab via ajax
	 * @param string $set
	 * @param integer $id
	 * @return void
	 */
	public function actionAjaxLoadSetTab($set,$id)
	{

		$set=urldecode($set);
		$model=$this->loadModel($id);
		$alreadyExcluded = Subject::getPupilsExcludedAlready($model, $set);
		
		$rawData = Subject::getPupilsInFilteredSet($model->mapped_subject,$set,$model->cohort_id);
		//Get a list of UPNs that have been excluded from the actual settings table and
		//build another array element using this value
		$pupils=Subject::getExcludedPupils($model->id,$set);
		$filteredSets=Subject::getExcludedSets($model->id);


		//Is the set in the list of filtered sets
		if(in_array($set,$filteredSets))
		$itemsCssClass='filtered-set';

		//Get excluded pupils and generate key for CSS
		foreach ($rawData as $key=>$value)
		{
			if(in_array($rawData[$key]['pupil_id'],$pupils)){
			$rawData[$key]['exclude']=true;
			$rawData[$key]['css']='excluded';
			}

			if(in_array($rawData[$key]['pupil_id'],$alreadyExcluded)){
			$rawData[$key]['css'].=' already-excluded';
			}

		}

		$dataProvider=new CArrayDataProvider($rawData,array(
		    'pagination'=>array(
        		'pageSize'=>50,
    			),
		));
		
		$this->renderPartial('_pupilGrid',array(
			'set'=>$set,
			'itemsCssClass'=>$itemsCssClass,
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
		Yii::app()->end();
	}
	
	/**
	 * Updates the excludedpupils table. This method recieves the posted vars $subject_id, $pupil_id and $checked from
	 * $.ajax call in sets.php view named updateExcludedPupil
	 */
	public function actionAjaxUpdateExcludedPupil()
	{
		Subject::updateExcludedPupil($_POST);
		Yii::app()->end();
	}

	public function actionAjaxUpdateExcludedSet()
	{
		Subject::updateExcludedSet($_POST);
		Yii::app()->end();
	}
	
	
    /**
     * A special action used with editable to update attributes edited inline
     * @return void
     */
	public function actionEditableUpdate()
	{
		$es = new TbEditableSaver('Subject');
		try {
		    $es->update();
		} catch(CException $e) {
		    echo CJSON::encode(array('success' => false, 'msg' => $e->getMessage()));
		    return;
		}
   
		Yii::app()->eventLog->log("success",PtEventLog::SUBJECT_2,"{$es->attribute} was updated for subject ID {$es->model->id}.");
		echo CJSON::encode(array('success' => true));
		Yii::app()->end();
	}
	
	/**
	 * Returns qualification details from the ks4pointscore table
	 * @param string $qualification
	 */
	public function actionGetAcceptedResults($qualification)
	{
		$acceptedResult = Subject::getAcceptedResults($qualification);
		foreach($acceptedResult as $key=>$value){
			$results[]=$value['result'];
			$inclusion = $value['inclusion_2014'];
		}
		$inclusionHtml=($inclusion=='No') ? '<span class="label label-important">No</span>' : '<span class="label label-success">Yes</span>';
		$results = implode(", ",$results);
		echo "<strong>Accepted Results:</strong><br>$results<br>Inclusion in 2014 Performance tables:$inclusionHtml";
		Yii::app()->end();
	}
	
	/**
	 * Auto generates subjects based upon a schools existing subjects
	 */
	public function actionAutoCreateSubjects()
	{
		Yii::app()->build->autoCreateSubjects();
		Yii::app()->user->setFlash('success','<strong>Success!</strong> Subjects have been automatically created.');	
		Yii::app()->user->setFlash('info',"Now would be a good time to <i class='icon-play-circle'></i> <a href='#' class='start-tour'><strong>Take a Guided Tour</strong></a>. 
			You can take a guided tour at any time by clicking the link at the base of this page.");
		$this->redirect(array('admin'));
	}
	
	/**
	 *  Ks4 Qualifications Action
	 */
	public function actionKs4Qualifications()
	{
		$rawData = Subject::getKs4Qualifications();
		
		$dataProvider=new CArrayDataProvider($rawData,array(
		    'pagination'=>array(
        		'pageSize'=>300,
    			),
		));
		
		$this->renderPartial('_qualificationsGrid',array(
			'dataProvider'=>$dataProvider));
		
	}
	
}
