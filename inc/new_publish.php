<?php

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

        $notification_type_push = $woptifications_options['notification_type']['push'];
        $notification_type_popup = $woptifications_options['notification_type']['popup'];

        error_log('push enable = '.$notification_type_push.' popup enable = '.$notification_type_popup);

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

            $popup_title = $woptifications_options['product_title'];
            $popup_title = str_replace($text_vars, $vars, $popup_title);

            $popup_content = $woptifications_options['product_content'];
            $popup_content = str_replace($text_vars, $vars, $popup_content);

            if($woptifications_options['product_custom_link_enable'] == 1) {
                $url = $woptifications_options['product_custom_link'];
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

            $push_title = $woptifications_options['product_push_title'];
            $push_title = str_replace($text_vars, $vars, $push_title);

            $push_content = $woptifications_options['product_push_content'];
            $push_content = str_replace($text_vars, $vars, $push_content);

            $thumb = $woptifications_options['product_use_thumb'] == 1 ? $thumb : $woptifications_options['push_icon']['thumbnail'];

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

            $popup_title = $woptifications_options['publish_title'];
            $popup_title = str_replace($text_vars, $vars, $popup_title);

            $popup_content = $woptifications_options['publish_content'];
            $popup_content = str_replace($text_vars, $vars, $popup_content);

            if($woptifications_options['publish_custom_link_enable'] == 1) {
                $url = $woptifications_options['publish_custom_link'];
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

            $push_title = $woptifications_options['publish_push_title'];
            $push_title = str_replace($text_vars, $vars, $push_title);

            $push_content = $woptifications_options['publish_push_content'];
            $push_content = str_replace($text_vars, $vars, $push_content);

            $thumb = $woptifications_options['publish_use_thumb'] == 1 ? $thumb : $woptifications_options['push_icon']['thumbnail'];

        }


        $args = array(
            'title'     =>  $popup_title,
            'content'   =>  $popup_content,
            'type'      =>  $woptifications_options['publish_alert_type'],
            'post_type' =>  $type,
            'post_id'   =>  $post_id,
        ); 

        $pushargs = array(
            'title'     =>  $push_title,
            'content'   =>  $push_content,
            'url'       =>  $url,
            'thumb'     =>  $thumb,
            'post_type' =>  $type,
            'post_id'   =>  $post_id,
        ); 

        if($notification_type_popup == 1) {
            set_transient( 'woptificationsPopup_post'.'_'. mt_rand( 100000, 999999 ), $args, 15 );
        }
        if($notification_type_push == 1) {
            set_transient( 'woptificationsPush_post'.'_'. mt_rand( 100000, 999999 ), $pushargs, 15 );
        }
    }

} 
add_action('after_setup_theme', 'woptifications_new_publish_notification', 1 );

?>