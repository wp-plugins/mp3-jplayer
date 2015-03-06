<?php
if ( !class_exists("MP3j_Main") ) { class MP3j_Main	{
	
	// ---------------------- Update Me
	var $version_of_plugin = "2.3.1"; 
	var $M_no = 0;
	var $F_no = 0;
	var $S_no = 0;
	var $Caller = false;
	var $S_autotrack = 0;
	var $S_arb = 1;
	var $Player_ID = 0;
	var $scriptsflag = "false";
	var $postID = false;
	var $F_listname = false;
	var $F_listlength = false;	
	var $F_LISTS = array();
	var $isExcerpt = false;
	var $isAllowedExcerpt = false;
	var $setup = array( 
		'stylesheet' 		=> true,
		'cssHead' 			=> true,
		'stylesheetPopout' 	=> true,
		'cssPopout' 		=> true,
		'designPage' 		=> true
	);
	
	var $currentID = '';
	var $LibraryI = false;
	var $JS = array(
		'playlists' => array(),
		'players' 	=> array()
	);
	var $dbug = array(
		'str' => '',
		'arr' => array()
	);
	var $adminOptionsName = "mp3FoxAdminOptions";
	var $theSettings = array();
	var $Rooturl;
	var $WPinstallpath;
	var $textdomain = "mp3-jplayer";
	var $newCSScustom;
	var $stylesheet = "";
	var $folder_order = "asc";
	var $PP_css_url = "";
	var $PluginFolder = "";
	
	var $allowedFeedExtensions = array();
	var $allowedFeedMimes = array();
	var $formatsFeedRegex = '';
	var $formatsFeedSQL = '';
	var $FIRST_FORMATS = false;
	var $SCRIPT_CALL = false;
	var $JSvars = false;
	
	var $EXTpages = array();
	var $SKINS = array();
	var $menuHANDLES = array(
		'parent' => false,
		'design' => false
	);
	
/**
*	Sets some vars and makes stored 
*	options compatible. 
*/
	function MP3j_Main ()
	{ 
		$this->WPinstallpath = get_bloginfo('wpurl');
		$this->Rooturl = preg_replace("/^www\./i", "", $_SERVER['HTTP_HOST']);
		$this->PluginFolder = plugins_url('', __FILE__);
		//$this->newCSScustom = $this->PluginFolder . "/css/player-silverALT.css";
		$this->newCSScustom = $this->PluginFolder . "/css/example.css";
		$this->theSettings = $this->getAdminOptions();
		//$this->theSettings = $this->OPS; //alias, to go
		if ( ! isset( $_POST['update_mp3foxSettings'] ) ) {
			$this->setAllowedFeedTypesArrays();
		}
		//$this->SKINS = $this->getSkinData();
	}
	

////

//~~~~~
	function onInit ()
	{
		$this->SKINS = $this->getSkinData();
	}


//~~~~~
	function get_excerpt_handler( $stored = "" )
	{ 
		global $post;
		$this->dbug['str'] .= "\n#early excerpt [" .$post->post_title. "]#";
		$this->isExcerpt = true;
		$this->isAllowedExcerpt = false;
		if ( $stored != "" && $this->theSettings['run_shcode_in_excerpt'] ) { 
			$this->isAllowedExcerpt = true;	
		}
		return $stored;
	}

//~~~~~
	function afterExcerpt ( $stuff = '' )
	{
		global $post;
		$this->dbug['str'] .= "\n#late excerpt [" .$post->post_title. "]#";
		$this->isExcerpt = false;
		$this->isAllowedExcerpt = false;
		return $stuff;
	}

//~~~~~
	function canRun ()
	{
		$allowed = true;
		
		if ( $this->isExcerpt === true ) {
			if ( $this->isAllowedExcerpt === false ) {
				$this->dbug['str'] .= "\nExiting (isExcerpt, allowed/manual:false)";
				$allowed = false;
			}
		}
		else {
			if ( ! $this->Caller && ! is_singular() ) { 
				if ( $this->theSettings['player_onblog'] == 'false' ) {
					$this->dbug['str'] .= "\nExiting (player_onblog is unticked)";
					$allowed = false;
				}
			}
		}
		return $allowed;
	}

	
	

//##########	
	function getImageSizeWP ( $size )
	{
		$dims = array();
		$dims['width'] = get_option( $size . '_size_w' );
		$dims['height'] = get_option( $size . '_size_h' );
		
		//fallback if the sizes aren't defined
		if ( $dims['width'] === false ) {
			$dims['width'] = ( 'thumbnail' === $size ) ? '150' : '400';
			$dims['height'] = ( 'thumbnail' === $size ) ? '150' : '400';
		}
		return $dims;
	}
	
	
//~~~~~	
	function getSkinData ()
	{
		$custom_css = ( $this->theSettings['custom_stylesheet'] == "/" ) ? $this->newCSScustom : $this->prep_value( $this->theSettings['custom_stylesheet'] );
		if ( strpos( $custom_css, '/' ) === 0 ) {
			$protocol = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) ? 'https' : 'http';
			$custom_css = $protocol . '://' . $this->Rooturl . $custom_css;
		}
		
		//v2 skins
		$opValue = 'defaultDark';
		$this->SKINS[ $opValue ] = array(
			'opValue' => $opValue,
			'opName' => 'Dark',
			'url' => $this->PluginFolder . "/css/dark.css"
		);
		$opValue = 'defaultLight';
		$this->SKINS[ $opValue ] = array(
			'opValue' => $opValue,
			'opName' => 'Light',
			'url' => $this->PluginFolder . "/css/light.css"
		);
		$opValue = 'defaultText';
		$this->SKINS[ $opValue ] = array(
			'opValue' => $opValue,
			'opName' => 'Text',
			'url' => $this->PluginFolder . "/css/text.css"
		);
		
		//v1 skins
		$this->SKINS['styleG'] = array(
			'opValue' => 'styleG',
			'opName' => 'v1 Dark - legacy support',
			'url' => $this->PluginFolder . "/css/v1-skins/v1-dark.css"
		);
		$this->SKINS['styleF'] = array(
			'opValue' => 'styleF',
			'opName' => 'v1 Light - legacy support',
			'url' => $this->PluginFolder . "/css/v1-skins/v1-silver.css"
		);
		
		//user's custom css
		$this->SKINS['styleI'] = array(
			'opValue' => 'styleI',
			'opName' => 'Custom CSS - Enter your own URL below',
			'url' => $custom_css
		);
		
		return $this->SKINS;
	}
	
	

//###############	
	function defineJSvars ()
	{
		if ( ! $this->JSvars ) {
			echo "\n<script>";
			echo "\nvar MP3jPLAYLISTS = [];";
			echo "\nvar MP3jPLAYERS = [];";
			echo "\n</script>\n";
			$this->JSvars = true;
		}
	}


//###############
	function setAllowedFeedTypesArrays ()
	{
		$formats = $this->theSettings['audioFormats'];
		
		//extensions
		$allowedFeedExtensions = array();
		if ( 'true' === $formats['mp3'] ) {
			$allowedFeedExtensions[] = 'mp3';
		}
		if ( 'true' === $formats['mp4'] ) {
			$allowedFeedExtensions[] = 'mp4';
			$allowedFeedExtensions[] = 'm4a';
		}
		if ( 'true' === $formats['ogg'] ) {
			$allowedFeedExtensions[] = 'ogg';
			$allowedFeedExtensions[] = 'oga';
		}
		if ( 'true' === $formats['wav'] ) {
			$allowedFeedExtensions[] = 'wav';
		}
		if ( 'true' === $formats['webm'] ) {
			$allowedFeedExtensions[] = 'webm';
		}
		$this->allowedFeedExtensions = $allowedFeedExtensions;
		$this->setFeedFormatsRegex( $allowedFeedExtensions );
		
		//mimes
		$allowedFeedMimes = array();
		if ( 'true' === $formats['mp3'] || 'true' === $formats['mp4'] ) {
			$allowedFeedMimes[] = 'audio/mpeg';
		}
		if ( 'true' === $formats['ogg'] ) {
			$allowedFeedMimes[] = 'audio/ogg';
		}
		if ( 'true' === $formats['wav'] ) {
			$allowedFeedMimes[] = 'audio/wav';
		}
		if ( 'true' === $formats['webm'] ) {
			$allowedFeedMimes[] = 'audio/webm';
		}
		$this->allowedFeedMimes = $allowedFeedMimes;
		$this->setFeedFormatsSQL( $allowedFeedMimes );
	}
	

//###############	
	function setFeedFormatsRegex ( $extensions )
	{
		$i = 1;
		$count = count( $extensions );
		$regex = '';
		foreach ( $extensions as $ext ) {
			$regex .= $ext . ( $i < $count ? '|' : '');
			$i++;
		}
		$regex .= '';
		$this->formatsFeedRegex = $regex;
	}
	

//###############	
	function setFeedFormatsSQL ( $mimes ) 
	{
		$i = 1;
		$count = count( $mimes );
		$mimeTypes = '';
		foreach( $mimes as $m ) {
			$mimeTypes .= "post_mime_type='" . $m . "'" . ( $i < $count ? " OR " : "");
			$i++;
		}
		$this->formatsFeedSQL = $mimeTypes;
	}
	
	
//###############
	function grab_library_info( $mimeTypes = '' )
	{		 
		if ( $this->LibraryI !== false && $mimeTypes === '' ) {
			return $this->LibraryI;
		}
		
		$ops = $this->theSettings;
		$LIB = false;
		switch( $this->theSettings['library_sortcol'] ) {
			case "date": 
				$order = " ORDER BY post_date " . $ops['library_direction']; 
				break;
			case "title":
				$order = " ORDER BY post_title " . $ops['library_direction']; 
				break;
			case "caption": 
				$order = " ORDER BY post_excerpt " . $ops['library_direction'] . ", post_title " . $ops['library_direction']; 
				break;
			default: 
				$order = "";
		}
		global $wpdb;		
		
		$MIMES = ( $mimeTypes !== '' ) ? $mimeTypes : "post_mime_type = 'audio/mpeg' OR post_mime_type = 'audio/ogg' OR post_mime_type = 'audio/wav' OR post_mime_type = 'audio/webm'";
		$audio = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE " . $MIMES . $order);
		
		if ( !empty($audio) )
		{	
			foreach ( $audio as $obj ) {
				$Titles[] = $obj->post_title;
				$Excerpts[] = $obj->post_excerpt;
				$Descriptions[] = $obj->post_content;
				$PostIDs[] = $obj->ID;
				$URLs[] = $obj->guid;
				$File = strrchr( $obj->guid, "/");
				$Filenames[] = str_replace( "/", "", $File);
				$postDates[] = $obj->post_date_gmt;
				$mimes[] = $obj->post_mime_type;
			}		
			if ( !empty( $Filenames ) ) {
				if ( $ops['library_sortcol'] == "file" ) { 
					natcasesort($Filenames);
					if ( $ops['library_direction'] == "DESC" ) {
						$Filenames = array_reverse($Filenames, true);
					}
				}
				$c = count( $Filenames );
				$LIB = array(	
					'filenames' => $Filenames,
					'titles' => $Titles,
					'urls' => $URLs,
					'excerpts' => $Excerpts,
					'descriptions' => $Descriptions,
					'postIDs' => $PostIDs,
					'postDates' => $postDates,
					'mimes' => $mimes,
					'count' => $c
				);
				if ( empty($mimeTypes) ) {
					$this->LibraryI = $LIB;
				}
			}
		}
		return $LIB;
	}




//###############
	function grabLibraryURLs( $mimeType )
	{
		global $wpdb;		
		$audio = $wpdb->get_results("SELECT DISTINCT guid FROM $wpdb->posts WHERE post_mime_type = '" . $mimeType . "'");
			
		$URLs = array();		
		if ( !empty($audio) ) {
			foreach ( $audio as $obj ) {
				$URLs[] = $obj->guid;
			}
		}	
		return ( empty($URLs) ? false : $URLs );
	}


//###############		
	function grabFolderURLs( $folder, $extensions = "" )
	{
		$items = array();
		$filenames = array();
		$modTimes = array();
		$extensions = ( $extensions === "" ) ? "mp3|m4a|mp4|oga|ogg|wav|webm|webma" : $extensions;
		$fp = $folder;
		
		if ( ($isLocal = strpos($folder, $this->Rooturl)) || preg_match("!^/!", $folder) )
		{
			if ( $isLocal !== false ) {
				$fp = str_replace($this->Rooturl, "", $folder);
				$fp = str_replace("www.", "", $fp);
				$fp = str_replace("http://", "", $fp);
				$fp = str_replace("https://", "", $fp);
			} 
			
			$path = $_SERVER['DOCUMENT_ROOT'] . $fp;
			if ( $handle = @opendir( $path ) )
			{
				$j=0;
				while ( false !== ( $file = readdir( $handle ) ) )
				{
					if ( $file != '.' && $file != '..' && filetype($path.'/'.$file) == 'file' && preg_match( "!\.(" . $extensions . ")$!i", $file ) )
					{
						$modTimes[$j] = @filemtime( $path . '/'. $file ); //supress errors!
						$items[$j++] = $file;
					}
				}
				closedir($handle);
				
				if ( ($c = count($items)) > 0 )
				{
					$orderedFiles = array();
					if ( $this->theSettings['folderFeedSortcol'] === 'date' ) {
						natcasesort( $modTimes );
						foreach ( $modTimes as $i => $uts ) {
							$orderedFiles[] = $items[$i];  
						}
						$items = $orderedFiles;
					}
					else {
						natcasesort( $items );
					}
					
					//if ( $this->folder_order != "asc" ) { //TODO:support this still?
					if ( $this->theSettings['folderFeedDirection'] === "DESC" ) {
						$items = array_reverse( $items, true );
						$modTimes = array_reverse( $modTimes, true );
					}
					
					$fp = preg_replace( "!/+$!", "", $fp );
					foreach ( $items as $i => $mp3 )
					{
						$filenames[$i] = $mp3;
						$items[$i] = "http://" . $_SERVER['HTTP_HOST'] . $fp . "/" . $mp3;
					}
				}
				$this->dbug['str'] .= "\nRead folder for " . $extensions . " - Done, " . $c . " in folder http://" . $_SERVER['HTTP_HOST'] . $fp;
				
				//Return the info
				return array(
					'files' => $items, //TODO: remove this duplicate
					'dates' => $modTimes,
					'urls' => $items, 
					'filenames' => $filenames
				);
			}
			else { //Error
				$this->dbug['str'] .= "\nRead folder - Couldn't open local folder, check path/permissions to http://" . $_SERVER['HTTP_HOST'] . $fp;
				return true;
			}
		}
		else { //Error
			$this->dbug['str'] .= "\nRead folder - Path was remote or unreadable." . $fp;
			return false;
		}
	}
	

//~~~ PRE-BUILD ROUTINE ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~	
	
//###############
	function newTrack ()
	{
		return array(
			'src' 			=> '',
			'filename' 		=> '',
			'formats' 		=> array(),
			'counterpart' 	=> '',
			'title' 		=> '',
			'caption' 		=> '',
			'image'			=> '',
			'link' 			=> ''
		);
	}


//###############	
	function getFileFormat ( $src )
	{
		$format = '';		
		$extension = strrchr( $src, '.' );		
		
		if ( $extension !== false )
		{
			$extension = str_replace( '.', '', $extension );
			$format = strtolower( $extension );
		
			if ( $format == 'mp4' ) {
				$format = 'm4a';
			}
			elseif ( $format == 'ogg' )	{
				$format = 'oga';
			}
			elseif ( $format == 'webm' || $format == 'webma' ) {
				$format = 'webma';
			}
			elseif ( ! in_array($format, array('m4a', 'oga', 'wav')) ) { //remaining type is mp3, make sure it's set to this anyhow even if unrecognised.
				$format = 'mp3';
			}
		}
		return $format;
	}
	

//###############
	function makeTrack( $value, $counterpart = '' )
	{		
		$track = $this->newTrack();
		
		//remove \n and \r characters 
		$v = str_replace( array(chr(10), chr(13)), "", $value ); 
		$vRev = strrev($v); //so explodes at last @
		$vSplit = explode('@', $vRev, 2);
		
		//User entered source value, may be incomplete at this stage 
		$src = ( ! empty($vSplit[0]) ) ? strrev($vSplit[0]) : '';
		if ( preg_match('/^www\./i', $src) && $src != "www.mp3" ) { //if it's url with no http then add it
			$src = "http://" . $src;
		}				
		$track['src'] = $src;
		
		//Filename
		$fileRev = explode('/', $vSplit[0], 2);
		$filename = ( ! empty($fileRev[0]) ) ? strrev($fileRev[0]) : '';
		$track['filename']	= $filename;
		
		//Declare formats, catch generic and unsupported extensions, jplayer wants them audio specific.
		$track['formats'][0] = $this->getFileFormat( $src );
		if ( ! empty( $counterpart ) ) {
			$track['counterpart'] = $counterpart;
			$track['formats'][1] = $this->getFileFormat( $counterpart );
		}

		//User entered title
		$title = ( empty($vSplit[1]) ) ? '' : strrev( $vSplit[1] );
		$track['title']	= $title;
		return $track;
	}


//~~~ PLAYLIST ROUTINE ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~	
//###############	
	function generatePlaylist ( $list )
	{
		if ( empty( $list ) || ! is_array( $list ) ) {
			return false;
		}
		foreach ( $list as $i => $track ) {
			$list[$i] = $this->findParts( $track );
		}
		if ( $this->theSettings['allow_remoteMp3'] == "false" ) {
			$list = $this->removeRemoteTracks( $list );
		}
		return $list;
	}
	

//########################
	function findParts ( $track )
	{
		$LIB = $this->grab_library_info();
		$LIBindex = ( $LIB === false ) ? false : array_search( $track['src'], $LIB['filenames'] );
		$isURL = ( strpos($track['src'], 'http://') === false && strpos($track['src'], 'https://') === false ) ? false : true;
		
		if ( $LIBindex !== false ) //in library
		{
			//text
			$track['src'] = $LIB['urls'][$LIBindex];
			$track['title'] = ( $track['title'] === "" ) ? $LIB['titles'][$LIBindex] : $track['title'];
			$track['caption'] = ( $track['caption'] === "" ) ? $LIB['excerpts'][$LIBindex] : $track['caption'];
			
			//image
			if ( $track['image'] === 'true' ) {
				$track['image'] = $this->getPostImageUrl( $LIB['postIDs'][$LIBindex] );
			}
			//elseif ( $track['image'] === 'false' ) {
			//	$track['image'] = '';
			//}
			
			//counterpart
			if ( $track['counterpart'] === '' &&  $this->theSettings['autoCounterpart'] === 'true' ) { 
				$track = $this->getFEEDCounterpart( $track, $LIB );
			}
		}
		else
		{
			if ( ! $isURL ) { //local path
				if ( strpos($track['src'], "/") !== 0 ) { //no starting slash so prepend df path
					$track['src'] = ( $this->theSettings['mp3_dir'] == "/" ) ? $this->theSettings['mp3_dir'] . $track['src'] :  $this->theSettings['mp3_dir'] . "/" . $track['src'];
				}
				if ( $track['counterpart'] === '' &&  $this->theSettings['autoCounterpart'] === 'true' ) { 					
					$path = strrev( strstr( strrev($track['src']), '/' ) );
					$folderCparts = $this->grabFolderURLs( $path, 'oga|ogg|wav|webm|webma' );
					$track = $this->getFEEDCounterpart( $track, $folderCparts );
				}
			}
			if ( $track['title'] === '' ) {
				$track['title'] = ( $this->theSettings['hide_mp3extension'] == "true" ) ? preg_replace( '/\.(mp3|mp4|m4a|ogg|oga|wav|webm)$/i', "", $track['filename'] ) : $track['filename'];
			}
		}
		
		$track['title'] = str_replace('"', '\"', $track['title']); //escape quotes for js
		$track['caption'] = str_replace('"', '\"', $track['caption']); //escape quotes for js
	
		return $track;
	}


//###############
	function removeRemoteTracks ( $playlist )
	{
		$filtered = array();
		foreach ( $playlist as $track )
		{	
			if ( strpos($track['url'], $this->Rooturl) !== false 
				|| ( strpos($track['url'], 'http://') === false && strpos($track['url'], 'https://') === false )
				|| ( strpos($this->theSettings['mp3_dir'], "http://") !== false && strpos($track['url'], $this->theSettings['mp3_dir']) !== false ) )
			{
				$filtered[] = $track;			
			}
		}
		return $filtered;
	}


//################################
	function writePlaylistJS ( $tracks, $name = "noname", $numbering = false )
	{
		$count = count($tracks);
		if ( $count < 1 ) {
			return; 
		}
		
		$js = "[";
		$no = 1;
		$numdisplay = '';
		foreach ( $tracks as $tr )
		{	
			if ( $this->FIRST_FORMATS === false ) {
				$this->FIRST_FORMATS = $tr['formats'][0] . ( !empty($tr['formats'][1]) ? ','.$tr['formats'][1] : '' );
			}
			if ( $this->theSettings['encode_files'] == "true" ) {
				$tr['src'] = base64_encode( $tr['src'] );
				$tr['counterpart'] = base64_encode( $tr['counterpart'] );
			}
			
			$js .= "\n\t{ name: \"";
			if ( $this->theSettings['add_track_numbering'] == "true" ) { 
				$numdisplay = ( $numbering === false ) ? $no : $numbering;
				$js .= $numdisplay . ". ";
			}
			$js .= $tr['title']. "\", formats: [\"" .$tr['formats'][0] . "\"" . ( ! empty($tr['formats'][1]) ? ", \"".$tr['formats'][1]."\"" : "" ) . "], mp3: \"" .$tr['src']. "\", counterpart:\"" . $tr['counterpart'] . "\", artist: \"" .$tr['caption']. "\", image: \"" .$tr['image']. "\", imgurl: \"" .$tr['link']. "\" }";
			if ( $no != $count ) { 
				$js .= ","; 
			}
			$no++;
		}
		$js .= "\n]";
		
		$this->defineJSvars();
		return "<script>\nMP3jPLAYLISTS." .$name. " = " .$js. ";\n</script>\n\n";
	}


//~~~ COLLECTION ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//###############
	function customFieldsToTracks ( $id = "" )
	{
		if ( $id == "" ) { 
			global $post;
			$id = $post->ID;	
		}
		if ( empty( $id ) ) { 
			return false; 
		}		
		
		global $wpdb;
		$postmeta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE post_id =" .$id. " AND meta_value!='' ORDER BY meta_key ASC" );
		if ( ! empty( $postmeta ) )
		{
			$TRACKS = array();
			$Ks = array();
			$Vs = array();
			foreach ( $postmeta as $obj ) {
				if ( preg_match('/^([0-9]+(\s)?)?mp3(\..*)?$/', $obj->meta_key) == 1 ) { 
					$Ks[] = trim( $obj->meta_key );
					$Vs[] = trim( $obj->meta_value );
				}
			}
		
			if ( ! empty($Ks) )	{ 
				natcasesort($Ks); //sort by keys (user numbering)
				foreach ( $Ks as $i => $k ) {
					$track = $this->makeTrack( $Vs[$i] );
					$keyParts = explode('.', $k, 2);
					$track['caption'] = ( empty($keyParts[1]) ) ? '' : $keyParts[1];
					$TRACKS[] = $track;
				}			
				$TRACKS = $this->addFEEDtracks( $TRACKS );
				$PLAYLIST = $this->generatePlaylist( $TRACKS );
				return ( count( $PLAYLIST ) < 1 ? false : $PLAYLIST );
			}
		}
		return false;
	}
	
	
//###########################
	function cleanString ( $string, $delimiter )
	{
		$string = str_replace( array("</p>", "<p>", "<br />", "<br>", "<br/>", chr(10), chr(13)), "", $string );
		$string = trim( $string );
		$string = trim( $string, $delimiter );
		return $string;
	}

	
//###########################
	function stringToArray ( $string, $delimiter )
	{
		$items = explode( $delimiter, $string );
		foreach ( $items as $i => $item ) {
			$items[$i] = trim( $item ); 
		}
		//TODO:always return array
		return $items;
	}
	

//###########################
	function IDsToTracks ( $ids, $images = '' )
	{
		$ids = $this->cleanString( $ids, ',' );
		if ( empty( $ids ) ) {
			return false;
		}
				
		$IDs =  $this->stringToArray( $ids, ',' );
		$LIB = $this->grab_library_info();
		$TRACKS = array();

		if ( is_array($IDs) && is_array($LIB['postIDs']) )
		{
			$keyids = array();
			foreach ( $LIB['postIDs'] as $index => $postID ) {
				$keyids[$postID] = $index;
			}
			
			foreach ( $IDs as $i => $postID ) {	
				if ( array_key_exists($postID, $keyids) ) {
					$index = $keyids[$postID];
					$track = $this->makeTrack( $LIB['filenames'][$index] );
					if ( $images === 'true' ) {
						$track['image'] = $this->getPostImageUrl( $postID );
					}
					$TRACKS[] = $track;
				}
			}
		}
		$PLAYLIST = $this->generatePlaylist( $TRACKS );
		return ( count( $PLAYLIST ) < 1 ? false : $PLAYLIST );
	}


//###########################
	function stringsToTracks ( $files, $counterparts, $captions = '', $images = '', $imageLinks = '' )
	{
		$files = $this->cleanString( $files, $this->theSettings['f_separator'] );
		if ( empty( $files ) ) {
			return false;
		}
		
		$captions = $this->cleanString( $captions, $this->theSettings['c_separator'] );
		$images = $this->cleanString( $images, ',' );
		$imageLinks = $this->cleanString( $imageLinks, ',' );
		
		$F = $this->stringToArray( $files, $this->theSettings['f_separator'] );
		$C = $this->stringToArray( $captions, $this->theSettings['c_separator'] );
		$Cpt = $this->stringToArray( $counterparts, ',' );
		$I = $this->stringToArray( $images, ',' );
		$L = $this->stringToArray( $imageLinks, ',' );
		
		$TRACKS = array();
		foreach ( $F as $i => $f ) {	
			$cpt = ( empty( $Cpt[$i] ) ) ? "" : $Cpt[$i];
			$track = $this->makeTrack( $f, $cpt );
			$track['caption'] = ( empty( $C[$i] ) ) ? "" : $C[$i];
			//$track['image'] = ( empty( $I[$i] ) ) ? "" : $I[$i];
			$track['image'] = ( empty( $I[$i] ) ) ? ((!empty($I[0]) && $I[0] == 'true') ? $I[0] : "") : $I[$i];
			
			$track['link'] = ( empty( $L[$i] ) ) ? "" : $L[$i];
			$TRACKS[] = $track;
		}
		$TRACKS = $this->addFEEDtracks( $TRACKS );
		$PLAYLIST = $this->generatePlaylist( $TRACKS );
		return ( count( $PLAYLIST ) < 1 ? false : $PLAYLIST );
	}
	
	
//###########################
	function addFEEDtracks( $tracks )
	{
		$TRACKS = array();
		foreach ( $tracks as $t )
		{	
			if ( preg_match( "!^FEED:(DF|ID|LIB|/.*)$!i", $t['src'] ) == 1 ) { // keep ID for backwards compat
				$t['src'] = stristr( $t['src'], ":" );
				$t['src'] = str_replace( ":", "", $t['src'] );
				$feedTracks = $this->getFeed( $t );
				foreach ( $feedTracks as $j => $ft ) {
					$TRACKS[] = $ft;
				}
			}
			else {
				$TRACKS[] = $t;
			}
		}
		return $TRACKS;
	}


//###############	
	function isAllowedMPEG ( $file )
	{
		$allowed = false;
		$format = $this->getFileFormat( $file );
		if ( in_array( $format, $this->allowedFeedExtensions ) ) {
			$allowed = true;
		}
		return $allowed;
	}	
	
	
//###############
	function getFEEDCounterpart ( $TRACK, $LIB )
	{
		if ( ! is_array( $LIB ) ) {
			return $TRACK;
		}
		
		$fParts = explode( '.', $TRACK['filename'] );
		if ( empty($fParts[1]) || ($fParts[1] !== 'mp3' && $fParts[1] !== 'mp4' && $fParts[1] !== 'm4a') ) { //bail unless primary is mpeg
			return $TRACK;
		}
		
		$haystack = $LIB['filenames'];
		$tries = array( 'oga', 'ogg', 'webma', 'webm', 'wav' ); //prioritised extensions
		foreach ( $tries as $try ) {
			$needle = $fParts[0] . '.' . $try;
			$match = array_keys( $haystack, $needle );
			if ( is_array($match) && count($match) > 0 ) {
				$TRACK['counterpart'] = $LIB['urls'][ ($match[0]) ];
				$TRACK['formats'][1] = $this->getFileFormat( $TRACK['counterpart'] );
				break;
			}
		}
		return $TRACK;
	}

	
//############################
	function getFeed ( $track )
	{
		$TRACKS = array();
		if ( $track['src'] == "ID" )
		{ 
			// do nothing  since 1.5
		}
		elseif ( $track['src'] == "LIB" )
		{
			$lib = $this->grab_library_info(); //grab all
			if ( $lib ) {
				foreach ( $lib['filenames'] as $j => $file ) {
					if ( in_array( $lib['mimes'][$j], $this->allowedFeedMimes ) ) {
						$add = true;
						if ( $lib['mimes'][$j] === 'audio/mpeg' ) {
							$add = $this->isAllowedMPEG( $file );
						}
						if ( $add ) {
							$FEEDtr = $this->makeTrack( $file );
							$FEEDtr['caption'] = ( empty( $track['caption'] ) ) ? $lib['excerpts'][$j] : $track['caption'];
							//$FEEDtr['image'] = ( $track['image'] === 'true' ) ? $this->getPostImageUrl( $lib['postIDs'][$j] ) : ( $track['image'] === 'false' ? '' : $track['image'] );
							$FEEDtr['image'] = ( $track['image'] === 'true' ) ? $this->getPostImageUrl( $lib['postIDs'][$j] ) : $track['image'];
							$FEEDtr['link'] = ( empty( $track['link'] ) ) ? "" : $track['link'];
							if ( $this->theSettings['autoCounterpart'] === 'true' ) {
								$FEEDtr = $this->getFEEDCounterpart( $FEEDtr, $lib );
							}
							$TRACKS[] = $FEEDtr;
						}
					}
				}
			}
		}
		else
		{
			$folder = ( $track['src'] == "DF" ) ? $this->theSettings['mp3_dir'] : $track['src'];
			
			$folderInfo = $this->grabFolderURLs( $folder, $this->formatsFeedRegex ); //grab ticked
			$folderCparts = ( $this->theSettings['autoCounterpart'] === 'true' ) ? $this->grabFolderURLs( $folder, 'oga|ogg|wav|webm|webma' ) : false;
			$tracks = $folderInfo['files'];
			if ( $tracks !== true && $tracks !== false && count( $tracks ) > 0 ) {
				foreach ( $tracks as $j => $file ) {
					$FEEDtr = $this->makeTrack( $file );
					$FEEDtr['caption'] = $track['caption'];
					$FEEDtr['image'] = $track['image'];
					$FEEDtr['link'] = $track['link'];
					if ( $this->theSettings['autoCounterpart'] === 'true' ) {
						$FEEDtr = $this->getFEEDCounterpart( $FEEDtr, $folderCparts );
					}
					$TRACKS[] = $FEEDtr;
				}
			}
		}
		return $TRACKS;
	}

////
//###############
	function pickTracks ( $slicesize, $TRACKS )
	{
		$no = intval( trim($slicesize) );
		if ( $no < 1 || ! is_array( $TRACKS ) ) {
			return $TRACKS;
		}
		$count = count( $TRACKS );
		$no = ( $no > $count ) ? $count : $no;
		
		$picker = array();
		foreach ( $TRACKS as $i => $tr ) {
			$picker[] = $i;
		}
		shuffle( $picker );
		$picker = array_slice($picker, 0, $no);
		natsort( $picker );
		
		$PICKED = array();
		foreach ( $picker as $trIndex ) {
			$PICKED[] = $TRACKS[$trIndex];
		}
		return $PICKED;
	}

	
/*	Looks for any active widget that isn't ruled out by 
	the page filter. Returns true if finds a widget that will be building. */		
	function has_allowed_widget( $type )
	{
		global $_wp_sidebars_widgets;
		if ( empty($_wp_sidebars_widgets) ) {
			$SBsettings = get_option('sidebars_widgets', array());
		}
		else {
			$SBsettings = $_wp_sidebars_widgets;
		}
		if ( empty($SBsettings) || is_null($SBsettings) ) {
			return false;
		}
		$active = array();
		$scripts = false;
		foreach ( $SBsettings as $key => $arr ) { 
			if ( is_array($arr) && $key != "wp_inactive_widgets" ) {
				foreach ( $arr as $i => $widget ) {
					if ( strchr($widget, $type) ) {
						$active[] = $widget;
					} 
				}
			}
		}
		$this->dbug['arr'][] = $active;
		
		if ( !empty($active) ) { 
			$name = "widget_". $type;
			$ops = get_option($name);
			foreach ( $active as $i => $widget ) {
				$wID = strrchr( $widget, "-" );
				$wID = str_replace( "-", "", $wID );
				foreach ( $ops as $j => $arr ) {
					if ( $j == $wID ) {
						if ( !$this->page_filter($arr['restrict_list'], $arr['restrict_mode']) ) {
							$scripts = true;
							break 2;
						}
					}	
				}
			}
		}
		return $scripts;
	}
		
		
/*	Checks current page against widget page-filter settings.
	returns true if widget should be filtered out. */	
	function page_filter( $list, $mode ) {
		$f = false;
		if ( !empty($list) ) {
			$pagelist = explode( ",", $list );
			if ( !empty($pagelist) ) {
				foreach ( $pagelist as $i => $id ) { 
					$pagelist[$i] = str_replace( " ", "", $id ); 
				}
			}
			if ( !is_singular() ) { //look for 'index' or 'archive' or 'search'
				if ( $mode == "include" ) {
					if ( is_home() ) {
						if ( strpos($list, "index") === false ) { $f = true; }
					}
					if ( is_archive() ) {
						if ( strpos($list, "archive") === false ) { $f = true; }
					}
					if ( is_search() ) {
						if ( strpos($list, "search") === false ) { $f = true; }
					}
				}
				if ( $mode == "exclude" ) {
					if ( is_home() ) {
						if ( strpos($list, "index") !== false ) { $f = true; }
					}
					if ( is_archive() ) {
						if ( strpos($list, "archive") !== false ) { $f = true; }
					}
					if ( is_search() ) {
						if ( strpos($list, "search") !== false ) { $f = true; }
					}
				}
			} else { //check the id's against current page
				global $post;
				$thisID = $post->ID;
				if ( $mode == "include" ) {
					$f = true;
					foreach ( $pagelist as $i => $id ) {
						if ( $id == $thisID ) { $f = false; }
					}
					
					if ( is_single() ) {
						if ( strpos($list, "post") !== false ) {
							$f = false;
						}
					}
				}
				if ( $mode == "exclude" ) {
					foreach ( $pagelist as $i => $id ) {
						if ( $id == $thisID ) { $f = true; }
					}
					
					if ( is_single() ) {
						if ( strpos($list, "post") !== false ) {
							$f = true;
						}
					}
				}
			}
		}
		return $f;
	}		
		
		
/*	Checks whether current post ID 
	content contains a shortcode. */
	function has_shortcodes ( $shtype = "" )
	{ 	
		global $post;
		if ( empty($post->ID) ) {
			return false;
		}
		$success = false;
		global $wpdb;
		$content = $wpdb->get_results("SELECT post_content FROM $wpdb->posts WHERE ID=" . $post->ID );
		$con = $content[0]->post_content;
		
		if ( $shtype != "" ) { //check for it
			if ( strpos($con, $shtype) !== false ) { 
				//return true;
				$success = true;
			}
		} else { //check for all player making shortcodes
			if ( strpos($con, "[mp3-jplayer") !== false || strpos($con, "[mp3j") !== false || strpos($con, "[mp3t") !== false || strpos($con, "[mp3-popout") !== false ) {
				//return true;
				$success = true;
			}
			if ( $this->theSettings['replace_WP_playlist'] === 'true' ){
				if ( strpos($con, "[playlist") !== false ) {
					$success = true;
				}
			}
			if ( $this->theSettings['replace_WP_audio'] === 'true' || $this->theSettings['replace_WP_attached'] === 'true' ){
				if ( strpos($con, "[audio") !== false ) {
					$success = true;
				}
			}
			if ( $this->theSettings['replace_WP_embedded'] === 'true' ){
				if ( strpos($con, "[embed") !== false ) {
					$success = true;
				}
			}
		}
		//return false;
		return $success;
	}
	
/* Checks for a string in post content */
	function post_has_string ( $str = "" )
	{ 
		global $post;
		if ( empty($post->ID) ) {
			return false;
		}		
		global $wpdb;
		$content = $wpdb->get_results("SELECT post_content FROM $wpdb->posts WHERE ID=" . $post->ID );
		$con = $content[0]->post_content;
		$success = false;
		if ( $str !== '' ) //check for it
		{
			if ( strpos($con, $str) !== false ) {
				$success = true;
			}
		} 
		else //check replacement settings and look for relevant extensions
		{
			if ( $this->theSettings['replace_WP_embedded'] === 'true' || $this->theSettings['make_player_from_link'] === 'true' ) {
				$extensions = array( '.mp3', '.mp4', '.m4a', '.ogg', '.oga', '.wav', '.webm', '.webma' );
				foreach ( $extensions as $ext ) {
					if ( strpos($con, $ext) !== false ) {
						$success = true;
						break;
					}
				}
			}	
		}
		return $success;
	}


/*	Swaps out links for player shortcodes, hooked to the_content. */
	function replace_links ( $stuff = '' )
	{
		if ( ! $this->canRun() ) {
			return $stuff;
		}
		$needles = array( '\"', '{TEXT}', '{URL}' );
		$replacers = array( '"', '$6', '$2' );
		$remove = "/<a ([^=]+=['\"][^\"']+['\"] )*href=['\"](([^\"']+(\.mp3|\.m4a|\.oga|\.ogg|\.wav|\.webm)))['\"]( [^=]+=['\"][^\"']+['\"])*>([^<]+)<\/a>/i";
		$add = str_replace($needles, $replacers, $this->theSettings['make_player_from_link_shcode'] );
		
		return preg_replace( $remove, $add, $stuff );
	}


/*	Enqueues js and css scripts. */
	function add_Scripts( $theme )
	{
		$version = substr( get_bloginfo('version'), 0, 3);
		
		//jquery and jquery-ui
		//backward compat for older versions of WP, otherwise jquery components will just enqueue from wp-includes
		if ( $this->theSettings['disable_jquery_libs'] != "yes" ) {
			if ( $version >= 3.1 ) {
				wp_enqueue_script( 'jquery-ui-slider', $this->PluginFolder . '/js/wp-backwards-compat/ui.slider.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse' ), '1.8.10' );
			} 
			else { //pre WP 3.1
				wp_enqueue_script( 'jquery-ui-widget', $this->PluginFolder . '/js/wp-backwards-compat/ui.widget.js', array( 'jquery', 'jquery-ui-core' ), '1.8.10' );
				wp_enqueue_script( 'jquery-ui-mouse', $this->PluginFolder . '/js/wp-backwards-compat/ui.mouse.js', false, '1.8.10' );
				wp_enqueue_script( 'jquery-ui-slider', $this->PluginFolder . '/js/wp-backwards-compat/ui.slider.js', false, '1.8.10' );
			}
			wp_enqueue_script( 'jquery-touch-punch', $this->PluginFolder . '/js/wp-backwards-compat/jquery.ui.touch-punch.min.js', false, '0.2.2' );
			$this->dbug['str'] .= "\n\nScript request added (jQuery & UI)";
		} 
		else {
			$this->dbug['str'] .= "\n\nScripts are OFF (jQuery & UI)";
		}
		
		//jplayer and plugin js
		wp_enqueue_script( 'jplayer271', $this->PluginFolder . '/js/jquery.jplayer.min.2.7.1.js', false, '2.7.1' );
		wp_enqueue_script( 'mp3-jplayer', $this->PluginFolder . '/js/mp3-jplayer-2.3.1.js', false, '2.3.1' );
	
		$skins = $this->SKINS;
		if ( isset( $skins[ $theme ]['url'] ) ) {
			$themepath = $skins[ $theme ]['url'];
		} else { //fall back to default
			$themepath = $skins['defaultLight']['url'];
			$this->dbug['str'] .= "\nNo css, falling back to default";
		}
		
		if ( $this->setup['stylesheet'] === true ) {
			wp_enqueue_style( 'mp3-jplayer', $themepath, false, $this->version_of_plugin );
		}
		if ( $this->setup['cssHead'] === true ) {
			echo $this->writeColoursCSS();
		}
		$this->PP_css_url = $themepath;
		$this->SCRIPT_CALL = true;
		return;
	}
	
	
