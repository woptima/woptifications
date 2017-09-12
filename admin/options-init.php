<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "woptifications_options";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    //$theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => $opt_name,
        'display_name' => 'Woptifications Options',
        'page_title' => 'Woptifications',
        'update_notice' => TRUE,
        'menu_type' => 'menu',
        'menu_title' => 'Woptifications options',
        'allow_sub_menu' => TRUE,
        'page_parent' => 'themes.php',
        'page_parent_post_type' => 'your_post_type',
        'customizer' => TRUE,
        'default_mark' => '*',
        'google_api_key' => 'AIzaSyCE0hAyl9RjdX3J6URK6ttQDU5lmOnBj0I',
        'class' => 'woptifications-option',
        'hints' => array(
            'icon' => 'el el-bulb',
            'icon_position' => 'right',
            'icon_color' => '#81d742',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
                'shadow' => '1',
            ),
            'tip_position' => array(
                'my' => 'top center',
                'at' => 'top center',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover click',
                ),
                'hide' => array(
                    'effect' => 'fade',
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => TRUE,
        'output_tag' => TRUE,
        'settings_api' => TRUE,
        'cdn_check_time' => '1440',
        'compiler' => TRUE,
        'page_permissions' => 'manage_options',
        'save_defaults' => TRUE,
        'show_import_export' => TRUE,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
        'use_cdn' => FALSE,
        'dev_mode' => FALSE,
    );

    // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
    /*
    $args['share_icons'][] = array(
        'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
        'title' => 'Visit us on GitHub',
        'icon'  => 'el el-github'
        //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
    );
    $args['share_icons'][] = array(
        'url'   => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
        'title' => 'Like us on Facebook',
        'icon'  => 'el el-facebook'
    );
    $args['share_icons'][] = array(
        'url'   => 'http://twitter.com/reduxframework',
        'title' => 'Follow us on Twitter',
        'icon'  => 'el el-twitter'
    );
    $args['share_icons'][] = array(
        'url'   => 'http://www.linkedin.com/company/redux-framework',
        'title' => 'Find us on LinkedIn',
        'icon'  => 'el el-linkedin'
    );
    */
    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */

    /*
     * ---> START HELP TABS
     */

    /*

    $tabs = array(
        array(
            'id'      => 'redux-help-tab-1',
            'title'   => __( 'Theme Information 1', 'admin_folder' ),
            'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
        ),
    );
    Redux::setHelpTab( $opt_name, $tabs );

    // Set the help sidebar
    $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'admin_folder' );
    Redux::setHelpSidebar( $opt_name, $content );

    */
    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    function populate_post_types() {
        $post_types=get_post_types(['public' => true, '_builtin' => true],'names');
        $ret = array();
        foreach ($post_types as $post_type) {
            $ret[$post_type] = $post_type;
        }
        return $ret;
    }

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Hooks settings', 'woptifications' ),
        'id'     => 'alerts',
        'desc'   => __( 'Select which alerts to display', 'woptifications' ),
        'icon'   => 'el el-fork',
        'fields' => array(
            array(
                'id'       => 'alert_hooks',
                'type'     => 'checkbox',
                'title'    => __( 'Select alert hooks', 'woptifications' ),
                'options'  => array(
                        'post' => __( 'post publish', 'woptifications'),
                        'comment' => __( 'new comment', 'woptifications'),
                    ),
            ),
            array(
                'id'       => 'publish_post_types',
                'type'     => 'checkbox',
                'title'    => __( 'Select publish post types', 'woptifications' ),
                'options'  => populate_post_types(),
            ),
            array(
                'id'       => 'comment_post_types',
                'type'     => 'checkbox',
                'title'    => __( 'Select new comment post types', 'woptifications' ),
                'options'  => populate_post_types(),
            )
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Content settings', 'woptifications' ),
        'id'     => 'content_settings',
        'desc'   => __( 'Notification output contents', 'woptifications' ),
        'icon'   => 'el el-edit',
        )
    );

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Post publish content', 'woptifications' ),
        'id'     => 'publish_content_settings',
        'icon'   => 'el el-file-new',
        'subsection' => true,
        'fields' => array(
            array(
                'id'       => 'publish_alert_type',
                'type'     => 'select',
                'title'    => __('Select alert type', 'woptifications'), 
                'options'  => array(
                    'info' => __( 'info', 'woptifications'),
                    'warning' => __( 'warning', 'woptifications'),
                    'success' => __( 'success', 'woptifications'),
                    'error' => __( 'error', 'woptifications'),
                ),
                'default'  => 'info',
            ),
            array(
                'id'       => 'publish_title',
                'type'     => 'text',
                'title'    => __('Notification title', 'woptifications'),
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%url%% <br />
                    %%author%% <br />
                    %%type%% <br />
                    %%thumbnail%% <br />
                    %%categories%%', 'woptifications'),

            ),
            array(
                'id'       => 'publish_content',
                'type'     => 'editor',
                'title'    => __('Notification content', 'woptifications'), 
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%url%% <br />
                    %%author%% <br />
                    %%type%% <br />
                    %%thumbnail%% <br />
                    %%categories%%', 'woptifications'),
                'args'    => array(
                    'teeny'            => false,
                    'textarea_rows'    => 10
                ),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'  => __( 'New comment content', 'woptifications' ),
        'id'     => 'comment_content_settings',
        'icon'   => 'el el-comment',
        'subsection' => true,
        'fields' => array(
            array(
                'id'       => 'comment_alert_type',
                'type'     => 'select',
                'title'    => __('Select alert type', 'woptifications'), 
                'options'  => array(
                    'info' => __( 'info', 'woptifications'),
                    'warning' => __( 'warning', 'woptifications'),
                    'success' => __( 'success', 'woptifications'),
                    'error' => __( 'error', 'woptifications')
                ),
                'default'  => 'info',
            ),
            array(
                'id'       => 'comment_title',
                'type'     => 'text',
                'title'    => __('Notification title', 'woptifications'),
                'subtitle' => __('You can use the following variables: <br />
                    %%post_title%% <br />
                    %%comment_url%% <br />
                    %%author%% <br />
                    %%author_avatar%% <br />
                    %%post_type%% <br />
                    %%post_thumbnail%% <br />
                    %%post_url%%', 'woptifications'),

            ),
            array(
                'id'       => 'comment_content',
                'type'     => 'editor',
                'title'    => __('Notification content', 'woptifications'), 
                'subtitle' => __('You can use the following variables: <br />
                    %%post_title%% <br />
                    %%comment_url%% <br />
                    %%author%% <br />
                    %%author_avatar%% <br />
                    %%post_type%% <br />
                    %%post_thumbnail%% <br />
                    %%post_url%%', 'woptifications'),
                'args'    => array(
                    'teeny'            => false,
                    'textarea_rows'    => 10
                ),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Product publish content', 'woptifications' ),
        'id'     => 'product_content_settings',
        'icon'   => 'el el-gift',
        'subsection' => true,
        'fields' => array(
            array(
                'id'       => 'product_alert_type',
                'type'     => 'select',
                'title'    => __('Select alert type', 'woptifications'), 
                'options'  => array(
                    'info' => __( 'info', 'woptifications'),
                    'warning' => __( 'warning', 'woptifications'),
                    'success' => __( 'success', 'woptifications'),
                    'error' => __( 'error', 'woptifications')
                ),
                'default'  => 'info',
            ),
            array(
                'id'       => 'product_title',
                'type'     => 'text',
                'title'    => __('Notification title', 'woptifications'),
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%url%% <br />
                    %%author%% <br />
                    %%type%% <br />
                    %%thumbnail%% <br />
                    %%categories%% <br />
                    %%price%%', 'woptifications'),

            ),
            array(
                'id'       => 'product_content',
                'type'     => 'editor',
                'title'    => __('Notification content', 'woptifications'), 
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%url%% <br />
                    %%author%% <br />
                    %%type%% <br />
                    %%thumbnail%% <br />
                    %%categories%% <br />
                    %%price%%', 'woptifications'),
                'args'    => array(
                    'teeny'            => false,
                    'textarea_rows'    => 10
                ),
            ),
        )
    ) );


    Redux::setSection( $opt_name, array(
        'title'  => __( 'Notification settings', 'woptifications' ),
        'id'     => 'toastr_settings',
        'desc'   => __( 'Configure the notifications', 'woptifications' ),
        'icon'   => 'el el-adjust-alt',
        'fields' => array(
            array(
                'id'       => 'options',
                'type'     => 'checkbox',
                'title'    => __( 'Notification options', 'woptifications' ),
                'options'  => array(
                        'closeButton' => __( 'close button', 'woptifications'),
                        'progressBar' => __( 'progress bar', 'woptifications'),
                        'preventDuplicates' => __( 'prevent duplicates', 'woptifications'),
                        'newestOnTop' => __( 'newest ontop', 'woptifications'),
                    ),
            ),
            array(
                'id'       => 'positionClass',
                'type'     => 'radio',
                'title'    => __( 'Notification position', 'woptifications' ),
                'options'  => array(
                        'toast-top-right' => __( 'top right', 'woptifications'),
                        'toast-bottom-right' => __( 'bottom right', 'woptifications' ),
                        'toast-bottom-left' => __( 'bottom left', 'woptifications'),
                        'toast-top-left' => __( 'top left', 'woptifications'),
                        'toast-top-full-width' => __( 'top full width', 'woptifications'),
                        'toast-bottom-full-width' => __( 'bottom full width', 'woptifications'),
                        'toast-top-center' => __( 'top center', 'woptifications'),
                        'toast-bottom-center' => __( 'bottom center', 'woptifications'),
                    ),
                'default'  => 'bottom_right',
            ),
            array(
                'id'       => 'showEasing',
                'type'     => 'select',
                'title'    => __('Entrance easing', 'woptifications'), 
                'options'  => array(
                        'linear' => __( 'linear', 'woptifications'),
                        'swing' => __( 'swing', 'woptifications'),
                    ),
                'default'  => 'swing',
            ),
            array(
                'id'       => 'hideEasing',
                'type'     => 'select',
                'title'    => __('Exit easing', 'woptifications'), 
                'options'  => array(
                        'linear' => __( 'info', 'woptifications'),
                        'swing' => __( 'swing', 'woptifications'),
                    ),
                'default'  => 'swing',
            ),
            array(
                'id'       => 'showMethod',
                'type'     => 'select',
                'title'    => __('Exit easing', 'woptifications'), 
                'options'  => array(
                        'fadeIn' => __( 'fade in', 'woptifications'),
                        'slideDown' => __( 'slide down', 'woptifications'),
                    ),
                'default'  => 'slideDown',
            ),
            array(
                'id'       => 'hideMethod',
                'type'     => 'select',
                'title'    => __('Exit easing', 'woptifications'), 
                'options'  => array(
                        'fadeOut' => __( 'fade out', 'woptifications'),
                        'slideUp' => __( 'slide up', 'woptifications'),
                    ),
                'default'  => 'slideUp',
            ),
            array(
                'id'        => 'showDuration',
                'type'      => 'slider',
                'title'     => __('Entrance animation duration', 'woptifications'),
                "default"   => 1000,
                "min"       => 100,
                "step"      => 100,
                "max"       => 5000,
                'display_value' => 'text'
            ),
            array(
                'id'        => 'hideDuration',
                'type'      => 'slider',
                'title'     => __('Exit animation duration', 'woptifications'),
                "default"   => 1000,
                "min"       => 100,
                "step"      => 100,
                "max"       => 5000,
                'display_value' => 'text'
            ),
            array(
                'id'        => 'timeOut',
                'type'      => 'slider',
                'title'     => __('Display duration', 'woptifications'),
                'subtitle' => __('Set to 0 to keep notification visible until user dismissal', 'woptifications'),
                "default"   => 5000,
                "min"       => 500,
                "step"      => 500,
                "max"       => 300000,
                'display_value' => 'text'
            ),
            array(
                'id'        => 'extendedTimeOut',
                'type'      => 'slider',
                'title'     => __('Entended display duration after hover', 'woptifications'),
                'subtitle' => __('Set to 0 to keep notification visible until user dismissal after hover', 'woptifications'),
                "default"   => 1000,
                "min"       => 500,
                "step"      => 500,
                "max"       => 30000,
                'display_value' => 'text'
            ),
        )
    ) );



    /*
     * <--- END SECTIONS
     */
