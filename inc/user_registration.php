<?php

/**
* User registration hook
* Set transients on new user registration 
*/
class woptificationsUserRegistration
{
    
    function __construct()
    {
        add_action( 'user_register', array($this, 'woptifications_notify_new_user'), 10, 1 );
    }
    // New user regiatration notification setup
    function woptifications_notify_new_user( $user_id ) { 
        global $woptifications_options;

        $new_user_notif = $woptifications_options['alert_hooks']['registration'];
        $user_custom_fields = $woptifications_options['user_custom_fields'];
        $default_message = $woptifications_options['registration_default_message'];
        $notification_type_push = $woptifications_options['notification_type']['push'];
        $notification_type_popup = $woptifications_options['notification_type']['popup'];

        if($new_user_notif != 1) {
            return;
        }

        $user_info = get_userdata($user_id);

        $username =  $user_info->user_login;
        $nickname =  $user_info->user_nicename;
        $avatar = get_avatar_url($user_id); 

        $text_vars = [
            '%%username%%',
            '%%nickname%%',
            '%%avatar%%',
        ];
        $vars = [
            $username,
            $nickname,
            $avatar,
        ];

        foreach ($user_custom_fields as $key => $value) {
            $text_vars[] = '%%'.$value.'%%'; 
            $vars[] = get_user_meta($user_id, $value); 
        }

        $popup_title = $default_message == 1 ? 'New user joined!' : $woptifications_options['registration_title']; 
        $popup_title = str_replace($text_vars, $vars, $popup_title);

        $popup_content = $default_message == 1 ? '%%username%% has just registered' : $woptifications_options['registration_content'];
        $popup_content = str_replace($text_vars, $vars, $popup_content);

        $push_title = $default_message == 1 ? 'New user joined!' : $woptifications_options['registration_push_title'];
        $push_title = str_replace($text_vars, $vars, $push_title);

        $push_content = $default_message == 1 ? '%%username%% has just registered' : $woptifications_options['registration_push_content'];
        $push_content = str_replace($text_vars, $vars, $push_content);

        $args = array(
            'title'     =>  $popup_title,
            'content'   =>  $popup_content,
            'type'      =>  $woptifications_options['registration_alert_type'],
            'post_type' =>  'registration',
            'post_id'   =>  $user_id, 
        ); 

        $thumb = $woptifications_options['registration_user_thumb'] == 1 ? $avatar : $woptifications_options['push_icon']['thumbnail'];

        $pushargs = array(
            'title'     =>  $push_title,
            'content'   =>  $push_content,
            'post_type' =>  'registration',
            'post_id'   =>  $user_id,
        ); 

        if($notification_type_popup == 1) {
            set_transient( 'woptificationsPopup_registration'.'_'. mt_rand( 100000, 999999 ), $args, 15 );
        }
        if($notification_type_push == 1) {
            set_transient( 'woptificationsPush_registration'.'_'. mt_rand( 100000, 999999 ), $pushargs, 15 );
        }
        
    } 

}


$woptificationsUserRegistration = new woptificationsUserRegistration();