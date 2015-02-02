<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Avant les articles
 * 4x 25%
 */
function tarteaucitronWidgetBeforePost($content) {
	if (is_singular(array('post')) && is_active_sidebar( 'tarteaucitron-before-post' ) && is_main_query()) { 
        dynamic_sidebar('tarteaucitron-before-post');
    }
	return '<div class="clear"></div>'.$content;
}
add_filter('the_content', 'tarteaucitronWidgetBeforePost', 50);

/**
 * Avant les articles
 * 1x 100%
 */
function tarteaucitronWidgetBeforePostXL($content) {
	if (is_singular(array('post')) && is_active_sidebar('tarteaucitron-before-post-xl') && is_main_query()) { 
        dynamic_sidebar('tarteaucitron-before-post-xl'); 
    }
	return '<div class="clear"></div>'.$content;
}
add_filter('the_content', 'tarteaucitronWidgetBeforePostXL', 50);

/**
 * Après les articles
 * 4x 25%
 */
function tarteaucitronWidgetAfterPost($content) {
	if (is_singular(array('post')) && is_active_sidebar('tarteaucitron-after-post') && is_main_query()) {
        ob_start();
		dynamic_sidebar('tarteaucitron-after-post');
        $sidebar = ob_get_contents();
        ob_end_clean();
    }
    return $content . $sidebar . '<div class="clear"></div>';
}
add_filter('the_content', 'tarteaucitronWidgetAfterPost', 50);

/**
 * Après les articles
 * 1x 100%
 */
function tarteaucitronWidgetAfterPostXL($content) {
	if (is_singular(array('post')) && is_active_sidebar('tarteaucitron-after-post-xl') && is_main_query()) {
        ob_start();
		dynamic_sidebar('tarteaucitron-after-post-xl');
        $sidebar = ob_get_contents();
        ob_end_clean();
    }
    return $content . $sidebar;
}
add_filter('the_content', 'tarteaucitronWidgetAfterPostXL', 50);

/**
 * Enregistrement des sidebars
 */
load_plugin_textdomain( 'tarteaucitron', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
if (function_exists('register_sidebar') ) {

    $allWidgetizedAreas = array("tarteaucitron-before-post", "tarteaucitron-before-post-xl", "tarteaucitron-after-post", "tarteaucitron-after-post-xl");
    
    foreach ($allWidgetizedAreas as $WidgetAreaName) {
		if(preg_match('#^tarteaucitron-before#', $WidgetAreaName)) {
			$name = __('Avant les articles', 'tarteaucitron');
			$pos = __('Placez des widgets au dessus de vos articles et pages.', 'tarteaucitron');
		} else {
			$name = __('Après les articles', 'tarteaucitron');
			$pos = __('Placez des widgets en dessous de vos articles et pages.', 'tarteaucitron');
		}
	
		if(preg_match('#-xl$#', $WidgetAreaName)) {
			$name .= ' (1x100%)';
			$class = 'tarteaucitronWidget100p';
		} else {
			$name .= ' (4x25%)';
			$class = 'tarteaucitronWidget25p';
		}
        register_sidebar(array(
			'name' => $name,
			'before_widget' => '<div class="'.$class.'">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
			'id' => $WidgetAreaName,
			'description' => $pos
        ));
    
    }
}