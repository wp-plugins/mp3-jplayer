<?php
/*
Plugin Name: MP3-jPlayer
Plugin URI: http://sjward.org/jplayer-for-wordpress
Description: Adds an mp3 player with a playlist to any Wordpress pages and posts that you have assigned mp3's to. 
Version: 1.3.1
Author: Simon Ward
Author URI: http://www.sjward.org
License: GPL2
*/

/*  Copyright 2010  Simon Ward  (email: sinomward@yahoo.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists("mp3Fox"))
{
	class mp3Fox
	{
		var $playerHasBeenSet = "false";
		var $customFieldsGrabbed = "false";
		
		var $adminOptionsName = "mp3FoxAdminOptions";
		var $theSettings = array();
		
		var $postMetaKeys = array();
		var $postMetaValues = array();
			
   /**
	* 	CHECKS IF THERE'S TRACKS 
	*	(HOOKED to wp_head and runs first)
	*	
	*	Checks only when page is singular at the mo and runs add_scripts if there are tracks. (Currently always adding 
	* 	scripts to posts index page when player is allowed, and then checking for tracks during content hook)
	*	
	* 	@todo: conditional enqueuing for post's index - need a fail-safe way of grabbing the post ids that are gonna be be displayed  
	*/
		function check_if_scripts_needed() {
			
			$this->theSettings = get_option( $this->adminOptionsName );
			if (is_singular()) {
				if ( $this->grab_Custom_Meta() > 0 ) {
					$this->add_Scripts( $this->theSettings['player_theme'] );
				}
			}
			if ( is_home() && $this->theSettings['player_onblog'] == "true" ) {
				$this->add_Scripts( $this->theSettings['player_theme'] );
			}
			return;
		}


   /**
	* 	HANDLES PLAYER ADDITION LOGIC.
	*	(HOOKED to the_content)
	*
	* 	The meta key match is done now (rather than in header) on each loop if the page is the posts index.
	*	
	* 	@todo:	move all key-matching (and array-building) to header once a fail-safe method of pulling the posts that
	*	will be displayed when not in the loop is figured out).  
	*/
		function add_player($content='') {
			
			if ($this->playerHasBeenSet == "true") {
				return $content;
			}
			if ( is_home() && $this->theSettings['player_onblog'] == "true" ) {
				if ( $this->grab_Custom_Meta() > 0 ) {
					$customvalues = $this->postMetaValues;
					$customkeys = $this->postMetaKeys;
				}
				else {
					return $content;
				}
			}
			else if ( is_singular() && $this->customFieldsGrabbed == "true" ) {
				$customvalues = $this->postMetaValues;
				$customkeys = $this->postMetaKeys;
			}
			else {
				return $content;
			}
			
			$theSplitMeta = $this->splitup_meta( $customkeys, $customvalues );
			$theAssembledMeta = $this->compare_swap( $theSplitMeta, $customkeys, $customvalues );
			$theTrackLists = $this->sort_tracks( $theAssembledMeta, $customkeys );
			$thePlayList = $this->remove_mp3remote( $theTrackLists );
			if ( $thePlayList['count'] == 0 ) {
				return $content;
			}
			
			$this->write_startup_vars( $thePlayList['count'] );
			$this->write_playlist( $thePlayList );
			$theplayer = $this->write_player_html( $thePlayList['count'] );
			$content = $theplayer . $content . "<br clear=\"all\" />";
			$this->playerHasBeenSet = "true";
			return $content;
		}


   /**
	* 	GETS RELEVANT META keys/values from the current page/post and
	* 	creates arrays with common indexes.
	*
	* 	Returns number of tracks
	*/
		function grab_Custom_Meta() {
			
			global $wpdb;
			global $post;
			$pagesmeta = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id =" .$post->ID. "  ORDER BY meta_key ASC");
			
			$i = 0;
			$metacount = 0;
			foreach ( $pagesmeta as $obj ) {
				$flag = 0;
				foreach ( $obj as $k => $value ) {
					if ( $k == "meta_key" ){
						if ( preg_match('/^([0-9]+(\s)?)?mp3(\..*)?$/', $value) == 1 ) {
							$this->postMetaKeys[$i] = $value;
							$metacount++;
							$flag = 1;
						}
					}
					if ( $k == "meta_value" ){
						if ( $flag == 1 ) {
							$this->postMetaValues[$i++] = $value;
						}
					}
				}
			}
			if ( $metacount > 0 ) {
				$this->customFieldsGrabbed = "true";
			}
			
			return $metacount;
		}
		

   /**
	* 	GRABS LIBRARY titles/excerpts/uri's of any audio in wp's library
	*
	* 	Return: excerpts, titles, uri's, filenames, count. 
	*/
		function grab_library_info() {		
		
			global $wpdb;
			$audioInLibrary = $wpdb->get_results("SELECT DISTINCT guid, post_title, post_excerpt FROM $wpdb->posts WHERE post_mime_type = 'audio/mpeg'");
			$j=0;
			$Lcount = count($audioInLibrary);
			
			foreach ( $audioInLibrary as $libkey => $libvalue ) {
				foreach ( $libvalue as $itemkey => $itemvalue ) {
					if ( $itemkey == "guid" ) {
						$libraryURLs[$j] = $itemvalue;
						$libraryFilenames[$j] = strrchr( $libraryURLs[$j], "/");
						$libraryFilenames[$j] = str_replace( "/", "", $libraryFilenames[$j]); 
					}
					if ( $itemkey == "post_title" ) {
						$libraryTitles[$j] = $itemvalue;
					}
					if ( $itemkey == "post_excerpt" ) {
						$libraryExcerpts[$j] = $itemvalue;
					}
				}
				$j++;
			}
			$theLibrary = array(	'excerpts' => $libraryExcerpts,
									'titles' => $libraryTitles,
									'urls' => $libraryURLs,
									'filenames' => $libraryFilenames,
									'count' => $Lcount );
			return $theLibrary;
		}
		
	
   /**	
	* 	SPLITS up the custom keys/values into artist/title/file arrays with correlating indices. if there's 
	*	no title then uses the filename, if there's no artist then checks whether to use the previous artist.
	*
	*	@todo: merging this function with compare_swap is prob more efficient
	*
	* 	Return arrays: artists, titles, filenames. 
	*/
		function splitup_meta($customkeys, $customvalues) {		
			
			/* artists */
			$prevArtist = "";
			foreach ( $customkeys as $i => $ckvalue ) {
				$splitkey = explode('.', $ckvalue, 2);
				if ( $splitkey[1] == "" ) {
					if ( preg_match('/^([0-9]+(\s)?)?mp3\.$/', $ckvalue) == 1 ) {
						$customArtists[$i] = "";
					}
					else {
						$customArtists[$i] = $prevArtist;
					}
				}
				else {
					$customArtists[$i] = $splitkey[1];
				}
				$prevArtist = $customArtists[$i];
			}
				
			/* titles & filenames */
			foreach ( $customvalues as $i => $cvvalue ) {	
			
				$checkfortitle = strpos($cvvalue, '@');
				if ( $checkfortitle === false ) {
					$customTitles[$i] = str_replace(".mp3", "", $cvvalue);
					$customFilenames[$i] = $cvvalue;
					if ( $this->theSettings['hide_mp3extension'] == "false" ) {
						$customTitles[$i] .= ".mp3";
					}
				}
				else {
					$reversevalue = strrev($cvvalue);
					$splitvalue = explode('@', $reversevalue, 2);
					$customTitles[$i] = strrev($splitvalue[1]);
					$customFilenames[$i] = strrev($splitvalue[0]);
				}
				if ( preg_match('/\.mp3$/', $customFilenames[$i]) == 0 ) {
					$customFilenames[$i] .= ".mp3";
				}
				if ( strpos($customFilenames[$i], "www.") !== false ) {
					$customFilenames[$i] = str_replace("www.", "", $customFilenames[$i]);
					if ( strpos($customFilenames[$i], "http://") === false ) {
						$customFilenames[$i] = "http://" .$customFilenames[$i];
					}
				}
			}
			
			$theSplitMeta = array(	'artists' => $customArtists, 
									'titles' => $customTitles,
									'files' => $customFilenames );
			return $theSplitMeta;
		}
	
			
   /**	
	*	LOOKS FOR any $customFilenames that exist in the library and grabs their full uri's, otherwise 
	*	adds the default path or makes sure has an http if uri/remote. Then if needed cleans up titles or swaps 
	*	titles or artists for the library ones. Returns sanitized arrays ready for playlist 
	*
	*	Return: artists, titles, urls.
	*/
		function compare_swap($theSplitMeta, $customkeys, $customvalues) {
						
			$library = $this->grab_library_info();
			
			foreach ( $theSplitMeta['files'] as $i => $cfvalue ) {
				if ( $library['count'] == 0 ) {
					$inLibraryID = false;
				}
				else {
					$inLibraryID = array_search( $cfvalue, $library['filenames'] );
				}
				$mp3haswww = strpos($cfvalue, 'http://');
				
				/* if file is local but not in library */
				if ( $mp3haswww === false && $inLibraryID === false ) { 
					if ( $this->theSettings['mp3_dir'] == "/" ) {
						$theSplitMeta['files'][$i] = $this->theSettings['mp3_dir'] . $theSplitMeta['files'][$i];
					}
					else {
						$theSplitMeta['files'][$i] = $this->theSettings['mp3_dir']. "/" . $theSplitMeta['files'][$i];
					}
				}
				
				/* if file is in library */
				if ( $inLibraryID !== false ) { 
					$theSplitMeta['files'][$i] = $library['urls'][$inLibraryID];
					if ( $this->theSettings['playlist_UseLibrary'] == "true" ) {
						$theSplitMeta['titles'][$i] = $library['titles'][$inLibraryID];
						$theSplitMeta['artists'][$i] = $library['excerpts'][$inLibraryID];
					}
					else {
						if ( preg_match('/^([0-9]+(\s)?)?mp3$/', $customkeys[$i]) == 1 ) {
							$theSplitMeta['artists'][$i] = $library['excerpts'][$inLibraryID];
						}
						if ( preg_match('/^([0-9]+(\s)?)?mp3\.$/', $customkeys[$i]) == 1 ) {
							$theSplitMeta['artists'][$i] = "";
						}
						if ( strpos($customvalues[$i], '@') === false ) {
							$theSplitMeta['titles'][$i] = $library['titles'][$inLibraryID];
						}
					}
				}
				
				/* if file is remote or user is over-riding the default path */
				if ( $mp3haswww !== false && $inLibraryID === false ) { 
					if ( strpos($theSplitMeta['titles'][$i], 'http://') !== false || strpos($theSplitMeta['titles'][$i], 'www.') !== false ) {
						$theSplitMeta['titles'][$i] = strrchr($theSplitMeta['titles'][$i], "/");
						$theSplitMeta['titles'][$i] = str_replace( "/", "", $theSplitMeta['titles'][$i]);
					}
				}
			}
			
			$theAssembledMeta = array(	'artists' => $theSplitMeta['artists'], 
										'titles' => $theSplitMeta['titles'],
										'files' => $theSplitMeta['files'] );
			return $theAssembledMeta;
			
		}
		
		
   /**	
	*	SORTS either the titles(if a-z ticked) or the keys (only if there's
	*	any numbering in them) and makes an ordering array
	*
	*	Return: artists, titles, files, order.
	*/
		function sort_tracks($theAssembledMeta, $customkeys) {		
			
			$x = 0;
			if ( $this->theSettings['playlist_AtoZ'] == "true" ) {
				natcasesort($theAssembledMeta['titles']);
				foreach ($theAssembledMeta['titles'] as $kt => $vt) {
					$indexorder[$x++] = $kt;
				} 
			}
			else {
				$numberingexists = 0;
				foreach ( $customkeys as $ki => $val ) {
					if ( preg_match('/^[0-9]/', $val) ) {
						$numberingexists++;
						break;
					}
				}
				if ( $numberingexists > 0 ) {
					natcasesort($customkeys);
					foreach ( $customkeys as $kf => $vf ) {
						$indexorder[$x++] = $kf;
					}
				}
				else {
					foreach ( $theAssembledMeta['titles'] as $kt => $vt ) {
						$indexorder[$x++] = $kt;
					}
				} 
			}
			
			$theTrackLists = array(	'artists' => $theAssembledMeta['artists'], 
									'titles' => $theAssembledMeta['titles'],
									'files' => $theAssembledMeta['files'],
									'order' => $indexorder );
			return $theTrackLists;
		}
			
	
	/**
	*	REMOVES any REMOTE tracks from the playlist arrays if allow_remoteMp3 is unticked. 
	*	current logic requires this filter be run after filenames have been sanitized/replaced by compare_swap() which
	*	is not ideal.
	*
	*	return: artists, titles, filenames, order, count
	*
	*	@todo: re-write function to filter pre compare_swap() 'cos if no tracks left then don't want to have searched
	*	library and enqueued stuff etc. 
	*/
		function remove_mp3remote( $theTrackLists ) {	
	
			if ( $this->theSettings['allow_remoteMp3'] == "false" ) {
				$localurl = get_bloginfo('url');
				foreach ( $theTrackLists['order'] as $ik => $i ) {
					if ( strpos($theTrackLists['files'][$i], $localurl) !== false || strpos($theTrackLists['files'][$i], "http://") === false || (strpos($this->theSettings['mp3_dir'], "http://") !== false && strpos($theTrackLists['files'][$i], $this->theSettings['mp3_dir']) !== false) ) {
						$playlistFilenames[$i] = $theTrackLists['files'][$i];
						$playlistTitles[$i] = $theTrackLists['titles'][$i];
						$playlistArtists[$i] = $theTrackLists['artists'][$i];
						$indexorderAllowed[$x++] = $i;
					}
				}
			}
			else {
				$playlistFilenames = $theTrackLists['files'];
				$playlistTitles = $theTrackLists['titles'];
				$playlistArtists = $theTrackLists['artists'];
				$indexorderAllowed = $theTrackLists['order'];
			}
			$playlistTitles = str_replace('"', '\"', $playlistTitles); // escapes quotes for the js array
			$nAllowed = count($playlistFilenames);
			
			$thePlayList = array(	'artists' => $playlistArtists, 
									'titles' => $playlistTitles,
									'files' => $playlistFilenames,
									'order' => $indexorderAllowed,
									'count' => $nAllowed );
			return $thePlayList;
		}
	
   
   /**
	* 	ENQUEUES the js and css scripts.
	*/
		function add_Scripts($theme='') {
			
			wp_enqueue_script( 'jquery', '/wp-content/plugins/mp3-jplayer/js/jquery.js' );
			wp_enqueue_script( 'ui.core', '/wp-content/plugins/mp3-jplayer/js/ui.core.js', array( 'jquery' ) );
			wp_enqueue_script( 'ui.progressbar.min', '/wp-content/plugins/mp3-jplayer/js/ui.progressbar.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'ui.slider.min', '/wp-content/plugins/mp3-jplayer/js/ui.slider.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'jquery.jplayer.min', '/wp-content/plugins/mp3-jplayer/js/jquery.jplayer.min.js', array( 'jquery' ) );	
			wp_enqueue_script( 'mp3-jplayer', '/wp-content/plugins/mp3-jplayer/js/mp3-jplayer.js', array( 'jquery' ) ); 
			
			if ( $theme == "styleB" ) {
				wp_enqueue_style( 'mp3jplayer-green', '/wp-content/plugins/mp3-jplayer/css/mp3jplayer-green.css' );
			}
			else if ( $theme == "styleC" ) {
				wp_enqueue_style( 'mp3jplayer-blu', '/wp-content/plugins/mp3-jplayer/css/mp3jplayer-blu.css' );
			}
			else if ( $theme == "styleD" ) {
				wp_enqueue_style( 'mp3-jplayer-cyanALT', '/wp-content/plugins/mp3-jplayer/css/mp3-jplayer-cyanALT.css' );
			}
			else { // default
				wp_enqueue_style( 'mp3jplayer-grey', '/wp-content/plugins/mp3-jplayer/css/mp3jplayer-grey.css' );
			}
			return;
		}
	
	
   /**
	*	WRITES player START-UP JS vars  
	*/
		function write_startup_vars( $count ) {
			
			$wpinstallpath = get_bloginfo('wpurl');
			echo "\n\n<script type=\"text/javascript\">\n<!--\n";
			echo "var foxpathtoswf = \"" .$wpinstallpath. "/wp-content/plugins/mp3-jplayer/js\";\n";
			echo "var foxAutoPlay =" . $this->theSettings['auto_play'] . ";\n";
			echo "var foxInitialVolume =" . $this->theSettings['initial_vol'] . ";\n";
			echo "var foxpathtoimages = \"" .$wpinstallpath. "/wp-content/plugins/mp3-jplayer/css/images/\";\n";
			if ( $count < 2 ) {
				echo "var foxShowPlaylist = \"false\";\n";
			}
			else {
				echo "var foxShowPlaylist = \"" .$this->theSettings['playlist_show']. "\";\n";
			}
			echo "//-->\n</script>\n\n";
			return;
		}
   	
	   	
   /**
	* 	WRITES the FINAL PLAYLIST array.
	*/
		function write_playlist( $thePlayList ) {
			
			echo "\n\n<script type=\"text/javascript\">\n<!--\n";
			echo "var foxPlayList = [\n";
			$tracknumber = 1;
			foreach ( $thePlayList['order'] as $ik => $i ) {
				echo "{name: \"" .$tracknumber. ". " .$thePlayList['titles'][$i]. "\", mp3: \"" .$thePlayList['files'][$i]. "\", artist: \"" .$thePlayList['artists'][$i]. "\"}";
				if ( $tracknumber != $thePlayList['count'] ) {
					echo ",";
				}
				echo "\n";
				$tracknumber++;
			}
			echo "];\n";
			echo "//-->\n</script>\n\n";
			return;
		}
   

   /**
	* 	WRITES PLAYER HTML
	*/
		function write_player_html( $count ) {			
			
			if ( $this->theSettings['player_float'] == "left" ) {
				$floater = "float: left; padding: 5px 50px 50px 0px;";
			}
			else if ( $this->theSettings['player_float'] == "right" ) {
				$floater = "float: right; padding: 5px 0px 50px 50px;";
			}
			else {
				$floater = "position: relative; padding: 5px 0px 50px 0px;";
			}
			if ( $this->theSettings['show_downloadmp3'] == "false" ) {
				$showMp3Link = "visibility: hidden;"; 
			}
			else {
				$showMp3Link = "visibility: visible;";
			}
			if ( $count < 2 ) {
				$showlistcontrols = "visibility: hidden;";
			}
			else {
				$showlistcontrols = "visibility: visible;";
			}
			
			$player = "<div id=\"jquery_jplayer\"></div>
			<div class=\"jp-playlist-player\" style=\"" .$floater. "\">
				<div class=\"jp-innerwrap\">
					<div id=\"innerx\"></div>
					<div id=\"innerleft\"></div>
					<div id=\"innerright\"></div>
					<div id=\"innertab\"></div>\n
					<div class=\"jp-interface\">
						<ul class=\"jp-controls\">
							<li><a href=\"#\" id=\"jplayer_play\" class=\"jp-play\" tabindex=\"1\">play</a></li>
							<li><a href=\"#\" id=\"jplayer_pause\" class=\"jp-pause\" tabindex=\"1\">pause</a></li>
							<li><a href=\"#\" id=\"jplayer_stop\" class=\"jp-stop\" tabindex=\"1\">stop</a></li>
							<li><a href=\"#\" id=\"jplayer_previous\" class=\"jp-previous\" tabindex=\"1\" style=\"" .$showlistcontrols. "\">previous</a></li>
							<li><a href=\"#\" id=\"jplayer_next\" class=\"jp-next\" tabindex=\"1\" style=\"" .$showlistcontrols. "\">next</a></li>
						</ul>
						<div id=\"sliderVolume\"></div>
						<div id=\"bars_holder\">
							<div id=\"loaderBar\"></div>
							<div id=\"sliderPlayback\"></div>
						</div>
						<div id=\"jplayer_play_time\" class=\"jp-play-time\"></div>
						<div id=\"jplayer_total_time\" class=\"jp-total-time\"></div>
						<div id=\"status\"></div>
						<div id=\"player-track-title\"></div>
						<div id=\"player-artist\"></div>
						<div id=\"downloadmp3-button\" style=\"" .$showMp3Link. "\"></div>
						<div id=\"playlist-toggle\" style=\"" .$showlistcontrols. "\" onclick=\"javascript:toggleplaylist();\">HIDE PLAYLIST</div>
					</div>
				</div>\n
				<div id=\"playlist-wrap\">	
					<div id=\"jplayer_playlist\" class=\"jp-playlist\"><ul><li></li></ul></div>
				</div>
			</div>\n";
			
			return $player;
		}

   
   /**
	*	called when PLUGIN is ACTIVATED to create options if none exist.
	*/
		function initFox() { 
			
			$this->getAdminOptions();
		}
		
			
   /**
	*	called when PLUGIN DEactivated, keeps the admin settings if option was ticked.
	*/
		function uninitFox() { 
			
			$theOptions = get_option($this->adminOptionsName);
			if ( $theOptions['remember_settings'] == "false" ) {
				delete_option($this->adminOptionsName);
			}
		}
			
	
   /**
	*	RETURNS the ADMIN settings, 
	*	or creates and returns defaults if they don't exist.
	*/
		function getAdminOptions() {
			
			$mp3FoxAdminOptions = array( // default settings
							'initial_vol' => '100',
							'auto_play' => 'true',
							'mp3_dir' => 'http://www.sjward.org/mp3',
							'player_theme' => 'styleA',
							'allow_remoteMp3' => 'true',
							'playlist_AtoZ' => 'false',
							'player_float' => 'none',
							'player_onblog' => 'true',
							'playlist_UseLibrary' => 'false',
							'playlist_show' => 'true',
							'remember_settings' => 'false',
							'hide_mp3extension' => 'false',
							'show_downloadmp3' => 'false' );
			
			$theOptions = get_option($this->adminOptionsName);
			if ( !empty($theOptions) ) {
				foreach ( $theOptions as $key => $option ){
					$mp3FoxAdminOptions[$key] = $option;
				}
			}
			update_option($this->adminOptionsName, $mp3FoxAdminOptions);
			return $mp3FoxAdminOptions;
		}
		
			
   /**
	* 	UPDATES and DISPLAYS ADMIN settings on the settings page.
	*
	*/
		function printAdminPage() { 
			
			$theOptions = $this->getAdminOptions();
			if (isset($_POST['update_mp3foxSettings']))
			{
				if (isset($_POST['mp3foxAutoplay'])) {
					$theOptions['auto_play'] = $_POST['mp3foxAutoplay'];
				} 
				else { 
					$theOptions['auto_play'] = "false";
				}
				if (isset($_POST['mp3foxVol'])) {
					$theOptions['initial_vol'] = preg_replace("/[^0-9]/","", $_POST['mp3foxVol']); 
					if ($theOptions['initial_vol'] < 0 || $theOptions['initial_vol']=="") {
						$theOptions['initial_vol'] = "0";
					}
					if ($theOptions['initial_vol'] > 100) {
						$theOptions['initial_vol'] = "100";
					}
				}
				if (isset($_POST['mp3foxfolder'])) {
					$theOptions['mp3_dir'] = preg_replace("!^.*www*\.!", "http://www.", $_POST['mp3foxfolder']);
					if (strpos($theOptions['mp3_dir'], "http://") === false) {
						if (preg_match("!^/!", $theOptions['mp3_dir']) == 0) {
							$theOptions['mp3_dir'] = "/" .$theOptions['mp3_dir'];
						} 
						else {
							$theOptions['mp3_dir'] = preg_replace("!^/+!", "/", $theOptions['mp3_dir']);
						} 
						if (preg_match("!.+/+$!", $theOptions['mp3_dir']) == 1) {
							$theOptions['mp3_dir'] = preg_replace("!/+$!", "", $theOptions['mp3_dir']);
						}
					}	
					if ($theOptions['mp3_dir'] == "") {
						$theOptions['mp3_dir'] = "/";
					}
				}
				if (isset($_POST['mp3foxTheme'])) {
						$theOptions['player_theme'] = $_POST['mp3foxTheme'];
				}
				if (isset($_POST['mp3foxAllowRemote'])) {
					$theOptions['allow_remoteMp3'] = $_POST['mp3foxAllowRemote'];
				}
				else { 
					$theOptions['allow_remoteMp3'] = "false";
				}
				if (isset($_POST['mp3foxAtoZ'])) {
					$theOptions['playlist_AtoZ'] = $_POST['mp3foxAtoZ'];
				}
				else { 
					$theOptions['playlist_AtoZ'] = "false";
				}
				if (isset($_POST['mp3foxFloat'])) {
						$theOptions['player_float'] = $_POST['mp3foxFloat'];
				}
				if (isset($_POST['mp3foxOnBlog'])) {
					$theOptions['player_onblog'] = $_POST['mp3foxOnBlog'];
				} 
				else { 
					$theOptions['player_onblog'] = "false";
				}
				if (isset($_POST['mp3foxUseLibrary'])) {
					$theOptions['playlist_UseLibrary'] = $_POST['mp3foxUseLibrary'];
				}
				else { 
					$theOptions['playlist_UseLibrary'] = "false";
				}
				if (isset($_POST['mp3foxShowPlaylist'])) {
					$theOptions['playlist_show'] = $_POST['mp3foxShowPlaylist'];
				}
				else { 
					$theOptions['playlist_show'] = "false";
				}
				if (isset($_POST['mp3foxRemember'])) {
					$theOptions['remember_settings'] = $_POST['mp3foxRemember'];
				}
				else { 
					$theOptions['remember_settings'] = "false";
				}
				if (isset($_POST['mp3foxHideExtension'])) {
					$theOptions['hide_mp3extension'] = $_POST['mp3foxHideExtension'];
				}
				else { 
					$theOptions['hide_mp3extension'] = "false";
				}
				if (isset($_POST['mp3foxDownloadMp3'])) {
					$theOptions['show_downloadmp3'] = $_POST['mp3foxDownloadMp3'];
				}
				else { 
					$theOptions['show_downloadmp3'] = "false";
				}
				
				update_option($this->adminOptionsName, $theOptions);
				?>

				<div class="updated"><p><strong><?php _e("Settings Updated.", "mp3Fox");?></strong></p></div>
			
			<?php 
			} 
			?>
			
			<div class="wrap">
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					
					<h2>Mp3-jPlayer</h2>
					<p class="description" style="margin-top: -4px; margin-bottom: 18px;"><a href="#howto">How to make a playlist</a></p></h2>
					
					<p class="description" style="margin: 5px 120px 30px 0px;">Below are the global settings for Mp3-jPlayer. The player 
						will automatically appear on any posts or pages that you have assigned mp3's to.</p>
					
					<h4 style="margin-bottom: 4px;">Player</h4>
					<p style="margin-bottom: 6px;">&nbsp; Initial volume &nbsp; <input type="text" style="text-align:right;" size="2" name="mp3foxVol" value="<?php echo $theOptions['initial_vol'] ?>" /> &nbsp; <span class="description">(0 - 100)</span></p>
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAutoplay" value="true" <?php if ($theOptions['auto_play'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Autoplay</p>
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxShowPlaylist" value="true" <?php if ($theOptions['playlist_show'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Start with the playlist showing</p>
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxOnBlog" value="true" <?php if ($theOptions['player_onblog'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Show a player on the posts index
						page<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(the player is added to the most recent post that has mp3's assigned)</span></p>
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAtoZ" value="true" <?php if ($theOptions['playlist_AtoZ'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Playlist the tracks in alphabetical order</p>
					
					<h4 style="margin-bottom: 4px;"><br />Library</h4>
					<p style="margin-bottom: 5px;">&nbsp; <input type="checkbox" name="mp3foxUseLibrary" value="true" <?php if ($theOptions['playlist_UseLibrary'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Always use my Media Library titles and excerpts when they exist</p>
					<p class="description" style="margin: 0px 0px 9px 35px;"><a href="media-new.php">Upload new tracks</a>
					<br /><a href="upload.php?post_mime_type=audio">Go to media library</a></p>
					
			<?php	
			//media settings page has moved in WP 3
			if ( substr(get_bloginfo('version'), 0, 1) > 2 ) { // if WP 3.x
				$mediapagelink = get_bloginfo('wpurl') . "/wp-admin/options-media.php"; 
			}
			else {
				$mediapagelink = get_bloginfo('wpurl') . "/wp-admin/options-misc.php";
			}
			
			$upload_dir = wp_upload_dir();
			$localurl = get_bloginfo('url');
			$uploadsfolder = str_replace($localurl, "", $upload_dir['baseurl']); // is empty string only if library is empty
			if ( $uploadsfolder != "" ) { 
				echo "<p class=\"description\" style=\"margin: 0px 120px 20px 35px;\">Your Media Library uploads folder is currently set to <code>" .$uploadsfolder. "</code> , you can always <a href=\"" . $mediapagelink . "\">change it</a> without affecting any of your playlists.</p>";
			}
			?>
					
					<p class="description" style="margin: 0px 120px 5px 10px;">If you have mp3's that don't appear in the Media Library (eg.
						if you use ftp outside of Wordpress, or want to play music from another domain) then you can specify a default path or URI to the folder that contains them. Doing this means you only need
						write their filenames when making a playlist. You can over-ride your default path / URI anytime on a playlist by specifying the full URI for an mp3.</p> 
					<p>&nbsp; Default path or URI &nbsp; <input type="text" size="55" name="mp3foxfolder" value="<?php echo $theOptions['mp3_dir'] ?>" /></p>
					
					<p style="margin-top: 15px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAllowRemote" value="true" <?php if ($theOptions['allow_remoteMp3'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Allow mp3's from other domains on
						the player's playlists<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(unchecking this option doesn't affect mp3's using your default path or URI)</span></p>
					
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxHideExtension" value="true" <?php if ($theOptions['hide_mp3extension'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Hide .mp3 if a filename is displayed
						<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(filenames are displayed when there's no available titles)</span></p>
					
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxDownloadMp3" value="true" <?php if ($theOptions['show_downloadmp3'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Display a 'Download mp3' button.</p>
					
					<h4 style="margin-bottom: 4px;"><br />Style</h4>
					<p>&nbsp; <input type="radio" name="mp3foxTheme" value="styleA" <?php if ($theOptions['player_theme'] == "styleA") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;&nbsp;Neutral<br />
							&nbsp; <input type="radio" name="mp3foxTheme" value="styleB" <?php if ($theOptions['player_theme'] == "styleB") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;&nbsp;Green<br />
							&nbsp; <input type="radio" name="mp3foxTheme" value="styleC" <?php if ($theOptions['player_theme'] == "styleC") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;&nbsp;Blue<br />
							&nbsp; <input type="radio" name="mp3foxTheme" value="styleD" <?php if ($theOptions['player_theme'] == "styleD") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;&nbsp;Cyan (Alternative style)</p>
					
					<h4 style="margin-bottom: 4px;"><br />Position</h4>
					<p>&nbsp; Left &nbsp;<input type="radio" name="mp3foxFloat" value="left" <?php if ($theOptions['player_float'] == "left") { _e('checked="checked"', "mp3Fox"); }?> />&nbsp; 
							| &nbsp;<input type="radio" name="mp3foxFloat" value="right" <?php if ($theOptions['player_float'] == "right") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Right</p>
					<p>&nbsp; Left &nbsp;<input type="radio" name="mp3foxFloat" value="none" <?php if ($theOptions['player_float'] == "none") { _e('checked="checked"', "mp3Fox"); }?> />&nbsp; but not floated <span class="description">(content appears below the player)</span></p>
					<br /><br />
					
					<p style="margin-top: 4px;"><input type="submit" name="update_mp3foxSettings" class="button-primary" value="<?php _e('Update Settings', 'mp3Fox') ?>" />
					 &nbsp; Remember settings if plugin is deactivated &nbsp;<input type="checkbox" name="mp3foxRemember" value="true" <?php if ($theOptions['remember_settings'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /></p>
				</form>
				
				<p>__________________________________________________________________________________</p>
				<a name="howto"></a>
				<p class="description" style="margin: 20px 120px 5px 10px;"><strong>How to assign an mp3</strong> from your Media Library or your default location:</p>
				<p class="description" style="margin: 10px 120px 5px 10px;">1. Go to a page/post edit screen and scroll down to the custom fields (below the content 
						box)<br />2. Write <code>mp3</code> into the left hand box<br />3. Write the filename into the right hand box and hit update page</p>
				
				<p class="description" style="margin: 20px 120px 5px 10px;"><strong>Adding a title and caption:</strong></p>
				<p class="description" style="margin: 10px 120px 5px 10px;">1. Add a dot, then a caption in the left hand box like so: <code>mp3.Caption</code><br />2. Add the title, then an '@' before the filename like so: <code>Title@filename</code></p>
				
				<p class="description" style="margin: 20px 120px 5px 10px;"><strong>Ordering the tracks:</strong></p>
				<p class="description" style="margin: 10px 120px 5px 10px;">Number the left boxes eg:<code>1 mp3.Caption</code> will be first on the playlist. Un-numbered tracks appear
						below any numbered tracks.</p>
				
				<p class="description" style="margin: 10px 120px 5px 10px;"><br />More help and examples are available from the <a href="http://sjward.org/jplayer-for-wordpress">plugin home page</a></p>  
				<br /><br /><br /><br /><br />
			</div>
		
		<?php
		}
			
	} /* end class mp3Fox */
}

if ( class_exists("mp3Fox") ) {
	$mp3_fox = new mp3Fox();
}
if ( isset($mp3_fox) ) {
	
	/* initialize admin page */
	if ( !function_exists("mp3Fox_ap") ) {
		function mp3Fox_ap() {
			global $mp3_fox;
			if ( !isset($mp3_fox) ) {
				return;
			}
			if ( function_exists('add_options_page') ) {
				add_options_page('MP3 jPlayer', 'MP3 jPlayer', 9, basename(__FILE__), array(&$mp3_fox, 'printAdminPage'));
			}
		}
	}
		
	/* action hooks */
	add_action('activate_mp3-jplayer/mp3jplayer.php',  array(&$mp3_fox, 'initFox'));
	add_action('deactivate_mp3-jplayer/mp3jplayer.php',  array(&$mp3_fox, 'uninitFox'));
	add_action('wp_head', array(&$mp3_fox, 'check_if_scripts_needed'), 1);
	add_action('admin_menu', 'mp3Fox_ap');
		
	/* filter hooks */
	add_filter('the_content', array(&$mp3_fox, 'add_player'));
	
}
?>