//~~~~~
	function writeColoursCSS ()
	{			
		$settings = $this->theSettings;
		$colours = $settings['colour_settings'];
		$imgDir = $this->PluginFolder . "/css/images/";		
		
		$CSS = "\n<style type=\"text/css\">\n";
		$CSS .= ".innertab { background-color:" . $colours['screen_colour'] . "; } \n";
		$CSS .= ".playlist-colour { background:" . $colours['playlist_colour'] . "; } \n";
		$CSS .= ".loadMI_mp3j, .loadB_mp3j, .load_mp3j { background:" . $colours['loadbar_colour'] . "; } \n";
		$CSS .= ".poscolMI_mp3j, .MIsliderVolume .ui-widget-header, .vol_mp3t .ui-widget-header, .vol_mp3j .ui-widget-header { background-color:" . $colours['posbar_colour'] . "; } \n";
		$CSS .= ".interface-mjp { color:" . $colours['screen_text_colour'] . "; } \n";
		$CSS .= ".player-track-title { font-size:" . $colours['font_size_1'] . "px; } \n";
		$CSS .= ".a-mjp { color:" . $colours['list_text_colour'] . "; background-image:none !important; } \n";
		$CSS .= ".a-mjp:hover { background-image:none !important; color:" . $colours['list_hover_colour'] . " !important; background:" . $colours['listBGa_hover'] . " !important; } \n";
		$CSS .= ".a-mjp.mp3j_A_current { background-image:none !important; color:" . $colours['list_current_colour'] . " !important; background:" . $colours['listBGa_current'] . " !important; } \n";
		$CSS .= ".a-mjp { font-size:" . $colours['font_size_2'] . "px; } \n";
		$CSS .= ".transport-MI div, .mp3j-popout-MI:hover, .playlist-toggle-MI:hover, .dloadmp3-MI.whilelinks { background-color:" . $colours['posbar_colour'] . "; border-color:" . $colours['posbar_colour'] . "; } \n";
		$CSS .= ".popout-text-mjp:hover { color:" . $colours['posbar_colour'] . "; } \n";
		$CSS .= "span.textbutton_mp3j, .transport-MI div, .transport-MI div:hover { color:" . $colours['list_current_colour'] . "; } \n";
		//$CSS .= "span.textbutton_mp3j:hover { color:" . $colours['list_hover_colour'] . "; } \n";
		$CSS .= ".mp3-tint, .Smp3-tint { background-color:" . ( $colours['indicator'] == "tint"  ? "#aaa" : $colours['posbar_colour'] ) . "; } \n";
		$CSS .= "</style>\n";
		
		return $CSS;
	}
	
	
