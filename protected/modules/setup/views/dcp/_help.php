<i class="icon-question-sign"></i> <a href='#' class='show-help'>Show Help</a>
<div class="help-container">
<dl>
<dt>General Information</dt>
<dd>
<p>Both Name and Date can be edited directly by clicking on the <span class="editable">underlined text</span>.</p>
<p>You can create up to <?php echo Yii::app()->common->totalDcps;?> data collection points or targets per year group. 
 Once you have mapped results to DCPs/Targets you can view them in reports. The system automatically checks for new pupils and will prompt you to rebuild the result set if necessary. The system will also automatically remove leavers
  from existing result sets.</p>
</dd>

<dt>Name</dt>
<dd>
<p>The name of the DCP or Target.</p>
</dd>

<dt>Date</dt>
<dd>
<p>A point in time used for showing progress over time (will be used in future reports).</p>
</dd>

<dt>Result Set</dt>
<dd>
<p>The result set the DCP or Target takes its results from.</p>
</dd>

<dt>Default</dt>
<dd>
<p>By setting a DCP or Target as the default it will automatically be selected when viewing reports.</p>
</dd>

<dt>Last Built</dt>
<dd>
<p>The date and time the system last built the DCP/Target from its result set.</p>
</dd>

<dt>Requires Rebuild</dt>
<dd>
<p>Indicates whether the DCP or Target requires rebuilding. A 'Possibly?' link will be displayed if the system
detects that there are pupils on your MIS that are not part of the existing result set. You can use the link to review missing pupils.</p>
</dd>

<dt>Verify/Rebuild button group</dt>
<dd>
<p>Use this button group to verify or rebuild the result set.</p>
</dd>

</dl>
</div>