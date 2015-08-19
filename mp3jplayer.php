<?php 
/* 
Plugin Name: MP3-jPlayer
Plugin URI: http://mp3-jplayer.com
Description: Easy, Flexible Audio for WordPress. 
Version: 2.4
Author: Simon Ward
Author URI: http://www.sjward.org
License: GPL2
  		
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.
*/


if ( ! function_exists( 'get_bloginfo' ) ) { //prevent direct access
	die();
}


$path = dirname( __FILE__ );
include_once( $path . '/widget-ui.php' );
include_once( $path . '/widget-sh.php' );
include_once( $path . '/template-functions.php' );
include_once( $path . '/main.php' );
include_once( $path . '/frontend.php' );


if ( class_exists( 'MP3j_Front' ) ) {
	$MP3JP = new MP3j_Front();
}

if ( isset( $MP3JP ) ) {
	
	add_action( 'init', array( &$MP3JP, 'onInit' ), 100 );
	add_action( 'widgets_init',  array( &$MP3JP, 'registerWidgets' ) );
	
	if ( is_admin() ) {
		include_once( $path . '/admin-settings.php');
		include_once( $path . '/admin-colours.php');
		add_action( 'admin_menu', array( &$MP3JP, 'createAdminPages' ), 100 );
		add_action( 'deactivate_mp3-jplayer/mp3jplayer.php', array( &$MP3JP, 'deactivate' ) );
	}
	
	
	//$MP3JP->registerShortcodes();
	$ops = $MP3JP->theSettings;
	add_shortcode( 'mp3t', array( &$MP3JP, 'inline_play_handler' ) );
	add_shortcode( 'mp3j', array( &$MP3JP, 'inline_play_graphic' ) );
	add_shortcode( 'mp3-jplayer', array( &$MP3JP, 'primary_player' ) );
	
	remove_shortcode( 'popout' );
	add_shortcode( 'popout', array( &$MP3JP, 'popout_link_player' ) );
	add_shortcode( 'mp3-popout', array( &$MP3JP, 'popout_link_player' ) );
	
	if ( $ops['replace_WP_playlist'] === 'true' && ! is_admin() ) {
		remove_shortcode('playlist');
		add_shortcode('playlist', array(&$MP3JP, 'replacePlaylistShortcode'));
	}
	
	if ( ! is_admin() && ($ops['replace_WP_audio'] === 'true' || $ops['replace_WP_embedded'] === 'true' || $ops['replace_WP_attached'] === 'true') )	{
		remove_shortcode('audio');
		add_shortcode('audio', array(&$MP3JP, 'replaceAudioShortcode'));
	}
	
	
	//$MP3JP->registerTagCallbacks();
	add_action( 'mp3j_addscripts', array( &$MP3JP, 'scripts_tag_handler' ), 1, 1 );
	add_action( 'mp3j_put', array( &$MP3JP, 'template_tag_handler' ), 10, 1 );
	add_action( 'mp3j_debug', array( &$MP3JP, 'debug_info' ), 10, 1 );
	add_filter( 'mp3j_grab_library', array( &$MP3JP, 'grablibrary_handler' ), 10, 1 );
	add_action( 'mp3j_settings', array( &$MP3JP, 'mp3j_settings' ), 1, 1 );
	
	/*
	* should use this conditional but it means that as page loads up players initially appear unstyled (aren't styled until
	* last minute) TODO:enqueue style in header when poss.
	* always run both hooks for the mo.
	*/
	//$WPversion = substr( get_bloginfo('version'), 0, 3);
	//if ( $WPversion < 3.3 ) {
		add_action('wp_head', array( &$MP3JP, 'header_scripts_handler' ), 2);	//Support for WP versions below 3.3
	//}
	add_action('wp_footer', array( &$MP3JP, 'checkAddScripts' ), 1); 			//Final chance to enqueue, process this action early (priority < 20).
	add_action('wp_footer', array( &$MP3JP, 'footercode_handler' ), 200); 	//Add any inline js, process this action late (enqueues fire at priority 20).
	
	add_filter('get_the_excerpt', array( &$MP3JP, 'get_excerpt_handler' ), 1);
	add_filter('the_content', array( &$MP3JP, 'afterExcerpt' ), 9999);
	
	if ( $ops['make_player_from_link'] == "true" ) {
		add_filter('the_content', array( &$MP3JP, 'replace_links' ), 1);
	}
	if ( $ops['run_shcode_in_excerpt'] == "true" ) {
		add_filter( 'the_excerpt', 'shortcode_unautop' );
		add_filter( 'the_excerpt', 'do_shortcode' );
	}	

	
	
}

?>