//~~~~~
	function makeColourPropsJS ()
	{
		$O = $this->theSettings;
		$C = $O['colour_settings'];
		$ppBG = ( $O['popout_background'] == "" ) ? "#fff" : $O['popout_background'];
		
		$js = '			colours: [';
		$js .= '"' .$ppBG. '", ';
		$js .= '"' .$C['screen_colour']. '", ';
		$js .= '"' .$C['playlist_colour']. '", ';
		$js .= '"' .$C['loadbar_colour']. '", ';
		$js .= '"' .$C['posbar_colour']. '", ';
		$js .= '"' .$C['listBGa_hover']. '", ';
		$js .= '"' .$C['listBGa_current']. '", ';
		$js .= '"' .$C['screen_text_colour']. '", ';
		$js .= '"' .$C['list_text_colour']. '", ';
		$js .= '"' .$C['list_hover_colour']. '", ';
		$js .= '"' .$C['list_current_colour']. '", ';
		$js .= '"' .$O['popout_background_image']. '", ';
		$js .= $O['popout_width']. ', ';
		$js .= $O['popout_max_height'];
		$js .= ' ],';
		
		return $js;
	}
		

//~~~~~
	function drawPlaylistPlayer( $ATTS, $isPopoutLink = false )
	{
		$pID = $this->Player_ID;
		$O = $this->theSettings;
		$C = $O['colour_settings'];
		extract( $ATTS );

		//Prep inline css..
		$pad_t = $O['paddings_top'];
		$pad_b = $O['paddings_bottom'];
		$pad_i = $O['paddings_inner'];
		
		//..player alignment / width
		if ( $pos == "left" ) { 
			$floater = "float:left; padding:" . $pad_t . " " . $pad_i . " " . $pad_b . " 0px;";
		}
		else if ( $pos == "right" ) { 
			$floater = "float:right; padding:" . $pad_t . " 0px " . $pad_b . " " . $pad_i . ";";
		}
		else if ( $pos == "absolute" ) {
			$floater = "position:absolute;";
		}
		else if ( $pos == "rel-C" ) { 
			$floater = "position:relative; padding:" . $pad_t . " 0px " . $pad_b . " 0px; margin:0px auto 0px auto;"; 
		}
		else if ( $pos == "rel-R" ) { 
			$floater = "position:relative; padding:" . $pad_t . " 0px " . $pad_b . " 0px; margin:0px 0px 0px auto;"; 
		}
		else { 
			$floater = "position: relative; padding:" . $pad_t . " 0px " . $pad_b . " 0px; margin:0px;";
		}
		$width = ( $width == "" ) ? " width:" . $O['player_width'] . ";" : " width:" . $width . ";";
		
		//..other inline bits css/html
		$heightProp = 		( !empty($height) ) 		? $height : ""; //will just use css sheet setting if empty
		$title = 			( $title == "" ) 			? "" : "<h2>" . $title . "</h2>";
		$list = 			( $list == "true" ) 		? "HIDE" : "SHOW";
		$listtog_html = 	( $trackCount > 1 ) 			? "<div class=\"playlist-toggle-MI\" id=\"playlist-toggle_" . $pID. "\">" . $list . " PLAYLIST</div>" : "";
		//$showpopoutbutton = ( $O['enable_popout'] == "true" ) 		? "visibility: visible;" : "visibility: hidden;";
		$showpopoutbutton = ( $O['enable_popout'] == "true" ) 		? "" : "display:none;";
		$PLscroll = 		( $O['max_list_height'] != "" ) 		? " style=\"overflow:auto; max-height:" . $O['max_list_height'] . "px;\"" : "";
		//$popouttext = 		( $O['player_theme'] == "styleH" && $O['popout_button_title'] == "") ? "Pop-Out" : $O['popout_button_title'];
		$popouttext = $ATTS['pptext'];
		
		//Prep image handling css/html
		$imgCSS = '';			//inline css added to image wrapper
		$tweakerClass = '';		//class affecting image
		if ( 'autoW' === $ATTS['imagesize'] ) { 		//fit images to player width.
			$imgCSS .= ' width:100%; height:' .$height. ';';
			$ppImgW = '100%';
			$ppImgH = $height;
		}
		elseif ( 'autoH' === $ATTS['imagesize'] ) { 	//fit images to player height.
			$imgCSS .= ' width:auto; height:' .$height. ';';
			$tweakerClass = ' Himg';
			$ppImgW = 'auto';
			$ppImgH = $height;
		}
		elseif ( 'full' === $ATTS['imagesize'] ) {	 	//leave images alone.
			$imgCSS .= ' width:auto; height:' .$height. ';';
			$tweakerClass = ' Fimg';
			$ppImgW = 'auto';
			$ppImgH = $height;
		}
		else { 					//use specific WP media sizes, and set the player height.
			$dims = $this->getImageSizeWP( $ATTS['imagesize'] );
			$imgCSS .= ' width:' .$dims['width']. 'px; height:' .$dims['height']. 'px;';
			$heightProp = $dims['height']. 'px';
			$ppImgW = $dims['width']. 'px';
			$ppImgH = $dims['height']. 'px';
		}
		
		//Make class names
		$CSSext = "-mjp";
		$titleAlign =	' ' . $ATTS['titlealign'] . $CSSext;
		$listAlign = 	' ' . $ATTS['listalign'] . $CSSext;
		$imageAlign = 	' ' . $ATTS['imagealign'] . $CSSext;
		$ulClass = 		( $C['playlist_tint'] === 'none' ) 		? '' : ' ' . $C['playlist_tint'] . $CSSext;
		$font1Class = 	( $ATTS['font_family_1'] === 'theme' ) 	? '' : ' ' . $ATTS['font_family_1'] . $CSSext;
		$font2Class = 	( $ATTS['font_family_2'] === 'theme' ) 	? '' : ' ' . $ATTS['font_family_2'] . $CSSext;
		$posbarClass = 	( $C['posbar_tint'] === 'none' ) 		? '' : ' ' . $C['posbar_tint'] . $CSSext;
		$liClass = 		( $C['list_divider'] === 'none' ) 		? '' : ' ' . $C['list_divider'] . $CSSext;
		$titleBold = 	( $ATTS['titlebold'] === 'true' ) 		? ' bold' . $CSSext : ' norm' . $CSSext;
		$titleItalic =	( $ATTS['titleitalic'] === 'true' ) 	? ' italic' . $CSSext : ' plain' . $CSSext;
		$captionWeight =( $ATTS['captionbold'] === 'true' ) 	? ' childBold' . $CSSext : ' childNorm' . $CSSext;
		$captionItalic =( $ATTS['captionitalic'] === 'true' ) 	? ' childItalic' . $CSSext : ' childPlain' . $CSSext;
		$listWeight =	( $ATTS['listbold'] === 'true' ) 		? ' childBold' . $CSSext : ' childNorm' . $CSSext;
		$listItalic =	( $ATTS['listitalic'] === 'true' ) 		? ' childItalic' . $CSSext : ' childPlain' . $CSSext;
		$titleHide = 	( $C['titleHide'] === 'true' ) 		? ' titleHide' . $CSSext : '';
		
		$INTERFACE_CLASSES = $font1Class;
		$TITLE_CLASSES = $titleAlign . $titleBold . $titleItalic . $captionWeight . $captionItalic . $titleHide;
		$IMAGE_CLASSES =  $tweakerClass . $imageAlign;
		$POSCOL_CLASSES = $posbarClass;
		$UL_CLASSES = $ulClass . $font2Class . $liClass . $listWeight . $listItalic . $listAlign;
		
		$customFontSize1 = ( $ATTS['fontsize'] !== '' ) ? " font-size:" .$ATTS['fontsize']. ";" : "";
		$customTitleColour = ( $ATTS['titlecol'] !== '' ) ? " color:" .$ATTS['titlecol']. ";" : "";
		
		$ppTitleColour = ( $ATTS['titlecol'] !== '' ) ? $ATTS['titlecol'] : $C['screen_text_colour'];
		
		$ppINTERFACE_STYLE = 'cssInterface: { "color": "' .$ppTitleColour. '" },';
		$ppTITLE_STYLE = 'cssTitle: { "left": "' .$ATTS['titleoffset']. '", "right":"' .$ATTS['titleoffsetr']. '", "top":"' .$ATTS['titletop']. '" },';
		$ppIMAGE_STYLE = 'cssImage: { "overflow": "' .$ATTS['imgoverflow']. '", "width":"'.$ppImgW.'", "height":"'.$ppImgH.'"  },';
		$ppFONT_SIZES = 'cssFontSize: { "title": "' .$ATTS['font_size_1']. 'px", "caption": "' .( intval($ATTS['font_size_1']) * 0.7 ). 'px", "list": "' .$ATTS['font_size_2']. 'px" },';
				
		//if ( $this->PP_css_settings === '' ) {
			$PPcss = $this->makeColourPropsJS();
			$PPcss .= "\n\t\t\t" .$ppINTERFACE_STYLE . "\n\t\t\t" . $ppTITLE_STYLE . "\n\t\t\t" . $ppIMAGE_STYLE . "\n\t\t\t" . $ppFONT_SIZES;
			$PPcss .= "\n\t\t\tclasses: { interface:'" .$INTERFACE_CLASSES. "', title:'" .$TITLE_CLASSES. "', image:'" .$IMAGE_CLASSES. "', poscol:'" .$POSCOL_CLASSES. "', ul:'" .$UL_CLASSES. "' }";
			//$this->PP_css_settings = $PPcss;
		//}
		
		if ( $isPopoutLink ) {
			return array( 'html' => '', 'js' => $PPcss );
		}
		
		//Image html
		$img_html = '<div class="MI-image' . $IMAGE_CLASSES . '" id="MI_image_' .$pID. '" style="' .$imgCSS. ' overflow:' . $ATTS['imgoverflow'] . ';"></div>';		
		
		//Downloader html
		$dlframe_html = '';
		if ( $O['force_browser_dload'] == "true" )
		{
			$dlframe_html .= '<div id="mp3j_finfo_' . $pID . '" class="mp3j-finfo" style="display:none;">';
			$dlframe_html .= 	'<div class="mp3j-finfo-sleeve">';
			$dlframe_html .= 		'<div id="mp3j_finfo_gif_' . $pID . '" class="mp3j-finfo-gif"></div>';
			$dlframe_html .= 		'<div id="mp3j_finfo_txt_' . $pID . '" class="mp3j-finfo-txt"></div>';
			$dlframe_html .= 		'<div class="mp3j-finfo-close" id="mp3j_finfo_close_' . $pID . '">X</div>'; 
			$dlframe_html .= 	'</div>';
			$dlframe_html .= '</div>';
			$dlframe_html .= '<div id="mp3j_dlf_' . $pID . '" class="mp3j-dlframe" style="display:none;"></div>';
		}
		
		//Playlist html
		$list_html = "<div class=\"listwrap_mp3j\" id=\"L_mp3j_" . $pID . "\"" . $PLscroll . ">";
		$list_html .= 	"<div class=\"wrapper-mjp\">";
		$list_html .= 		"<div class=\"playlist-colour\"></div>";
		$list_html .= 		"<div class=\"wrapper-mjp\">";
		$list_html .= 			"<ul class=\"ul-mjp" . $UL_CLASSES . "\" id=\"UL_mp3j_" . $pID . "\"><li></li></ul>";
		$list_html .= 		"</div>";
		$list_html .= 	"</div>";
		$list_html .= "</div>";
		
		//Build the player
		$player = "\n <div id=\"wrapperMI_" . $pID . "\" class=\"wrap-mjp " . $userClasses . "\" style=\"" . $floater . $width . "\">" . $title;
		$player .= 		"\n\t<div style=\"display:none;\" class=\"Eabove-mjp\" id=\"Eabove-mjp_" .$pID. "\"></div>";
		$player .= 		"\n\t <div class=\"subwrap-MI\">";
		$player .= 			"\n\t\t <div class=\"jp-innerwrap\">";
		$player .= 				"\n\t\t\t <div class=\"innerx\"></div>";
		$player .= 				"\n\t\t\t <div class=\"innerleft\"></div>";
		$player .= 				"\n\t\t\t <div class=\"innerright\"></div>";
		$player .= 				"\n\t\t\t <div class=\"innertab\"></div>";
		$player .= 				"\n\t\t\t <div class=\"interface-mjp" . $INTERFACE_CLASSES . "\" style=\"height:" . $heightProp . ";" .$customTitleColour. "\" id=\"interfaceMI_" . $pID . "\">";
		$player .= 					"\n\t\t\t\t " .$img_html;
		$player .= 					"\n\t\t\t\t <div id=\"T_mp3j_" . $pID . "\" class=\"player-track-title" . $TITLE_CLASSES . "\" style=\"left:" . $ATTS['titleoffset'] . "; right:" . $ATTS['titleoffsetr'] . ";  top:" . $ATTS['titletop'] . ";" .$customFontSize1. "\"></div>";
		$player .= 					"\n\t\t\t\t <div class=\"bars_holder\">";
		$player .= 						"\n\t\t\t\t\t <div class=\"loadMI_mp3j\" id=\"load_mp3j_" . $pID . "\"></div>";
		$player .= 						"\n\t\t\t\t\t <div class=\"poscolMI_mp3j" . $POSCOL_CLASSES . "\" id=\"poscol_mp3j_" . $pID . "\"></div>";
		$player .= 						"\n\t\t\t\t\t <div class=\"posbarMI_mp3j\" id=\"posbar_mp3j_" . $pID . "\"></div>";
		$player .= 					"\n\t\t\t\t </div>";
		$player .= 					"\n\t\t\t\t <div id=\"P-Time-MI_" . $pID . "\" class=\"jp-play-time\"></div>";
		$player .= 					"\n\t\t\t\t <div id=\"T-Time-MI_" . $pID . "\" class=\"jp-total-time\"></div>";
		$player .= 					"\n\t\t\t\t <div id=\"statusMI_" . $pID . "\" class=\"statusMI\"></div>";
		$player .= 					"\n\t\t\t\t <div class=\"transport-MI\">" . $play_h . $stop_h . $prevnext . "</div>";
		$player .= 					"\n\t\t\t\t <div class=\"buttons-wrap-mjp\" id=\"buttons-wrap-mjp_" . $pID. "\">";
		$player .= 						"\n\t\t\t\t\t " . $listtog_html;
		$player .= 						"\n\t\t\t\t\t <div class=\"mp3j-popout-MI\" id=\"lpp_mp3j_" . $pID. "\" style=\"" .$showpopoutbutton. "\">" . $popouttext . "</div>";
		$player .= 						"\n\t\t\t\t\t " . $dload_html;
		$player .= 					"\n\t\t\t\t </div>";
		$player .= 				"\n\t\t\t </div>";
		$player .= 				"\n\t\t\t <div class=\"mjp-volwrap\">";
		$player .= 					"\n\t\t\t\t <div class=\"MIsliderVolume\" id=\"vol_mp3j_" . $pID . "\"></div>";
		$player .= 					"\n\t\t\t\t <div class=\"innerExt1\" id=\"innerExt1_" . $pID . "\"></div>";
		$player .= 					"\n\t\t\t\t <div class=\"innerExt2\" id=\"innerExt2_" . $pID . "\"></div>";
		$player .= 				"\n\t\t\t </div>";
		$player .= 			"\n\t\t </div>";
		$player .= 			"\n\t\t <div style=\"display:none;\" class=\"Ebetween-mjp\" id=\"Ebetween-mjp_" .$pID. "\"></div>";
		$player .= 			"\n\t\t " . $list_html;
		$player .= 		"\n\t </div>";
		$player .= 		"\n\t " . $dlframe_html;
		$player .= 		"\n\t <div class=\"mp3j-nosolution\" id=\"mp3j_nosolution_" . $pID . "\" style=\"display:none;\"></div>";
		$player .= 		"\n\t <div style=\"display:none;\" class=\"Ebelow-mjp\" id=\"Ebelow-mjp_" .$pID. "\"></div>";
		$player .= "\n </div> \n";
		
		//return $player;
		return array( 'html' => $player, 'js' => $PPcss );
	}
	
	
