<?php $this->beginContent('//layouts/main'); ?> 

<!--Begin Header-->
<?php $this->beginContent('//layouts/_header'); ?>
<?php $this->endContent(); ?>
<!--End Header-->

<!--Begin Alert-->
<?php $this->beginContent('//layouts/_alert'); ?>
<?php $this->endContent(); ?>
<!--End Alert-->

<!--Begin 2 column content-->
<div class="row">
  <div class="span10">
      <?php echo $content; ?>
  </div>
  <?php if($this->menu):?>
  <div class="span2">
    <div class="well" style="padding: 20px 0; min-height:500px;">

  <?php $this->widget('bootstrap.widgets.TbMenu', array(
  'items'=>$this->menu,
  'type'=>'list',
  //'htmlOptions'=>array('class'=>'operations'),
  ));?>
  </div><!--End well-->
  </div><!--End span-->
  <?php endif;?>

  <?php if($this->clips['ks4Filter']):?>
    <div class="span2">
    <div class="well sticky-ks4filter"  style="margin-top:20px;">
  <?php echo $this->clips['ks4Filter']?>
  </div><!--End well-->
  </div><!--End span-->
  <?php endif;?>
  
</div><!--End row-->
<!--End 2 column content-->

<?php $this->endContent(); ?>