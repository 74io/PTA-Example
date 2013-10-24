<?php
$this->sectionTitle="About";
$this->sectionSubTitle="Pupil Tracking Analytics (PTA)";
$this->breadcrumbs=array(
  'About',
);?>

<div class="span8">
<h3>Change Log</h3>
<p>Details of releases and changes to PTA are outlined below along with the build number. The
current build number is always displayed in the footer.</p>

<h5>Build 1.0.7 Released - 14/10/2013</h5>
<ul>
  <li><span class="label label-success">Fixed</span> - Fixed issue with excluded subjects appearing in pupil level reports. Within the subjects setup area
    it is possible to exclude subjects rather than deleting them. Although the summary, breakdown and subject reports calculated these subjects correctly
    they were still appearing on pupil level reports. This has now been fixed.</li>
</ul>

<h5>Build 1.0.6 Released - 16/09/2013</h5>
<ul>
<li><span class="label label-warning">New</span></i> - User interface changes. The position of the percentage and the number of pupils button has now
changed throughout. The percentage is displayed on the left and the number of pupils on the right. This reads much better because the eye automatically
scans the traffic lighted percentage and then moves right to the number of pupils button. The button itself is now highlighted blue when selected and the text for the selected row
becomes bold. </li>
<li><span class="label label-warning">New</span></i> - Should Achievers. Where appropriate the dropdown buttons now display a 'Should Achievers' link. 'Should Achievers' 
  are all the pupils not achieving a result for the currently selected data collection point (DCP) who should be according to their target. In a nutshell these
  are the pupils with the most potential to improve your performance table figures.</li>
<li><span class="label label-warning">New</span></i> - KS4 Breakdown Headlines report displays Cohort, Pupil Premium, No FSM, SEN, Girls, Boys
, EAL, CLA and Ethnic Minority across a range of key performance table figures in a single grid. Over 260 sub reports enable users to instantly see the achievers, non achievers and
should achievers within each area.</li>
<li><span class="label label-warning">New</span></i> - KS4 Breakdown Attainers report displays Cohort, High, Middle and Low English and Maths 
attainer groups across a range of key performance table figures in a single grid. Over 208 sub reports enable users to instantly see the achievers, non achievers and
should achievers within each area.</li>
<li><span class="label label-warning">New</span></i> - KS4 Breakdown SEN report. Compare Cohort, SEN, SEN No Statement, School Action, School Action Plus and
Statement groups across a range of key performance table figures in a single grid. 180 sub reports enable users to instantly see the achievers, non achievers and
should achievers within each area.</li>
<li><span class="label label-warning">New</span></i> - Persistent absence highlighted. For SIMStoPTP users, the percentage present cell is now highlighted red when
it is below 85%.</li>
<li><span class="label label-info">Info</span></i> - The default mode when viewing reports is now '2014 Onwards'.</li>
<li><span class="label label-info">Info</span></i> - The wording 'Indicators' within setup has now become 'Filters'. This makes more sense in the context of the application.</li>
<li><span class="label label-info">Info</span></i> - DCP Badges within the pupil level reports have now changed order. After manually checking over 600 reports against
  each pupil's pupil level report we found the optimum order for the eye to scan. The badges can very quickly be used to check and cross reference reports.</li>
  <li><span class="label label-info">Info</span></i> - The filter now once again follows you as you scroll down the page. However, you can unpin it using the link in the top right corner.<l/i>
  <li><span class="label label-success">Fixed</span> - Date format for date of birth when auto imported from SIMS.</li>
</ul>

<h5>Build 1.0.5 Released - 16/07/2013</h5>
<ul>
<li><span class="label label-warning">New</span></i> - Screen cast tutorials. Starting with 2 screen casts to help
SIMS.Net users export data from SIMS and import data to Analytics. Screen casts can be accessed from the relevant 'Watch Screen Cast'
links at the base of pages or by navigation to Support > Tutorials.</li>
</ul>