/*	Stores and returns 
	updated compatible options. */
	function getAdminOptions()
	{
		$colour_keys = array(
			'screen_colour' 	=> 'rgba(0, 0, 0, 0.13)',
			'loadbar_colour' 	=> 'rgba(5, 86, 0, 0.59)',
			'posbar_colour' 	=> 'rgb(12, 180, 0)',
			'posbar_tint' 		=> 'soften',
			'playlist_colour' 	=> 'rgba(16, 16, 16, 0.82)',
			'playlist_tint' 	=> 'lighten1',
			'list_divider' 		=> 'none',
			'screen_text_colour' => '#202020', 
			'list_text_colour' 	=> '#ffffff',
			'list_current_colour' => '#29f000',
			'list_hover_colour' => '#c8fdbd',
			'listBGa_current' 	=> 'transparent',
			'listBGa_hover' 	=> 'transparent',
			'font_size_1'			=> '23',
			'font_size_2'			=> '17',
			'font_family_1'			=> 'theme',
			'font_family_2'			=> 'theme',
			'titleAlign'	=> 'left',
			'titleOffset' 	=> '16px',
			'titleOffsetR'	=> '16px',
			'titleBold'		=> 'true',
			'titleHide'		=> 'false',
			'titleItalic'	=> 'false',
			'titleTop' 		=> '20px',
			'captionBold'		=> 'false',
			'captionItalic'		=> 'true',
			'listBold'		=> 'false',
			'listItalic'	=> 'false',
			'listAlign'		=> 'left',
			'imageAlign'	=> 'right',
			'imgOverflow' 	=> 'false',
			'userClasses' 	=> '',
			'indicator' 	=> 'colour',
			'adminBG' 			=> '#f6f6f6',
			'adminCheckerIMG' 	=> 'false',
			'adminIMG' 			=> $this->PluginFolder . '/css/admin/images/test-image.jpg',
			'adminSizer_w' 		=> '570px',
			'adminSizer_h' 		=> '320px'
		);
		
		$audioFormats = array(
			'mp3'  => 'true',
			'mp4'  => 'true',
			'ogg'  => 'false',
			'wav'  => 'false',
			'webm' => 'false'
		);
		
		$mp3FoxAdminOptions = array( // defaults
			'initial_vol' => '100',
			'auto_play' => 'false',
			'mp3_dir' => '/',
			'player_theme' => 'defaultDark',
			'allow_remoteMp3' => 'true',
			'player_float' => 'none',
			'player_onblog' => 'true',
			'playlist_show' => 'true',
			'remember_settings' => 'true',
			'hide_mp3extension' => 'true',
			'show_downloadmp3' => 'false',
			//'disable_template_tag' => 'false',
			'db_plugin_version' => $this->version_of_plugin,
			'custom_stylesheet' => $this->newCSScustom,
			'echo_debug' => 'false',
			'add_track_numbering' => 'false',
			'enable_popout' => 'true',
			'playlist_repeat' => 'false',
			'player_width' => '100%',
			'popout_background' => '#f0f0f0',
			'popout_background_image' => '',
			'colour_settings' => $colour_keys,
			//'use_fixed_css' => 'false',
			'paddings_top' => '5px',
			'paddings_bottom' => '30px',
			'paddings_inner' => '30px',
			'popout_max_height' => '600',
			'popout_width' => '400',
			'popout_button_title' => 'Popout',
			'max_list_height' => '450',
			'encode_files' => 'true',
			'library_sortcol' => 'file',
			'library_direction' => 'ASC',
			'disable_jquery_libs' => '',
			'run_shcode_in_excerpt' => 'false',
			'f_separator' 			=> ',',
			'c_separator' 			=> ';',
			'volslider_on_singles' 			=> 'false',
			'volslider_on_mp3j' 			=> 'false',
			'dload_text' 					=> 'Download',
			'loggedout_dload_text' 			=> 'Log in to download',
			'loggedout_dload_link' 			=> $this->WPinstallpath . '/wp-login.php',
			//'touch_punch_js' 				=> 'true',
			'force_browser_dload' 			=> 'true',
			'dloader_remote_path' 			=> '',
			'make_player_from_link' 		=> 'true',
			'make_player_from_link_shcode' 	=> '[mp3j track="{TEXT}@{URL}" volslider="y"]',
			'audioFormats' 					=> $audioFormats,
			'replace_WP_playlist' 			=> 'true',
			'replace_WP_audio' 				=> 'true',
			'replace_WP_embedded' 			=> 'true',
			'replace_WP_attached' 			=> 'true',
			'replacerShortcode_playlist' 	=> 'player',
			'replacerShortcode_single' 		=> 'mp3j',
			'imageSize' 		=> 'autoH',
			'folderFeedSortcol' => 'file',
			'folderFeedDirection' 	=> 'ASC',
			'autoCounterpart' 	=> 'true',
			'allowRangeRequests' 	=> 'true',
			'playerHeight' 		=> '92px',
			'font_size_mp3t' 	=> '18px',
			'font_size_mp3j' 	=> '18px',
			'showErrors'		=> 'admin'
		);
		
		$theOptions = get_option($this->adminOptionsName);							
		
		if ( ! empty($theOptions) )
		{
			if ( $theOptions['db_plugin_version'] !== $this->version_of_plugin ) //do compat
			{
				//backwards compat with v1.4 style
				$styleCompat = $this->do_style_compat( $theOptions['player_theme'], $theOptions['custom_stylesheet'] ); 
				if ( $styleCompat[0] )
				{
					$theOptions['player_theme'] = $styleCompat[0];
					$theOptions['custom_stylesheet'] = $styleCompat[2];
				}			
				//ditch un-needed stored settings
				foreach ( $theOptions as $key => $option )
				{
					if ( array_key_exists( $key, $mp3FoxAdminOptions) )
					{
						$mp3FoxAdminOptions[$key] = $option;
					}
				}
				
				//add in any new colour keys
				foreach ( $colour_keys as $key => $val )
				{
					if ( ! array_key_exists( $key, $mp3FoxAdminOptions['colour_settings'] ) )
					{
						$mp3FoxAdminOptions['colour_settings'][ $key ] = $val;
					}
				}
				
				//Pre v2 compatibility stuff
				$saved_version = intval( substr( $theOptions['db_plugin_version'], 0 ,1 ) );
				if ( $saved_version < 2 )
				{
					//fill-in any old default colours that weren't shown admin-side
					$mp3FoxAdminOptions['colour_settings'] = $this->do_colourkey_compat( $mp3FoxAdminOptions['colour_settings'], $mp3FoxAdminOptions['player_theme'] );
					
					//convert old separate colour and opacity fields to rgba in single field
					$mp3FoxAdminOptions['colour_settings'] = $this->areaColourReformat( $mp3FoxAdminOptions['colour_settings'] );
					
					//set a compatible default height
					$mp3FoxAdminOptions['playerHeight'] = ( "styleI" == $mp3FoxAdminOptions['player_theme'] ) ? '71px' : '62px';
					
					$mp3FoxAdminOptions['colour_settings']['titleTop'] = "8px";
					$mp3FoxAdminOptions['colour_settings']['font_size_1'] = "16";
					$mp3FoxAdminOptions['colour_settings']['font_size_2'] = "12";
					$mp3FoxAdminOptions['colour_settings']['font_family_1'] = "verdana";
					$mp3FoxAdminOptions['colour_settings']['font_family_2'] = "verdana";
					$mp3FoxAdminOptions['colour_settings']['posbar_tint'] = "none";
					$mp3FoxAdminOptions['font_size_mp3t'] = '14px';
					$mp3FoxAdminOptions['font_size_mp3j'] = '14px';
					
					$styleKey = $mp3FoxAdminOptions['player_theme'];
					
					if ( $styleKey == 'styleH' ) {
						$mp3FoxAdminOptions['player_theme'] = 'defaultText';
					} else {
						$mp3FoxAdminOptions['colour_settings']['titleBold'] = "false";
						$mp3FoxAdminOptions['colour_settings']['captionItalic'] = "false";
					}
					
					if ( $styleKey == 'styleF' || $styleKey == 'styleG' || $styleKey == 'styleH' ) {
						$mp3FoxAdminOptions['colour_settings']['userClasses'] = 'fullbars';
					} elseif ( $styleKey == 'styleI' ) {
						if ( $mp3FoxAdminOptions['custom_stylesheet'] === $this->newCSScustom ) {
							$mp3FoxAdminOptions['custom_stylesheet'] = '/';
							$mp3FoxAdminOptions['player_theme'] = 'styleF';
						}
					}
				}
				
				$mp3FoxAdminOptions['db_plugin_version'] = $this->version_of_plugin; //set last!
				update_option($this->adminOptionsName, $mp3FoxAdminOptions);
			}
			else
			{
				$mp3FoxAdminOptions = $theOptions;
			}
		}
		else //save new defaults
		{
			update_option($this->adminOptionsName, $mp3FoxAdminOptions);
		}		
		
		return $mp3FoxAdminOptions;
	}


