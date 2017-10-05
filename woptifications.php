<?php
/*
   Plugin Name: Woptifications
   Plugin URI: http://wordpress.org/plugins/woptifications/
   Version: 2.1
   Author: woptima
   Description: Near real-time notifications on font-end. Applicable to events such as post publish or commenting. Supports custom post types and woocommerce products.
   Text Domain: woptifications
   License: GPLv3
  */


$woptifications_minimalRequiredPhpVersion = '5.4';

function woptifications_noticePhpVersionWrong() {
    global $woptifications_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Woptifications" requires a newer version of PHP to be running.',  'woptifications').
            '<br/>' . __('Minimal version of PHP required: ', 'woptifications') . '<strong>' . $woptifications_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'woptifications') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function woptifications_PhpVersionCheck() {
    global $woptifications_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $woptifications_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'woptifications_noticePhpVersionWrong');
        return false;
    }
    return true;
}


function woptifications_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('woptifications', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

add_action('plugins_loaded','woptifications_i18n_init');

if (woptifications_PhpVersionCheck()) {
    include_once('woptifications_init.php');
    woptifications_init(__FILE__);
}
