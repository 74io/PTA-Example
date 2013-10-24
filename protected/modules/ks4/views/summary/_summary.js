$(document).ready(function () {
  //Event for IOS
  /*
  $('.tab-content .dropdown-menu').on('touchstart', function(e) {
    e.stopPropagation();
  });*/

  $('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });


  $('#submit-button').click(function(){
        appendCohortFields();
        //Pass the active tab to the form
        var tab = $('#atoc ul li.active a').attr('href').replace('#atoc_tab_','')-1;
       $('<input>').attr({
       'type':'hidden',
       'name':'Ks4FF[activeTab]'})
       .val(tab).appendTo('#filter-form');
      $('#filter-form').submit();
      });

  $('#cohort-container select').change(function(){
     appendCohortFields();
       
  //Pass the active tab to the form
  var tab = $('#atoc ul li.active a').attr('href').replace('#atoc_tab_','')-1;
       $('<input>').attr({
       'type':'hidden',
       'name':'Ks4FF[activeTab]'})
       .val(tab).appendTo('#filter-form');
      
        $('#filter-form').submit();
  });

$('a.group-link').dropdownrow({
  noColumns:countColumns('#ks4summary-grid-1')
  });

  var w = $('#atoc .nav-pills').parent().width();
  $('#atoc .nav-pills').attr({'data-spy':'affix',
            'data-offset-top':'517'}).addClass('sticky-ks4pills').css('width',w);


  /*Place code in here if we do not want it actived on mobile platforms
  if( !isMobile.any() ){

  }
  */

});//End document ready