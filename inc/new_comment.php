<?php


/**
* New comment hook
* Set transient on new comment
*/
class woptificationsNewComment
{
    
    function __construct()
    {
        add_filter('comment_post', array($this, 'woptifications_notify_new_comment') );
    }

    // New comment notification setup
    function woptifications_notify_new_comment( $comment_id ) { 
        global $woptifications_options;

        $new_comment_notif = $woptifications_options['alert_hooks']['comment'];
        $types_opts = $woptifications_options['comment_post_types'];
        $notification_type_push = $woptifications_options['notification_type']['push'];
        $notification_type_popup = $woptifications_options['notification_type']['popup'];

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

        $comment_post_type = 'comment';

        $postID = $comment->comment_post_ID;

        $comment_author =  get_comment_author($comment_id);
        $comment_author_avatar =  get_avatar_url($comment->user_id);
        $comment_post_title = get_the_title($postID); 
        $comment_post_url = get_the_permalink($postID); 
        $comment_post_thumb = get_the_post_thumbnail_url($postID,'thumbnail'); 
        $comment_link = get_comment_link( $comment_id ); 

        $text_vars = [
            '%%post_title%%'    => $comment_post_title,
            '%%comment_url%%'   => $comment_link,
            '%%author%%'        => $comment_author,
            '%%author_avatar%%' => $comment_author_avatar,
            '%%type%%'          => $comment_post_type,
            '%%thumbnail%%'     => $comment_post_thumb,
            '%%post_url%%'      => $comment_post_url,
        ];


        $popup_title = $woptifications_options['comment_title'];
        $popup_title = str_replace(array_keys($text_vars), $text_vars, $popup_title);

        $popup_content = $woptifications_options['comment_content'];
        $popup_content = str_replace(array_keys($text_vars), $text_vars, $popup_content);

        if($woptifications_options['publish_custom_link_enable'] == 1) {
            $text_vars['%%comment_url%%'] = $woptifications_options['publish_custom_link'];
        }


        $push_title = $woptifications_options['comment_push_title'];
        $push_title = str_replace(array_keys($text_vars), $text_vars, $push_title);

        $push_content = $woptifications_options['comment_push_content'];
        $push_content = str_replace(array_keys($text_vars), $text_vars, $push_content);

        $args = array(
            'title'     =>  $popup_title,
            'content'   =>  $popup_content,
            'type'      =>  $woptifications_options['comment_alert_type'],
            'post_type' =>  'comment',
            'post_id'   =>  $comment_id, 
        ); 

        $thumb = $woptifications_options['comment_use_thumb'] == 1 ? $comment_author_avatar : $woptifications_options['push_icon']['thumbnail'];

        $pushargs = array(
            'title'     =>  $push_title,
            'content'   =>  $push_content,
            'url'       =>  $comment_link,
            'thumb'     =>  $thumb,
            'post_type' =>  'comment',
            'post_id'   =>  $post_id,
        ); 
        
        if($notification_type_popup == 1) {
            error_log('setting comment popup transient: '.$args);
            set_transient( 'woptificationsPopup_comment'.'_'. mt_rand( 100000, 999999 ), $args, 15 );
        }
        if($notification_type_push == 1) {
            error_log('setting comment push transient: '.$pushargs);
            set_transient( 'woptificationsPush_comment'.'_'. mt_rand( 100000, 999999 ), $pushargs, 15 );
        }
        
    } 
    

}


$woptificationsNewComment = new woptificationsNewComment();