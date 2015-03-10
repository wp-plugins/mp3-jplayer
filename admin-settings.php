<?php

function mp3j_print_admin_page()
{ 
	
	global $MP3JP;
	$O = $MP3JP->getAdminOptions();
	$colours_array = array();
	
	
	if ( isset( $_POST['update_mp3foxSettings'] ) )
	{
		//prep/sanitize values
		if (isset($_POST['mp3foxVol'])) {
			$O['initial_vol'] = preg_replace("/[^0-9]/", "", $_POST['mp3foxVol']); 
			if ($O['initial_vol'] < 0 || $O['initial_vol']=="") { $O['initial_vol'] = "0"; }
			if ($O['initial_vol'] > 100) { $O['initial_vol'] = "100"; }
		}
		if (isset($_POST['mp3foxPopoutMaxHeight'])) {
			$O['popout_max_height'] = preg_replace("/[^0-9]/", "", $_POST['mp3foxPopoutMaxHeight']); 
			if ( $O['popout_max_height'] == "" ) { $O['popout_max_height'] = "750"; }
			if ( $O['popout_max_height'] < 200 ) { $O['popout_max_height'] = "200"; }
			if ( $O['popout_max_height'] > 1200 ) { $O['popout_max_height'] = "1200"; }
		}
		if (isset($_POST['mp3foxPopoutWidth'])) {
			$O['popout_width'] = preg_replace("/[^0-9]/", "", $_POST['mp3foxPopoutWidth']); 
			if ( $O['popout_width'] == "" ) { $O['popout_width'] = "400"; }
			if ( $O['popout_width'] < 250 ) { $O['popout_width'] = "250"; }
			if ( $O['popout_width'] > 1600 ) { $O['popout_width'] = "1600"; }
		}
		if (isset($_POST['mp3foxMaxListHeight'])) {
			$O['max_list_height'] = preg_replace("/[^0-9]/", "", $_POST['mp3foxMaxListHeight']); 
			if ( $O['max_list_height'] < 0 ) { $O['max_list_height'] = ""; }
		}
		if (isset($_POST['mp3foxfolder'])) { 
			$O['mp3_dir'] = $MP3JP->prep_path( $_POST['mp3foxfolder'] ); 
		}
		if (isset($_POST['mp3foxPopoutBGimage'])) { 
			$O['popout_background_image'] = $MP3JP->prep_path( $_POST['mp3foxPopoutBGimage'] );
		}
		
		$O['dloader_remote_path'] = ( isset($_POST['dloader_remote_path']) ) ? $MP3JP->prep_value( $_POST['dloader_remote_path'] ) : "";
		$O['loggedout_dload_link'] = ( $_POST['loggedout_dload_link'] == "" ) ? "" : $MP3JP->prep_value( $_POST['loggedout_dload_link'] ); //allow it to be empty
		
		if (isset($_POST['mp3foxFloat'])) {
			$O['player_float'] = $MP3JP->prep_value( $_POST['mp3foxFloat'] );
		}
		if (isset($_POST['librarySortcol'])) { 
			$O['library_sortcol'] = $MP3JP->prep_value( $_POST['librarySortcol'] );
		}
		if (isset($_POST['libraryDirection'])) {
			$O['library_direction'] = $MP3JP->prep_value( $_POST['libraryDirection'] );
		}
		if (isset($_POST['folderFeedSortcol'])) { 
			$O['folderFeedSortcol'] = $MP3JP->prep_value( $_POST['folderFeedSortcol'] );
		}
		if (isset($_POST['folderFeedDirection'])) {
			$O['folderFeedDirection'] = $MP3JP->prep_value( $_POST['folderFeedDirection'] );
		}
		if (isset($_POST['file_separator'])) {
			$O['f_separator'] = $MP3JP->prep_value( $_POST['file_separator'] );
		}
		if (isset($_POST['caption_separator'])) {
			$O['c_separator'] = $MP3JP->prep_value( $_POST['caption_separator'] );
		}
		if (isset($_POST['mp3foxDownloadMp3'])) { 
			$O['show_downloadmp3'] = $MP3JP->prep_value( $_POST['mp3foxDownloadMp3'] ); 
		}
		if (isset($_POST['replacerShortcode_playlist'])) {
			$O['replacerShortcode_playlist'] = $MP3JP->prep_value( $_POST['replacerShortcode_playlist'] );
		}
		if (isset($_POST['replacerShortcode_single'])) {
			$O['replacerShortcode_single'] = $MP3JP->prep_value( $_POST['replacerShortcode_single'] );
		}
		if (isset($_POST['showErrors'])) {
			$O['showErrors'] = $MP3JP->prep_value( $_POST['showErrors'] );
		}
		
		$O['echo_debug'] 			= ( isset($_POST['mp3foxEchoDebug']) ) 			? "true" : "false";
		$O['add_track_numbering'] 	= ( isset($_POST['mp3foxAddTrackNumbers']) ) 	? "true" : "false";
		$O['enable_popout'] 		= ( isset($_POST['mp3foxEnablePopout']) ) 		? "true" : "false";
		$O['playlist_repeat'] 		= ( isset($_POST['mp3foxPlaylistRepeat']) ) 	? "true" : "false";
		$O['encode_files'] 			= ( isset($_POST['mp3foxEncodeFiles']) ) 		? "true" : "false";
		$O['run_shcode_in_excerpt'] = ( isset($_POST['runShcodeInExcerpt']) ) 		? "true" : "false";
		$O['volslider_on_singles'] 	= ( isset($_POST['volslider_onsingles']) ) 		? "true" : "false";
		$O['volslider_on_mp3j'] 	= ( isset($_POST['volslider_onmp3j']) ) 		? "true" : "false";
		//$O['touch_punch_js'] 		= ( isset($_POST['touch_punch_js']) ) 			? "true" : "false";
		$O['force_browser_dload'] 	= ( isset($_POST['force_browser_dload']) ) 		? "true" : "false";
		$O['make_player_from_link']	= ( isset($_POST['make_player_from_link']) )	? "true" : "false";
		$O['auto_play'] 			= ( isset($_POST['mp3foxAutoplay']) ) 			? "true" : "false";
		$O['allow_remoteMp3'] 		= ( isset($_POST['mp3foxAllowRemote']) ) 		? "true" : "false";
		$O['player_onblog'] 		= ( isset($_POST['mp3foxOnBlog']) ) 			? "true" : "false";
		$O['playlist_show'] 		= ( isset($_POST['mp3foxShowPlaylist']) ) 		? "true" : "false";
		$O['remember_settings'] 	= ( isset($_POST['mp3foxRemember']) ) 			? "true" : "false";
		$O['hide_mp3extension'] 	= ( isset($_POST['mp3foxHideExtension']) ) 		? "true" : "false";
		$O['replace_WP_playlist'] 	= ( isset($_POST['replace_WP_playlist']) ) 		? "true" : "false";
		$O['replace_WP_audio'] 		= ( isset($_POST['replace_WP_audio']) ) 		? "true" : "false";
		$O['replace_WP_embedded'] 	= ( isset($_POST['replace_WP_embedded']) ) 		? "true" : "false";
		$O['replace_WP_attached'] 	= ( isset($_POST['replace_WP_attached']) ) 		? "true" : "false";
		$O['autoCounterpart'] 		= ( isset($_POST['autoCounterpart']) ) 			? "true" : "false";
		$O['allowRangeRequests'] 	= ( isset($_POST['allowRangeRequests']) ) 		? "true" : "false";
		
		$O['paddings_top'] 			= ( $_POST['mp3foxPaddings_top'] == "" ) 	? "0px" : $MP3JP->prep_value( $_POST['mp3foxPaddings_top'] );
		$O['paddings_bottom'] 		= ( $_POST['mp3foxPaddings_bottom'] == "" ) ? "0px" : $MP3JP->prep_value( $_POST['mp3foxPaddings_bottom'] );
		$O['paddings_inner'] 		= ( $_POST['mp3foxPaddings_inner'] == "" ) 	? "0px" : $MP3JP->prep_value( $_POST['mp3foxPaddings_inner'] );
		$O['font_size_mp3t'] 		= ( $_POST['font_size_mp3t'] == "" ) 		? "14px" : $MP3JP->prep_value( $_POST['font_size_mp3t'] );
		$O['font_size_mp3j'] 		= ( $_POST['font_size_mp3j'] == "" ) 		? "14px" : $MP3JP->prep_value( $_POST['font_size_mp3j'] );
		$O['dload_text'] 			= ( $_POST['dload_text'] == "" ) 			? "" : $MP3JP->strip_scripts( $_POST['dload_text'] );
		$O['loggedout_dload_text'] 	= ( $_POST['loggedout_dload_text'] == "" ) 	? "" : $MP3JP->strip_scripts( $_POST['loggedout_dload_text'] );
		
		$hasFormat = false;
		foreach ( $O['audioFormats'] as $k => $f ) {
			if ( isset($_POST['audioFormats'][$k]) ) {
				$O['audioFormats'][$k] = "true";
				$hasFormat = true;
			}
			else {
				$O['audioFormats'][$k] = "false";
			}
		}
		if ( ! $hasFormat ) {
			$O['audioFormats']['mp3'] = "true";
		}
		
		if (isset($_POST['mp3foxPlayerWidth'])) { 
			$O['player_width'] = $MP3JP->prep_value( $_POST['mp3foxPlayerWidth'] );
		}
		if (isset($_POST['disableJSlibs'])) {
			$O['disable_jquery_libs'] = ( preg_match("/^yes$/i", $_POST['disableJSlibs']) ) ? "yes" : "";
		}
		if ( isset($_POST['mp3foxPopoutButtonText']) ) {
			$O['popout_button_title'] = $MP3JP->strip_scripts( $_POST['mp3foxPopoutButtonText'] );
		}
		if ( isset($_POST['make_player_from_link_shcode']) ) {
			$O['make_player_from_link_shcode'] = $MP3JP->strip_scripts( $_POST['make_player_from_link_shcode'] );
		}
		if ( isset($_POST['mp3foxPopoutBackground']) ) { 
			$O['popout_background'] = $MP3JP->prep_value( $_POST['mp3foxPopoutBackground'] );
		}
		if ( isset($_POST['mp3foxPluginVersion']) ) { 
			$O['db_plugin_version'] = $MP3JP->prep_value( $_POST['mp3foxPluginVersion'] );
		}
		
		update_option($MP3JP->adminOptionsName, $O);
		$MP3JP->theSettings = $O;
		$MP3JP->setAllowedFeedTypesArrays();
		?>
		
		<!-- Settings saved message -->
		<div class="updated"><p><strong><?php _e("Settings Updated.", $MP3JP->textdomain );?></strong></p></div>
	<?php 
	}


	$current_colours = $O['colour_settings'];
	?>
	<div class="wrap">
		
		
		<h2>&nbsp;</h2>
		<h1>MP3-jPlayer
			<span class="description" style="font-size:10px;">Version <?php echo $MP3JP->version_of_plugin; ?></span>
			&nbsp;<span class="description" style="font-size:13px; font-weight:700;"><a class="button-secondary" style="background-color:#f0fff0;" target="_blank" href="http://mp3-jplayer.com/help-docs/">Help & Docs &raquo;</a></span>
		</h1>
		<p style="margin-bottom:10px;">&nbsp;</p>
		<?php 
		if ( $O['disable_jquery_libs'] == "yes" ) { 
			echo '&nbsp;<span style="font-size: 11px; font-weight:700; color:#f66;">(jQuery and UI scripts are turned off)</span>';
		} 
		?>
		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			
			
			<div class="mp3j-tabbuttons-wrap">
				<div class="mp3j-tabbutton" id="mp3j_tabbutton_1">Files</div>
				<div class="mp3j-tabbutton" id="mp3j_tabbutton_0">Players</div>
				<div class="mp3j-tabbutton" id="mp3j_tabbutton_3">Downloads</div>
				<div class="mp3j-tabbutton" id="mp3j_tabbutton_4">Popout</div>
				<div class="mp3j-tabbutton" id="mp3j_tabbutton_2">Advanced</div>
				<br class="clearB" />
			</div>
		
			
			<div class="mp3j-tabs-wrap">
				
				<!-- TAB 0.......................... -->
				<div class="mp3j-tab" id="mp3j_tab_0">
					<p class="tabD" style="margin:0 0 10px 0; max-width:550px;">These are the player default settings, most of them can be set per-player using <strong><a target="_blank" href="http://mp3-jplayer.com/shortcode-reference/">shortcode parameters</a></strong>.</p>
					
					<p style="margin-bottom:10px;"><label>Initial volume: &nbsp; </label><input type="text" style="text-align:center;" size="2" name="mp3foxVol" value="<?php echo $O['initial_vol']; ?>" /> &nbsp; <span class="description">(0 - 100)</span></p>
					<p><input type="checkbox" name="mp3foxAutoplay" id="mp3foxAutoplay" value="true" <?php if ($O['auto_play'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); } ?> /><label for="mp3foxAutoplay"> &nbsp; Autoplay</label> &nbsp; <span class="description">(Disallowed by most touchscreen devices, this will only activate on desktops and laptops)</span></p>
					<p><input type="checkbox" name="mp3foxPlaylistRepeat" id="mp3foxPlaylistRepeat" value="true" <?php if ($O['playlist_repeat'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); } ?> /><label for="mp3foxPlaylistRepeat"> &nbsp; Loop</label></p>
					<p><input type="checkbox" name="mp3foxAddTrackNumbers" id="mp3foxAddTrackNumbers" value="true" <?php if ($O['add_track_numbering'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); } ?> />
						<label for="mp3foxAddTrackNumbers"> &nbsp; Number the tracks</label></p>
					
					<br><br>
					<div style="float:left; width:260px; margin-right:10px;">
					
						<div style="background:#e9e9e9; border-bottom:1px solid #fff; padding:4px 0 4px 10px; margin:0 0 5px 0;">Single-File Text Players</div>
						<table style="margin:0 0 0px 10px;">
							<tr>
								<td><strong>Font Size</strong>:</td>
								<td><input type="text" value="<?php echo $O['font_size_mp3t']; ?>" name="font_size_mp3t" style="width:70px;" /></td>
							</tr>
							<tr>
								<td><label for="volslider_onsingles"><strong>Volume Control</strong>: &nbsp;</label></td>
								<td><input type="checkbox" name="volslider_onsingles" id="volslider_onsingles" value="true" <?php if ($O['volslider_on_singles'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> /></td>
							</tr>
						</table>
					</div>
					
					<div style="float:left; width:260px;">
						<div style="background:#e9e9e9; border-bottom:1px solid #fff; padding:4px 0 4px 10px; margin:0 0 5px 0;">Single-File Button Players</div>
						<table style="margin:0 0 0px 10px;">
							<tr>
								<td><strong>Font Size</strong>:</td>
								<td><input type="text" value="<?php echo $O['font_size_mp3j']; ?>" name="font_size_mp3j" style="width:70px;" /></td>
							</tr>
							<tr>
								<td><label for="volslider_onmp3j"><strong>Volume Control</strong>: &nbsp;</label></td>
								<td><input type="checkbox" name="volslider_onmp3j" id="volslider_onmp3j" value="true" <?php if ($O['volslider_on_mp3j'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> /></td>
							</tr>
						</table>
					</div>
					
					<br class="clearB"><br><br>
					<div style="background:#e9e9e9; border-bottom:1px solid #fff; padding:4px 0 4px 10px; margin:0 0 5px 0; width:520px;">Playlist Players</div>
					<table style="margin:0 0 0px 10px;">
						<tr>
							<td><strong>Width:</strong></td>
							<td><input type="text" style="width:100px;" name="mp3foxPlayerWidth" value="<?php echo $O['player_width']; ?>" /></td>
							<td><span class="description">pixels (px) or percent (%)</span></td>
						</tr>
						<tr>
							<td><strong>Alignment:</strong></td>
							<td><select name="mp3foxFloat" style="width:100px;">
									<option value="none" <?php if ( 'none' == $O['player_float'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Left</option>
									<option value="rel-C" <?php if ( 'rel-C' == $O['player_float'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Centre</option>
									<option value="rel-R" <?php if ( 'rel-R' == $O['player_float'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Right</option>
									<option value="left" <?php if ( 'left' == $O['player_float'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Float Left</option>
									<option value="right" <?php if ( 'right' == $O['player_float'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Float Right</option>
								</select></td>
							<td></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><strong>Margins:</strong></td>
							<td colspan="2">&nbsp;<span class="description">pixels (px) or percent (%)</span></td>
						</tr>
						<tr>
							<td></td>
							<td colspan="2"><input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_top" value="<?php echo $O['paddings_top']; ?>" /> Above players<br />
								<input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_inner" value="<?php echo $O['paddings_inner']; ?>" /> Inner margin (floated players)<br />
								<input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_bottom" value="<?php echo $O['paddings_bottom']; ?>" /> Below players</td>
						</tr>
					</table>
					<br>
					
					<p style="margin:0 0 6px 15px;"><input type="checkbox" name="mp3foxEnablePopout" id="mp3foxEnablePopout" value="true" <?php if ($O['enable_popout'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> />
						<label for="mp3foxEnablePopout"> &nbsp; Show popout player button</label></p>
					
					<p style="margin:0 0 0px 15px;"><input type="checkbox" name="mp3foxShowPlaylist" id="mp3foxShowPlaylist" value="true" <?php if ($O['playlist_show'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> />
						<label for="mp3foxShowPlaylist"> &nbsp; Start with playlists open</label></p>
					
					
					<p style="margin:5px 0 0 45px;"><label>Max playlist height:</label>
						&nbsp; <input type="text" size="6" style="text-align:center;" name="mp3foxMaxListHeight" value="<?php echo $O['max_list_height']; ?>" />
						px &nbsp; <span class="description">(a scroll bar will show for longer playlists, leave it blank for no limit)</span></p>
					
				</div><!-- CLOSE TAB -->
			
				
				
				<!-- TAB 1......................... -->
				<div class="mp3j-tab" id="mp3j_tab_1">
					
					<?php
					////
					//Library
					$library = $MP3JP->grab_library_info();
					$libCount = ( $library ) ? $library['count'] : "0";
					$libText = '';
					$libButton = '';
					$liblist = '';
					
					$libText .= '<span class="tabD" style="margin:0 0 10px 0;">Library contains <strong>' . $libCount . '</strong> audio file' . ( $libCount != 1 ? 's' : '' ) . '</span>. &nbsp;<strong><a href="media-new.php">Upload new &raquo;</a></strong>';
					
					if ( $libCount > 0 )
					{
						$libButton .= '<a class="button-secondary" href="javascript:" onclick="jQuery(\'#library-list\').toggle();" id="library-open">View files</a>';
						$n = 1;
						
						$liblist .= '<div id="library-list" style="display:none;"><table class="fileList">';
						$liblist .= 	'<tr>';
						$liblist .= 		'<th>&nbsp;</th>';
						$liblist .= 		'<th>&nbsp;</th>';
						$liblist .= 		'<th>Filename</th>';
						$liblist .= 		'<th>&nbsp;&nbsp;Title</th>';
						$liblist .= 		'<th>&nbsp;&nbsp;Caption&nbsp;&nbsp;&nbsp;&nbsp;</th>';
						$liblist .= 		'<th>Uploaded&nbsp;&nbsp;&nbsp;&nbsp;</th>';
						$liblist .= 	'</tr>';								
						foreach ( $library['filenames'] as $i => $file )
						{	
							$niceDate = date( 'jS F Y', strtotime($library['postDates'][$i]) );
							$liblist .= '<tr>';
							$liblist .= 	'<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="post.php?post=' . $library['postIDs'][$i] . '&amp;action=edit" target="_blank">Edit</a>&nbsp;&nbsp;</td>';
							$liblist .= 	'<td><span style="color:#aaa;font-size:11px;">' . $n . '&nbsp;</span></td>';
							$liblist .= 	'<td>' . $file . '</td>';
							$liblist .= 	'<td><span style="color:#aaa;">&nbsp;&nbsp;' . $library['titles'][$i] . '</span>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
							$liblist .= 	'<td><span style="color:#aaa;">&nbsp;&nbsp;' . $library['excerpts'][$i] . '</span>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
							$liblist .= 	'<td><span style="color:#aaa; font-size:11px;">' . $niceDate . '</span>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
							$liblist .= '</tr>';
							$n++;
						}
						$liblist .= '</table></div>';
					}
					
					////
					//Default Folder
					$n = 1;
					$folderInfo = $MP3JP->grabFolderURLs( $O['mp3_dir'] ); //grab all
					$folderText = '';
					$folderButton = '';
					$folderHtml = '';
					
					if ( is_array($folderInfo) )
					{
						$folderuris = $folderInfo['files'];
						$uploadDates = $folderInfo['dates'];
						foreach ( $folderuris as $i => $uri ) {
							$files[$i] = strrchr( $uri, "/" );
							$files[$i] = str_replace( "/", "", $files[$i] );
						}
						$c = (!empty($files)) ? count($files) : 0;
						
						$folderText .= "<span class=\"tabD\">This folder contains <strong>" . $c . "</strong> audio file" . ( $c != 1 ? 's' : '' ) . "</span>";
						
						if ( $c > 0 ) {
							$folderButton .= '<a class="button-secondary" href="javascript:" onclick="jQuery(\'#folder-list\').toggle();">View files</a>';
							
							$folderHtml .= '<div id="folder-list" style="display:none;">';
							$folderHtml .= '<table class="fileList">';
							$folderHtml .= 	'<tr>';
							$folderHtml .= 		'<th>&nbsp;</th>';
							$folderHtml .= 		'<th>Filename</th>';
							$folderHtml .= 		'<th>&nbsp;</th>';
							$folderHtml .= 		'<th>Uploaded</th>';
							$folderHtml .= 	'</tr>';
							foreach ( $files as $i => $val ) 
							{										
								$niceDate = date( 'jS F Y', $uploadDates[$i] );
								$folderHtml .= 	'<tr>';
								$folderHtml .= 		'<td><span style="color:#aaa;font-size:11px;">' . $n . '</span></td>';
								$folderHtml .= 		'<td>' . $val . '</td>';
								$folderHtml .= 		'<td>&nbsp;</td>';
								$folderHtml .= 		'<td><span class="description">' . $niceDate . '</span></td>';
								$folderHtml .= 	'</tr>';
								$n++;
							}
							$folderHtml .= '</table>';
							$folderHtml .= '</div>';
						}
					}
					elseif ( $folderInfo == true ) {
						$folderText .= "<p class=\"tabD\">Unable to read or locate the folder <code>" . $O['mp3_dir'] . "</code> check the path and folder permissions</p>";
					} 
					else { 
						$folderText .= "<p class=\"tabD\">No info is available on remote folders but you can play from here if you know the filenames</p>"; 
					}
					?>						
					
					
					<!-- File Lists -->
					<table>
						<tr>
							<td style="width:100px;"><strong>Media Library</strong>&nbsp;&nbsp;</td>
							<td style="width:100px;"><?php echo $libButton; ?>&nbsp;&nbsp;</td>
							<td><?php echo $libText; ?></td>
						</tr>
					</table>	
					<?php echo $liblist; ?>
					<br><hr />
					
					<table> 
						<tr>
							<td style="width:100px;"><strong>Default Folder</strong>&nbsp;&nbsp;</td>
							<td style="width:100px;"><?php echo $folderButton; ?>&nbsp;&nbsp;</td>
							<td><strong>Path:</strong> <input type="text" style="width:250px;" name="mp3foxfolder" value="<?php echo $O['mp3_dir']; ?>" /> &nbsp; <strong><a href="javascript:" onclick="jQuery('#folderHelp').toggle(300);">Help..</a></strong></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $folderText; ?></td>
						</tr>
					</table>
					<?php echo $folderHtml; ?>
					
					<div id="folderHelp" class="helpBox" style="display:none; margin:10px 0 0 30px; max-width:550px;">
						<p class="description">You can specify a default folder path (local or remote) to play audio from, eg. <code>/my/music</code> or <code>www.anothersite.com/music</code>.
							You can then just write filenames in playlists to play from here (you don't need a full url).</p>
					</div>
					<hr /><br>
					
					
				
					<!-- Auto Counterpart -->
					<label for="autoCounterpart" style="margin:0px 0 0 0px;">Auto-find counterpart files &nbsp; </label>
					<input type="checkbox" name="autoCounterpart" id="autoCounterpart" value="true" <?php echo ( $O['autoCounterpart'] === "true" ? 'checked="checked"' : ''); ?>/>
					
					<p class="description" style="margin:10px 0 0 0px;">This will pick up a fallback format if it's in the same location as the playlisted track, based on a 
						filename match. <strong><a href="javascript:" onclick="jQuery('#counterpartHelp').toggle(300);">Help..</a></strong></p> 
					
					<div id="counterpartHelp" class="helpBox" style="display:none; margin:10px 0 0 30px; max-width:550px;">
						<p class="description" style="margin-bottom:10px;">With this option ticked, the plugin will automatically look for counterpart files for any players on a page. The 
							playlisted (primary) track must be from the MPEG family (an mp3, m4a, or mp4 file).</p>
						
						<p class="description" style="margin-bottom:10px;">Auto-counterparting works for MPEGS in the library, in local folders, and when using bulk play or FEED commands.
							Just make sure your counterparts have the same filename, and are in the same location as the primary track. You can always manually add a counterpart to any 
							primary track format by using the <code>counterpart</code> parameter in a shortcode and specifying a url.</p>
						
						<p class="description" style="margin-bottom:10px;">Automatic Counterparts are chosen with the following format priority: OGG, WEBM, WAV.</p>
					</div>
					
				
					<br><br>
					<p><strong>Bulk-Play Settings</strong>
						<br><span class="description">Choose which audio formats are playlisted when bulk-playing from folders, the library, and via the</span> <code>FEED</code> <span class="description">command in playlists.</span></p>
					
					<p style="margin:12px 0 20px 0;">
						<?php
						foreach ( $O['audioFormats'] as $k => $f )
						{
							echo '<input class="formatChecker" type="checkbox" name="audioFormats[' .$k. ']" id="audioFormats_' .$k. '" value="true"' . ( $f === 'true' ? ' checked="checked"' : '' ) . '/>';
							echo '<label for="audioFormats_' .$k. '">' .$k. '</label> &nbsp;&nbsp;&nbsp;&nbsp;';
						}
						?>
					</p>
					
					<table style="margin-left:30px;">								
						<tr>
							<td>Order Library by:&nbsp;&nbsp;</td>
							<td>
								<select name="librarySortcol" style="width:160px;">
									<option value="file" <?php if ( 'file' == $O['library_sortcol'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Filename</option>
									<option value="date" <?php if ( 'date' == $O['library_sortcol'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Date Uploaded</option>
									<option value="caption" <?php if ( 'caption' == $O['library_sortcol'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Caption, Title</option>
									<option value="title" <?php if ( 'title' == $O['library_sortcol'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Title</option>
								</select>&nbsp;&nbsp;
							</td>
							<td>&nbsp; Direction: &nbsp;</td>
							<td>
								<select name="libraryDirection" style="width:100px;">
									<option value="ASC" <?php if ( 'ASC' == $O['library_direction'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Asc</option>
									<option value="DESC" <?php if ( 'DESC' == $O['library_direction'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Desc</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>Order Folders by:&nbsp;&nbsp;</td>
							<td>
								<select name="folderFeedSortcol" style="width:160px;">
									<option value="file" <?php if ( 'file' == $O['folderFeedSortcol'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Filename</option>
									<option value="date" <?php if ( 'date' == $O['folderFeedSortcol'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Date Uploaded</option>
								</select>&nbsp;&nbsp;
							</td>
							<td>&nbsp; Direction: &nbsp;</td>
							<td>
								<select name="folderFeedDirection" style="width:100px;">
									<option value="ASC" <?php if ( 'ASC' == $O['folderFeedDirection'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Asc</option>
									<option value="DESC" <?php if ( 'DESC' == $O['folderFeedDirection'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Desc</option>
								</select>
							</td>
						</tr>
					</table>
					
					<br />
					<p style="margin-left:30px;"><span class="description" id="feedCounterpartInfo"></span></p>
					
					
				</div><!-- CLOSE FILES TAB -->
				
				
				
				<!-- DOWNLOADS TAB .......................... -->
				<div class="mp3j-tab" id="mp3j_tab_3">
					<p class="tabD" style="margin:0 0 10px 0;">Download buttons are shown on playlist players, use these options to set their behavior.</p>
					
					<table>
						<tr>
							<td><strong>Show Download Button</strong>:</td>
							<td><select name="mp3foxDownloadMp3" style="width:150px;">
									<option value="true" <?php if ( 'true' == $O['show_downloadmp3'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>Yes</option>
									<option value="false" <?php if ( 'false' == $O['show_downloadmp3'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>No</option>
									<option value="loggedin" <?php if ( 'loggedin' == $O['show_downloadmp3'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>To logged in users</option>
								</select></td>
						</tr>
						<tr>
							<td><strong>Button Text</strong>:</td>
							<td><input type="text" style="width:150px;" name="dload_text" value="<?php echo $O['dload_text']; ?>" /></td>
						</tr>
						
					</table>
					
					
					<p class="description" style="margin:20px 0 5px 0px; max-width:400px;">When setting any players for logged-in downloads, use the following options to set the text/link for any logged out visitors.</p>
					<table>
						<tr>
							<td><strong>Visitor Text</strong>:</td>
							<td>
								<input type="text" style="width:150px;" name="loggedout_dload_text" value="<?php echo $O['loggedout_dload_text']; ?>" />
							</td>
						</tr>
						<tr>
							<td><strong>Visitor Link</strong>:</td>
							<td>
								<input type="text" style="width:300px;" name="loggedout_dload_link" value="<?php echo $O['loggedout_dload_link']; ?>" />
								&nbsp; <span class="description">Optional URL for the visitor text</span>
							</td>
						</tr>
						
					</table>
					
					<br><br>
					<input type="checkbox" name="force_browser_dload" id="force_browser_dload" value="true" <?php if ($O['force_browser_dload'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> />
					&nbsp;<label for="force_browser_dload">Smooth Downloads</label>
					<p class="description" style="margin:10px 0 10px 30px; max-width:500px;">This makes downloading seamless for most users, or it will display a dialog box with a link when a seamless download is not possible.</p>
					
					<p class="description" style="margin:10px 0 10px 30px; max-width:500px;">If you play from other domains and want seamless downloads, then use 
						the field below to specify a path to the downloader file. <strong><a href="<?php echo $MP3JP->PluginFolder; ?>/remote/help.txt">See help on setting this up</a></strong></p>
					
					<table style="margin-left:25px;">
						<tr>
							<td><strong>Path to remote downloader file</strong>:</td>
							<td>
								<input type="text" style="width:150px;" name="dloader_remote_path" value="<?php echo $O['dloader_remote_path']; ?>" />
							</td>
						</tr>
					</table>
					
					
				</div>
				
				
				
				
				
				<!-- POPOUT TAB .......................... -->
				<div class="mp3j-tab" id="mp3j_tab_4">
					
					<table style="margin-left:0px;">
						<tr>
							<td><strong>Launch Button Text</strong>:</td>
							<td><input type="text" style="width:150px;" name="mp3foxPopoutButtonText" value="<?php echo $O['popout_button_title']; ?>" /><span class="description"> &nbsp;The default text shown on popout links and buttons.</span></td>
						</tr>
						<tr>
							<td><strong>Window Width</strong>:</td>
							<td><input type="text" size="4" style="text-align:center;" name="mp3foxPopoutWidth" value="<?php echo $O['popout_width']; ?>" /> px <span class="description">&nbsp; (250 - 1600)</span></td>
						</tr>
						<tr>
							<td><strong>Window Height</strong>: &nbsp;</td>
							<td><input type="text" size="4" style="text-align:center;" name="mp3foxPopoutMaxHeight" value="<?php echo $O['popout_max_height']; ?>" /> px <span class="description">&nbsp; (200 - 1200) &nbsp; a scroll bar will show for longer playlists</span></td>
						</tr>
						<tr>
							<td><strong>Background Colour</strong>:</td>
							<td><input type="text"name="mp3foxPopoutBackground" style="width:100px;" value="<?php echo $O['popout_background']; ?>" /></td>
						</tr>
						<tr>
							<td><strong>Background Image</strong>:</td>
							<td><input type="text" style="width:100%;" name="mp3foxPopoutBGimage" value="<?php echo $O['popout_background_image']; ?>" /></td>
						</tr>
					</table>
				</div><!-- CLOSE POPOUT TAB -->
				
				
				<!--  TAB 2.......................... -->
				<div class="mp3j-tab" id="mp3j_tab_2">
					<?php $greyout_text = ( $O['disable_jquery_libs'] == "yes" ) ? ' style="color:#d6d6d6;"' : ''; ?>
					<p class="tabD" style="margin:0 0 10px 0;">Choose which aspects of your content you'd like MP3-jPlayer to handle.</p>
					
					<table>
						<tr>
							<td><input type="checkbox" name="replace_WP_audio" id="replace_WP_audio" value="true" <?php if ($O['replace_WP_audio'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); } ?> /> 
								&nbsp; <label for="replace_WP_audio">Audio Players</label></td>
							<td><span class="description">Use the 'Add Media' Button on post/page edit screens and choose 'Embed Player' from the right select (WP 3.6+).</span></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="replace_WP_playlist" id="replace_WP_playlist" value="true" <?php if ($O['replace_WP_playlist'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); } ?> /> 
								&nbsp; <label for="replace_WP_playlist">Playlist Players</label></td>
							<td><span class="description">Use the 'Add Media' Button on post/page edit screens and choose 'Audio Playlist' from the left menu (WP 3.9+).</span></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="make_player_from_link" id="make_player_from_link" value="true" <?php if ($O['make_player_from_link'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); } ?> /> 
								&nbsp; <label for="make_player_from_link">Links to Audio Files</label> &nbsp;</td>
							<td><span class="description">Links within post/page content will be turned into players using the shortcode specified under the 'Advanced' tab.</span></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="replace_WP_attached" id="replace_WP_attached" value="true" <?php if ($O['replace_WP_attached'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); } ?> /> 
								&nbsp; <label for="replace_WP_attached">Attached Audio</label></td>
							<td><span class="description">Use the shortcode <code>[audio]</code> in posts and pages to playlist any attached audio.</span></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="replace_WP_embedded" id="replace_WP_embedded" value="true" <?php if ($O['replace_WP_embedded'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); } ?> /> 
								&nbsp; <label for="replace_WP_embedded">URLs</label></td>
							<td><span class="description">Paste urls directly into posts and pages (WP 3.6+).</span></td>
						</tr>
						
					</table>
					
					<br>
					<p><span class="description">You can always use MP3-jPlayer's own shortcodes and widgets regardless of the above settings.</span></p>
					
					
					
					
					<br><br><hr>
					<p class="tabD" style="margin:0 0 10px 0; max-width:550px;">On pages like index, archive and search pages, set whether to show players within posts. These settings won't affect player widgets.</p>
					<p><input type="checkbox" name="mp3foxOnBlog" id="mp3foxOnBlog" value="true" <?php if ($O['player_onblog'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> />
						<label for="mp3foxOnBlog"> &nbsp; Show players when the full content is used.</p>
					<p><input type="checkbox" name="runShcodeInExcerpt" id="runShcodeInExcerpt" value="true" <?php if ($O['run_shcode_in_excerpt'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> />
						<label for="runShcodeInExcerpt"> &nbsp; Show players when excerpts are used.</label> 
						&nbsp;<span class="description">NOTE: this works for manually written post excerpts only, write your shortcodes into the excerpt field on post edit screens.</span></p>
					
					
					
					
					
					<br><br><hr>
					<p class="tabD" style="margin:0 0 10px 0;">Misc File Settings</p>
					<p><input type="checkbox" name="allowRangeRequests" id="allowRangeRequests" value="true"<?php echo ( $O['allowRangeRequests'] === "true" ? ' checked="checked"' : ''); ?>/><label for="allowRangeRequests">&nbsp;&nbsp; Allow position seeking beyond buffered</label></p>
					<p class="description" style="margin:0 0 5px 30px; max-width:550px;">Lets users seek to end of tracks without waiting for media to load. Most servers 
						should allow this by default, if you are having issues then check that your server has the <code>accept-ranges: bytes</code> header set, or 
						you can just switch this option off.</p>
					<p><input type="checkbox" id="mp3foxHideExtension" name="mp3foxHideExtension" value="true" <?php if ($O['hide_mp3extension'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> /> &nbsp; <label for="mp3foxHideExtension">Hide file extensions if a filename is displayed</label><br /><span class="description" style="margin-left:30px;">Filenames are displayed when there's no available titles.</span></p>
					<p><input type="checkbox" id="mp3foxEncodeFiles" name="mp3foxEncodeFiles" value="true" <?php if ($O['encode_files'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> /> &nbsp; <label for="mp3foxEncodeFiles">Encode URLs</label><br /><span class="description" style="margin-left:28px;">Provides some obfuscation of your urls in the page source.</span></p>
					<p><input type="checkbox" id="mp3foxAllowRemote" name="mp3foxAllowRemote" value="true" <?php if ($O['allow_remoteMp3'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> /> &nbsp; <label for="mp3foxAllowRemote">Allow playing of off-site files</label><br /><span class="description" style="margin-left:28px;">Un-checking this option filters out any files coming from other domains, but doesn't affect ability to play from a remote default path if one has been set above.</span></p>

					
					
					
					
					<br><br><hr>
					<p class="tabD" style="margin:0 0 10px 0;">Conversion Options</p>
					<table>
						<tr>
							<td><strong>Turn</strong> <code>[audio]</code> <strong>shortcodes into</strong>:</td>
							<td>
								<select name="replacerShortcode_single" style="width:200px; font-weight:500;">
									<option value="mp3j"<?php if ( 'mp3j' == $O['replacerShortcode_single'] ) { echo ' selected="selected"'; } ?>>Single Players - Graphic</option>
									<option value="mp3t"<?php if ( 'mp3t' == $O['replacerShortcode_single'] ) { echo ' selected="selected"'; } ?>>Single Players - Text</option>
									<option value="player"<?php if ( 'player' == $O['replacerShortcode_single'] ) { echo ' selected="selected"'; } ?>>Playlist Players</option>
									<option value="popout"<?php if ( 'popout' == $O['replacerShortcode_single'] ) {  echo ' selected="selected"'; } ?>>Popout Links</option>
								</select>
							</td>
						</tr>
						<tr>
							<td><strong>Turn</strong> <code>[playlist]</code> <strong>shortcodes into</strong>:&nbsp;&nbsp;&nbsp;</td>
							<td>
								<select name="replacerShortcode_playlist" id="replacerShortcode_playlist" style="width:200px; font-weight:500;">
									<option value="player"<?php if ( 'player' == $O['replacerShortcode_playlist'] ) { echo ' selected="selected"'; } ?>>Playlist Players</option>
									<option value="popout"<?php if ( 'popout' == $O['replacerShortcode_playlist'] ) {  echo ' selected="selected"'; } ?>>Popout Links</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="vTop"><br /><strong>Turn converted links into</strong>:</td>
							<td>
								<br />
								<textarea class="widefat" style="width:400px; height:100px;" name="make_player_from_link_shcode"><?php 
									$deslashed = str_replace('\"', '"', $O['make_player_from_link_shcode'] );
									echo $deslashed; 
									?></textarea><br />
								<span class="description">Placeholders:</span> <code>{TEXT}</code> <span class="description">- Link text,</span> <code>{URL}</code> <span class="description">- Link url.
									<br />This field can also include arbitrary text/html.</span>
							</td>
						</tr>
					</table>							
					
					
					
					
					<br><br><hr>
					<p class="tabD" style="margin:0 0 10px 0;">Javascripts</p>

					<?php $bgc = ( $O['disable_jquery_libs'] == "yes" ) ? "#fdd" : "#f9f9f9"; ?>
					<div style="margin: 20px 0px 10px 0px; padding:6px; background:<?php echo $bgc; ?>; border:1px solid #ccc;">
						<p style="margin:0 0 5px 18px; font-weight:700;">Disable jQuery and jQuery-UI javascript libraries? &nbsp; <input type="text" style="width:60px;" name="disableJSlibs" value="<?php echo $O['disable_jquery_libs']; ?>" /></p>
						<p style="margin: 0 0 8px 18px;"><span class="description"><span style="color:#333;">CAUTION!!</span> This option will bypass the request <strong>from this plugin only</strong> for both jQuery <strong>and</strong> jQuery-UI scripts,
							you <strong>MUST</strong> be providing these scripts from an alternative source.
							<br />Type <code>yes</code> in the box and save settings to bypass jQuery and jQuery-UI.</span></p>
					</div>
					
					
					
					<br><br><hr>
					<p class="tabD" style="margin:0 0 10px 0;">Playlist Separators</p>
					<div style="margin: 10px 0px 10px 0px; padding:6px 18px 6px 18px; background:#f9f9f9; border:1px solid #ccc;">
						<span class="description">If you manually write playlists then you can choose the separators you use in the tracks and captions lists. 
							<br /><span style="color:#333;">CAUTION!!</span> You'll need to manually update any existing playlists if you change the separators!</span>
						
						<p style="margin:10px 0 0 20px;"><strong>Files:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<select name="file_separator" style="width:120px; font-size:11px; line-height:16px;">
								<option value="," <?php if ( ',' == $O['f_separator'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>, (comma)</option>
								<option value=";" <?php if ( ';' == $O['f_separator'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>; (semicolon)</option>
								<option value="###" <?php if ( '###' == $O['f_separator'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>### (3 hashes)</option>
							</select>
							&nbsp;&nbsp;<span class="description">eg.</span> <code>tracks="fileA.mp3 <?php echo $O['f_separator']; ?> Title@fileB.mp3 <?php echo $O['f_separator']; ?> fileC.mp3"</code></p>
						
						<p style="margin-left:20px;"><strong>Captions:</strong> &nbsp;&nbsp; 
							<select name="caption_separator" style="width:120px; font-size:11px; line-height:16px;">
								<option value="," <?php if ( ',' == $O['c_separator'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>, (comma)</option>
								<option value=";" <?php if ( ';' == $O['c_separator'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>; (semicolon)</option>
								<option value="###" <?php if ( '###' == $O['c_separator'] ) { _e('selected="selected"', $MP3JP->textdomain ); } ?>>### (3 hashes)</option>
							</select>
							&nbsp;&nbsp;<span class="description">eg.</span> <code>captions="Caption A <?php echo $O['c_separator']; ?> Caption B <?php echo $O['c_separator']; ?> Caption C"</code></p>
					</div>
					
					<br><br><hr>
					<p class="tabD" style="margin:0 0 10px 0;">Other Settings</p>
					
					
					<p style="margin-bottom:10px;"><strong>Show error messages</strong>:
						&nbsp;&nbsp;&nbsp;
						<select name="showErrors">
							<option value="false"<?php if ( 'false' == $O['showErrors'] ) { echo ' selected="selected"'; } ?>>Never</option>
							<option value="admin"<?php if ( 'admin' == $O['showErrors'] ) { echo ' selected="selected"'; } ?>>To Admins only</option>
							<option value="true"<?php if ( 'true' == $O['showErrors'] ) { echo ' selected="selected"'; } ?>>To All</option>
						</select></p>
					
					
					<p><input type="checkbox" id="mp3foxEchoDebug" name="mp3foxEchoDebug" value="true" <?php if ($O['echo_debug'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> /> &nbsp;<label for="mp3foxEchoDebug">Turn on debug</label><br />&nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(info appears in the source view near the bottom)</span></p>
					
					
					
				</div><!-- CLOSE ADVANCED TAB -->
			</div><!-- close tabs wrapper -->
			
			<hr /><br />
			<table>
				<tr>
					<td>
						<input type="submit" name="update_mp3foxSettings" class="button-primary" style="font-weight:700;" value="<?php _e('Save All Changes', $MP3JP->textdomain ) ?>" />&nbsp;&nbsp;&nbsp;
					</td>
					<td>
						 <p style="margin-top:5px;"><label for="mp3foxRemember">Remember settings if plugin is deactivated &nbsp;</label>
							<input type="checkbox" id="mp3foxRemember" name="mp3foxRemember" value="true" <?php if ($O['remember_settings'] == "true") { _e('checked="checked"', $MP3JP->textdomain ); }?> /></p>
					</td>
				<tr>
			</table>

			<!--<input type="hidden" id="fox_styling" name="MtogBox1" value="<?php //echo $O['admin_toggle_1']; // Colour settings toggle state ?>" />-->
			<input type="hidden" name="mp3foxPluginVersion" value="<?php echo $MP3JP->version_of_plugin; ?>" />
		
		</form>
		<br>
		<hr>
		<div style="margin: 15px 0px 0px 0px; min-height:30px;">
			<p class="description" style="margin: 0px 120px px 0px; font-weight:700; color:#d0d0d0;">
				<a class="button-secondary" target="_blank" href="http://mp3-jplayer.com/help-docs/">Help & Docs &raquo;</a>
				&nbsp;&nbsp; <a class="button-secondary" target="_blank" href="http://mp3-jplayer.com/skins">Get Skins &raquo;</a>
				&nbsp;&nbsp; <a class="button-secondary" target="_blank" href="http://mp3-jplayer.com/add-ons">Get Add-Ons &raquo;</a>
			</p>
		</div>
		
		
		<div style="margin: 15px auto; height:100px;">
		</div>
	</div>

<?php
	
}
?>