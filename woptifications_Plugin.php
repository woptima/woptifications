<?php

Redux::init( 'woptifications_options' );

function log_the_var($var) {
    echo '<script type="text/javascript">console.log('.$var.')</script>';
}

// Toastr settings
function woptifications_toastr_settings() {
    global $woptifications_options;

    $toastr_opts = array(
            "debug" => false,
            "positionClass" => $woptifications_options['positionClass'],
            "onclick" => null,
            "showDuration" => $woptifications_options['showDuration'],
            "hideDuration" => $woptifications_options['hideDuration'],
            "timeOut" => $woptifications_options['timeOut'],
            "extendedTimeOut" => $woptifications_options['extendedTimeOut'],
            "showEasing" => $woptifications_options['showEasing'],
            "hideEasing" => $woptifications_options['hideEasing'],
            "showMethod" => $woptifications_options['showMethod'],
            "hideMethod" => $woptifications_options['hideMethod']
        );
    foreach ($woptifications_options as $key => $value) {
        if($value == 1) {
            $toastr_opts[$key] = true;
        }
    }
    return $toastr_opts;
}

function woptifications_toastr_css() {
    return '';
}

// Load scripts
function woptifications_load_scripts() {
    global $woptifications_options;
    $cat_match = $woptifications_options['cat_match'];
    $product_cat_match = $woptifications_options['product_cat_match'];

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('heartbeat');
    wp_enqueue_script( 'woptifications-toastr', plugins_url( 'vendor/toastr/toastr.min.js', __FILE__ ), array('jquery'), '', true);
    wp_enqueue_style( 'woptifications-toastr', plugins_url( 'vendor/toastr/toastr.min.css', __FILE__ ), '', true);
    wp_enqueue_script('woptifications', plugins_url( 'js/main.js', __FILE__ ), array('jquery', 'woptifications-toastr'), '', true);

    wp_localize_script( 'woptifications', 'woptifications_toastr_opts', woptifications_toastr_settings() );
    wp_localize_script('woptifications', 'woptifications_vars', array( 
        "postID" => get_the_ID(),
        "cat_match" => $cat_match,
        "product_cat_match" => $product_cat_match
        ) );
    wp_add_inline_style( 'wop-toastr', woptifications_toastr_css() );

}
add_action('wp_enqueue_scripts', 'woptifications_load_scripts');

function woptifications_load_admin_scripts() {
    wp_enqueue_style( 'woptifications-backend', plugins_url( 'css/backend.css', __FILE__ ), '', true);
}
add_action('admin_enqueue_scripts', 'woptifications_load_admin_scripts');


// Heartbeat Settings
function woptifications_heartbeat_settings( $settings ) {
    $settings['interval'] = 15; //Anything between 15-60
    $settings['autostart'] = true; 
    return $settings;
}
add_filter('heartbeat_settings', 'woptifications_heartbeat_settings');


// Post publish notifications setup
function woptifications_new_publish_notification() {
    global $woptifications_options;

    $new_post_notif = $woptifications_options['alert_hooks']['post'];
    $types_opts = $woptifications_options['publish_post_types'];

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
      add_action( 'publish_'.$active_type, 'woptifications_notify_published_post' );
    }

    function woptifications_notify_published_post( $post_id ) {
        global $woptifications_options;

        $post = get_post( $post_id );
        $url = get_the_permalink($post_id);
        $title = $post->post_title;
        $author = get_the_author_meta('display_name', $post->post_author);
        $type = $post->post_type;
        $thumb = get_the_post_thumbnail_url($post_id,'thumbnail');
        $category_ids = array();
        $category_links = "";
        $price = "";

        $text_vars = [
            '%%title%%',
            '%%url%%',
            '%%author%%',
            '%%type%%',
            '%%thumbnail%%',
            '%%categories%%',
            '%%price%%',
        ];
        

        //If woocommerce product
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )  && $type == 'product') {

            $theproduct = wc_get_product($post_id);

            $category_list = wp_get_post_terms( $post_id, 'product_cat' );

            foreach($category_list as $category){
                $category_ids[] = $category->term_id;
                $link = get_category_link( $category->term_id );
                $cat_name = $category->name;
                $category_links .= '<a class="btn btn-info btn-xs" href="'.$link.'">'.$cat_name.'</a> ';
            }

            $price = $theproduct->get_price().get_woocommerce_currency();

            $vars = [
                $title,
                $url,
                $author,
                $type,
                $thumb,
                $category_links,
                $price
            ];

            $title = $woptifications_options['product_title'];
            $title = str_replace($text_vars, $vars, $title);

            $content = $woptifications_options['product_content'];
            $content = str_replace($text_vars, $vars, $content);

        //If any other post type
        } else {

            $category_list = get_the_category($post_id);
            foreach($category_list as $category){
                $category_ids[] = $category->term_id;
                $link = get_category_link( $category->term_id );
                $cat_name = $category->name;
                $category_links .= '<a class="btn btn-info btn-xs" href="'.$link.'">'.$cat_name.'</a>';
            }

            $vars = [
                $title,
                $url,
                $author,
                $type,
                $thumb,
                $category_links,
                $price
            ];

            $title = $woptifications_options['publish_title'];
            $title = str_replace($text_vars, $vars, $title);

            $content = $woptifications_options['publish_content'];
            $content = str_replace($text_vars, $vars, $content);

        }
        

        $args = array(
            'title'     =>  $title,
            'content'   =>  $content,
            'type'      =>  $woptifications_options['publish_alert_type'],
            'post_type' =>  $type,
            'post_id'   =>  $post_id,
        ); 

        set_transient( 'woptifications_post'.'_'. mt_rand( 100000, 999999 ), $args, 15 );
    }

} 
add_action('after_setup_theme', 'woptifications_new_publish_notification', 1 );


