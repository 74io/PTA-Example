<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    
	<?php $baseUrl=Yii::app()->theme->baseUrl;?>
	<?php Yii::app()->clientScript->registerCssFile($baseUrl.'/css/font-awesome.min.css');?>
    <?php Yii::app()->clientScript->registerCssFile($baseUrl.'/css/screen.css');?>

    <!--[if IE]>
	<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl.'/css/ie.css' ?>" />
	<![endif]-->
    <?php Yii::app()->clientScript->registerCoreScript('jquery');?> 
    <?php Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-widget-factory.min.js');?>

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements and respond.js for media query support-->
    <!--[if lt IE 9]>
      <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
      <script src="/js/respond.min.js"></script>
    <![endif]-->

	<!-- Le other CSS and JS -->
	<script type="text/javascript">
	$(document).ready(function () {	
		$.ajaxSetup({
		cache: false
		});	
		$('.ajax-loading').ajaxStart(function() {
		$(this).show();
		}).ajaxStop(function() {
		$(this).hide();
		}); 

		$(".alert-message").ajaxError(function(event, request, settings){
			  $(this).html('<?php echo $this->ajaxBootAlert()?>').hide().fadeIn();
			});

	});
	</script>
	
	
	<!-- Le favicon -->
    <link rel="shortcut icon" href="/images/favicon.ico">

	<!-- Le Google Tracking Code -->
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-1199892-1']);
	  _gaq.push(['_setDomainName', 'pupiltracking.com']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
</script>
  </head>
  <body>
		<?php $this->widget('bootstrap.widgets.TbNavbar',array(
		    	'brand'=>'Pupil Tracking Analytics',
				'type'=>'inverse',
				'collapse'=>true, // requires bootstrap-responsive.css
    			'brandUrl'=>'/',
    			'items'=>array(
				        array(
				            'class'=>'bootstrap.widgets.TbMenu',
				            'items'=>array(
				   
		        			array('label'=>'Reports','visible'=>(!Yii::app()->user->isGuest && $this->schoolSetUp['defaultCohort']),
							'items'=>array(
		            			array('label'=>'KS4 Summary', 'url'=>array('/ks4')),
		            			array('label'=>'KS4 Breakdown', 'url'=>array('/ks4/breakdown')),
		            			array('label'=>'KS4 Subject', 'url'=>array('/ks4/subject')),
		            		
		        			)),
		        			
		        			array('label'=>'Setup','visible'=>Yii::app()->user->checkAccess(array('admin','data manager')),
							'items'=>array(
		        				array('label'=>'Overview', 'url'=>array('/setup')),
		            			array('label'=>'My School', 'url'=>array('/setup/myschool/admin')),
		            			array('label'=>'Cohorts', 'url'=>array('/setup/cohort/admin')),
		            			array('label'=>'Filters', 'url'=>array('/setup/indicator/admin')),
		            			array('label'=>'KS2 Data', 'url'=>array('/setup/keystage/admin')),
		            			array('label'=>'Build Core Data', 'url'=>array('/setup/build/admin')),
		            			array('label'=>'Subjects', 'url'=>array('/setup/subject/admin')),
		            			array('label'=>'Data Collection Points', 'url'=>array('/setup/dcp/admin')),
		            			array('label'=>'Targets', 'url'=>array('/setup/target/admin')),
		            			'---',
		            			array('label'=>'Results', 'url'=>array('/setup/result'),'visible'=>(Yii::app()->user->checkAccess(array('admin','data manager')) && $this->schoolSetUp['mis']=='SIMS')),
		            			array('label'=>'Users', 'url'=>array('/user'),'visible'=>Yii::app()->user->checkAccess('admin')),
		            			array('label'=>'Log', 'url'=>array('/event'),'visible'=>Yii::app()->user->checkAccess('admin')),
		        			)),
		        			
		        			array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
	
			)),
	
						     array(
					            'class'=>'bootstrap.widgets.TbMenu',
					            'htmlOptions'=>array('class'=>'pull-right'),
					            'items'=>array(
						        			array('label'=>'Support', 'htmlOptions'=>array('class'=>'pull-right'), 'visible'=>!Yii::app()->user->isGuest,
						        				'items'=>array(
						        					array('label'=>'About','url'=>array('/site/about'),'visible'=>!Yii::app()->user->isGuest),
						        					array('label'=>'Tutorials','url'=>array('/site/tutorials'),'visible'=>!Yii::app()->user->isGuest),
						        				)),
						        		    array('label'=>Yii::app()->user->name, 'url'=>array('/user/account'), 'visible'=>!Yii::app()->user->isGuest,
						        			'icon'=>'user white', 
											'items'=>array(

												array('label'=>'My Account', 'url'=>array('/user/account'), 'visible'=>!Yii::app()->user->isGuest),	
												array('label'=>'Logout', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
												)),				
					            ),
					        ),
				        ),//End items
	));?>
	

<div id="header">
	<div class="container">
	<div class="row">
		<div class="span5">
			    <?php if (isset($this->breadcrumbs)):?>
			    <?php $this->widget('bootstrap.widgets.TbBreadcrumbs',array(
			        'links'=>$this->breadcrumbs,
			        'separator'=>'/',
			    )); ?>
				<?php endif?>
		</div>
	<div class="span7">			
	<h3 class="pull-right school-name"><?php echo $this->schoolSetUp['name']?>
	<?php if($this->schoolSetUp['defaultCohort']):?>
	<?php echo " (".$this->schoolSetUp['defaultCohort'].")"?>
	<?php endif?>
	</h3>
	</div>
		</div>
		</div><!-- End container -->
		</div><!-- End header -->
			
	<div class="container main">
		<?php echo $content;?>
	</div><!-- End container -->
	      <footer class="footer">
	           <div class="container">
        			<p class="pull-right"><a href="#" id="totop"><strong>Back to top</strong> <i class="icon-arrow-up"></i></a></p>
          			<p>&copy; Pupil Tracking Limited <?php echo date('Y')." | Build 1.0.7" ?></p>
          </div>
        </footer>
        <?php Yii::app()->clientScript->registerScript('help', "
		$('.show-help').toggle( function(e){
			$('.help-container').slideDown();
			$(e.target).text('Hide Help');
			return false;
		},
		function (e){
			$('.help-container').slideUp();
			$(e.target).text('Show Help');
			return false;
		});
		$('#totop').click(function () {
		$('body, html').animate({
		  scrollTop: 0
	    }, 300);
		return false;
	  });");?>

<!--Start Uservoice js-->
<script type="text/javascript">
  var uvOptions = {};
  (function() {
    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/K8bNCaymG4cWVEdTxaXAVA.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>
<!--End Uservoice js-->
  </body>
</html>