/**
 *	Backwards compat function - Updates player skin
 *	options if user has old settings.
 */
	function do_style_compat ( $s, $path = "" ) 
	{
		//custom stlesheet
		$csspath = $this->PluginFolder . "/css/mp3jplayer-cyanALT.css"; //orig custom stylesheet in v1.4 
		$path = ( $path == $csspath || $path == "" ) ? $this->newCSScustom : $path;
		
		//player theme
		if ( "styleA" == $s ) { 	//orig 'neutral'
			$s = "styleF";
		} 
		elseif ( "styleB" == $s ) {	//orig 'green'
			$s = "styleF";
		} 
		elseif ( "styleC" == $s ) {	//orig 'blu'
			$s = "styleF";
		} 
		elseif ( "styleD" == $s ) {	//orig 'cyanALT', or custom css
			$s = "styleI";
		} 
		elseif ( "styleE" == $s ) {	//orig 'text'
			$s = "styleH";
		} 
		else { 
			$s = false; 
		}
		return array( $s, $path );
	}	
	
	
/**
 *	Backwards compat function (for pre v2 settings) - Puts any default colours (those that
 *	weren't specified in user's options) into the current settings for saving.
 */
	function do_colourkey_compat ( $current, $style )
	{
		//old default colour arrays
		$silver = array( 
			'screen_colour'			=> '#a7a7a7', 		
			'screen_opacity' 		=> '35',
			'loadbar_colour' 		=> '#34A2D9', 		
			'loadbar_opacity' 		=> '70',
			'posbar_colour' 		=> '#5CC9FF', 		
			'posbar_opacity' 		=> '80', 				
			'posbar_tint' 			=> 'softenT',
			'playlist_colour' 		=> '#f1f1f1', 	
			'playlist_opacity' 		=> '100', 			
			'playlist_tint' 		=> 'darken1', 		
			'list_divider' 			=> 'med',
			'screen_text_colour' 	=> '#525252', 
			'list_text_colour' 		=> '#525252', 	
			'list_current_colour' 	=> '#47ACDE', 	
			'list_hover_colour' 	=> '#768D99',
			'listBGa_current' 		=> 'transparent', 	
			'listBGa_hover'			=> 'transparent',
			'indicator' 			=> 'colour',
			'font_size_1'			=> '16',
			'font_size_2'			=> '14',
			'font_family_1'			=> 'theme',
			'font_family_2'			=> 'theme',
			'volume_grad' 			=> 'light'
		);
		$darkgrey = array( 
			'screen_colour' 		=> '#333', 			
			'screen_opacity' 		=> '15',
			'loadbar_colour' 		=> '#34A2D9', 		
			'loadbar_opacity' 		=> '70',
			'posbar_colour' 		=> '#5CC9FF', 		
			'posbar_opacity' 		=> '100', 				
			'posbar_tint' 			=> 'darken',
			'playlist_colour' 		=> '#fafafa', 	
			'playlist_opacity' 		=> '100', 			
			'playlist_tint' 		=> 'darken2', 		
			'list_divider' 			=> 'none',
			'screen_text_colour' 	=> '#525252', 
			'list_text_colour' 		=> '#525252', 	
			'list_current_colour' 	=> '#34A2D9', 	
			'list_hover_colour' 	=> '#768D99',
			'listBGa_current' 		=> 'transparent', 		
			'listBGa_hover' 		=> 'transparent',
			'indicator' 			=> 'colour',
			'font_size_1'			=> '16',
			'font_size_2'			=> '14',
			'font_family_1'			=> 'theme',
			'font_family_2'			=> 'theme',
			'volume_grad' 			=> 'dark'
		);
		$text = array(
			'screen_colour' 		=> 'transparent', 		
			'screen_opacity' 		=> '100',
			'loadbar_colour' 		=> '#aaa', 			
			'loadbar_opacity' 		=> '20',
			'posbar_colour' 		=> '#fff', 				
			'posbar_opacity' 		=> '58', 				
			'posbar_tint' 			=> 'none',
			'playlist_colour' 		=> '#f6f6f6', 		
			'playlist_opacity' 		=> '100', 			
			'playlist_tint' 		=> 'lighten2', 			
			'list_divider' 			=> 'none',
			'screen_text_colour' 	=> '#869399',
			'list_text_colour' 		=> '#777', 			
			'list_current_colour' 	=> '#47ACDE', 	
			'list_hover_colour' 	=> '#829FAD',
			'listBGa_current' 		=> 'transparent', 	
			'listBGa_hover' 		=> 'transparent',
			'indicator' 			=> 'tint',
			'font_size_1'			=> '16',
			'font_size_2'			=> '14',
			'font_family_1'			=> 'theme',
			'font_family_2'			=> 'theme',
			'volume_grad' 			=> 'dark'
		);
		
		switch( $style ) {	
			case "styleG": 
				$old_colours = $darkgrey;
				break;
			
			case "styleH":
				$old_colours = $text;
				break;
			
			default: 
				$old_colours = $silver;
		}
		
		foreach ( $old_colours as $key => $val ) {
			if ( array_key_exists( $key, $current ) && $current[ $key ] == '' ) {
				$current[ $key ] = $val;
			}
		}
		return $current;	
	}