// New comment notification setup
function woptifications_notify_new_comment( $comment_id ) { 
    global $woptifications_options;

    $new_comment_notif = $woptifications_options['alert_hooks']['comment'];
    $types_opts = $woptifications_options['comment_post_types'];

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

    $title = $woptifications_options['comment_title'];
    $title = str_replace($text_vars, $vars, $title);

    $content = $woptifications_options['comment_content'];
    $content = str_replace($text_vars, $vars, $content);

    $args = array(
        'title'     =>  $title,
        'content'   =>  $content,
        'type'      =>  $woptifications_options['comment_alert_type'],
        'post_type' =>  'comment',
        'post_id'   =>  $comment_id, 
    ); 
    set_transient( 'woptifications_comment'.'_'. mt_rand( 100000, 999999 ), $args, 17 );
} 
add_filter('comment_post', 'woptifications_notify_new_comment');


// Send notifications
function woptifications_heartbeat_received($response, $data){
    global $wpdb;
    
    $data['woptifications'] = array();

    if($data['woptifications_status'] == 'ready') {

        $viewed_post_id = intval($data['viewed_post_id']);
        $cat_match =  intval($data['cat_match']);
        $product_cat_match =  intval($data['product_cat_match']);

        error_log($cat_match);
        error_log($product_cat_match);

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

                $post_type = $notification['post_type'];
                $post_id = $notification['post_id'];

                // if category match selected and notifiaction product categories match viewed product categories
                if($post_type == 'product' && $product_cat_match == 1) {

                    $viewed_cats_ids = array();
                    $notification_cats_ids = array();

                    $viewed_cats = wp_get_post_terms( $viewed_post_id, 'product_cat' );
                    foreach ($viewed_cats as $viewed_cat) {
                        $viewed_cats_ids[] = $viewed_cat->term_id;
                    }

                    $notification_cats = wp_get_post_terms( $post_id, 'product_cat' );
                    foreach ($notification_cats as $notification_cat) {
                        $notification_cats_ids[] = $notification_cat->term_id;
                    }

                    $matches = array_intersect($viewed_cats_ids, $notification_cats_ids);

                    if(count($matches) > 0) {
                        $data['woptifications'][$id] = $notification; 
                    }

                    continue;

                // if category match selected and notifiaction post categories match viewed post categories
                } elseif($post_type == 'post' && $cat_match == 1 && is_single($viewed_post_id)) {

                    $viewed_cats_ids = array();
                    $notification_cats_ids = array();

                    $viewed_cats = get_the_category($viewed_post_id);
                    foreach ($viewed_cats as $viewed_cat) {
                        $viewed_cats_ids[] = $viewed_cat->term_id;
                    }

                    $notification_cats = get_the_category($post_id);
                    foreach ($notification_cats as $notification_cat) {
                        $notification_cats_ids[] = $notification_cat->term_id;
                    }

                    $matches = array_intersect($viewed_cats_ids, $notification_cats_ids);

                    if(count($matches) > 0) {
                        $data['woptifications'][$id] = $notification; 
                    }

                    continue;

                } else {
                    $data['woptifications'][$id] = $notification; 
                } 

            }
        }
    }
        
    return $data;
}
add_filter('heartbeat_received', 'woptifications_heartbeat_received', 10, 2); 
add_filter('heartbeat_nopriv_received', 'woptifications_heartbeat_received', 10, 2);

