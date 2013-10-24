<?php $this->beginContent('//layouts/main'); ?> 

<!--Begin Header-->
<?php $this->beginContent('//layouts/_header'); ?>
<?php $this->endContent(); ?>
<!--End Header-->

<!--Begin Alert-->
<?php $this->beginContent('//layouts/_alert'); ?>
<?php $this->endContent(); ?>
<!--End Alert-->

<!--Begin 1 column content-->
<div class="row">
  <div class="span12">
      <?php echo $content; ?>
  </div>

</div><!--End row-->
<!--End 1 column content-->

<?php $this->endContent(); ?>