<h5>Build 1.0.4 Released - 04/06/2013</h5>
<ul>
<li><span class="label label-warning">New</span></i> - Discount codes can now be added for subjects where applicable. Simply visit the 'Subjects' area within setup
to add the necessary codes. Discount codes affect all A*-G summary figures in both school and pupil level reports. When viewing pupil level reports, discounted qualifications
appear faded for the current data collection point (DCP). The eligible qualification's point score is highlighted with a tick icon. Should the eligible target qualification be different
from the DCP qualification, this is also highlighted. Note, when viewing reports in '2014 Onwards' mode discounting will be applied. When viewing reports in 'Pre 2014' mode 
discounting will not be applied.</li>
<li><span class="label label-success">Fixed</span> - Fixed issue in pupil level reports where 'Capped 8' in equivalence mode ('2014 Onwards') was still displaying figures
	based upon each subject's volume indicator ('Pre 2014').</li>
<li><span class="label label-info">Info</span></i> - The wording for the 'Mode' filter when viewing reports has now changed and is more explicit. The 2 modes are now
'Pre 2014 - Using subject volume, no discounting' and '2014 Onwards - Using subject equivalent & discounting'. In September 2013 '2014 Onwards' will become the default mode.</li>
<li><span class="label label-info">Info</span></i> - Help for the subjects area within setup has been updated and now provides much more detail to help users when completing the 'Type'
	column. All guided tours have been updated to reflect discounting and report mode changes.</li>
<li><span class="label label-info">Info</span></i> - Information displayed within the header of pupil level reports now takes up less real estate on the page.</li>
<li><span class="label label-info">Info</span></i> - Minor changes to some application icons. All icons are now represented by fonts rather than images for faster load times.</li>
</ul>

<h5>Build 1.0.3 Released - 21/05/2013</h5>
<ul>
<li><span class="label label-warning">New</span></i> - Users can now filter on Pupil Premium when viewing reports.</li>
<li><span class="label label-info">Info</span></i> - Analytics builds core data (classes, sets, teachers, attendance etc.) whenever a report is viewed for the first time
that day or if an administrator clicks the 'Build Core Data' button within the setup area. If the building of core data fails when a report is viewed then an alert is now displayed.
For example, if you are a Pupil Tracking Plus (PTP) user and have not yet added the 'Pupil_Premium' to the list of general fields on PTP then the building of core data will fail
and you will receive a warning.</li>
<li><span class="label label-info">Info</span></i> - PTP users please note. Filters are cached for 10 minutes. Therefore if you manually import data into a field e.g. 'Pupil_Premium'
do not expect the filter to change instantly following a core data rebuild.</li>
</ul>

<h5>Build 1.0.2 Released - 20/04/2013</h5>
<ul>
<li><span class="label label-warning">New</span></i> - Users can now filter on multiple items within each filter including blanks. For example you can now filter on all pupils
not on the SEN register who are either Polish or French. A 'Reset Filter' link has also been added. Because the filter now has the potential to grow beyond the length of the page the sticky
behaviour has been removed.</li>
<li><span class="label label-warning">New</span></i> - Pupil level reports can now be accessed by clicking on the pupil's surname within all group listings.</li>
<li><span class="label label-warning">New</span></i> - Pupil level reports contain data collection point (DCP) badges. The badges easily identify achievement in the key areas. 
Blue badges display information e.g.
 Low, Middle or High attainer and KS2 APS. Green and red badges indicated true or false respectively in the following areas: 
 Ebacc, 5xA*-C, A*-C English, A*-C Maths, 3LP English, 3LP Maths, 4LP English, 4LP Maths.</li>
 <li><span class="label label-warning">New</span></i> - Pupil level reports contain various sub reports including summary of headline figures, residuals, subject averages and
  KS2 data/levels progress.</li>
<li><span class="label label-warning">New</span></i> - Pupil level reports contain 2 tracking charts that track progress across all data collection points (DCPs). The first
standardises all point scores which allows users to compare the achievement of each subject across all data collection points. A slump in achievement in one subject relative to others is easy to spot.
The second compares average point scores across all data collection points relative to the current target.</li>
<li><span class="label label-warning">New</span></i> - Pupil level reports contain attendance and teacher data for SIMStoPTP enabled schools.</li>
<li><span class="label label-warning">New</span></i> - School level reports now display date of birth in group listings. For SIMStoPTP enabled schools attendance data is also displayed.</li>