//~~~~~
	function areaColourReformat ( $currentColours )
	{
		$c = $currentColours;
		//screen
		$rgb = $this->hexToRGB( $c['screen_colour'], true );
		$c['screen_colour'] = ( $c['screen_opacity'] >= '100' ) ? 'rgb(' .$rgb. ')' : 'rgba(' .$rgb. ',' .( intval($c['screen_opacity']) / 100 ). ')';
		
		//load
		$rgb = $this->hexToRGB( $c['loadbar_colour'], true );
		$c['loadbar_colour'] = ( $c['loadbar_opacity'] >= '100' ) ? 'rgb(' .$rgb. ')' : 'rgba(' .$rgb. ',' .( intval($c['loadbar_opacity']) / 100 ). ')';
		
		//pos
		$rgb = $this->hexToRGB( $c['posbar_colour'], true );
		$c['posbar_colour'] = ( $c['posbar_opacity'] >= '100' ) ? 'rgb(' .$rgb. ')' : 'rgba(' .$rgb. ',' .( intval($c['posbar_opacity']) / 100 ). ')';
		
		//playlist
		$rgb = $this->hexToRGB( $c['playlist_colour'], true );
		$c['playlist_colour'] = ( $c['playlist_opacity'] >= '100' ) ? 'rgb(' .$rgb. ')' : 'rgba(' .$rgb. ',' .( intval($c['playlist_opacity']) / 100 ). ')';
	
		return $c;
	}


	
