<?php
/* 
Plugin Name: MP3-jPlayer
Plugin URI: http://sjward.org/jplayer-for-wordpress
Description: Adds an mp3 player with a playlist to any Wordpress pages and posts that you have assigned mp3's to. 
Version: 1.3.4
Author: Simon Ward
Author URI: http://www.sjward.org
License: GPL2
*/

/*  
	Copyright 2010  Simon Ward  (email: sinomward@yahoo.co.uk)

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
		// -------------------------------
		var $version_of_plugin = "1.3.4"; 
		var $option_count = 16;
		var $iscompat = false;
		// -------------------------------
		
		var $playerHasBeenSet = "false";
		var $customFieldsGrabbed = "false";
		var $countPlaylist = 0;
		var $tagflag = "false";
		var $scriptsflag = "false";
		var $postMetaKeys = array();
		var $postMetaValues = array();
		var $feedKeys = array();
		var $feedValues = array();
		var $stylesheet = "";
		var $mp3LibraryWP = array();
		var $mp3LibraryI = array();
		var $PlayerPlaylist = array();
		var $idfirstFound;
		
		var $adminOptionsName = "mp3FoxAdminOptions";
		var $theSettings = array();
		
		// debug
		var $playerSetMethod = "*not attempted*";
		var $putTag_runCount = 0;
		var $shortcode_runCount = 0;
		var $defaultAdd_runCount = 0;
		var $playerAddedOnRun = 0;
		var $debugCount = "0";
		var $scriptsForced = "false";
	
			
   /**
	* 	Handles SCRIPT ADDITION. If page is the posts index then always adding scripts (and then checking for tracks
	*	during content hook), if singular then only adding them when either they've been flagged, or when there's mp3s in this id's
	*	custom meta.
	*	
	*	(called via wp_head)
	*/
		function check_if_scripts_needed() {
			
			$this->make_compatible();
			if ( $this->scriptsflag == "true" && $this->theSettings['disable_template_tag'] == "false" ) {
				if ( $this->stylesheet == "" ) {
					$this->stylesheet = $this->theSettings['player_theme'];
				}
				$this->add_Scripts( $this->stylesheet );
				if (is_singular() ) {
					$this->TT_grab_Custom_Meta();
				}
				$this->scriptsForced = "true";
			}
			else {
				if ( is_singular() ) {
					if ( $this->TT_grab_Custom_Meta() > 0  ) {
						$this->add_Scripts( $this->theSettings['player_theme'] );
					}
				}
				if ( is_home() && $this->theSettings['player_onblog'] == "true" ) {
					$this->add_Scripts( $this->theSettings['player_theme'] );
				}
			}
			return;
		}
		
			
   /**
	* 	Handles DEFAULT player addition via CONTENT.
	*	(Called via the_content hook)
	*
	* 	The meta key match is done now (rather than in the head) on each loop if the page is the posts index.
	*/
		function add_player($content='') {
			
			$this->defaultAdd_runCount++;
			if ($this->playerHasBeenSet == "true") {
				return $content;
			}
			if ( $this->tagflag == "true" && $this->theSettings['disable_template_tag'] == "false" ) {
				if ( empty($this->idfirstFound) ) {
					if ( $this->TT_grab_Custom_Meta() > 0 ) {
						$customvalues = $this->postMetaValues;
						$customkeys = $this->postMetaKeys;
						global $post; 
						$this->idfirstFound = $post->ID;
					}
				}
				return $content;
			}
			
			if ( is_home() && $this->theSettings['player_onblog'] == "true" ) {
				if ( $this->TT_grab_Custom_Meta() > 0 ) {
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
			$this->countPlaylist = $thePlayList['count'];
			$this->PlayerPlaylist = $thePlayList;			
			
			if ( strpos($content, "[mp3-jplayer") !== false ) {
				return $content;
			}
			
			$this->write_startup_vars( $thePlayList['count'], $this->theSettings['auto_play'], $this->theSettings['playlist_show'] );
			$this->write_playlist( $thePlayList );
			
			$theplayer = $this->write_player_html( $thePlayList['count'], $this->theSettings['player_float'], $this->theSettings['show_downloadmp3'] );
			$content = $theplayer . $content . "<br clear=\"all\" />";
			$this->playerHasBeenSet = "true";
			$this->playerSetMethod = "content (default)";
			$this->playerAddedOnRun = $this->defaultAdd_runCount;
			return $content;
		}


   /**
	*	Handles player addition via SHORTCODE. 
	*	The attributes overide the settings page values.
	*	(Called via [mp3-jplayer] shortcode)
	*/
		function shortcode_handler($atts, $content = null) {
			
			$this->shortcode_runCount++;
			if ( $this->tagflag == "true" && $this->theSettings['disable_template_tag'] == "false" ) {
				return;
			} 
			if ($this->playerHasBeenSet == "true") {
				return;
			}
			if ($this->customFieldsGrabbed == "false") {
				return;
			}
			
			extract(shortcode_atts(array(
				'pos' => $this->theSettings['player_float'],
				'dload' => $this->theSettings['show_downloadmp3'],
				'play' => $this->theSettings['auto_play'],
				'list' => $this->theSettings['playlist_show']
			), $atts));
			
			$this->write_startup_vars( $this->PlayerPlaylist['count'], $play, $list );
			$this->write_playlist( $this->PlayerPlaylist );
				
			$theplayer = $this->write_player_html( $this->countPlaylist, $pos, $dload );
			$this->playerHasBeenSet = "true";
			$this->playerSetMethod = "shortcode";
			$this->playerAddedOnRun = $this->shortcode_runCount;
			return $theplayer;			
		}
	
	
   /**
	*	Handles player addition via mp3j_put TAG.
	*/
		function template_tag_handler( $id = "", $pos = "", $dload = "", $play = "", $list = "" ) {
			
			$this->putTag_runCount++;
			if ( $this->playerHasBeenSet == "true" ) {
				return;
			}
			if ( $this->theSettings['disable_template_tag'] == "true" ) {
				return;
			}
			if ( $this->tagflag == "false" ) {
				return;
			} 
			if ( ((is_home() || is_archive()) && $this->theSettings['player_onblog'] == "true") || is_singular() ) {
				if ( $id == "first" && !empty($this->idfirstFound) ) {
					$id = $this->idfirstFound;
				}
				
				if ( $this->TT_grab_Custom_Meta($id) > 0 && $id != "feed" ) {
					$customvalues = $this->postMetaValues;
					$customkeys = $this->postMetaKeys;
				}
				else if ( $id == "feed" ) {
					$customvalues = $this->feedValues;
					$customkeys = $this->feedKeys;
				}
				else {
					return;
				}
			}
			else {
				return;
			}
			
			$theSplitMeta = $this->splitup_meta( $customkeys, $customvalues );
			$theAssembledMeta = $this->compare_swap( $theSplitMeta, $customkeys, $customvalues );
			$theTrackLists = $this->sort_tracks( $theAssembledMeta, $customkeys );
			$thePlayList = $this->remove_mp3remote( $theTrackLists );
			if ( $thePlayList['count'] == 0 ) {
				return;
			}
			$this->countPlaylist = $thePlayList['count'];
			$this->PlayerPlaylist = $thePlayList;
			
			if ( $pos == "" ) {
				$pos = $this->theSettings['player_float'];
			}
			if ( $dload == "" ) {
				$dload = $this->theSettings['show_downloadmp3'];
			}
			if ( $play == "" ) {
				$play = $this->theSettings['auto_play'];
			}
			if ( $list == "" ) {
				$list = $this->theSettings['playlist_show'];
			}
			
			$this->write_startup_vars( $thePlayList['count'], $play, $list );
			$this->write_playlist( $thePlayList );
			
			$theplayer = $this->write_player_html( $thePlayList['count'], $pos, $dload );
			$this->playerHasBeenSet = "true";
			$this->playerSetMethod = "mp3j_put";
			$this->playerAddedOnRun = $this->putTag_runCount;
			echo $theplayer;
			return;			
		}
	

   /**
	*	FLAGS for an UPCOMING mp3j_put TAG.
	*	Called via mp3j_flag.
	*/	
		function flag_tag_handler($set = 1) {
			if ( $set == 0 ) {
				$this->tagflag = "false";
			}
			if ( $set == 1 ) {
				$this->tagflag = "true";
			}
			return;
		}
	
	
   /**
	*	FLAGS for SCRIPTS to be added.
	*	Called via mp3j_addscripts.
	*/	
		function scripts_tag_handler( $style = "" ) {
			
			$this->scriptsflag = "true";
			if ( $style == "" ) {
				$this->theSettings = get_option($this->adminOptionsName);
				$this->stylesheet = $this->theSettings['player_theme'];
			}
			else {
				$this->stylesheet = $style;
			}
			return;
		}
		
	
   /**
	*	Returns Mp3 LIBRARY in INDEXED arrays.
	*	Called via mp3j_grab_library.
	*/			
		function grablibrary_handler( $thereturn ) {
			
			if ( empty($this->mp3LibraryI) ) {
				$this->grab_library_info();
			}
			$thereturn = $this->mp3LibraryI;			
			return $thereturn;
		}


   /**
	*	Returns Mp3 LIBRARY as returned from the SELECT query.
	*	Called via mp3j_grab_library.
	*/			
		function grablibraryWP_handler( $thereturn ) {
			
			if ( empty($this->mp3LibraryWP) ) {
				$this->grab_library_info();
			}
			$thereturn = $this->mp3LibraryWP;
			return $thereturn;
		}


   /**
	* 	GETS custom field META from post/page.
	* 	Takes optional post id, creates indexed arrays.
	* 	Returns number of tracks
	*/
		function TT_grab_Custom_Meta( $id = "" ) {
			
			if ( $id == "feed" ) {
				return 1;
			}
			
			global $wpdb;
			global $post;
			if ( $id == "" ) {
				$id = $post->ID;
			}
			$pagesmeta = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id =" .$id. "  ORDER BY meta_key ASC");
			
			if ( !empty($this->postMetaValues) ) {
				unset( $this->postMetaKeys );
				unset( $this->postMetaValues );
				$this->postMetaKeys = array();
				$this->postMetaValues = array();
				$this->customFieldsGrabbed = "false";
			}
			
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
	*	Creates ALTERNATIVE META ARRAYS.
	*	Called via mp3j_set_meta.
	*
	*	$mode not used yet 
	*/			
		function feed_metadata( $tracks, $captions = "", $mode = 1 ) {
			
			if ( empty($tracks) || !is_array($tracks) ) {
				return;
			}
			if ( $mode == 1 ) {
				unset( $this->feedKeys );
				unset( $this->feedValues );
				$this->feedKeys = array();
				$this->feedValues = array();
				
				$j = 1;
				if ( empty($captions) ) {
					foreach ( $tracks as $i => $file ) {
						$this->feedKeys[$i] = $j++ . " mp3";
						$this->feedValues[$i] = $file;
					}
				}
				else {
					foreach ( $tracks as $i => $file ) {
						if ( !empty($captions[$i]) ) {
							$this->feedKeys[$i] = $j++ . " mp3." . $captions[$i];
						}
						else {
							$this->feedKeys[$i] = $j++ . " mp3";
						}
						$this->feedValues[$i] = $file;
					}
				}
				
				return;
			}
			else {
				return;
			}
		}
		
   
   /**
	* 	Returns LIBRARY mp3 filenames, titles, excerpts, content, uri's 
	*	in indexed arrays.
	*/
		function grab_library_info() {		
		
			global $wpdb;
			$audioInLibrary = $wpdb->get_results("SELECT DISTINCT guid, post_title, post_excerpt, post_content FROM $wpdb->posts WHERE post_mime_type = 'audio/mpeg'"); 
			$j=0;
			$Lcount = count($audioInLibrary);
			$this->mp3LibraryWP = $audioInLibrary;
			
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
					if ( $itemkey == "post_content" ) {
						$libraryDescriptions[$j] = $itemvalue;
					}
				}
				$j++;
			}
			$theLibrary = array(	'filenames' => $libraryFilenames,
									'titles' => $libraryTitles,
									'urls' => $libraryURLs,
									'excerpts' => $libraryExcerpts,
									'descriptions' => $libraryDescriptions,
									'count' => $Lcount );
			$this->mp3LibraryI = $theLibrary;
			return $theLibrary;
		}
		
		
   /**	
	* 	SPLITS up the custom keys/values into artist/title/file indexed arrays. if there's 
	*	no title then uses the filename, if there's no artist then checks whether to use the previous artist.
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
	*	Returns PREPARED ARRAYS that are ready for js playlist.
	*	Looks for $customFilenames that exist in the library and grabs their full uri's, otherwise 
	*	adds default path or makes sure has an http when remote. Cleans up titles that are uri's, swaps 
	*	titles and/or artists for the library ones when required. 
	*
	*	Return: artists, titles, urls.
	*/
		function compare_swap($theSplitMeta, $customkeys, $customvalues) {
			
			if ( empty($this->mp3LibraryI) ) {
				$library = $this->grab_library_info();
			}
			else {
				$library = $this->mp3LibraryI;
			}
			
			foreach ( $theSplitMeta['files'] as $i => $cfvalue ) 
			{
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
	*	SORTS by either the titles(if a-z ticked) or by the keys (only if there's
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
	*	is maybe not ideal.
	*
	*	return: artists, titles, filenames, order, count
	*
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
		function add_Scripts( $theme ) {
			
			wp_enqueue_script( 'jquery', '/wp-content/plugins/mp3-jplayer/js/jquery.js' );
			wp_enqueue_script( 'ui.core', '/wp-content/plugins/mp3-jplayer/js/ui.core.js', array( 'jquery' ) );
			wp_enqueue_script( 'ui.progressbar.min', '/wp-content/plugins/mp3-jplayer/js/ui.progressbar.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'ui.slider.min', '/wp-content/plugins/mp3-jplayer/js/ui.slider.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'jquery.jplayer.min', '/wp-content/plugins/mp3-jplayer/js/jquery.jplayer.min.js', array( 'jquery' ) );	
			wp_enqueue_script( 'mp3-jplayer', '/wp-content/plugins/mp3-jplayer/js/mp3-jplayer.js', array( 'jquery' ) ); 
			
			// Set the style sheets choosable from admin
			
			$small = "";
			if ( $this->theSettings['use_small_player'] == "true" ) {
				$small = "-sidebar";
			}
			if ( $theme == "styleA" ) {
				$theme = "/wp-content/plugins/mp3-jplayer/css/mp3jplayer-grey" . $small . ".css";
			}
			if ( $theme == "styleB" ) {
				$theme = "/wp-content/plugins/mp3-jplayer/css/mp3jplayer-green" . $small . ".css";
			}
			if ( $theme == "styleC" ) {
				$theme = "/wp-content/plugins/mp3-jplayer/css/mp3jplayer-blu" . $small . ".css";
			}
			if ( $theme == "styleD" ) {
				$theme = "/wp-content/plugins/mp3-jplayer/css/mp3-jplayer-cyanALT" . $small . ".css";
			}
			
			$name = strrchr( $theme, "/");
			$name = str_replace( "/", "", $name);
			$name = str_replace( ".css", "", $name);
			wp_enqueue_style( $name, $theme );
			return;
		}
	
	
   /**
	*	WRITES player START-UP JS vars  
	*/
		function write_startup_vars( $count, $autoplay = "", $showlist = "" ) {
			
			if ( $autoplay != "true" && $autoplay != "false" ) {
				$autoplay = $this->theSettings['auto_play'];
			}
			if ( $showlist != "true" && $showlist != "false" ) {
				$showlist = $this->theSettings['playlist_show'];
			}
			
			$wpinstallpath = get_bloginfo('wpurl');
			echo "\n\n<script type=\"text/javascript\">\n<!--\n";
			echo "var foxpathtoswf = \"" .$wpinstallpath. "/wp-content/plugins/mp3-jplayer/js\";\n";
			echo "var foxAutoPlay =" . $autoplay . ";\n";
			echo "var foxInitialVolume =" . $this->theSettings['initial_vol'] . ";\n";
			echo "var foxpathtoimages = \"" .$wpinstallpath. "/wp-content/plugins/mp3-jplayer/css/images/\";\n";
			if ( $count < 2 ) {
				echo "var foxShowPlaylist = \"false\";\n";
			}
			else {
				echo "var foxShowPlaylist = \"" .$showlist. "\";\n";
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
		function write_player_html( $count, $position, $download ) {			
			
			if ( $this->theSettings['use_small_player'] == "true" ) { //set player widths for centred and right aligned positions  
				$width = "201px";
			}
			else {
				$width = "281px";
			}
			
			if ( $position == "left" ) {
				$floater = "float: left; padding: 5px 50px 50px 0px;";
			}
			else if ( $position == "right" ) {
				$floater = "float: right; padding: 5px 0px 50px 50px;";
			}
			else if ( $position == "absolute" ) {
				$floater = "position: absolute;";
			}
			else if ( $position == "rel-C" ) {
				$floater = "position:relative; padding:5px 0px 50px 0px; width:" . $width . "; margin:0px auto 0px auto;";
			}
			else if ( $position == "rel-R" ) {
				$floater = "position:relative; padding:5px 0px 50px 0px; width:" . $width . "; margin:0px 0px 0px auto;";
			}
			else {
				$floater = "position: relative; padding: 5px 0px 50px 0px;";
			}
			
			if ( $download == "true" ) {
				$showMp3Link = "visibility: visible;"; 
			}
			else {
				$showMp3Link = "visibility: hidden;";
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
	*	DEBUG HELP, prints vars/arrays to browser source view.
	*	Called via mp3j_debug() template tag.
	*	needs improvement.
	*/	
		function debug_info( $display = "" ) {	
			
			$this->make_compatible();
			$this->debugCount++;
			echo "\n\n<!-- *** DEBUG " . $this->debugCount;
			if ( $display == "" ) { echo " (Vars)"; }
			else { echo " (All)"; }
			echo " * MP3-jPlayer (" . $this->version_of_plugin . ") ***\n\nPage type: ";
			
			if ( is_home() ) { echo "Posts index"; }
			else if ( is_single() ) { echo "Single post"; }
			else if ( is_page() ) { echo "Single page"; }
			else if ( is_archive() ) { echo "Archive"; }
			else { echo "other"; }
			echo "\nAllow tags: ";
			if ( $this->theSettings['disable_template_tag'] == "false" ) { echo "Yes"; }
			else { echo "NO"; }
			echo "\nScripts flagged: " . $this->scriptsflag . "\nScripts forced: " . $this->scriptsForced . "\nCustom fields grabbed: " . $this->customFieldsGrabbed . "\nmp3j_put flagged: " . $this->tagflag;
			echo "\n\n*** Calls to add player functions\ncontent (default): " . $this->defaultAdd_runCount . "\nshortcode: " . $this->shortcode_runCount . "\nmp3j_put: " . $this->putTag_runCount;
			echo "\n\nAttempted to add via: " . $this->playerSetMethod;
			if ( $this->playerAddedOnRun > 0 ) { echo " on call no. " . $this->playerAddedOnRun; }
			echo "\nplaylist count: " . $this->countPlaylist;
			echo "\nflagged as added: " . $this->playerHasBeenSet;
			echo "\n\nADMIN SETTINGS:\n";
			print_r($this->theSettings);
			
			if ( $display == "" || $display == "vars" ) { 
				echo " \n\n-->\n\n";
				return;
			}
			
			//*
			echo "\n\nMETA KEY MATCHES:\n";
			print_r($this->postMetaKeys);
			echo "\n\nMETA VALUES:\n";
			print_r($this->postMetaValues);
			echo "\n\nFEED KEYS:\n";
			print_r($this->feedKeys);
			echo "\n\nFEED VALUES:\n";
			print_r($this->feedValues);
			echo "\n\nTHE PLAYLIST:\n";
			print_r($this->PlayerPlaylist);
			if ( empty($this->mp3LibraryI) ) { $this->grab_library_info(); } 
			echo "\n\n* MP3's IN LIBRARY:\n";
			print_r($this->mp3LibraryI);
			// */
			
			echo " \n\n-->\n\n";
			return;	
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
	*	Makes sure options array is up to date with current plugin.
	*/
		function make_compatible() {
			
			if ( $this->iscompat == true ) {
				return;
			}
			$options = get_option($this->adminOptionsName);			
			if ( count($options) == $this->option_count ) {
				$this->theSettings = $options;
			}
			else {
				$this->theSettings = $this->getAdminOptions();
			}
			$this->iscompat = true;
			return;
		}


   /**
	*	RETURNS updated set of ADMIN SETTINGS with any new options and default values 
	*	Added to the db.  
	*/
		function getAdminOptions() {
			
			$mp3FoxAdminOptions = array( // default settings
							'initial_vol' => '100',
							'auto_play' => 'true',
							'mp3_dir' => '/',
							'player_theme' => 'styleA',
							'allow_remoteMp3' => 'true',
							'playlist_AtoZ' => 'false',
							'player_float' => 'none',
							'player_onblog' => 'true',
							'playlist_UseLibrary' => 'false',
							'playlist_show' => 'true',
							'remember_settings' => 'true',
							'hide_mp3extension' => 'false',
							'show_downloadmp3' => 'false',
							'disable_template_tag' => 'false',
							'db_plugin_version' => $this->version_of_plugin,
							'use_small_player' => 'false' );
			
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
				if (isset($_POST['disableTemplateTag'])) {
					$theOptions['disable_template_tag'] = $_POST['disableTemplateTag'];
				}
				else { 
					$theOptions['disable_template_tag'] = "false";
				}
				if (isset($_POST['mp3foxSmallPlayer'])) {
					$theOptions['use_small_player'] = $_POST['mp3foxSmallPlayer'];
				} 
				else { 
					$theOptions['use_small_player'] = "false";
				}
				
				update_option($this->adminOptionsName, $theOptions);
				?>

				<div class="updated"><p><strong><?php _e("Settings Updated.", "mp3Fox");?></strong></p></div>
			
			<?php 
			} 
			?>
			
			<div class="wrap">
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					
					<h2>Mp3-jPlayer <span class="description" style="font-size: 10px;"> (<?php echo $this->version_of_plugin; ?>)</span></h2>
					
					<p class="description" style="margin: 8px 120px 0px 0px;">Below are the global settings for the player, it 
						will automatically appear on any posts and pages that you have a playlist on. You can use the shortcode to over-ride some of these options.</p>
					<h4 class="description" style="margin-top: 5px; margin-bottom: 30px; font-weight:500"><a href="#howto">Help</a></h4>
					 
					<h3 style="margin-bottom: 0px;">Player</h3>
					<p style="margin-top: 7px; margin-bottom: 5px;">&nbsp; Initial volume &nbsp; <input type="text" style="text-align:right;" size="2" name="mp3foxVol" value="<?php echo $theOptions['initial_vol'] ?>" /> &nbsp; <span class="description">(0 - 100)</span></p>
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAutoplay" value="true" <?php if ($theOptions['auto_play'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Autoplay</p>
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxShowPlaylist" value="true" <?php if ($theOptions['playlist_show'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Start with the playlist showing</p>
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxOnBlog" value="true" <?php if ($theOptions['player_onblog'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Show a player on the posts index page when there's something to play
						<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(the player is added to the highest post in the list that has a playlist)</span></p>
					<p style="margin-top: 0px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAtoZ" value="true" <?php if ($theOptions['playlist_AtoZ'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Playlist the tracks in alphabetical order</p>
					<p style="margin-top: 0px; margin-bottom: 15px;">&nbsp; <input type="checkbox" name="mp3foxDownloadMp3" value="true" <?php if ($theOptions['show_downloadmp3'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Display a 'Download mp3' button</p>

					
					<h3 style="margin-bottom: 4px;"><br />Library mp3's</h3>
					<p style="margin-bottom: 5px;">&nbsp; <input type="checkbox" name="mp3foxUseLibrary" value="true" <?php if ($theOptions['playlist_UseLibrary'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Always use Media Library titles and excerpts when they exist</p>
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
				echo "<p class=\"description\" style=\"margin: 0px 120px 15px 35px;\">Your Media Library uploads folder is currently set to <code>" .$uploadsfolder. "</code> , you can always <a href=\"" . $mediapagelink . "\">change it</a> without affecting any playlists.</p>";
			}
			?>
					
					<h3 style="margin-bottom: 8px;"><br />Non-Library mp3's</h3>
					<p class="description" style="margin: 0px 120px 0px 7px;">Set a folder to play non-library tracks from in the box below. eg <code>/mymusic</code>
						or <code>www.another-domain.com/folder</code>. You only need write filenames for tracks from here. You can over-ride the
						default path/URI anytime on a playlist by putting the full URI for an mp3.</p>
					 
					<p>&nbsp; Default path or URI &nbsp; <input type="text" size="55" name="mp3foxfolder" value="<?php echo $theOptions['mp3_dir'] ?>" /></p>
					<p style="margin-top: 20px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="mp3foxAllowRemote" value="true" <?php if ($theOptions['allow_remoteMp3'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Allow mp3's from other domains on
						the player's playlists<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(unchecking this option doesn't affect mp3's using your default path above if it's an external URI)</span></p>
					<p style="margin-top: 0px; margin-bottom: 14px;">&nbsp; <input type="checkbox" name="mp3foxHideExtension" value="true" <?php if ($theOptions['hide_mp3extension'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Hide .mp3 if a filename is displayed
						<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(filenames are displayed when there's no available titles)</span></p>
					
					<h3 style="margin-bottom: 6px;"><br />Style</h3>
					<p style="margin-bottom: 0px;">&nbsp; <input type="radio" name="mp3foxTheme" value="styleA" <?php if ($theOptions['player_theme'] == "styleA") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;&nbsp;Neutral<br />
							&nbsp; <input type="radio" name="mp3foxTheme" value="styleB" <?php if ($theOptions['player_theme'] == "styleB") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;&nbsp;Green<br />
							&nbsp; <input type="radio" name="mp3foxTheme" value="styleC" <?php if ($theOptions['player_theme'] == "styleC") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;&nbsp;Blue<br />
							&nbsp; <input type="radio" name="mp3foxTheme" value="styleD" <?php if ($theOptions['player_theme'] == "styleD") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;&nbsp;Cyan (Alternative style)</p>
					<p style="margin-top:10px; margin-bottom: 24px;">&nbsp; <input type="checkbox" name="mp3foxSmallPlayer" value="true" <?php if ($theOptions['use_small_player'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp; Use a small player</p>

					<h3 style="margin-bottom: 6px;"><br />Position</h3>
					<p>&nbsp; Left &nbsp;<input type="radio" name="mp3foxFloat" value="left" <?php if ($theOptions['player_float'] == "left") { _e('checked="checked"', "mp3Fox"); }?> />&nbsp;&nbsp; 
							| &nbsp;&nbsp;<input type="radio" name="mp3foxFloat" value="right" <?php if ($theOptions['player_float'] == "right") { _e('checked="checked"', "mp3Fox"); }?> />&nbsp; Right
							<br /><span class="description">&nbsp; (<strong>floated</strong>, content wraps around the player)</span></p>
					
					<p>&nbsp; Left &nbsp;<input type="radio" name="mp3foxFloat" value="none" <?php if ($theOptions['player_float'] == "none") { _e('checked="checked"', "mp3Fox"); }?> />&nbsp;&nbsp;
						| &nbsp;&nbsp;<input type="radio" name="mp3foxFloat" value="rel-C" <?php if ($theOptions['player_float'] == "rel-C") { _e('checked="checked"', "mp3Fox"); }?> />&nbsp; Centre &nbsp;
						| &nbsp;&nbsp;<input type="radio" name="mp3foxFloat" value="rel-R" <?php if ($theOptions['player_float'] == "rel-R") { _e('checked="checked"', "mp3Fox"); }?> />&nbsp; Right
						<br /><span class="description">&nbsp; (<strong>relative</strong>, content appears above/below the player)</span></p>
					
					<p style="margin-top: 20px; margin-bottom: 8px;">&nbsp; <input type="checkbox" name="disableTemplateTag" value="true" <?php if ($theOptions['disable_template_tag'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /> &nbsp;Ignore player template-tags in theme
						<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(player positioning within content only)</span></p>
					
					<br /><br /><br />
					<p style="margin-top: 4px;"><input type="submit" name="update_mp3foxSettings" class="button-primary" value="<?php _e('Update Settings', 'mp3Fox') ?>" />
					 &nbsp; Remember settings if plugin is deactivated &nbsp;<input type="checkbox" name="mp3foxRemember" value="true" <?php if ($theOptions['remember_settings'] == "true") { _e('checked="checked"', "mp3Fox"); }?> /></p>
				</form>
				
				<a name="howto"></a>
				<div style="margin: 26px 120px 0px 0px; border-top: 1px solid #555; height: 10px;"></div>
				<p style="margin: 0px 120px 0px 0px;">&nbsp;</p>
				<h4 style="margin: 0px 120px 0px 0px">Help</h4> 
				<p class="description" style="margin: 10px 120px 0px 10px;"><strong>How to play mp3's</strong></p>
				<p class="description" style="margin: 10px 120px 10px 10px;">Add tracks on page/post edit screens using the custom fields (below the content box), as follows:</p> 
				<p class="description" style="margin: 0px 120px 10px 10px;">1. Enter <code>mp3</code> into the left hand box
					<br />2. Write the filename* into the right hand box and hit 'add custom field'
					<br />3. Repeat above to add more mp3's and  and hit 'update page' when you're done</p>
				<p class="description" style="margin: 0px 120px 5px 10px;">* If the file is not in either the media library or your default location then use a full URI.</p>
				
				<p class="description" style="margin: 20px 120px 5px 10px;"><strong>Adding a title and caption:</strong></p>
				<p class="description" style="margin: 10px 120px 5px 10px;">1. Add a dot, then a caption in the left hand box, eg: <code>mp3.Caption</code><br />2. Add the title, then an '@' before the filename, eg: <code>Title@filename</code></p>
				
				<p class="description" style="margin: 20px 120px 5px 10px;"><strong>Ordering the tracks:</strong></p>
				<p class="description" style="margin: 10px 120px 5px 10px;">Number the left boxes, eg:<code>1 mp3.Caption</code> will be first on the playlist. Un-numbered tracks appear
						below any numbered tracks.</p>
				
				<p class="description" style="margin: 20px 120px 5px 10px;"><strong>Shortcode:</strong></p>
				<p class="description" style="margin: 10px 120px 5px 10px;"><code>[mp3-jplayer]</code></p>
				<p class="description" style="margin: 5px 120px 5px 10px;">Has the following attributes for control of player position, download, autoplay, and show playlist on each page/post:</p>
				<p class="description" style="margin: 5px 120px 5px 10px;">pos - left, right, rel (or none), rel-C, rel-R, absolute)
					<br />dload - true, false
					<br />play - true, false
					<br />list - true, false</p>
				<p class="description" style="margin: 10px 120px 5px 10px;">eg. <code>[mp3-jplayer play=&quot;true&quot; dload=&quot;true&quot; pos=&quot;rel-C&quot;]</code></p>
				<p class="description" style="margin: 5px 120px 5px 10px;">Shortcode parameters over-ride the settings on this page</p>
				
				<p class="description" style="margin: 20px 120px 5px 10px;"><strong>Template tags:</strong></p>
				<p class="description" style="margin: 10px 120px 3px 10px;"><code>mp3j_addscripts( $style )</code></p>
				<p class="description" style="margin: 0px 120px 3px 10px;"><code>mp3j_flag( $set )</code></p>
				<p class="description" style="margin: 0px 120px 3px 10px;"><code>mp3j_grab_library( $format )</code></p>
				<p class="description" style="margin: 0px 120px 3px 10px;"><code>mp3j_set_meta( $tracks, $captions )</code></p>
				<p class="description" style="margin: 0px 120px 3px 10px;"><code>mp3j_put( $id, $pos, $dload, $autoplay, $showplaylist )</code></p>
				<p class="description" style="margin: 0px 120px 14px 10px;"><code>mp3j_debug( $output )</code></p>
				<p class="description" style="margin: 0px 120px 5px 10px;">eg: <code>&lt;?php if ( function_exists( 'mp3j_put' ) ) { mp3j_put( 3, 'absolute', '', 'true' ); } ?&gt;</code></p>
										
				<?php
				echo '<p class="description" style="margin: 15px 120px 5px 10px;">See the <a href="' . get_bloginfo('wpurl') . '/wp-content/plugins/mp3-jplayer/readme.htm">readme</a> for more detailed info.</p>';
				//echo '<p class="description" style="margin: 20px 120px 5px 10px;"><a href="http://sjward.org/jplayer-for-wordpress">Plugin home page</a></p>';
				?>
				
				<div style="margin: 40px 120px 0px 0px; border-top: 1px solid #555; height: 30px;">
					<p class="description" style="margin: 0px 120px px 0px;"><a href="http://sjward.org/jplayer-for-wordpress">Plugin home page</a></p>
				</div>
				<br /><br /><br /><br />
			</div> 
		
		<?php
		}
				
	} //end class
} // end if


if ( class_exists("mp3Fox") ) {
	$mp3_fox = new mp3Fox();
}
if ( isset($mp3_fox) ) {

/* initialize mp3-jplayer admin page */	

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
	
/*  mp3j hooks */

	function mp3j_put( $id = "", $pos = "", $dload = "", $play = "", $list = "" ) {
		
		do_action( 'mp3j_put', $id, $pos, $dload, $play, $list );
	}
	
	function mp3j_flag( $set = 1 ) {
		
		do_action('mp3j_flag', $set);
	}
	
	function mp3j_addscripts( $style = "" ) {
		
		do_action('mp3j_addscripts', $style);
	}
	
	function mp3j_debug( $display = "" ) {
		
		do_action('mp3j_debug', $display);
	}
	
	function mp3j_grab_library( $format = 1 ) { 
		
		$thereturn = array();
		if ( $format == 1 ) {
			$library = apply_filters('mp3j_grab_library', $thereturn );
			return $library;
		}
		if ( $format == 0 ) {
			$library = apply_filters('mp3j_grab_library_wp', $thereturn );
			return $library;
		}
		else {
			return;
		}
	}
	
	function mp3j_set_meta( $tracks, $captions = "", $mode = 1 ) {
		
		if ( empty($tracks) || !is_array($tracks) ) {
				return;
			}  
		do_action('mp3j_set_meta', $tracks, $captions, $mode);
	}
	
/* register hooks */
	
	//admin 
	add_action('activate_mp3-jplayer/mp3jplayer.php',  array(&$mp3_fox, 'initFox'));
	add_action('deactivate_mp3-jplayer/mp3jplayer.php',  array(&$mp3_fox, 'uninitFox'));
	add_action('admin_menu', 'mp3Fox_ap');
	
	//template
	add_action('wp_head', array(&$mp3_fox, 'check_if_scripts_needed'), 2);
	add_filter('the_content', array(&$mp3_fox, 'add_player'));
	add_shortcode('mp3-jplayer', array(&$mp3_fox, 'shortcode_handler'));
	add_action('mp3j_put', array(&$mp3_fox, 'template_tag_handler'), 10, 5 );
	add_action('mp3j_flag', array(&$mp3_fox, 'flag_tag_handler'), 10, 1 );
	add_action('mp3j_addscripts', array(&$mp3_fox, 'scripts_tag_handler'), 1, 1 );
	add_action('mp3j_debug', array(&$mp3_fox, 'debug_info'), 10, 1 );	
	add_filter('mp3j_grab_library', array(&$mp3_fox, 'grablibrary_handler'), 10, 1 );
	add_filter('mp3j_grab_library_wp', array(&$mp3_fox, 'grablibraryWP_handler'), 10, 1 );
	add_action('mp3j_set_meta', array(&$mp3_fox, 'feed_metadata'), 10, 3 );
		
}
?>