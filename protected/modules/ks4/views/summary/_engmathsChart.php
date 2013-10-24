<h3 style="text-align:center;">Venn Diagram for Current DCP</h3>
<div id="venn">
<img src="<?php echo Yii::app()->request->baseUrl.'/images/venn.gif'?>" />

<div class="circle-a">
<?php echo $dataProvider->rawData[0]['col1']?><br>
<?php echo $dataProvider->rawData[0]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[0]['col2'])?>
</div>

<div class="circle-b">
<?php echo $dataProvider->rawData[1]['col1']?><br>
<?php echo $dataProvider->rawData[1]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[1]['col2'])?>
</div>


<div class="circle-c">
<?php echo $dataProvider->rawData[2]['col1']?><br>
<?php echo $dataProvider->rawData[2]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[2]['col2'])?>
</div>

<div class="top-a">
<?php echo $dataProvider->rawData[4]['col1']?><br>
<?php echo $dataProvider->rawData[4]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[4]['col2'])?>
</div>

<div class="middle-a">
<?php echo $dataProvider->rawData[5]['col1']?><br>
<?php echo $dataProvider->rawData[5]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[5]['col2'])?>
</div>

<div class="middle-b">
<?php echo $dataProvider->rawData[3]['col1']?><br>
<?php echo $dataProvider->rawData[3]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[3]['col2'])?>
</div>

<div class="middle-c">
<?php echo $dataProvider->rawData[6]['col1']?><br>
<?php echo $dataProvider->rawData[6]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[6]['col2'])?>
</div>

<div class="bottom-a">
<?php echo $dataProvider->rawData[7]['col1']?><br>
<?php echo $dataProvider->rawData[7]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[7]['col2'])?>
</div>

<div class="bottom-b">
<?php echo $dataProvider->rawData[9]['col1']?><br>
<?php echo $dataProvider->rawData[9]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[9]['col2'])?>
</div>
 
<div class="bottom-c">
<?php echo $dataProvider->rawData[8]['col1']?><br>
<?php echo $dataProvider->rawData[8]['col2']?>%<br>
No pupils: <?php echo $component->getNumber($dataProvider->rawData[8]['col2'])?>
</div>


</div><!-- End Venn -->