//~~~~~
	function hexToRGB ( $hexStr, $returnAsString = false, $seperator = ',' )
	{
		$hexStr = preg_replace( "/[^0-9A-Fa-f]/", '', $hexStr ); // Gets a proper hex string
		$rgbArray = array();
		
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
	}

	
	
//~~~~~
	function mp3j_admin_header ()
	{
		wp_enqueue_style('mp3jp-settings-css', $this->PluginFolder .'/css/admin/admin-settings.css' );
		wp_enqueue_script('mp3jp-settings-js', $this->PluginFolder .'/js/admin/admin-settings.js' );
	}
		
		
//~~~~~
	function mp3j_admin_footer () {}
	
	
//~~~~~
	function mp3j_admin_colours_header ()
	{
		//scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget'); 		
		wp_enqueue_script('jquery-ui-mouse');  		
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-ui-resizable');
		wp_enqueue_script('mp3jp-colours-js', $this->PluginFolder .'/js/admin/admin-colours.js' );
		wp_enqueue_script('spectrum-CP-js', $this->PluginFolder .'/js/spectrum/spectrum.js' );
		
		//styles
		wp_enqueue_style('spectrum-CP-css', $this->PluginFolder .'/css/admin/spectrum.css' );
		wp_enqueue_style('mp3jp-colours-css', $this->PluginFolder .'/css/admin/admin-colours.css' );
	}
	