<li><span class="label label-warning">New</span></i> - KS2 Average Total Points has been added to school level reports.</li>
<li><span class="label label-info">Info</span></i> - Update of calendar widgets have been necessary following core framework upgrade.</li>
</ul>

<h5>Build 1.0.1 Released - 27/02/2013</h5>
<ul>
<li><span class="label label-warning">New</span></i> - Subject level reports displaying number of pupils achieving A*-A, A*-C, A*-G as well as average point score and number
of fails are now available. Users can drill down to pupils who are grouped in their classes for convenience. Traffic light colour coding is used throughout to highlight
pupils performing at, above and below target. For SIMStoPTP enabled schools the teacher's name is displayed above each class along with percentage present, percentage unauthorised
absences and number of lates.</li>
<li><span class="label label-warning">New</span></i> - Guided tours added to both the school and subject level reports. Look for the 'Take a Guided Tour' link at the base
  of the page.</li>
<li><span class="label label-success">Fixed</span> - Fixed issue with obsolete excluded pupils and classes not being cleared from the database when the entire set of class codes changed.
For clarification this issue effected schools that had manually excluded pupils and classes for a specific subject one day and then updated their entire set of class 
codes for the same subject to a completely different set of class codes the next day. The clean up routine is now more thorough.</li>
<li><span class="label label-success">Fixed</span> - Fixed issue with the modal dialogue disappearing off to the side when using the 'Verify' button on the DCP and target
  pages in IE9.</li>
<li><span class="label label-info">Info</span></i> - Speeded up build core data routine. When a user views a report or an administrator hits the 'Build Core Data' button 
  the core data is updated for that day's session. This routine is now up to 5 times faster for some schools.</li>
</ul>




<h5>Build 1.0.0 Released - 28/01/2013</h5>
<ul>
<li><span class="label label-warning">New</span></i> - The home page now displays menu items that the user will be forwarded to once logged in.
  The list of menu items/buttons will grow as more reports become available.</li>

<li><span class="label label-warning">New</span></i> - Animated help now guides users through the setup process providing prompts where necessary. Guided tours are also now available
on key pages and will be used in place of video tutorials whenever possible. For example it is now possible to take a guided tour of the Subjects area. Guided tour links are available
next to the help link on appropriate pages.</li>

<li><span class="label label-warning">New</span></i> - Very soon users will be able to sign up for a Pupil Tracking Analytics account online including a free account for a single user and
up to 2 DCPs and Targets. The 'My Account' area of PTA now offers the ability to upgrade an account if you are the super user. 
Note, Pupil Tracking Plus customers are automatically upgraded to premium for free.</li>

<li><span class="label label-warning">New</span></i> - Minor UI changes to the design of the header and footer area of the application.</li>

<li><span class="label label-warning">New</span></i> - For completeness the setup process now has a step 9 'View Reports'.</li>

<li><span class="label label-warning">New</span></i> - When filtering pupils from classes, pupils who have already been excluded from sets are highlighted in blue. This helps when
you need to map a single subject code to 2 different subjects and exclude the relevant pupils from each.</li>

<li><span class="label label-warning">New</span></i> - A feedback tab is now available at the bottom right of the application. This can be used to provide feedback and development ideas.
Note, this should not be used to report bugs. A bug report should be compiled as an email and sent to support@pupiltracking.com</li>

<li><span class="label label-success">Fixed</span> - When verifying a result set the 'Missing Pupils' tab displays pupils who have classes for subjects but who are missing 
  from this result set. This used to also include subjects that were not listed under the 'Subjects' area of the application. Now users will only be alerted if pupils are 'missing
  from the result set' if the subject is listed.</li>

<li><span class="label label-info">Info</span></i> - The help has been updated and is now hidden by default at the base of the page. Users can choose to display it if they wish.</li>
</ul>

