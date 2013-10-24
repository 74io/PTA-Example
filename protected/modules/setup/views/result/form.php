<!-- The file upload form used as target for the file upload widget -->
<?php $this->htmlOptions['class']='form-horizontal';?>
<?php echo CHtml::beginForm($this -> url, 'post', $this -> htmlOptions);?>
<div class="control-group">
<?php echo CHtml::activeLabel($this->model, 'resultName',array(
		'class'=>'control-label'))?>
<div class="controls">
<?php echo CHtml::activeTextField($this->model, 'resultName',array(
		'class'=>'span4',
		//'title'=>'A unique name for this result set that will be used when setting up DCPs and Targets. E.g. \'Y11 Autumn Grade\'',
		'maxlength'=>50));?>
		<span class='muted'><small>A unique name for this result set e.g. 'Y11 Spring Results'</small></span>
</div>
</div>

<div class="control-group">
<?php echo CHtml::activeLabel($this->model, 'resultDescription',array('class'=>'control-label'))?>
<div class="controls">
<?php echo CHtml::activeTextArea($this->model, 'resultDescription',array(
		'class'=>'span4',
		//'title'=>'A description that can be used to identify this result set (optional)',
		))?>
		<span class='muted'><small>(optional) A description that can be used to identify this result set</small></span>
</div>
</div>

<div class="control-group">
<?php echo CHtml::activeLabel($this->model, 'resultFirstRow',array('class'=>'control-label'))?>
<div class="controls">
<?php echo CHtml::activeCheckBox($this->model, 'resultFirstRow')?>
</div>
</div>

<div class="control-group">
<div class="controls">
<div class="row fileupload-buttonbar">
	<div class="span7">
		<!-- The fileinput-button span is used to style the file input field as button -->
		<span class="btn btn-primary fileinput-button"> <i class="icon-plus icon-white"></i> <span>Add file</span>
			<?php
            if ($this -> hasModel()) :
                echo CHtml::activeFileField($this -> model, $this -> attribute, $htmlOptions) . "\n";
            else :
                echo CHtml::fileField($name, $this -> value, $htmlOptions) . "\n";
            endif;
            ?>
		</span>
		<!-- 
		<button type="submit" class="btn btn-primary start">
			<i class="icon-upload icon-white"></i>
			<span>Start upload</span>
		</button>
		<button type="reset" class="btn btn-warning cancel">
			<i class="icon-ban-circle icon-white"></i>
			<span>Cancel upload</span>
		</button>
		<button type="button" class="btn btn-danger delete">
			<i class="icon-trash icon-white"></i>
			<span>Delete</span>
		</button>
		<input type="checkbox" class="toggle">
		 -->
	</div><!-- End span7 -->
	</div><!-- End fileupload-buttonbar -->
	</div><!-- End controls -->
	</div><!-- End control-group -->
	
	
	<div class="control-group">
	<div class="controls">
    <!-- The global progress information -->
    <div class="span5 fileupload-progress fade">
    <!-- The extended global progress information -->
          <div class="progress-extended">&nbsp;</div>
          	</div>
    	</div><!-- End controls -->
	</div><!-- End control-group -->
       


<!-- The table listing the files available for upload/download -->
<div class="control-group">
<div class="controls">
<table class="table table-striped">
	<tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
</table>
</div>
</div>
<?php echo CHtml::endForm();?>
