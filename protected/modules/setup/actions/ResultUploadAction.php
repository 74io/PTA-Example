<?php
/**
 * XUploadAction
 * =============
 * Basic upload functionality for an action used by the xupload extension.
 *
 * XUploadAction is used together with XUpload and XUploadForm to provide file upload funcionality to any application
 *
 * You must configure properties of XUploadAction to customize the folders of the uploaded files.
 *
 * Using XUploadAction involves the following steps:
 *
 * 1. Override CController::actions() and register an action of class XUploadAction with ID 'upload', and configure its
 * properties:
 * ~~~
 * [php]
 * class MyController extends CController
 * {
 *     public function actions()
 *     {
 *         return array(
 *             'upload'=>array(
 *                 'class'=>'xupload.actions.XUploadAction',
 *                 'path' =>Yii::app() -> getBasePath() . "/../uploads",
 *                 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads",
 *                 'subfolderVar' => "parent_id",
 *             ),
 *         );
 *     }
 * }
 *
 * 2. In the form model, declare an attribute to store the uploaded file data, and declare the attribute to be validated
 * by the 'file' validator.
 * 3. In the controller view, insert a XUpload widget.
 *
 * ###Resources
 * - [xupload](http://www.yiiframework.com/extension/xupload)
 *
 * @version 0.3
 * @author Asgaroth (http://www.yiiframework.com/user/1883/)
 */
class ResultUploadAction extends CAction {
    /**
     * The query string variable name where the subfolder name will be taken from.
     * If false, no subfolder will be used.
     * Defaults to null meaning the subfolder to be used will be the result of date("mdY").
     *
     * @see XUploadAction::init().
     * @var string
     * @since 0.2
     */
    public $subfolderVar;

    /**
     * Path of the main uploading folder.
     * @see XUploadAction::init()
     * @var string
     * @since 0.1
     */
    public $path;

    /**
     * Public path of the main uploading folder.
     * @see XUploadAction::init()
     * @var string
     * @since 0.1
     */
    public $publicPath;

    /**
     * The resolved subfolder to upload the file to
     * @var string
     * @since 0.2
     */
    private $_subfolder = "";

    /**
     * Initialize the propeties of this action, if they are not set.
     *
     * @since 0.1
     */
    
    public function init( ) {
    	
    	//dir with group apache write permission
         $this->path = Yii::app( )->getBasePath( )."/../../uploads";
        
    }
    

    /**
     * The main action that handles the file upload request.
     * @since 0.1
     * @author Asgaroth
     */
    public function run() {

            $this->init();
            $this->headers;
            $model = new ResultUploadForm;
            $model->attributes=$_POST['ResultUploadForm'];
            $model->file = CUploadedFile::getInstance( $model, 'file' );
            
            if( $model->file !== null ) {
                $model->mime_type = $model->file->getType();
                $model->size = $model->file->getSize();
                $model->name = $model->file->getName();
                $model->pathToFile = $this->path."/".uniqid().$model->name;
                
    
                if( $model->validate( ) ) {
                    $model->file->saveAs( $model->pathToFile );
                     
                    //Import CSV contents to DB  
					if($model->loadResults()!==true){
					$model->deleteResults($model->resultId);
					$this->deleteFile($model->pathToFile);
					
					echo json_encode( array( array( "error" => $model->errorMessage,
													"errorDetail"=>$model->notMatchedHtml ) ) );

					Yii::app()->end();
					}
					
					$this->deleteFile($model->pathToFile);
                    echo json_encode( array( array(
                            "name" => $model->name,
                            "type" => $model->mime_type,
                            "size" => $model->size,
                    		"path" => $this->path,
                            //"url" => $publicPath.$model->name,
                    		"resultName" => $model->resultName,
                    		"resultNumRecords" => $model->resultNumRecords,
                    		"resultSample" => CHtml::encode($model->resultSample),//Had to encode for IE8
                            "thumbnail_url" => true,
                            "delete_url" => $this->getController( )->createUrl( "deleteResult", array(
                                			"_method" => "delete",
                                			"file" => $model->pathToFile,
                    						"resultId"=>$model->resultId,
                    						"name"=>$model->name
                            				)),
                            "delete_type" => "POST"
                        ) ) );
                        
                } else {
                    echo json_encode( array( array( "error" => $model->getErrors( 'file' ), ) ) );
                    Yii::log( "XUploadAction: ".CVarDumper::dumpAsString( $model->getErrors( ) ), CLogger::LEVEL_ERROR, "xupload.actions.XUploadAction" );
                    
                }
            } else {
                throw new CHttpException( 500, "Could not upload file" );
            }
            Yii::app()->end();
    }
    
    /**
     * Output necessary headers
     */
    public function getHeaders()
    {
        header( 'Vary: Accept' );
        if( isset( $_SERVER['HTTP_ACCEPT'] ) && (strpos( $_SERVER['HTTP_ACCEPT'], 'application/json' ) !== false) ) {
            header( 'Content-type: application/json' );
        } else {
            header( 'Content-type: text/plain' );
        }
    	
    }
    
    /**
     * Deletes a file
     * @param string $file The path to the file that needs deleting
     */
    public function deleteFile($file)
    {
    		//Delete the file
			if(file_exists($file))
			unlink($file);
    	
    }
    


}