//~~~~~
	function prep_value ( $field )
	{	
		$search = array( "'", '"', '\\' );
		$option = str_replace( $search, "", $field );
		$option = strip_tags( $option );
		return $option;
	}
	

//~~~~~
	function strip_scripts ( $field )
	{ 
		$search = array(
			'@<script[^>]*?>.*?</script>@si',  // Strip out javascript 
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly 
			'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA 
		);
		$text = preg_replace( $search, '', $field ); 
		return $text; 
	}


//~~~~~
	function prep_path ( $field )
	{
		$search = array( "'", '"', ';', '\\' );
		$option = str_replace( $search, "", $field );
		$option = strip_tags( $option );
		$option = preg_replace( "!^www*\.!", "http://www.", $option ); //add default protocol if admin didn't
		
		if ( strpos($option, "http://") === false && strpos($option, "https://") === false) //if local path was entered
		{
			if (preg_match("!^/!", $option) == 0) { //no starting slash then add one
				$option = "/" . $option; 
			} else { 
				$option = preg_replace("!^/+!", "/", $option); //or just make sure theres only one
			} 
		}
		if (preg_match("!.+/+$!", $option) == 1) { //remove any ending slashes
			$option = preg_replace("!/+$!", "", $option); 
		}
		if ($option == "") { //set to domain root
			$option = "/";
		}
		return $option;
	}

	
//~~~~~
	function debug_info( $display = "" )
	{	
		echo "\n\n<!-- *** MP3-jPlayer - " . "version " . $this->version_of_plugin . " ***\n";
		if ( is_singular() ) { echo "\nTemplate: Singular "; }
		if ( is_single() ) { echo "Post"; }
		if ( is_page() ) { echo "Page"; }
		if ( is_search() ) { echo "\nTemplate: Search"; }
		if ( is_home() ) { echo "\nTemplate: Posts index"; }
		if ( is_front_page() ) { echo " (Home page)"; }
		if ( is_archive() ) { echo "\nTemplate: Archive"; }
		
		//echo "\nUse tags: ";
		//if ( $this->theSettings['disable_template_tag'] == "false" ) { 
		//	echo "Yes\n";
		//} else { 
		//	echo "No\n";
		//}
		echo $this->dbug['str'] . "\n";
		echo "\nPlayer count: " . $this->Player_ID;
		echo "\n\nAdmin Settings:\n"; 
		print_r($this->theSettings);
		
		$this->grab_library_info();
		echo "\nMP3's in Media Library: " . $this->LibraryI['count'] . "\n\n";
		print_r($this->LibraryI);
		
		echo "\n\nOther arrays:\n";
		foreach ( $this->dbug['arr'] as $i => $a ) {
			if ( is_array($a) ) {
				echo "\n" . $i . "\n";
				print_r($a);
			}
		}
		echo "\n-->\n\n";
		return;	
	}
	

}} // close class, close if.
?>