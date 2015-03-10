<?php 
/* 
Plugin Name: MP3-jPlayer
Plugin URI: http://mp3-jplayer.com
Description: Easy, Flexible Audio for WordPress. 
Version: 2.3.2
Author: Simon Ward
Author URI: http://www.sjward.org
License: GPL2
  	
	Copyright 2010 - 2015 Simon Ward
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with the plugin; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if ( ! function_exists( 'get_bloginfo' ) ) { //prevent direct access
	die();
}


$path = dirname(__FILE__);
include_once( $path . '/main.php');
include_once( $path . '/frontend.php'); //extends main

if ( class_exists("MP3j_Front") ) {
	$MP3JP = new MP3j_Front();
}

if ( isset($MP3JP) )
{
	include_once( $path . '/widget-ui.php'); //ui widget (playlister)
	include_once( $path . '/widget-sh.php'); //shortcodes Widget
	
	$ops = $MP3JP->theSettings;
	if ( is_admin() )
	{
		include_once( $path . '/admin-settings.php'); //settings page
		include_once( $path . '/admin-colours.php');
		
		function mp3j_adminpage()
		{
			/*
			//extension page - $p (arr)
			--
				[parent]-> str
				[title]-> str
				[menuName]-> str
				[capability]-> str
				[slug]-> str
				[drawFunction]-> str
				['scriptsFunction'] -> false/str
			*/
			
			//add menu pages	
			global $MP3JP;
			
			//Settings page
			$pluginpage = add_menu_page( 'Settings | MP3 jPlayer', 'MP3 jPlayer', 'manage_options', 'mp3-jplayer', 'mp3j_print_admin_page' ); //root				
			add_submenu_page( 'mp3-jplayer', 'Settings | MP3 jPlayer', 'Settings', 'manage_options', 'mp3-jplayer', 'mp3j_print_admin_page' ); //root in sub
			add_action( 'admin_head-'. $pluginpage, array(&$MP3JP, 'mp3j_admin_header') ); 
			$MP3JP->menuHANDLES['parent'] = $pluginpage;
			
			if ( $MP3JP->setup['designPage'] === true ) {
				$subm_colours = add_submenu_page( 'mp3-jplayer', 'Design | MP3 jPlayer', 'Design', 'manage_options', 'mp3-jplayer-colours', 'mp3j_print_colours_page' );
				add_action( 'admin_head-'. $subm_colours, array(&$MP3JP, 'mp3j_admin_colours_header') ); 
				$MP3JP->dbug['str'] .= 'colours handle: ' . $subm_colours;
				$MP3JP->menuHANDLES['design'] = $subm_colours;
			}
			//Extension pages
			foreach ( $MP3JP->EXTpages as $p ) {
				$submenu = add_submenu_page( $p['parent'], $p['title'], $p['menuName'], $p['capability'], $p['slug'], $p['drawFunction'] );
				$MP3JP->menuHANDLES[ $p['slug'] ] = $submenu;
				if ( $p['scriptsFunction'] !== false ) {
					add_action( 'admin_head-'. $submenu, $p['scriptsFunction'] );
				}
			}
						
			add_filter( 'plugin_action_links', 'mp3j_plugin_links', 10, 2 );
		}

		function mp3j_plugin_links( $links, $file )
		{ 
			//add a settings link on plugins page 
			if( $file == 'mp3-jplayer/mp3jplayer.php' ) {
				$settings_link = '<a href="admin.php?page=mp3-jplayer">'.__('Settings').'</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}
		
		add_action('deactivate_mp3-jplayer/mp3jplayer.php',  array(&$MP3JP, 'uninitFox'));
		add_action('admin_menu', 'mp3j_adminpage', 100);
		//add_action( 'admin_enqueue_scripts', array(&$MP3JP, 'adminPostEditHead') );
	}
	 
	
	//template functions
	function mp3j_addscripts( $style = "" ) {
		do_action('mp3j_addscripts', $style);
	}

	function mp3j_put( $shortcodes = "" ) {
		do_action( 'mp3j_put', $shortcodes );
	}

	function mp3j_debug( $display = "" ) {
		do_action('mp3j_debug', $display);
	}
	
	function mp3j_grab_library( $format = "" ) { 
		$lib = apply_filters('mp3j_grab_library', '' );
		return $lib;
	}
		
	function mp3j_settings ( $settings = array() ) {
		do_action('mp3j_settings', $settings );
	}
	add_action('mp3j_settings', array(&$MP3JP, 'mp3j_settings'), 1, 1 );
	
	
	//Widgets
	function mp3jplayer_widget_init() {
		register_widget( 'MP3_jPlayer' );
	}
	add_action( 'widgets_init', 'mp3jplayer_widget_init' ); 
	
	function mp3jshortcodes_widget_init() { 
		register_widget( 'MP3j_single' ); //silly name but can't change it now!
	}
	add_action( 'widgets_init', 'mp3jshortcodes_widget_init' );
	
	
	//Shortcodes
	add_shortcode('mp3t', array(&$MP3JP, 'inline_play_handler'));
	add_shortcode('mp3j', array(&$MP3JP, 'inline_play_graphic'));
	add_shortcode('mp3-jplayer', array(&$MP3JP, 'primary_player'));
	
	remove_shortcode('popout');
	add_shortcode('popout', array(&$MP3JP, 'popout_link_player'));
	add_shortcode('mp3-popout', array(&$MP3JP, 'popout_link_player'));
	
	if ( $ops['replace_WP_playlist'] === 'true' && ! is_admin() ) {
		remove_shortcode('playlist');
		add_shortcode('playlist', array(&$MP3JP, 'replacePlaylistShortcode'));
	}
	
	if ( ! is_admin() && ($ops['replace_WP_audio'] === 'true' || $ops['replace_WP_embedded'] === 'true' || $ops['replace_WP_attached'] === 'true') )	{
		remove_shortcode('audio');
		add_shortcode('audio', array(&$MP3JP, 'replaceAudioShortcode'));
	}
	
	
	//Template hooks

	add_action('init', array( &$MP3JP, 'onInit'), 100 );

	/*
	* should use this conditional but it means that as page loads up players initially appear unstyled (aren't styled until
	* last minute) TODO:enqueue style in header when poss.
	* always run both hooks for the mo.
	*/
	//$WPversion = substr( get_bloginfo('version'), 0, 3);
	//if ( $WPversion < 3.3 ) {
		add_action('wp_head', array(&$MP3JP, 'header_scripts_handler'), 2);	//Support for WP versions below 3.3
	//}
	add_action('wp_footer', array(&$MP3JP, 'checkAddScripts'), 1); 		//Final chance to enqueue, process this action early (priority < 20).
	add_action('wp_footer', array(&$MP3JP, 'footercode_handler'), 200); 	//Add any inline js, process this action late (enqueues fire at priority 20).
	
	add_filter('get_the_excerpt', array(&$MP3JP, 'get_excerpt_handler'), 1);
	add_filter('the_content', array(&$MP3JP, 'afterExcerpt'), 9999);
	
	//options
	if ( $ops['make_player_from_link'] == "true" ) {
		add_filter('the_content', array(&$MP3JP, 'replace_links'), 1);
	}
	if ( $ops['run_shcode_in_excerpt'] == "true" ) {
		add_filter( 'the_excerpt', 'shortcode_unautop');
		add_filter( 'the_excerpt', 'do_shortcode');
	}
	
	//theme template actions
	add_action('mp3j_put', array(&$MP3JP, 'template_tag_handler'), 10, 1 );
	add_action('mp3j_addscripts', array(&$MP3JP, 'scripts_tag_handler'), 1, 1 );
	add_filter('mp3j_grab_library', array(&$MP3JP, 'grablibrary_handler'), 10, 1 );
	add_action('mp3j_debug', array(&$MP3JP, 'debug_info'), 10, 1 );
	
	//retired
	function mp3j_set_meta( $tracks, $captions = "", $startnum = 1 ) { } //since 1.7
	function mp3j_flag( $set = 1 ) { } //since 1.6
	function mp3j_div() { } //since 1.8
}
?>