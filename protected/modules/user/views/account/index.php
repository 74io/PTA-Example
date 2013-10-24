<?php
$this->sectionTitle="My Account";
$this->sectionSubTitle="Manage your account settings";
$this->breadcrumbs=array(
	'My Account'
);

if($model->superAccountType==0 && $model->role=='super')
$upgradeButton = "<br><br>Want to upgrade to premium? ".
$this->widget('bootstrap.widgets.TbButton',array(
	'label' => 'Learn more',
	'size' => 'mini',
	'type'=>'primary',
	'url'=>array('upgradeAccount'),
),true);
//CHtml::link('Learn more',array('updateAccount'),array('class'=>'btn btn-mini btn-primary'));

?>

<div>
<?php $this->widget('ext.bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'type'=>'',
	'attributes'=>array(
		array(
          'label'=>'Email',
           'type'=>'raw',
           'value'=>$model->email."<br><br>".CHtml::link('Edit',array('updateEmail'),array('class'=>'btn')),
        ),
		array(
          'label'=>'Username',
           'type'=>'raw',
           'value'=>$model->username."<br><br>".CHtml::link('Edit',array('updateUsername'),array('class'=>'btn')),
        ),
		array(
          'label'=>'Password',
           'type'=>'raw',
           'value'=>CHtml::link('Edit',array('updatePassword'),array('class'=>'btn')),
        ),
		'role',
		array(
          'label'=>'Account Type',
           'type'=>'raw',
           'value'=>strtoupper($model->superAccountWording).$upgradeButton,
        ),
       )
)); ?>
</div>
