var dragCheck = false;

$(function() {
  
  $("#save_schedule").click (function(){
    $("input[name='save_schedule']").val("1");
    $("#hidden_form").submit();
  });
  
  $('#search_kids').on( "keyup", function() {
    var input = $(this).val();
    $(".all-kids .label").show();
    if ( input != '' )
      $(".all-kids .label").not("[data-name*="+ input +"]").hide();
  });
  
  $( ".draggable" ).draggable({
    prependTo: "body",
    helper: "clone"
  })
  .on('click', '.close-me', function(){
      
      if ( !$("#save_schedule").hasClass("btn-warning") )
        $("#save_schedule").removeClass("btn-success").addClass("btn-warning");
      
      var label = $(this).parent('.label');
      var label_id = label.attr('data-id');
      var droppable = $(this).parents('.droppable');
      var ids = droppable.attr('data-ids').split(',');
      ids = jQuery.grep(ids, function(value) {
        return value != label_id;
      });
      droppable.attr('data-ids', ids.toString());
      var input_object = droppable.find('input');
      input_object.val ( ids.toString() );
  
      label.remove();
     
  });
 
  $( ".droppable" ).droppable({
    hoverClass: "ui-state-active",
    drop: function( event, ui ) {
      
      if ( !$("#save_schedule").hasClass("btn-warning") )
        $("#save_schedule").removeClass("btn-success").addClass("btn-warning");
      
      var droppable = $(this);
      var draggable_id = ui.draggable.attr('data-id');
      var section_key = droppable.attr('data-section');
      
      var ids = ( $(this).attr('data-ids') != '' ) ? $(this).attr('data-ids') : 'empty';
      // check if this kid is already in this time slot
      if ( ids || ids == 'empty' )
      {
        ids = ( ids == 'empty' ) ? '' : ids;
        var myIdsArray = ids.split(',');
        if ( $.inArray( draggable_id, myIdsArray) == -1 ){
          // important - add a clone of the same element so we click its X again
          droppable.prepend(ui.draggable.clone(true));
          
          var extra = ( ids != '' ) ? ',' : '';
          droppable.attr('data-ids', ids + extra + draggable_id);
          
          // var input_object = $('input[name="'+section_key+'"]');
          var input_object = droppable.find('input');
          extra = ( input_object.val() != '' ) ? ',' : '';
          input_object.val ( input_object.val() + extra + draggable_id );
        }
        else{ 
          droppable.addClass('ui-state-error');
          setTimeout( function(){
            droppable.removeClass('ui-state-error');
          },1000);

        }
      }
    }
  });
  
});