<?php

/**
 * This is the model class for table "eventlog".
 *
 * The followings are the available columns in table 'eventlog':
 * @property integer $id
 * @property integer $user_id
 * @property string $level
 * @property string $category
 * @property string $message
 * @property string $date_time
 */
class EventLog extends CActiveRecord
{
	
	public $username;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EventLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'eventlog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('level', 'length', 'max'=>10),
			array('category', 'length', 'max'=>20),
			array('message', 'length', 'max'=>255),
			array('date_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, username, level, category, message, date_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		     return array(
            'user'=>array(self::BELONGS_TO, 'User', array('user_id'=>'id'),
		    'select'=>'user.username'
		    ),
        );
	}
	
	
	/**
	 * The default scope of queries used for this model
	 * @see CActiveRecord::defaultScope()
	 */
	
	public function defaultScope()
    {
    	
        return array();
    }
    

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user.username'=>'User',
			'user_id' => 'User',
			'level' => 'Level',
			'category' => 'Category',
			'message' => 'Message',
			'date_time' => 'Date Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->with = array('user');
		$criteria->compare('t.id',$this->id);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('user.username',$this->username,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('message',$this->message,true);
		
		if(strlen($this->date_time)) {
	        $criteria->params[':date_time'] = $this->date_time;
	        $criteria->addCondition("DATE_FORMAT(date_time,'%d-%m-%Y')=:date_time");
		}
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'sort'=>array(
				'defaultOrder'=>'t.id DESC',
		        'attributes'=>array(
		            'username'=>array(
		                'asc'=>'user.username',
		                'desc'=>'user.username DESC',
		            ),
		            '*',
		        ),
		    ),
		));
	}

	/**
	 * Fires after the record has been retrieved from the database
	 */
	public function afterFind()
	{
		$this->date_time = Yii::app()->dateFormatter->format('dd-MM-yyyy HH:mm:ss',$this->date_time);
		
	}
	
	/**
	 * Renders a column in CGridView or a row in CDetailView
	 */
	public function renderLevelColumn($data,$row)
	{
		$level = (is_object($data)) ? $data->level : $data;
		
		$evaluateLevel=strtolower($level);
		$level = ucfirst($level);
		switch($evaluateLevel){
			case"error":
				$html='<span class="label label-important">'.$level.'</span>';
				break;
			case"warning":
				$html='<span class="label label-warning">'.$level.'</span>';
				break;
			case"success":
				$html='<span class="label label-success">'.$level.'</span>';
				break;
			case"info":
				$html='<span class="label label-info">'.$level.'</span>';
				break;
		}
		
		return $html;
	}
	
	/**
	 * Renders a column in CGridView or a row in CDetailView
	 */
	public function renderUserColumn($data,$row)
	{
		$user = (is_object($data)) ? $data->user->username : $data;
		if($user==null)
		return 'System';
		else
		return $user;
	}
}