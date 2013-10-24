;(function ( $, window, document, undefined ) {
  $.widget( "pt.dropdownrow" , {
    options: {
    noColumns: 10,
    },

    _create: function()
    {
      var $row, $allRows;
      this.$allRows = $('table tr');
      
      //Binds with the context of 'this' maintained
      this._on(this.element, {
        click: "_handler"
      });

    },

      //Private method to remove previously inserted row and reset classes
      _reset: function()
      {
        this.$allRows.removeClass('selected');
        this.$allRows.css('font-weight','normal');
        this.$row.addClass('selected');
        this.$row.css('font-weight','bold');
        $('tr.drop-down').remove();
      },


      //Private method to fetch the data and insert it into the DOM
      _findAll: function(e){
          var that=this;
          $.ajax({
          url: e.target,
          cache: false
        })
        .done(function( html ) {
        that.$row.after('<tr class=\'drop-down\'><td colspan=\''+that.options.noColumns+'\'>'+html+'</td></tr>');
        $('#group-grid').slideDown();
        });
      },

      //Private method to remove/add classes
      _styleButton: function(e){
        //Clear Styles
        var $target = $(e.target);
        var $groupLink = $('a.group-link');
        var $buttonGroup = $('div.btn-group');

        $groupLink.parent().removeClass('group-active');
        $groupLink.removeClass('btn-info');
        $buttonGroup.find('a.dropdown-toggle').removeClass('btn-info');

         // The button clicked is a single button and not a drop down
         if($target.hasClass('group-link') && $target.hasClass('btn')){
          $buttonGroup.removeClass('open');
           $target.addClass('btn-info');
          }

      // The button clicked is a single button and not a drop down
      if($target.hasClass('group-link') && !$target.hasClass('btn')){
       $target.parents('div.btn-group').find('a.dropdown-toggle').addClass('btn-info');
      }
        $(this.element).parent().addClass('group-active');
        //this.$row.find('td').addClass('selected');
      },

      _handler: function(e){
        this.$row=$(e.target).closest('tr');
        this._reset();
        this._findAll(e);
        this._styleButton(e);
        return false;

      }

  });

})( jQuery, window, document );