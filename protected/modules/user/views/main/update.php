<?php
//Page titles


switch($this->action->id){
	case('updatepassword'):
	$this->sectionTitle="Change password";
	$this->sectionSubTitle="Change the password for user #".$model->id;
	$breadcrumb='Update Password';
	$help="";
	break;
	
	default:
	$this->sectionTitle="Update User";
	$this->sectionSubTitle="Update user #".$model->id;	
	$breadcrumb='Update';
	$help = $this->renderPartial('_help',array(),true);
}


//Page breadcrumbs
$this->breadcrumbs=array(
	'Users'=>array('admin'),
	$model->id=>array('update','id'=>$model->id),
	$breadcrumb,
);
?>

<?php echo $this->renderPartial('/main/_form',array('model'=>$model)); ?>
<?php echo $help;?>
