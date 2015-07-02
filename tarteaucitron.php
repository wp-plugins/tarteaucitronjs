<?php
/*
Plugin Name: tarteaucitron.js
Plugin URI: https://opt-out.ferank.eu/
Description: Installer le script tarteaucitron.js
Version: 1.0.1
Author: Amauri CHAMPEAUX
Author URI: http://amauri.champeaux.fr/a-propos
Domain Path: /languages/
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require('Admin.php');
require('Widgets.php');
require('Sidebars.php');

/**
 * Traductions
 */
function tarteaucitron_load_textdomain() {
    load_plugin_textdomain( 'tarteaucitron', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'tarteaucitron_load_textdomain' );

/**
 * POST vers tarteaucitron.js
 */
function tarteaucitron_post($query, $needLogin = 1) {
    $query .= '&langWP='.substr(get_locale(), 0, 2);
    if ($needLogin == 1) {
        $query .= '&uuid='.get_option('tarteaucitronUUID').'&token='.get_option('tarteaucitronToken').'&website='.$_SERVER['SERVER_NAME'];
    }
    $opts = array('http' =>
                  array(
                      'method'  => 'POST',
                      'header'  => 'Content-type: application/x-www-form-urlencoded',
                      'content' => $query
                  )
                 );    
    $context = stream_context_create($opts);
    return file_get_contents('https://opt-out.ferank.eu/pro/wordpress/token.php', false, $context);
}

/**
 * CSS et Javascript
 */
function tarteaucitron_user_css_js() {
	$domain = $_SERVER['SERVER_NAME'];
	wp_register_style('tarteaucitron', plugins_url('tarteaucitronjs/css/user.css'));

    wp_enqueue_style('tarteaucitron');
    wp_enqueue_script('tarteaucitron', '//opt-out.ferank.eu/tarteaucitron.js?domain='.$domain.'&uuid='.get_option('tarteaucitronUUID'), '', '', TRUE);
}
add_action('wp_enqueue_scripts', 'tarteaucitron_user_css_js');