<?php
$this->sectionTitle='Registration Code';
$this->sectionSubTitle='Users can create an account on this system using a registration code';
//Page bread crumbs
$this->breadcrumbs=array(
	'Users'=>array('admin'),
	'Registration Code',
);
?>
<p>Users can register an account if they are in possession of a valid registration code. By setting a registration code
 below with an appropriate expiry date you can create a small window when users can register an account by clicking 
  the 'Register' link on the login page.</p>

<?php $this->renderPartial('_regCodeForm',array('model'=>$model)); ?>

<i class="icon-question-sign"></i> <a href='#' class='show-help'>Show Help</a>
<div class="help-container">
<dl>
    <dt>Default Role</dt>
    <dd>When users register an account they are automatically assigned the role of <strong>Staff</strong>. You can edit their role after 
    registration if appropriate.</dd>
    <dt>Case Sensitivity</dt>
    <dd>The registration code is case sensitive. Ensure that the code you pass on to staff is in the correct case.</dd>
    <dt>Expiry Date</dt>
    <dd>Select an expiry date of around a week for security purposes. You can shorten the expiry date or change the
    registration code at any time to prevent further registrations.</dd>
    </dl>
</div>