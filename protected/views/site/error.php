<?php
$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>

<?php if($code=='404'):?>
<h2>Oops! This page cannot be found</h2>
<h2><small>Error <?php echo $code; ?></small></h2>
<div class="error">
<?php //echo CHtml::encode($message); ?>
</div>

<p>The page you are looking for does not exist. Use the navigation bar to navigate to the correct page.</p>
<?php endif;?>


<?php if($code=='403'):?>
<h2>Access Denied</h2>
<h2><small>Error <?php echo $code; ?></small></h2>
<div class="error">
<?php //echo CHtml::encode($message); ?>
</div>
<p>You are not authorized to access this page.</p>
<?php endif;?>

<?php if($code=='500'):?>
<h2>Oops! Something went wrong</h2>
<h2><small>Error <?php echo $code; ?></small></h2>
<div class="error">
<?php //echo CHtml::encode($message); ?>
</div>
<p>We have been notified and will get onto it pronto!</p>
<?php endif;?>