<?php

/**
*   scripts load class
*   Loading scripts and conditional toastr settings
*/
class woptificationsEqueueScriptsClass {
        
    function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'woptifications_load_scripts') );
        add_action('admin_enqueue_scripts', array($this, 'woptifications_load_admin_scripts') );

        add_filter('heartbeat_settings', array($this, 'woptifications_heartbeat_settings') );
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
        $push_icon = $woptifications_options['push_icon'];

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script('heartbeat');
        wp_enqueue_script( 'woptifications-toastr', plugins_url( 'woptifications/vendor/toastr/toastr.min.js' ), array('jquery'), '', true);
        wp_enqueue_style( 'woptifications-toastr', plugins_url( 'woptifications/vendor/toastr/toastr.min.css' ), '', true);
        wp_enqueue_script('woptifications', plugins_url( 'woptifications/js/main.js' ), array('jquery', 'woptifications-toastr'), '', true);

        wp_localize_script( 'woptifications', 'woptifications_toastr_opts', $this->woptifications_toastr_settings() );
        wp_localize_script('woptifications', 'woptifications_vars', array( 
            "postID" => get_the_ID(),
            "cat_match" => $cat_match,
            "product_cat_match" => $product_cat_match,
            "push_icon" => $push_icon['thumbnail'],
            ) );
        wp_add_inline_style( 'wop-toastr', $this->woptifications_toastr_css() );

    }
    

    // load admi scripts
    function woptifications_load_admin_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'woptifications-toastr', plugins_url( 'woptifications/vendor/toastr/toastr.min.js' ), array('jquery'), '', true);
        wp_enqueue_style( 'woptifications-toastr', plugins_url( 'woptifications/vendor/toastr/toastr.min.css' ), '', true);
        wp_enqueue_script('woptifications-backend', plugins_url( 'woptifications/js/backend.js' ), array('jquery'), '', true);
        wp_enqueue_style( 'woptifications-backend', plugins_url( 'woptifications/css/backend.css' ), '', true);    

        wp_localize_script( 'woptifications-backend', 'woptifications_toastr_opts', $this->woptifications_toastr_settings() );
        wp_localize_script('woptifications-backend', 'woptifications_vars', array( 
            "postID" => get_the_ID(),
            "cat_match" => $cat_match,
            "product_cat_match" => $product_cat_match,
            "push_icon" => $push_icon['thumbnail'],
            ) );
        wp_add_inline_style( 'wop-toastr', $this->woptifications_toastr_css() );

    }



    // Heartbeat Settings
    function woptifications_heartbeat_settings( $settings ) {
        $settings['interval'] = 15; //Anything between 15-60
        $settings['autostart'] = true; 
        return $settings;
    }


}

$woptificationsEqueueScripts  = new woptificationsEqueueScriptsClass();