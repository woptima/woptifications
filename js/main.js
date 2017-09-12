
toastr.options = woptifications_toastr_opts;

jQuery(document).ready(function($) {
  $(document).on('heartbeat-send', function(e, data) {
      data['woptifications_status'] = 'ready';    //need some data to kick off AJAX call
  });
  
  $(document).on('heartbeat-tick', function(e, data) {
    if(!data['woptifications']) {
      return;
    }
    jQuery.each( data['woptifications'], function( index, notification ) {
      setTimeout(function() {
        if ( index != 'blabla' ){
          toastr[notification['type']](notification['content'], notification['title']);
        }
      }, index * 1000 );
    });
  });
          
  jQuery(document).on('heartbeat-error', function(e, jqXHR, textStatus, error) {
      console.log('BEGIN ERROR');
      console.log(textStatus);
      console.log(error);         
      console.log('END ERROR');           
  });
}); 