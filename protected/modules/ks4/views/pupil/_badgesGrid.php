<?php 
//KS2 APS
$ks2ApsBadge =  '<span class="badge badge-info"> KS2 APS: '.$dataProvider->rawData['ks2_average'].'</span>';

//Ebacc badge
$ebacc=false;
if($dataProvider->rawData['science_astar_c_ebacc']==1
	&& $dataProvider->rawData['lang_astar_c']==1
	&& $dataProvider->rawData['humanity_astar_c']==1
	&& $dataProvider->rawData['english_astar_c']==1
	&& $dataProvider->rawData['maths_astar_c']==1){
	$ebacc=true;
}
$ebaccBadge = ($ebacc) ? '<span class="badge badge-success">Ebacc</span>' : '<span class="badge badge-important">Ebacc</span>';

//Attainer badge
switch($dataProvider->rawData['ks2_attainer']){
	case 1:
	$attainer = "Low Attainer";
	break;
	case 2:
	$attainer = "Middle Attainer";
	break;
	case 3:
	$attainer = "High Attainer";
	break;
	default:
	$attainer = "No Prior Attainment";
}
$attainmentBadge = '<span class="badge badge-info">'.$attainer.'</span>';

//5xA*-C Badge
$fiveAstarToCBadge = ($dataProvider->rawData['astar_c']>=5) ? '<span class="badge badge-success">5xA*-C</span>' : '<span class="badge badge-important">5xA*-C</span>';

//A*-C English
$astarToCEnglishBadge = ($dataProvider->rawData['english_astar_c']) ? '<span class="badge badge-success">A*-C English</span>' : '<span class="badge badge-important">A*-C English</span>';

//A*-C Maths
$astarToCMathsBadge = ($dataProvider->rawData['maths_astar_c']) ? '<span class="badge badge-success">A*-C Maths</span>' : '<span class="badge badge-important">A*-C Maths</span>';

//3 levels progress English
$lp3EnglishBadge = ($dataProvider->rawData['english_lp3']) ? '<span class="badge badge-success">3LP English</span>' : '<span class="badge badge-important">3LP English</span>';

//3 levels progress Maths
$lp3MathsBadge = ($dataProvider->rawData['maths_lp3']) ? '<span class="badge badge-success">3LP Maths</span>' : '<span class="badge badge-important">3LP Maths</span>';

//4 levels progress English
$lp4EnglishBadge = ($dataProvider->rawData['english_lp4']) ? '<span class="badge badge-success">4LP English</span>' : '<span class="badge badge-important">4LP English</span>';

//4 levels progress Maths
$lp4MathsBadge = ($dataProvider->rawData['maths_lp4']) ? '<span class="badge badge-success">4LP Maths</span>' : '<span class="badge badge-important">4LP Maths</span>';


?>

<!--Begin render badges-->
<ul class="inline" style="margin:0;">
	<li><strong>DCP Badges:</strong></li>
	<li><?php echo $attainmentBadge;?></li>
    <li><?php echo $ks2ApsBadge;?></li>
	<li><?php echo $fiveAstarToCBadge;?></li>
	<li><?php echo $astarToCEnglishBadge;?></li>
	<li><?php echo $lp3EnglishBadge;?></li>
	<li><?php echo $lp4EnglishBadge;?></li>
	<li><?php echo $astarToCMathsBadge;?></li>
	<li><?php echo $lp3MathsBadge;?></li>
    <li><?php echo $lp4MathsBadge;?></li>
    <li><?php echo $ebaccBadge;?></li>
</ul>
<!--End render badges-->

