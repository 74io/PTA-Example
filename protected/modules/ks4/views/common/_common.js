/**
 * Appends the fields from the cohort form to the filter form
 */
function appendCohortFields()
{
    $('#cohort-container select').each(function(index,obj){
    $('<input>').attr({
        'type':'hidden',
        'name':$(obj).attr('name')
      }).val($(obj).val()).appendTo('#filter-form');
    });
}

/**
 * Counts the number of columns in a table
 * @return {[type]} [description]
 */
function countColumns (el){
        var colCount = 0;
        $(el+' table tr:nth-child(1) td').each(function () {
        if ($(this).attr('colspan')) {
            colCount += +$(this).attr('colspan');
        } else {
            colCount++;
        }
        });
        return colCount;
}

$('#report-menu a').click(function(e){
        appendCohortFields();
        var $qs = '?'+$('#filter-form').serialize();
        var $href = $(e.target).attr('href');
        $href = $href.replace('#','');
        //alert($href);
        $(e.target).attr('href',$href+$qs);
});


//Event to start eguiders tour
$('.start-tour').click(function(){
    guiders.show('first');
});

$(document).ready(function () {
    /**
     * Add click event to pupil's surname link which contains all the data we need.
     * Load modal and resize accordingly.
     * Trigger a click on the first tab/pill so that it loads after the modal is displayed
     */
    $('.ks4-container').on('click', 'a.pupil', function(event){
      $('#modal').modal().css({
      'width': function () {
           return ($(document).width() * .8)+'px';
        },
        'height': function () {
           return ($(window).height() * .8)+'px';
        },
        'margin-left': function () {
           return -($(this).width() / 2);
        },
        'margin-top': function () {
           return -($(this).height() / 2);
        }
      });

      var data = $(this).data();
      $('#modal-label').text(data.forename+' '+data.surname+' '+data.yeargroup+data.form+' DOB '+data.dob);
      $('.modal-body').data(data);

      $('#pupil-pills a:first').trigger('click');
      event.preventDefault();

      return false;

    });//End onClick .pupil event


    /**
     * When a tab fires the shown event then load the data.
     * Here we use the id (set in the TbTabs widget) strip off the # and use that to form the URL
     */
    $('.modal a[data-toggle="tab"]').on('shown', function (e) {
      var mydata = $('.modal-body').data();
      var url =  e.target.hash.replace('#','');
     $.get('/ks4/pupil/'+url+'/?'+$.param(mydata), function(response) {
     $(e.target.hash).html(response);
      });
    });
    
    /**
     * Here we use the hidden event of the modal to remove the active class from the tab/pill so it can be called again using the trigger.
     */
    $('body').on('hidden', '.modal', function () {
      $('#pupil-pills li:first').removeClass('active');//Remove the active class so the trigger event will fire
    });


    $('body').on('shown', '#tracking-tabs a[data-toggle="tab"]', function () {
      chart.setSize($('#tracking-tabs_tab_1 .highcharts-container').width(),$('#tracking-tabs_tab_1 .highcharts-container').height());
    });

});