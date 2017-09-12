<?php

Redux::init( 'wop_options' );

// Toastr settings
function toastr_settings() {
    global $wop_options;

    $toastr_opts = array(
            "debug" => false,
            "positionClass" => $wop_options['positionClass'],
            "onclick" => null,
            "showDuration" => $wop_options['showDuration'],
            "hideDuration" => $wop_options['hideDuration'],
            "timeOut" => $wop_options['timeOut'],
            "extendedTimeOut" => $wop_options['extendedTimeOut'],
            "showEasing" => $wop_options['showEasing'],
            "hideEasing" => $wop_options['hideEasing'],
            "showMethod" => $wop_options['showMethod'],
            "hideMethod" => $wop_options['hideMethod']
        );
    foreach ($wop_options as $key => $value) {
        if($value == 1) {
            $toastr_opts[$key] = true;
        }
    }
    return $toastr_opts;
}

function toastr_css() {
    return '';
}

// Load scripts
function wop_load_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('heartbeat');
    wp_enqueue_script( 'wop-toastr', plugins_url( 'vendor/toastr/toastr.min.js', __FILE__ ), array('jquery'), '', true);
    wp_enqueue_style( 'wop-toastr', plugins_url( 'vendor/toastr/toastr.min.css', __FILE__ ), '', true);
    wp_enqueue_script('woptifications', plugins_url( 'js/main.js', __FILE__ ), array('jquery', 'wop-toastr'), '', true);

    wp_localize_script( 'woptifications', 'toastr_opts', toastr_settings() );
    wp_add_inline_style( 'wop-toastr', toastr_css() );
}
add_action('wp_enqueue_scripts', 'wop_load_scripts');

function wop_load_admin_scripts() {
    wp_enqueue_style( 'woptifications-backend', plugins_url( 'css/backend.css', __FILE__ ), '', true);
}
add_action('admin_enqueue_scripts', 'wop_load_admin_scripts');


// Heartbeat Settings
function wop_heartbeat_settings( $settings ) {
    $settings['interval'] = 15; //Anything between 15-60
    $settings['autostart'] = true; 
    return $settings;
}
add_filter('heartbeat_settings', 'wop_heartbeat_settings');


// Post publish notifications setup
function new_post_notification() {
    global $wop_options;

    $new_post_notif = $wop_options['alert_hooks']['post'];
    $types_opts = $wop_options['publish_post_types'];

    if($new_post_notif != 1) {
        return;
    }

    $active_types = array();

    foreach ($types_opts as $key => $value) {
        if($value == 1) {
            $active_types[] = $key;
        }
    }

    foreach($active_types as $active_type){
      add_action( 'publish_'.$active_type, 'wop_notify_published_post' );
    }

    function wop_notify_published_post( $post_id ) {
        global $wop_options;

        $post = get_post( $post_id );
        $url = get_the_permalink($post_id);
        $title = $post->post_title;
        $author = $post->post_author;
        $type = $post->post_type;
        $thumb = get_the_post_thumbnail_url($post_id,'thumbnail'); 
        $categories = array();
        $price = "";

        $text_vars = [
            '%%title%%',
            '%%url%%',
            '%%author%%',
            '%%type%%',
            '%%thumbnail%%',
            '%%categories%%',
            '%%price%%'
        ];
        $vars = [
            $title,
            $url,
            $author,
            $type,
            $thumb,
            $categories,
            $price
        ];

        //If woocommerce product
        if ( class_exists( 'WooCommerce' ) && $type == 'product') {

            $product = wc_get_product( $post_id );
            $product_cats = wp_get_post_terms( $post_id, 'product_cat' );
            foreach($product_cats as $product_cat){
                $link = get_category_link( $product_cat->term_id );
                $categories[$product_cat->cat_name] = '<a href="'.$link.'">'.$link.'</a> ';
            }
            $price = $product->get_regular_price();

            $title = $wop_options['product_title'];
            $title = str_replace($text_vars, $vars, $title);

            $content = $wop_options['product_content'];
            $content = str_replace($text_vars, $vars, $content);

        //If any other post type
        } else {

            $category_list=get_the_category($post_id);
            foreach($category_objects as $category_object){
                $link = get_category_link( $category->term_id );
                $categories[$category_object->cat_name] = '<a href="'.$link.'">'.$link.'</a> ';
            }

            $title = $wop_options['publish_title'];
            $title = str_replace($text_vars, $vars, $title);

            $content = $wop_options['publish_content'];
            $content = str_replace($text_vars, $vars, $content);

        }

        $args = array(
            'title'     =>  $title,
            'content'   =>  $content,
            'type'      =>  $wop_options['publish_alert_type']
        ); 

        set_transient( 'woptifications_post'.'_'. mt_rand( 100000, 999999 ), $args, 15 );
    }

} 
add_action('after_setup_theme', 'new_post_notification', 1 );


