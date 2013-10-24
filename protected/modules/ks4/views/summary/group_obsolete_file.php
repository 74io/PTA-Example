<?php 
exit;

$hasMisAccess = PtMisFactory::mis()->hasMisAccess();
echo $hasMisAccess; 
exit;?>

<?php 
/*$this->renderPartial('_groupGrid',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
			'component'=>$component,
			'hasMisAccess'=>$hasMisAccess,
		));*/