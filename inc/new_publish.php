<?php


/**
* New publish hook
* Setting content separately for Woo Products and other types of posts
*/
class woptificationsNewPublish
{
    
    function __construct()
    {
        global $woptifications_options;
        $this->woptifications_options = $this->woptifications_options;

        add_action('after_setup_theme', array($this, 'woptifications_new_publish_notification'), 1 );
    }

    function woptifications_notify_published_post( $post_id ) {
        

        $notification_type_push = $this->woptifications_options['notification_type']['push'];
        $notification_type_popup = $this->woptifications_options['notification_type']['popup'];

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
            '%%title%%'     => $title,
            '%%url%%'       => $url,
            '%%author%%'    => $author,
            '%%type%%'      => $type,
            '%%thumbnail%%' => $thumbnail,
            '%%categories%%'=> $categories,
            '%%price%%'     => $price
        ];
        

        //If woocommerce product
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )  && $type == 'product') {

            $theproduct = wc_get_product($post_id);

            $category_list = wp_get_post_terms( $post_id, 'product_cat' );

            if(is_array($category_list) && !empty($category_list)) {

                foreach($category_list as $category) {
                    $category_ids[] = $category->term_id;
                    $link = get_category_link( $category->term_id );
                    $cat_name = $category->name;
                    $text_vars['%%categories%%'] .= '<a class="btn btn-info btn-xs" href="'.$link.'">'.$cat_name.'</a> ';
                }

            }

            $text_vars['%%price%%'] = $theproduct->get_price().get_woocommerce_currency();

            $popup_title = $this->woptifications_options['product_title'];
            $popup_title = str_replace(array_keys($text_vars), $text_vars, $popup_title);

            $popup_content = $this->woptifications_options['product_content'];
            $popup_content = str_replace(array_keys($text_vars), $text_vars, $popup_content);

            if($this->woptifications_options['product_custom_link_enable'] == 1) {
                $text_vars['%%url%%'] = $this->woptifications_options['product_custom_link'];
            }

            $push_title = $this->woptifications_options['product_push_title'];
            $push_title = str_replace(array_keys($text_vars), $text_vars, $push_title);

            $push_content = $this->woptifications_options['product_push_content'];
            $push_content = str_replace(array_keys($text_vars), $text_vars, $push_content);

            $thumb = $this->woptifications_options['product_use_thumb'] == 1 ? $thumb : $this->woptifications_options['push_icon']['thumbnail'];

        //If any other post type
        } else {

            $category_list = get_the_category($post_id);
            foreach($category_list as $category){
                $category_ids[] = $category->term_id;
                $link = get_category_link( $category->term_id );
                $cat_name = $category->name;
                $text_vars['%%categories%%'] .= '<a class="btn btn-info btn-xs" href="'.$link.'">'.$cat_name.'</a>';
            }

            $popup_title = $this->woptifications_options['publish_title'];
            $popup_title =  str_replace(array_keys($text_vars), $text_vars, $popup_title);

            $popup_content = $this->woptifications_options['publish_content'];
            $popup_content =  str_replace(array_keys($text_vars), $text_vars, $popup_content);

            if($this->woptifications_options['publish_custom_link_enable'] == 1) {
                $text_vars['%%url%%'] = $this->woptifications_options['publish_custom_link'];
            }

            $push_title = $this->woptifications_options['publish_push_title'];
            $push_title =  str_replace(array_keys($text_vars), $text_vars, $push_title);

            $push_content = $this->woptifications_options['publish_push_content'];
            $push_content =  str_replace(array_keys($text_vars), $text_vars, $push_content);

            $thumb = $this->woptifications_options['publish_use_thumb'] == 1 ? $thumb : $this->woptifications_options['push_icon']['thumbnail'];

        }


        $args = array(
            'title'     =>  $popup_title,
            'content'   =>  $popup_content,
            'type'      =>  $this->woptifications_options['publish_alert_type'],
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

    // Post publish notifications setup
    function woptifications_new_publish_notification() {

        $new_post_notif = $this->woptifications_options['alert_hooks']['post'];
        $types_opts = $this->woptifications_options['publish_post_types'];

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

    } 
    
}

$woptificationsNewPublish = new woptificationsNewPublish();