// New comment notification setup
function wop_notify_new_comment( $comment_id ) { 
    global $wop_options;

    $new_comment_notif = $wop_options['alert_hooks']['comment'];
    $types_opts = $wop_options['comment_post_types'];

    if($new_comment_notif != 1) {
        return;
    }

    $active_types = array(); 

    foreach ($types_opts as $key => $value) {
        if($value) {
            $active_types[] = $key;
        }
    }

    $comment = get_comment( $comment_id );
    if ( ! $comment->user_id > 0 ) {
        return;
    }

    $comment_post_type = get_post_type($comment->comment_post_ID);
    if (!in_array($comment_post_type , $active_types)) {
        return;
    }

    $postID = $comment->comment_post_ID;

    $comment_author =  get_comment_author($comment_id);
    $comment_author_avatar =  get_avatar_url($comment->user_id);
    $comment_post_title = get_the_title($postID); 
    $comment_post_url = get_the_permalink($postID); 
    $comment_post_thumb = get_the_post_thumbnail_url($postID,'thumbnail'); 
    $comment_link = get_comment_link( $comment_id ); 

    $text_vars = [
        '%%post_title%%',
        '%%comment_url%%',
        '%%author%%',
        '%%author_avatar%%',
        '%%type%%',
        '%%thumbnail%%',
        '%%post_url%%',
    ];
    $vars = [
        $comment_post_title,
        $comment_link,
        $comment_author,
        $comment_author_avatar,
        $comment_post_type,
        $comment_post_thumb,
        $comment_post_url
    ];

    $title = $wop_options['comment_title'];
    $title = str_replace($text_vars, $vars, $title);

    $content = $wop_options['comment_content'];
    $content = str_replace($text_vars, $vars, $content);

    $args = array(
        'title'     =>  $title,
        'content'   =>  $content,
        'type'      =>  $wop_options['comment_alert_type']
    ); 
    set_transient( 'woptifications_comment'.'_'. mt_rand( 100000, 999999 ), $args, 17 );
} 
add_filter('comment_post', 'wop_notify_new_comment');


// Send notifications
function wop_heartbeat_received($response, $data){
    global $wpdb;
    
    $data['wop_notify'] = array();

    if($data['notify_status'] == 'ready') {

        $sql = $wpdb->prepare( 
            "SELECT * FROM $wpdb->options WHERE option_name LIKE  %s", 
            '_transient_' . 'woptifications' . '_%'
        );
            
        $notifications = $wpdb->get_results( $sql );//retrieve all publish post objects
            
        if ( empty( $notifications ) ) {
            return $data;
        }

        foreach ( $notifications as $db_notification ) {
            $id = str_replace( '_transient_', '', $db_notification->option_name );
            if ( false !== ($notification = get_transient($id))) { 
                $data['wop_notify'][$id] = $notification;      
            }
        }
    }
        
    return $data;
}
add_filter('heartbeat_received', 'wop_heartbeat_received', 10, 2); 
add_filter('heartbeat_nopriv_received', 'wop_heartbeat_received', 10, 2);