<h5>Build 0.9.2 Released - 20/11/2012</h5>
<ul>
<li><span class="label label-warning">New</span> - When reviewing DCPs and Targets a new 'Verify' button enables
users to verify a result set. Users can explore missing pupils, missing subjects, missing results and fail results.
The tools can be used to validate a result set and troubleshoot result set discrepancies. For example, if a subject is given
a BTEC type qualification expecting results in the format P,M,D and your result set contains the GCSE grades A,B,C the 
verify tools will tell you that A,B,C are listed as results equal to a fail. </li>
<li><span class="label label-warning">New</span> - New in-line editing tools for easier editing of subjects, DCPs and targets. 
When selecting a qualification a list of accepted results along with whether the qualification will be supported in the 2014 performance tables is displayed.
These new in-line editing tools also work with iPad. The previous ones did not.</li>
<li><span class="label label-warning">New</span> - Changes to system architecture and queries to potentially support
unlimited data collection points, although the limit will remain 10 DCPs per year group for the time being.</li>
<li><span class="label label-warning">New</span> - Design changes to enhance the use of PTA on iPad. PTA uses a responsive
design that maximises the screen resolution of the device. So whether you are using a tiny old fashioned monitor in school or a 27inch iMac at home
the interface should resize to suit.</li>
<li><span class="label label-warning">New</span> - Full support for SIMS as a stand alone system. All data apart from
result sets (pupils, classes, ks2 data, attendance, teachers, EAL, FSM, SEN etc) are imported automagically. New import tools enable SIMS users to upload results sets. A default report definition
is provided and can be edited to help export the appropriate result set through the SIMS report designer. We have also written
algorithms to match aspect names back to subjects without the need to manually edit column headings. Also, because PTA has full working
knowledge of your pupils and classes it will extract only the data it needs from any imported result set. 
It's also easy to use the verify tools to verify imported data.</li>

<li><span class="label label-success">Fixed</span> - Fixed issue when trying to rebuild subjects, DCPs and targets when the
 data in question was missing from PTP. All data is now built, but a warning is displayed alerting the user to discrepancies.
 The warning is also logged for reference.</li>
 
 <li><span class="label label-success">Fixed</span> - Fixed issue with jerky interface when closing achievers/non achievers view. This was an 
 Internet Explorer 9 issue only.</li>
 
<li><span class="label label-info">Info</span> - Changes to wording used within the system. Shared fields on PTP are now
 referred to as 'result set' on PTA and the word 'set' or 'set code' has now been changed to the word 'class'.</li>
 
<li><span class="label label-info">Info</span> - The drop down button for '5 x A*-C inc English & Maths' when viewing a report
has been changed to a standard button which shows only the achievers. Displaying non achievers was ambiguous and a cause
of confusion as non achievers were not displayed because they had an A*-C in English or Maths. I.e. they were not
missing an A*-C in both English and Maths. It was a grey area so we took away the grey!</li>

<li><span class="label label-info">Info</span> - Added help to KS2 Data area explaining how levels for 
low, middle and high attainers and calculating 3 and 4 levels progress in English and Maths are treated. Links to
relevant resources are also provided.</li>

<li><span class="label label-info">Info</span> - Other small enhancements, help updates and fixes we don't want to bore you with.</li>
 
</ul>
<h5>Build 0.9.1 Released - 28/10/2012</h5>
<ul >
 <li><span class="label label-success">Fixed</span> - Issue with Levels Progress. Query updating pupils with A*-B (3 LP) and A*-A (4 LP) was overriding
 all other levels progress calculations. The result being that only pupils achieving grades in the range A*-B (3 LP) and A*-A (4 LP)
 were being displayed.</li>
 <li><span class="label label-success">Fixed</span> - Issue with pupils not matching any accepted results for qualifications. If a pupil did not match any
 of the accepted results for any subjects they currently took then they were not
 included in analysis totals. This affected both 'Show non achievers' and average totals. Now any result not included in
 the subject's accepted results list will be counted as a fail.</li>
 </ul>
 
 <div class="alert alert-info">
<strong>Please note. </strong> Changes to the list of supported qualifications and accepted results are not tied to our build cycle and
can be updated at any time. To see the
latest list of supported qualifications you should navigate to Setup > Subjects and consult the help section there. If you can't find the
qualification you are looking for then let us know.
</div>



</div>
