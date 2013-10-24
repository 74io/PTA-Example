<i class="icon-question-sign"></i> <a href='#' class='show-help'>Show Help</a>
<i class="icon-play"></i> <a href='/site/tutorials#importing-results' class="screen-cast" target="_blank">Watch Screen Cast</a>
<div class="help-container">
<dl>
<dt>General Information</dt>
<dd><p>The import interface is robust enough to take anything you want to throw at it. So if you think you have
a file you wish to import then go ahead and import it. The system will let you know if it experiences any problems with the
file or cannot match any subjects etc. You don't even need to worry about the pupils contained in the file. When you map the
result set to a data collection point or target the system will automatically extract the correct results based upon its privileged
knowledge of your pupils and classes. So your result set could contain mixture of Y10 and Y11 pupils. If you map it to a Y11 data collection point
it will only extract results for Y11 pupils who actually have classes for subjects.</p></dd>

<dt>Exporting a Result Set From SIMS</dt>
<dd>
<ul><li>Step 1
<?php $this->widget('bootstrap.widgets.TbButton',array(
	'label' => 'Download SIMS Report Definition',
	//'type' => 'primary',
	'size' => 'mini',
	'icon'=>'icon-download',
	'url'=>'/downloads/pt_result_set.rptdef',
));?> and save it to your computer. The file is called pt_result_set.rptdef
</li>
<li>Step 2 - Open SIMS.Net and navigate to <strong>Reports > Design Report > Open an existing report</strong>. 
A new window will open. At the top of that window click the tools icon and select <strong>Import</strong>. 
Browse to the location of the pt_result_set file you just downloaded and import it</li>
<li>Step 3 - Open the pt_result_set report (Focus > Student) and immediately save it with a new name using <strong>Save As...</strong> You can then use
the pt_result_set report as a template to generate new reports in the future.</li>
<li>Step 4 - On the left under Report Summary click on <strong>Data Fields</strong>. Select <strong>Assessment Results (filtered)</strong>.
At the base of the page you will see the filter options. Select <strong>Result is [blank]</strong> and click <strong>Modify</strong>.
Under <strong>Condition is one of</strong> uncheck the [blank] option and instead check the appropriate result set and click <strong>OK</strong>.
You can add further filters if needed</li>
<li>Step 5 - On the left under Report Summary click <strong>Filter Students</strong>. Select <strong>Year group is Year 11</strong> and then
click <strong>Modify</strong>. Under <strong>Condition is one of</strong> check/uncheck the appropriate year group and click <strong>OK</strong></li>
<li>Step 6 - On the left under Report Summary click <strong>Default Output</strong>. The presentation should be of type Text. If not you need to select it.
Next to the right of the Format box select <strong>Comma separated</strong>. Doing this step before you select file output location gives the file the correct file
extension. Next click the <strong>Browse</strong> button and select a location and name for the outputted file. At this point careful file house keeping will
make life easier in the future. Give the file as specific a filename as possible e.g. 'Y11 Spring Grade Results' and save it in a logical place
you can locate in the future. After closing the dialog you should see the path to your file. It will most likely end in .txt rather
than .csv but don't worry it will import without problem. </li>
<li>Step 7 - Finally click the <strong>Finish</strong> button and then click <strong>Run my report</strong>. A csv file should be outputted to
your chosen location. The next time you need this data simply run the report and the most up-to-date data will
be outputted to the same location from where it can easily be selected for import to this system.</li>
<li>Step 8 - Finished! You can now import this file to Pupil Tracking Analytics</li>
</ul>
</dd>

<dt>File Format</dt>
<dd>
<p>Once you have exported the data using the method above you should be good to go. However, if you are
troubleshooting or manually creating a result set then read on.</p>
<p>The system accepts 3 columns of data in the format UPN, Aspect Name, Result. These will be matched to Pupil ID, Subject, Result on the system.
To prevent you from having to rename your aspects the system will look for either the SIMS subject code or the subject name you have
mapped to the subject code on the system. If it finds either of these patterns within the aspect name it will match it to the subject.
<br><br>
See the example below:</p>
<table class="table table-bordered table-condensed table-striped" style="width:50%;">
<thead>
<tr>
<th>UPN (Pupil ID)</th><th>Aspect Name (Subject)</th><th>Result</th></tr>
</thead>
<tbody>
<tr class="odd"><td>Z820200102073</td><td>En Spring Grade</td><td>A</td></tr>
<tr class="even"><td>M820200100079</td><td>Ar Spring Grade</td><td>B</td></tr>
<tr class="odd"><td>E820432108002</td><td>Drama Spring Grade</td><td>A*</td></tr>
<tr class="even"><td>R820432107018</td><td>Fr Spring Grade</td><td>C</td></tr>
<tr class="odd"><td>E820200103058</td><td>History Spring Grade</td><td>A*</td></tr>
<tr class="even"><td>Y820200107002</td><td>Ma Spring Grade</td><td>A</td></tr>
<tr class="odd"><td>K820432184007</td><td>Pe Spring Grade</td><td>C</td></tr>
<tr class="even"><td>Y820432184046</td><td>Technology Spring Grade</td><td>A*</td></tr>
<tr class="odd"><td>Q820432184049</td><td>Sc Spring Grade</td><td>E</td></tr>
<tr class="even"><td>D820200102037</td><td>Mu Spring Grade</td><td>G</td></tr>
</tbody>
</table>
<p>As long as either the SIMS subject code (e.g. En) or the subject name on the system (e.g. English) is part of the aspect column name
then it will be matched to the subject. For example in the above table En will be matched to En because the SIMS subject code is present
in the aspect name. Drama will also be matched to Dr because the system knows that you have mapped the subject Drama to Dr.
When naming aspects on SIMS it is good practice to include the subject code as part of the aspect name. If neither the subject code nor the subject
name is part of the aspect name then neither human nor computer will be able to decipher which subjects belong to which results. In this case
 you will need to manually edit the file in Excel to include the subject codes.
 </p>
</dd>

<dt>Result Name (optional)</dt>
<dd>
<p>The name of the result set. If a result name is not provided on import then the filename will
be used. If the name clashes with another result set name a unique number will be prepended.</p>
</dd>

<dt>Description (optional)</dt>
<dd>
<p>A description can be used to further describe the contents
of the imported file.</p>
</dd>

<dt>First Row Contains Column Headings</dt>
<dd>
<p>If the first row of your CSV file contains the column headings (normal) then leave this box checked. If it doesn't uncheck it.</p>
</dd>

</dl>
</div>