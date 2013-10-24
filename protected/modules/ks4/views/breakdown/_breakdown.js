
$(document).ready(function () {
    //Event for Mobile
  $('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });

  //Append the cohort-container form elements to the filter form
  $('#cohort-container select').change(function(){
     appendCohortFields();
     $('#filter-form').submit();
  });


$('#ks4breakdown-grid-0').find('table a.group-link').dropdownrow({
  noColumns:countColumns('#ks4breakdown-grid-0')
  });

//Control showing/hiding color coding
//
$('#toggle-color').on('click', function(e){
	var $target = $(e.target);
	var text = (e.target.text=="Hide colour coding") ? "Show colour coding" : "Hide colour coding";
	$('#ks4breakdown-grid-0').find('table.items tr td').toggleClass('white');
	$target.text(text);
	return false;
});

$('#toggle-targets').on('click', function(e){
	var $target = $(e.target);
	var text = (e.target.text=="Show target %") ? "Hide target %" : "Show target %";
	$('.target').fadeToggle();
	$target.text(text);
	return false;
});

//Add styles to table header so affixing the header works
$("#ks4breakdown-grid-0 thead th").each(function() {
			$(this).width($(this).width());
	});

$("#ks4breakdown-grid-0 tbody td").each(function() {
			$(this).width($(this).width());
	});

//Controls when the affix css is added on scroll down use $(window).scrollTop() to get correct height
$('thead').affix({
offset: { top: $('thead').offset().top-40}
});


});