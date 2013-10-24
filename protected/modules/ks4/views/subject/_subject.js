$(document).ready(function () {
//Event for mobile
 $('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });
//Get the cohort-container form values & add to the main form
//
$('#submit-button').click(function(){
	appendCohortFields();//Defined in _common.js
		});//End click event

//Pass the cohort-container values to the main form
$('#cohort-container select').change(function(){
			appendCohortFields();
			$('#filter-form').submit();
});

//Dropdown row
$('#ks4subject-grid').find('table a.group-link').dropdownrow({
  noColumns:countColumns('#ks4subject-grid')
  });

//Add tool top to subject name
jQuery('span[rel="tooltip"]').tooltip();


//Add styles to table header so affixing the header works
$("#ks4subject-grid thead th").each(function() {
			$(this).width($(this).width());
	});


$("#ks4subject-grid tbody td").each(function() {
			$(this).width($(this).width());
	});

//Controls when the affix css is added on scroll down
//$('thead').affix({offset:354});

$('thead').affix({
offset: { top: $('thead').offset().top-40}
});

});//End document ready