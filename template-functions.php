<?php


//Widgets
function mp3jplayer_widget_init() {
	register_widget( 'MP3_jPlayer' );
}
function mp3jshortcodes_widget_init() { 
	register_widget( 'MP3j_single' );
}




//hooks - settings
function MJPsettings_players() {
	do_action( 'MJPsettings_players' );
}
function MJPsettings_mp3t() {
	do_action( 'MJPsettings_mp3t' );
}
function MJPsettings_mp3j() {
	do_action( 'MJPsettings_mp3j' );
}
function MJPsettings_playlist() {
	do_action( 'MJPsettings_playlist' );
}
function MJPsettings_submit() {
	do_action( 'MJPsettings_submit' );
}

//hooks - design
function MJPdesign_text() {
	do_action( 'MJPdesign_text' );
}
function MJPdesign_areas() {
	do_action( 'MJPdesign_areas' );
}
function MJPdesign_fonts() {
	do_action( 'MJPdesign_fonts' );
}
function MJPdesign_alignments() {
	do_action( 'MJPdesign_alignments' );
}
function MJPdesign_mods() {
	do_action( 'MJPdesign_mods' );
}
function MJPdesign_submit() {
	do_action( 'MJPdesign_submit' );
}
	
//hooks - process
function MJPfront_mp3t( $atts = array() ) {
	return apply_filters( 'MJPfront_mp3t', $atts );
}
function MJPfront_mp3j( $atts = array() ) {
	return apply_filters( 'MJPfront_mp3j', $atts );
}


//hooks - frontend templates
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





//retired
function mp3j_set_meta( $tracks, $captions = "", $startnum = 1 ) { } //since 1.7
function mp3j_flag( $set = 1 ) { } //since 1.6
function mp3j_div() { } //since 1.8




?>