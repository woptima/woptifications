<?php

// check for category match
function woptifications_check_match($viewed_post_id,$post_id,$taxonomy) {
    $viewed_cats = wp_get_post_terms( $viewed_post_id, $taxonomy );
    foreach ($viewed_cats as $viewed_cat) {
        $viewed_cats_ids[] = $viewed_cat->term_id;
    }

    $notification_cats = wp_get_post_terms( $post_id, $taxonomy );
    foreach ($notification_cats as $notification_cat) {
        $notification_cats_ids[] = $notification_cat->term_id;
    }

    $matches = array_intersect($viewed_cats_ids, $notification_cats_ids);

    if(count($matches) > 0) {
        return true; 
    } else {
        return false;
    }
}

// Send notifications
function woptifications_heartbeat_received($response, $data){
    global $wpdb;
    
    $data['woptificationsPopup'] = array();
    $data['woptificationsPush'] = array();

    if($data['woptifications_status'] == 'ready') {

        $viewed_post_id = intval($data['viewed_post_id']);
        $cat_match =  intval($data['cat_match']);
        $product_cat_match =  intval($data['product_cat_match']);

        $sql = $wpdb->prepare( 
            "SELECT * FROM $wpdb->options WHERE option_name LIKE  %s", 
            '_transient_' . 'woptificationsPopup' . '_%'
        );

        $sql2 = $wpdb->prepare( 
            "SELECT * FROM $wpdb->options WHERE option_name LIKE  %s", 
            '_transient_' . 'woptificationsPush' . '_%'
        );
            
        $notifications = $wpdb->get_results( $sql );

        $pushifications = $wpdb->get_results( $sql2 );
            
        if ( empty( $notifications ) && empty( $pushifications )) {
            return $data;
        }

        if(!empty($notifications )) {
            foreach ( $notifications as $db_notification ) {
                $id = str_replace( '_transient_', '', $db_notification->option_name );

                if ( false !== ($notification = get_transient($id))) {

                    $post_type = $notification['post_type'];
                    $post_id = $notification['post_id'];

                    // check category match
                    if($post_type == 'product' && $product_cat_match == 1) {

                        if(woptifications_check_match($viewed_post_id,$post_id,"product_cat")) {
                            $data['woptificationsPopup'][$id] = $notification;
                        }

                        continue;

                    // check category match
                    } elseif($post_type == 'post' && $cat_match == 1 && is_single($viewed_post_id)) {

                        if(woptifications_check_match($viewed_post_id,$post_id,"category")) {
                            $data['woptificationsPopup'][$id] = $notification;
                        }

                        continue;

                    } else {
                        $data['woptificationsPopup'][$id] = $notification; 
                    } 

                }
            }
        }

        if(!empty($pushifications )) {
            foreach ( $pushifications as $db_notification ) {
                $id = str_replace( '_transient_', '', $db_notification->option_name );

                if ( false !== ($notification = get_transient($id))) {

                    $post_type = $notification['post_type'];
                    $post_id = $notification['post_id'];

                    // check category match
                    if($post_type == 'product' && $product_cat_match == 1) {

                        if(woptifications_check_match($viewed_post_id,$post_id,"product_cat")) {
                            $data['woptificationsPush'][$id] = $notification;
                        }

                        continue;

                    // check category match
                    } elseif($post_type == 'post' && $cat_match == 1 && is_single($viewed_post_id)) {

                        if(woptifications_check_match($viewed_post_id,$post_id,"category")) {
                            $data['woptificationsPush'][$id] = $notification;
                        }

                        continue;

                    } else {
                        $data['woptificationsPush'][$id] = $notification; 
                    } 

                }
            }
        }
    }
        
    return $data;
}
add_filter('heartbeat_received', 'woptifications_heartbeat_received', 10, 2); 
add_filter('heartbeat_nopriv_received', 'woptifications_heartbeat_received', 10, 2);

?>