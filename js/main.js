
toastr.options = woptifications_toastr_opts;

function woptifications_push(thetitle,thebody,theurl) {
  if (!Notification) {
    alert('Desktop notifications not available in your browser. Try Chromium.'); 
    return;
  }

  if (Notification.permission !== "granted")
    Notification.requestPermission();
  else {
    var notification = new Notification(thetitle, {
      icon: woptifications_vars.push_icon,
      body: thebody,
    });

    if(theurl.length && theurl != "") {
      notification.onclick = function () {
        window.open(theurl);      
      };
    } 
  }
}

jQuery(document).ready(function($) {
  $(document).on('heartbeat-send', function(e, data) {
      data['woptifications_status'] = 'ready';
      data['viewed_post_id'] = woptifications_vars.postID;
      data['cat_match'] = woptifications_vars.cat_match,
      data['product_cat_match'] = woptifications_vars.product_cat_match
  });
  
  $(document).on('heartbeat-tick', function(e, data) {
    if(!data['woptificationsPopup'] && !data['woptificationsPush']) {
      return;
    }
    jQuery.each( data['woptificationsPopup'], function( index, notification ) {
      setTimeout(function() {
        if ( index != 'blabla' &&  typeof notification['type'] !== 'undefined'){
          toastr[notification['type']](notification['content'], notification['title']);
        }
      }, index * 1000 );
    });
    jQuery.each( data['woptificationsPush'], function( index, notification ) {
      setTimeout(function() {
        if ( index != 'blabla' ){
          woptifications_push(notification['title'],notification['content'],notification['url'])
        }
      }, index * 1000 );
    });
  });
  /*     
  jQuery(document).on('heartbeat-error', function(e, jqXHR, textStatus, error) {
      console.log('BEGIN ERROR');
      console.log(textStatus);
      console.log(error);         
      console.log('END ERROR');           
  });
  */
}); 