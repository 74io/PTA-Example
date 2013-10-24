$('.main').on('click', '.pupil', function(event){

  $('#modal').modal().css({
       'width': function () { 
           return ($(document).width() * .8) + 'px';  
       },
       'height': function () { 
           return ($(window).height() * .8) + 'px';   
       },
       'margin-left': function () { 
           return -($(this).width() / 2); 
       },
       'margin-top': function () { 
           return -($(this).height() / 2);
       }
  });


});