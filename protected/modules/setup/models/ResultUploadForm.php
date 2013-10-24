<?php
class ResultUploadForm extends CFormModel
{
        public $file;
        public $mime_type;
        public $size;
        public $name;
        public $resultName;
        public $resultDescription;
        public $resultFirstRow=true;
        public $resultId;
        public $resultNumRecords;
        public $notMatched=array();
        public $errorMessage;
        public $pathToFile;
        /**
         * Declares the validation rules.
         * The rules state that username and password are required,
         * and password needs to be authenticated.
         */
        public function rules()
        {
                return array(
                		array('resultName, resultDescription, resultFirstRow','safe'),
						array('resultName','filter','filter'=>array($this,'filterResultName')),
                		array('resultName','filter','filter'=>array('PtFilter','stripSpecialCharsRelaxed')),
						array('resultName','filter','filter'=>array($this,'filterResultNameUnique')),
						array('resultDescription','filter','filter'=>array('PtFilter','stripHtml')),
                        array('file', 'file','types'=>array('csv','txt'),'maxSize'=>5242880),//5MB
                        //array('file', 'file'),
                );
        }

        /**
         * Declares attribute labels.
         */
        public function attributeLabels()
        {
                return array(
                		'resultName'=>'Result Name',
                		'resultDescription'=>'Result Description',
                		'resultFirstRow'=>'First row contains column headings',
                        'file'=>'Upload files',
                );
        }
        
        /**
         * Custom filter function
         * @return string
         */
        public function filterResultName($attribute)
        {
        	if($attribute==""){
        	$attribute = str_replace(".csv","",$this->name);
        	$attribute = str_replace(".txt","",$attribute);
        	return $attribute;
        	}
        	else
        	return $attribute;
	
        }
        
        /**
         * Custom filter function to ensure that the name is unique
         * @return string
         */
        public function filterResultNameUnique($attribute)
        {
        	$resultNames = Result::getResultSetFieldNames();
        	if($resultNames){
        	if(in_array(strtolower($attribute), array_map('strtolower', $resultNames)))//Non case sensitive search
        	return uniqid().$attribute;
        	}
        	
        	return $attribute;
        }
        
