<?php
//Page titles
$this->sectionTitle='Login Details';
$this->sectionSubTitle='Retrieve your username and reset your password';

//Page breadcrumbs
$this->breadcrumbs=array(
	'Login Details'
);
?>
<p>Complete the form below to have your username and a password reset link sent to your email address.</p>
<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>