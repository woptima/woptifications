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
        'menu_title' => 'Woptifications',
        'menu_icon'   =>  plugins_url( 'woptifications/img/icon.png'),
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

    function woptifications_populate_post_types() {
        $ret = array();

        $post_types=get_post_types(['public' => true],'names');

        foreach ($post_types as $post_type) {
            $ret[$post_type] = __($post_type);
        }
        
        if(class_exists( 'WooCommerce' )) {
            $ret['product'] = __('product', 'WooCommerce');
        }

        return $ret;
    }

    function woptifications_set_user_meta() {
        $inner_options = get_option('woptifications_options');
        $metas = $inner_options['user_custom_fields'];
        $custom_fields = ['%%username%%','%%nickname%%','%%avatar%%'];
        if(!empty($metas)) {
            foreach ($metas as $key => $value) {
                if($value != "") {
                    $custom_fields[] = '%%'.$value.'%%';
                }
            }
        }
        return implode("<br />",$custom_fields);
    }


    Redux::setSection( $opt_name, array(
        'title'  => __( 'Notification type', 'woptifications' ),
        'subtitle'   => __( 'Select which notification type to use on which devices', 'woptifications' ),
        'id'     => 'notification_type_select',
        'desc'   => __( 'Popups will not require the user to accept and may contain html content. Push notifications instead will require a one time acceptance by the user and will only contain text title, body and link on click.', 'woptifications' ),
        'icon'   => 'el el-rss',
        'fields' => array(
            array( 
                'id'       => 'push_test',
                'type'     => 'raw',
                'title'    => __('Test push notification', 'redux-framework-demo'),
                'content'  => '<div class="button woptifications-push-test">Notify me!</div>',
                'classes'  => 'inline'
            ),
            array( 
                'id'       => 'popup_test',
                'type'     => 'raw',
                'title'    => __('Test popup notification', 'redux-framework-demo'),
                'content'  => '<div class="button woptifications-popup-test">Alert me!</div>',
                'classes'  => 'inline'
            ),
            array(
                'id'       => 'notification_type',
                'type'     => 'checkbox',
                'title'    => __( 'Select notification type', 'woptifications' ),
                'options'  => array(
                        'popup' => __( 'Popup', 'woptifications'),
                        'push' => __( 'Push', 'woptifications'),
                    ),
            ),
        )
    ) );

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
                        'registration' => __( 'new user', 'woptifications'),
                    ),
            ),
            array(
                'id'       => 'publish_post_types',
                'type'     => 'checkbox',
                'title'    => __( 'Select post publish types', 'woptifications' ),
                'options'  => woptifications_populate_post_types(),
            ),
            array(
                'id'       => 'comment_post_types',
                'type'     => 'checkbox',
                'title'    => __( 'Select new comment post types', 'woptifications' ),
                'options'  => woptifications_populate_post_types(),
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
                'id'    => 'publish_popup_section',
                'type' => 'section',
                'title' => __('Popup content', 'woptifications'),
                'indent' => true,
            ),
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
                'id'       => 'cat_match',
                'type'     => 'switch',
                'title'    => __('Match categories', 'woptifications'),
                'subtitle'    => __('Show notification only if viewing a post from same category ', 'woptifications'),
                'default'  => false,
            ),
            array(
                'id'       => 'publish_title',
                'type'     => 'text',
                'title'    => __('Popup title', 'woptifications'),
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%url%% <br />
                    %%author%% <br />
                    %%type%%', 'woptifications'),
            ),
            array(
                'id'       => 'publish_content',
                'type'     => 'editor',
                'title'    => __('Popup content', 'woptifications'), 
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
            array(
                'id'     => 'publish_popup_section_end',
                'type'   => 'section',
                'indent' => false,
            ),
            array(
                'id'    => 'publish_push_section',
                'type' => 'section',
                'title' => __('Push notification content', 'woptifications'),
                'indent' => true,
            ),
            array(
                'id'       => 'publish_push_title',
                'type'     => 'text',
                'title'    => __('Push title', 'woptifications'),
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%author%% <br />
                    %%type%%', 'woptifications'),
            ),
            array(
                'id'       => 'publish_push_content',
                'type'     => 'textarea',
                'title'    => __('Push content', 'woptifications'),
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%author%% <br />
                    %%type%%', 'woptifications'),
            ),
            array(
                'id'       => 'publish_use_thumb',
                'type'     => 'switch', 
                'title'    => __('Use thumbnail as notification image', 'woptifications'),
                'subtitle'    => __('If set to off default icon set in notification settings>push notification will be used', 'woptifications'),
                'default'  => false,
            ),
            array(
                'id'       => 'publish_custom_link_enable',
                'type'     => 'switch',
                'title'    => __('Use custom link', 'woptifications'),
                'subtitle'    => __('If set to off post link will be used', 'woptifications'),
                'default'  => false,
            ),
            array(
                'id'       => 'publish_custom_link',
                'type'     => 'text',
                'title'    => __('Link url', 'woptifications'),
            ),
            array(
                'id'     => 'publish_push_section_end',
                'type'   => 'section',
                'indent' => false,
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
                'id'    => 'comment_popup_section',
                'type' => 'section',
                'title' => __('Popup content', 'woptifications'),
                'indent' => true,
            ),
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
             array(
                'id'     => 'comment_popup_section_end',
                'type'   => 'section',
                'indent' => false,
            ),
            array(
                'id'    => 'comment_push_section',
                'type' => 'section',
                'title' => __('Push notification content', 'woptifications'),
                'indent' => true,
            ),
            array(
                'id'       => 'comment_push_title',
                'type'     => 'text',
                'title'    => __('Push title', 'woptifications'),
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%author%% <br />
                    %%type%%', 'woptifications'),
            ),
            array(
                'id'       => 'comment_push_content',
                'type'     => 'textarea',
                'title'    => __('Push content', 'woptifications'),
                'subtitle' => __('You can use the following variables: <br />
                    %%title%% <br />
                    %%author%% <br />
                    %%type%%', 'woptifications'),
            ),
            array(
                'id'       => 'comment_use_thumb',
                'type'     => 'switch', 
                'title'    => __('Use thumbnail as notification image', 'woptifications'),
                'subtitle'    => __('If set to off default icon set in notification settings>push notification will be used', 'woptifications'),
                'default'  => false,
            ),
            array(
                'id'       => 'comment_custom_link_enable',
                'type'     => 'switch',
                'title'    => __('Use custom link', 'woptifications'),
                'subtitle'    => __('If set to off comment post link will be used', 'woptifications'),
                'default'  => false,
            ),
            array(
                'id'       => 'comment_custom_link',
                'type'     => 'text',
                'title'    => __('Link url', 'woptifications'),
            ),
            array(
                'id'     => 'comment_push_section_end',
                'type'   => 'section',
                'indent' => false,
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'  => __( 'New user content', 'woptifications' ),
        'id'     => 'registration_content_settings',
        'icon'   => 'el el-user',
        'subsection' => true,
        'fields' => array(
            array(
                'id'       => 'registration_alert_type',
                'type'     => 'select',
                'title'    => __('Select popup alert type', 'woptifications'), 
                'options'  => array(
                    'info' => __( 'info', 'woptifications'),
                    'warning' => __( 'warning', 'woptifications'),
                    'success' => __( 'success', 'woptifications'),
                    'error' => __( 'error', 'woptifications')
                ),
                'default'  => 'info',
            ),
            array(
                'id'       => 'registration_default_message',
                'type'     => 'switch',
                'title'    => __('Use predefined notification message', 'woptifications'),
                'subtitle'    => __('New user joined! %%username%% has just registered', 'woptifications'),
                'default'  => true,
            ),
            array(
                'id'    => 'info_registration',
                'type' => 'info',
                'style' => 'critical',
                'title' => __('<i class="el el-warning-sign"></i> Warning, since specific user fields may not be required upon user registration, be careful when choosing which fields to display in the message as they may result empty.', 'woptifications'),
                'required' => array('registration_default_message','equals','0'),
            ),
            array(
                'id'    => 'registration_popup_section',
                'type' => 'section',
                'title' => __('Popup content', 'woptifications'),
                'indent' => true,
                'required' => array('registration_default_message','equals','0'),
            ),
            array(
                'id'        =>'user_custom_fields',
                'type'      => 'multi_text',
                'title'     => __('Custom user meta', 'woptifications'),
                'subtitle'  => __('Insert custom meta fields to use in the title and content', 'woptifications'),
                'desc'      => __('Enter the meta fields exact names (no spaces, case sensitive), save and reload to view them.', 'woptifications'),
                'required' => array('registration_default_message','equals','0'),
            ),
            array(
                'id'       => 'registration_title',
                'type'     => 'text',
                'title'    => __('Notification title', 'woptifications'),
                'subtitle' => woptifications_set_user_meta(),
                'required' => array('registration_default_message','equals','0'),

            ),
            array(
                'id'       => 'registration_content',
                'type'     => 'editor',
                'title'    => __('Notification content', 'woptifications'), 
                'subtitle' => woptifications_set_user_meta(),
                'required' => array('registration_default_message','equals','0'),
                'args'     => array(
                    'teeny'            => false,
                    'textarea_rows'    => 10
                ),
            ),
            array(
                'id'     => 'registration_popup_section_end',
                'type'   => 'section',
                'indent' => false,
                'required' => array('registration_default_message','equals','0'),
            ),
            array(
                'id'    => 'registration_push_section',
                'type' => 'section',
                'title' => __('Push notification content', 'woptifications'),
                'indent' => true,
                'required' => array('registration_default_message','equals','0'),
            ),
            array(
                'id'       => 'registration_push_title',
                'type'     => 'text',
                'title'    => __('Push title', 'woptifications'),
                'subtitle' => woptifications_set_user_meta(),
                'required' => array('registration_default_message','equals','0'),
            ),
            array(
                'id'       => 'registration_push_content',
                'type'     => 'textarea',
                'title'    => __('Push content', 'woptifications'),
                'subtitle' => woptifications_set_user_meta(),
                'required' => array('registration_default_message','equals','0'),
            ),
            array(
                'id'       => 'registration_use_thumb',
                'type'     => 'switch', 
                'title'    => __('Use avatar as notification image', 'woptifications'),
                'subtitle'    => __('If set to off, default images loaded in the Push notification settings will be used.', 'woptifications'),
                'default'  => false,
            ),
            array(
                'id'     => 'registration_push_section_end',
                'type'   => 'section',
                'indent' => false,
                'required' => array('registration_default_message','equals','0'),
            ),
        )
    ) );

    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
        Redux::setSection( $opt_name, array(
            'title'  => __( 'Product publish content', 'woptifications' ),
            'id'     => 'product_content_settings',
            'icon'   => 'el el-gift',
            'subsection' => true,
            'fields' => array(
                array(
                    'id'    => 'publish_popup_section',
                    'type' => 'section',
                    'title' => __('Popup notification content', 'woptifications'),
                    'indent' => true,
                ),
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
                    'id'       => 'product_cat_match',
                    'type'     => 'switch', 
                    'title'    => __('Match categories', 'woptifications'),
                    'subtitle'    => __('Show notification only if viewing a post from same category ', 'woptifications'),
                    'default'  => false,
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
                array(
                    'id'     => 'product_popup_section_end',
                    'type'   => 'section',
                    'indent' => false,
                ),
                array(
                    'id'    => 'product_push_section',
                    'type' => 'section',
                    'title' => __('Push notification content', 'woptifications'),
                    'indent' => true,
                ),
                array(
                    'id'       => 'product_push_title',
                    'type'     => 'text',
                    'title'    => __('Push title', 'woptifications'),
                    'subtitle' => __('You can use the following variables: <br />
                        %%title%% <br />
                        %%author%% <br />
                        %%type%% <br />
                        %%price%%', 'woptifications'),
                ),
                array(
                    'id'       => 'product_push_content',
                    'type'     => 'textarea',
                    'title'    => __('Push content', 'woptifications'),
                    'subtitle' => __('You can use the following variables: <br />
                        %%title%% <br />
                        %%author%% <br />
                        %%type%% <br />
                        %%price%%', 'woptifications'),
                    'args'    => array(
                        'teeny'            => false,
                        'textarea_rows'    => 10
                    ),
                ),
                array(
                    'id'       => 'product_use_thumb',
                    'type'     => 'switch', 
                    'title'    => __('Use thumbnail as notification image', 'woptifications'),
                    'subtitle'    => __('If set to off default icon set in notification settings>push notification will be used', 'woptifications'),
                    'default'  => false,
                ),
                array(
                    'id'       => 'product_custom_link_enable',
                    'type'     => 'switch',
                    'title'    => __('Use custom link', 'woptifications'),
                    'subtitle'    => __('If set to off post link will be used', 'woptifications'),
                    'default'  => false,
                ),
                array(
                    'id'       => 'product_custom_link',
                    'type'     => 'text',
                    'title'    => __('Link url', 'woptifications'),
                ),
                array(
                    'id'     => 'product_push_section_end',
                    'type'   => 'section',
                    'indent' => false,
                ),
            )
        ) );
    }

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Notification settings', 'woptifications' ),
        'id'     => 'notification_settings',
        'desc'   => __( 'Notification output contents', 'woptifications' ),
        'icon'   => 'el el-comment',
        )
    );

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Popup', 'woptifications' ),
        'id'     => 'toastr_settings',
        'desc'   => __( 'Configure the notifications', 'woptifications' ),
        'icon'   => 'el el-adjust-alt',
        'subsection' => true,
        'fields' => array(
            array(
                'id'       => 'options',
                'type'     => 'checkbox',
                'title'    => __( 'Notification options', 'woptifications' ),
                'options'  => array(
                        'closea' => __( 'close a', 'woptifications'),
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
                'default'  => 'toast-bottom-right',
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
                "min"       => 0,
                "step"      => 500,
                "max"       => 300000,
                'display_value' => 'text'
            ),
            array(
                'id'        => 'extendedTimeOut',
                'type'      => 'slider',
                'title'     => __('Extended display duration after hover', 'woptifications'),
                'subtitle' => __('Set to 0 to keep notification visible until user dismissal after hover', 'woptifications'),
                "default"   => 1000,
                "min"       => 0,
                "step"      => 500,
                "max"       => 30000,
                'display_value' => 'text'
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Push notification', 'woptifications' ),
        'id'     => 'push_settings',
        'desc'   => __( 'Configure the notifications', 'woptifications' ),
        'icon'   => 'el el-picture',
        'subsection' => true,
        'fields' => array(
            array(
                'id'       => 'push_icon',
                'type'     => 'media', 
                'url'      => true,
                'title'    => __('Notification icon', 'woptifications'),
                'subtitle' => __('You website logo', 'woptifications'),
            ),
        )
    ) );


    /*
     * <--- END SECTIONS
     */