        /**
         * Loads a csv file into the database
         * @return bool
         */
        function loadResults()
        {
        	
        	$subjects = Subject::getSubjectKeyValuePairs();
        	
        	$this->resultId = $this->loadResult();
        	$startSql = "INSERT INTO resultdata  (resultmapping_id, pupil_id, mapped_subject, result) VALUES";
        	$filter = new PtFilter;
        	
        	
			if (($handle = fopen($this->pathToFile, "r")) !== false) {
			    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
			    		//Skip parsing of first row if it contains the column heading
				    	if($this->resultFirstRow){
				    		$this->resultFirstRow=false;
				    		continue;
				    	}
			
				    	//Only parse if the upn is not an empty string
				    	if($data[0]!=''){
					    	//UPN
					    	$data[0] = $filter->stripSpecialChars($data[0]);
					    	
					    	//Subject
					    	$data1 = $this->matchSubject($data[1],$subjects);
						    	if($data1===false){
						    	$this->notMatched[]=$data[1];
						    	}
					    	
					    	// Result
					    	$data[2] = addslashes(($data[2]));	
					        $insertArray[]="('$this->resultId','$data[0]','$data1','$data[2]')";
				        
				    	}
			        
			    }
			    fclose($handle);


			//If there are subjects not matched abort the import
			if($this->notMatched){
				$this->errorMessage = "The following subjects in your file could
										not be matched against any subject codes(mapped subjects) or subject names
										on this system:";
				$message = $this->name." contained invalid subjects. No data was imported.";
				Yii::app()->eventLog->log("warning",PtEventLog::IMPORT_1,$message);
				return false;
			}


			$this->resultNumRecords=count($insertArray);
			$sql=implode(",",$insertArray);
			$commitSql=$startSql.$sql;
			
			
			//Set max_allowed_packet=16M
			$maxSql="SET GLOBAL max_allowed_packet=16*1024*1024";
			Yii::app()->db->createCommand($maxSql)->execute();
			
        	try{
				Yii::app()->db->createCommand($commitSql)->execute();
				}
			catch(Exception $e){
					//The error message to be returned in the json
					$this->errorMessage = "There was a problem with your file check the log for details. No data has been imported.";
					//The error message to be logged
					$message="Upload CSV issue - ".$e->errorInfo[2];
					Yii::app()->eventLog->log("error",PtEventLog::IMPORT_1,$message);
					return false;
					}
			$message = $this->name." was imported.";
			$this->updateNumRecords();
			Yii::app()->eventLog->log("success",PtEventLog::IMPORT_1,$message);
			return true;
	        }
	        else{
	        	return false;
	        }
        	
        }
        
        /**
         * Loads a single result entry row into the resultmapping table and returns the insert id
         * @return integer
         */
        public function loadResult()
        {
        	$cohort = Cohort::getCurrentCohort();
        	$sql="INSERT INTO resultmapping (cohort_id,user_id, name,file_name,description,date_time) VALUES 
        			(:cohort_id, :user_id, :name, :fileName, :description, NOW()) ";
			$command=Yii::app()->db->createCommand($sql);
			$command->bindParam(':cohort_id', $cohort['id'], PDO::PARAM_STR);
			$command->bindParam(':user_id', Yii::app()->user->id, PDO::PARAM_INT);
			$command->bindParam(':name', $this->resultName, PDO::PARAM_STR);
			$command->bindParam(':fileName', $this->name, PDO::PARAM_STR);
			$command->bindParam(':description', $this->resultDescription, PDO::PARAM_STR);
            $command->execute();
            
            return Yii::app()->db->getLastInsertID();	
        }
        
        /**
         * Updates the number of records imported in the resultmapping table
         * @return void
         */
        public function updateNumRecords()
        {
        	$sql="UPDATE resultmapping SET num_records = :numRecords WHERE id=:resultId";
			$command=Yii::app()->db->createCommand($sql);
        	$command->bindParam(':numRecords', $this->resultNumRecords, PDO::PARAM_INT);
        	$command->bindParam(':resultId', $this->resultId, PDO::PARAM_INT);
            $command->execute();
        }
        
        /**
         * Deletes a result set from the system
         * @param integer $resultId The id of the result set
         */
        public static function deleteResults($resultId)
        {
        	$sql="DELETE FROM resultmapping WHERE id='$resultId'";
        	Yii::app()->db->createCommand($sql)->execute();
        	
        	$sql="DELETE FROM resultdata WHERE resultmapping_id='$resultId'";
        	Yii::app()->db->createCommand($sql)->execute();
        }
        
        
        /**
         * Returns a string containing an html table displaying the top 10 results just uploaded
         * @return string
         */
        public function getResultSample()
        {
        	$sql="SELECT * FROM resultdata WHERE resultmapping_id='".$this->resultId."' LIMIT 10 ";
        	$rows = Yii::app()->db->createCommand($sql)->queryAll();
        	
        	$dataProvider=new CArrayDataProvider($rows,array(
		    'pagination'=>array(
        		'pageSize'=>50,
    			),
		));
		
			return Yii::app()->controller->widget('bootstrap.widgets.TbGridView',array(
			'id'=>'result-sample-grid',
			'htmlOptions'=>array('style'=>'padding-top:0'),
			 'type'=>'bordered condensed',
			'template'=>'{items}',
			'dataProvider'=>$dataProvider,
			'columns'=>array(
					'pupil_id:html:Pupil ID',
					'mapped_subject:html:Subject',
					'result:html:Result',
				),
			),true); 
        }
        
        /**
         * Looks for either the subect code e.g. En or the subject name e.g. English in the string
         * and replaces it with the subject code if found
         * @param string $string The string to be searched
         * @params array $subjects An array of subjects in key=>value format e.g. en=>English
         * @return string
         */
       	public function matchSubject($string,$subjects)
       	{
    		
    		foreach($subjects as $key=>$value){
    	
		       	if (preg_match("/\b".$value['mapped_subject']."\b/i", $string))
		    		return $value['mapped_subject'];
		    			
		       	if (preg_match("/\b".$value['subject']."\b/i", $string))
		    		return $value['mapped_subject'];
    		
    		}
    		
    		return false;	
       	}
       	
       	/**
       	 * Returns a string of html containing the rows that were not matched
       	 * @return string
       	 */
       	public function getNotMatchedHtml()
       	{
       		$notMatched = array_unique($this->notMatched);
       		$html="<h5>Subjects</h5>";
       		$html.="<ul>";
       		foreach($notMatched as $value){
       			$html.="<li>".$value."</li>";
       		}
       		$html.="</ul>";
       		$html.="<p><strong>No data has been imported.</strong></p>";
       		return $html;
       	}
}
