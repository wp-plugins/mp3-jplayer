<?php
		function mp3j_print_admin_page() { 
			
			global $mp3_fox;
			$O = $mp3_fox->getAdminOptions();
			$colours_array = array();
			
			$openTab = '0';
			
			if ( isset( $_POST['update_mp3foxSettings'] ) )
			{
				
			//prep/sanitize number values
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
			
			//prep/sanitize paths
				if (isset($_POST['mp3foxfolder'])) { 
					$O['mp3_dir'] = $mp3_fox->prep_path( $_POST['mp3foxfolder'] ); 
				}
				if (isset($_POST['mp3foxCustomStylesheet'])) { 
					$O['custom_stylesheet'] = $mp3_fox->prep_path( $_POST['mp3foxCustomStylesheet'] ); 
				}
				if (isset($_POST['mp3foxPopoutBGimage'])) { 
					$O['popout_background_image'] = $mp3_fox->prep_path( $_POST['mp3foxPopoutBGimage'] );
				}
				
				$O['dloader_remote_path'] = ( isset($_POST['dloader_remote_path']) ) ? $mp3_fox->prep_value( $_POST['dloader_remote_path'] ) : "";
				$O['loggedout_dload_link'] = ( $_POST['loggedout_dload_link'] == "" ) ? "" : $mp3_fox->prep_value( $_POST['loggedout_dload_link'] ); //allow it to be empty
				
			//prep/sanitize options and checkboxes	
				if (isset($_POST['mp3foxTheme'])) { 
					$O['player_theme'] = $mp3_fox->prep_value( $_POST['mp3foxTheme'] );
				}
				if (isset($_POST['mp3foxFloat'])) {
					$O['player_float'] = $mp3_fox->prep_value( $_POST['mp3foxFloat'] );
				}
				if (isset($_POST['librarySortcol'])) { 
					$O['library_sortcol'] = $mp3_fox->prep_value( $_POST['librarySortcol'] );
				}
				if (isset($_POST['libraryDirection'])) {
					$O['library_direction'] = $mp3_fox->prep_value( $_POST['libraryDirection'] );
				}
				if (isset($_POST['file_separator'])) {
					$O['f_separator'] = $mp3_fox->prep_value( $_POST['file_separator'] );
				}
				if (isset($_POST['caption_separator'])) {
					$O['c_separator'] = $mp3_fox->prep_value( $_POST['caption_separator'] );
				}
				if (isset($_POST['mp3foxDownloadMp3'])) { 
					$O['show_downloadmp3'] = $mp3_fox->prep_value( $_POST['mp3foxDownloadMp3'] ); 
				}
				
				$O['disable_template_tag'] 	= ( isset($_POST['disableTemplateTag']) ) 		? "true" : "false";
				$O['echo_debug'] 			= ( isset($_POST['mp3foxEchoDebug']) ) 			? "true" : "false";
				$O['add_track_numbering'] 	= ( isset($_POST['mp3foxAddTrackNumbers']) ) 	? "true" : "false";
				$O['enable_popout'] 		= ( isset($_POST['mp3foxEnablePopout']) ) 		? "true" : "false";
				$O['playlist_repeat'] 		= ( isset($_POST['mp3foxPlaylistRepeat']) ) 	? "true" : "false";
				$O['use_fixed_css'] 		= ( isset($_POST['mp3foxUseFixedCSS']) ) 		? "true" : "false";
				$O['encode_files'] 			= ( isset($_POST['mp3foxEncodeFiles']) ) 		? "true" : "false";
				$O['run_shcode_in_excerpt'] = ( isset($_POST['runShcodeInExcerpt']) ) 		? "true" : "false";
				$O['volslider_on_singles'] 	= ( isset($_POST['volslider_onsingles']) ) 		? "true" : "false";
				$O['volslider_on_mp3j'] 	= ( isset($_POST['volslider_onmp3j']) ) 		? "true" : "false";
				$O['touch_punch_js'] 		= ( isset($_POST['touch_punch_js']) ) 			? "true" : "false";
				$O['force_browser_dload'] 	= ( isset($_POST['force_browser_dload']) ) 		? "true" : "false";
				$O['make_player_from_link']	= ( isset($_POST['make_player_from_link']) )	? "true" : "false";
				$O['auto_play'] 			= ( isset($_POST['mp3foxAutoplay']) ) 			? "true" : "false";
				$O['allow_remoteMp3'] 		= ( isset($_POST['mp3foxAllowRemote']) ) 		? "true" : "false";
				$O['player_onblog'] 		= ( isset($_POST['mp3foxOnBlog']) ) 			? "true" : "false";
				$O['playlist_show'] 		= ( isset($_POST['mp3foxShowPlaylist']) ) 		? "true" : "false";
				$O['remember_settings'] 	= ( isset($_POST['mp3foxRemember']) ) 			? "true" : "false";
				$O['hide_mp3extension'] 	= ( isset($_POST['mp3foxHideExtension']) ) 		? "true" : "false";
				
			//prep/sanitize other values
				if (isset($_POST['mp3foxPlayerWidth'])) { 
					$O['player_width'] = $mp3_fox->prep_value( $_POST['mp3foxPlayerWidth'] );
				}
				if (isset($_POST['disableJSlibs'])) {
					$O['disable_jquery_libs'] = ( preg_match("/^yes$/i", $_POST['disableJSlibs']) ) ? "yes" : "";
				}
				
				$O['paddings_top'] 		= ( $_POST['mp3foxPaddings_top'] == "" ) 		? "0px" : $mp3_fox->prep_value( $_POST['mp3foxPaddings_top'] );
				$O['paddings_bottom'] 	= ( $_POST['mp3foxPaddings_bottom'] == "" ) 	? "0px" : $mp3_fox->prep_value( $_POST['mp3foxPaddings_bottom'] );
				$O['paddings_inner'] 	= ( $_POST['mp3foxPaddings_inner'] == "" ) 		? "0px" : $mp3_fox->prep_value( $_POST['mp3foxPaddings_inner'] );
				
				if ( isset($_POST['mp3foxPopoutBackground']) ) { 
					$O['popout_background'] = $mp3_fox->prep_value( $_POST['mp3foxPopoutBackground'] );
				}
				if ( isset($_POST['mp3foxPluginVersion']) ) { 
					$O['db_plugin_version'] = $mp3_fox->prep_value( $_POST['mp3foxPluginVersion'] );
				}
				if ( isset($_POST['MtogBox1']) ) { 
					$O['admin_toggle_1'] = $mp3_fox->prep_value( $_POST['MtogBox1'] );
				}
				
			//prep/sanitize text
				$O['dload_text'] 			= ( $_POST['dload_text'] == "" ) 			? "DOWNLOAD MP3" : $mp3_fox->prep_text( $_POST['dload_text'] );
				$O['loggedout_dload_text'] 	= ( $_POST['loggedout_dload_text'] == "" ) 	? "" : $mp3_fox->prep_text( $_POST['loggedout_dload_text'] );
				
				if ( isset($_POST['mp3foxPopoutButtonText']) ) {
					$O['popout_button_title'] = $mp3_fox->prep_text( $_POST['mp3foxPopoutButtonText'] );
				}
				if ( isset($_POST['make_player_from_link_shcode']) ) {
					$O['make_player_from_link_shcode'] = $mp3_fox->prep_text( $_POST['make_player_from_link_shcode'] );
				}
				
			// Colours array//
				if (isset($_POST['mp3foxScreenColour'])) { $colours_array['screen_colour'] = $mp3_fox->prep_value( $_POST['mp3foxScreenColour'] ); }
				if (isset($_POST['mp3foxScreenOpac'])) { $colours_array['screen_opacity'] = $mp3_fox->prep_value( $_POST['mp3foxScreenOpac'] ); }
				if (isset($_POST['mp3foxLoadbarColour'])) { $colours_array['loadbar_colour'] = $mp3_fox->prep_value( $_POST['mp3foxLoadbarColour'] ); }
				if (isset($_POST['mp3foxLoadbarOpac'])) { $colours_array['loadbar_opacity'] = $mp3_fox->prep_value( $_POST['mp3foxLoadbarOpac'] ); }
				if (isset($_POST['mp3foxPosbarColour'])) { $colours_array['posbar_colour'] = $mp3_fox->prep_value( $_POST['mp3foxPosbarColour'] ); }
				if (isset($_POST['mp3foxPosbarTint'])) { $colours_array['posbar_tint'] = $mp3_fox->prep_value( $_POST['mp3foxPosbarTint'] ); }
				if (isset($_POST['mp3foxPosbarOpac'])) { $colours_array['posbar_opacity'] = $mp3_fox->prep_value( $_POST['mp3foxPosbarOpac'] ); }
				if (isset($_POST['mp3foxScreenTextColour'])) { $colours_array['screen_text_colour'] = $mp3_fox->prep_value( $_POST['mp3foxScreenTextColour'] ); }
				if (isset($_POST['mp3foxPlaylistColour'])) { $colours_array['playlist_colour'] = $mp3_fox->prep_value( $_POST['mp3foxPlaylistColour'] ); }
				if (isset($_POST['mp3foxPlaylistTint'])) { $colours_array['playlist_tint'] = $mp3_fox->prep_value( $_POST['mp3foxPlaylistTint'] ); }
				if (isset($_POST['mp3foxPlaylistOpac'])) { $colours_array['playlist_opacity'] = $mp3_fox->prep_value( $_POST['mp3foxPlaylistOpac'] ); }
				if (isset($_POST['mp3foxListTextColour'])) { $colours_array['list_text_colour'] = $mp3_fox->prep_value( $_POST['mp3foxListTextColour'] ); }
				if (isset($_POST['mp3foxListCurrentColour'])) { $colours_array['list_current_colour'] = $mp3_fox->prep_value( $_POST['mp3foxListCurrentColour'] ); }
				if (isset($_POST['mp3foxListHoverColour'])) { $colours_array['list_hover_colour'] = $mp3_fox->prep_value( $_POST['mp3foxListHoverColour'] ); }
				if (isset($_POST['mp3foxListBGaHover'])) { $colours_array['listBGa_hover'] = $mp3_fox->prep_value( $_POST['mp3foxListBGaHover'] ); }
				if (isset($_POST['mp3foxListBGaCurrent'])) { $colours_array['listBGa_current'] = $mp3_fox->prep_value( $_POST['mp3foxListBGaCurrent'] ); }
				if (isset($_POST['mp3foxVolGrad'])) { $colours_array['volume_grad'] = $mp3_fox->prep_value( $_POST['mp3foxVolGrad'] ); }
				if (isset($_POST['mp3foxListDivider'])) { $colours_array['list_divider'] = $mp3_fox->prep_value( $_POST['mp3foxListDivider'] ); }
				if (isset($_POST['mp3foxIndicator'])) { $colours_array['indicator'] = $mp3_fox->prep_value( $_POST['mp3foxIndicator'] ); }
				$O['colour_settings'] = $colours_array;
				
				update_option($mp3_fox->adminOptionsName, $O);
				$mp3_fox->theSettings = $O;
				?>
				
				<!-- Settings saved message -->
				<div class="updated"><p><strong><?php _e("Settings Updated.", $mp3_fox->textdomain );?></strong></p></div>
			
			<?php 
			}
			// Pick up current colours
			$current_colours = $O['colour_settings'];
			?>
			
			<div class="wrap">
			
				<h2>&nbsp;</h2>
				<h1>MP3-jPlayer
					<span class="description" style="font-size:10px;">  &nbsp; Version <?php echo $mp3_fox->version_of_plugin; ?></span>
					<?php 
					if ( $O['disable_jquery_libs'] == "yes" )
					{ 
						echo '&nbsp;<span style="font-size: 11px; font-weight:700; color:#f66;">(jQuery and UI scripts are turned off)</span>';
					} 
					?>
				</h1>
				<p>&nbsp;</p>
				
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					
					
					
					
					<div class="mp3j-tabbuttons-wrap">
						<div class="mp3j-tabbutton" id="mp3j_tabbutton_0">BASICS</div>
						<div class="mp3j-tabbutton" id="mp3j_tabbutton_1">STYLE</div>
						<div class="mp3j-tabbutton" id="mp3j_tabbutton_2">FILES</div>
						<div class="mp3j-tabbutton" id="mp3j_tabbutton_3">PLAYERS</div>
						<div class="mp3j-tabbutton" id="mp3j_tabbutton_4">POPOUT</div>
						<div class="mp3j-tabbutton" id="mp3j_tabbutton_5">TEMPLATE</div>
						<br class="clearB" />
					</div>
					<div class="mp3j-tabs-wrap">
					
					
						<!-- BASICS TAB .......................... -->
						<div class="mp3j-tab" id="mp3j_tab_0">
							<div class="tab-header"><span class="description">&nbsp;</span></div>
							
							<p>Initial volume &nbsp; <input type="text" style="text-align:center;" size="2" name="mp3foxVol" value="<?php echo $O['initial_vol']; ?>" /> &nbsp; <span class="description">(0 - 100)</span></p>
							<br />
							<p><input type="checkbox" name="mp3foxAutoplay" value="true" <?php if ($O['auto_play'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Autoplay</p>
							<p><input type="checkbox" name="mp3foxPlaylistRepeat" value="true" <?php if ($O['playlist_repeat'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Loop</p>
							<p><input type="checkbox" name="mp3foxAddTrackNumbers" value="true" <?php if ($O['add_track_numbering'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Number the tracks</p>
							
							<br />
							<p><input type="checkbox" name="make_player_from_link" value="true" <?php if ($O['make_player_from_link'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> 
										&nbsp; Turn mp3 links into players</p>
									<p style="margin-left:45px;"><span class="description">(Use the 'Add media' button on the edit screen to add links, or you can manually add/write links into the page. Links will be turned into players using the shortcode specified under 'Template Options' below.)</span></p>
									
							
							<br />
							<p><input type="checkbox" name="mp3foxOnBlog" value="true" <?php if ($O['player_onblog'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Show players in posts on index, archive, and search pages &nbsp;<span class="description">(doesn't affect widgets)</span></p>
							<p><input type="checkbox" name="runShcodeInExcerpt" value="true" <?php if ($O['run_shcode_in_excerpt'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Allow shortcodes in post excerpts &nbsp;<span class="description">(this works for manually written post excerpts only)</span></p>
							<br />
									
						</div><!-- CLOSE BASICS TAB -->
					
						
						<!-- FILES TAB .......................... -->
						<div class="mp3j-tab" id="mp3j_tab_2">
							
							<div class="tab-header"><span class="description">&nbsp;</span></div>
							
							<!-- MP3s -->
							<h3>Media Library</h3>
							<?php
							// create library file list //
							$library = $mp3_fox->grab_library_info();
							$L_count = ( $library ) ? $library['count'] : "0";
							echo "<p class=\"description\" style=\"margin:0 0 2px 35px;\">Library contains <strong>" . $L_count . "</strong> mp3";
							if ( $library['count'] != 1 ) { echo "'s&nbsp;"; }
							else { echo "&nbsp;"; }
							
							if ( $L_count > 0 ) {
								//echo "<a href=\"javascript:mp3jp_listtoggle('fox_library','files');\" id=\"fox_library-toggle\">Show files</a> | <a href=\"media-new.php\">Upload new</a>";
								echo "<a href=\"javascript:MP3J_ADMIN.showhideit('fox_library','files');\" id=\"fox_library-toggle\">Show files</a> | <a href=\"media-new.php\">Upload new</a>";
								echo "</p>";
								echo "<div id=\"fox_library-list\">\n";
								$liblist = '<p style="margin-left:0px;">';
								$br = '<br />';
								$tagclose = '</p>';
								$n = 1;
								foreach ( $library['filenames'] as $i => $file ) {
									//$liblist .= "<a href=\"media.php?attachment_id=" . $library['postIDs'][$i] . "&amp;action=edit\" style=\"font-size:11px;\">[Edit]</a>&nbsp;&nbsp;<span style=\"color:#aaa;font-size:11px;\">" . $n++ . "&nbsp;</span> " . $file . "&nbsp;&nbsp;<span style=\"color:#aaa;font-size:11px;\">\"" . $library['titles'][$i] . "\"&nbsp;&nbsp;" . $library['excerpts'][$i] . "</span>" . $br;
									switch( $O['library_sortcol'] ) {
										case "title":
											$liblist .= "<a href=\"media.php?attachment_id=" . $library['postIDs'][$i] . "&amp;action=edit\" style=\"font-size:11px;\">[Edit]</a>&nbsp;&nbsp;<span style=\"color:#aaa;font-size:11px;\">" . $n++ . "&nbsp;&nbsp;\"" . $library['titles'][$i] . "\"&nbsp;&nbsp;" . $library['excerpts'][$i] . "</span>&nbsp;&nbsp;" . $file . $br; 
											break;
										case "caption": 
											$liblist .= "<a href=\"media.php?attachment_id=" . $library['postIDs'][$i] . "&amp;action=edit\" style=\"font-size:11px;\">[Edit]</a>&nbsp;&nbsp;<span style=\"color:#aaa;font-size:11px;\">" . $n++ . "&nbsp;&nbsp;" . $library['excerpts'][$i] . "&nbsp;&nbsp;\"" . $library['titles'][$i] . "\"</span>&nbsp;&nbsp;" . $file . $br; 
											break;
										default: 
											$liblist .= "<a href=\"media.php?attachment_id=" . $library['postIDs'][$i] . "&amp;action=edit\" style=\"font-size:11px;\">[Edit]</a>&nbsp;&nbsp;<span style=\"color:#aaa;font-size:11px;\">" . $n++ . "</span>&nbsp;&nbsp;" . $file . "&nbsp;&nbsp;<span style=\"color:#aaa;font-size:11px;\">\"" . $library['titles'][$i] . "\"&nbsp;&nbsp;" . $library['excerpts'][$i] . "</span>" . $br;
									}
								}
								$liblist .= $tagclose;
								echo $liblist;
								echo '</div>';
							}
							else { echo "<a href=\"media-new.php\">Upload new</a></p>"; }
							?>
					
							<p class="description" style="margin:0 0 0 33px;">You just need to write filenames in playlists to play from the library.</p>
							<p style="margin:12px 0 12px 34px;">Order playlists by:&nbsp;
								<select name="librarySortcol" style="width:110px; font-size:11px;">
									<option value="file" <?php if ( 'file' == $O['library_sortcol'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Filename</option>
									<option value="date" <?php if ( 'date' == $O['library_sortcol'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Date Uploaded</option>
									<option value="caption" <?php if ( 'caption' == $O['library_sortcol'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Caption, Title</option>
									<option value="title" <?php if ( 'title' == $O['library_sortcol'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Title</option>
								</select>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Direction:&nbsp;
								<select name="libraryDirection" style="width:60px; font-size:11px;">
									<option value="ASC" <?php if ( 'ASC' == $O['library_direction'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>ASC</option>
									<option value="DESC" <?php if ( 'DESC' == $O['library_direction'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>DESC</option>
								</select>
							</p><br />					
					
							<!-- Folder -->
							<h3>Folder or URI</h3>
							<p class="description" style="margin:0 0 0 35px;">Set a default folder or uri for playing mp3's in the box below, eg. <code>/music</code> or <code>www.anothersite.com/music</code><br />You just need to write filenames in playlists to play from here.</p>
							<p style="margin:10px 0px 5px 35px;">Default path: &nbsp; <input type="text" style="width:385px;" name="mp3foxfolder" value="<?php echo $O['mp3_dir']; ?>" /></p>
			
							<?php 
							// create file-list if directory is local
							$n = 1;
							$folderuris = $mp3_fox->grab_local_folder_mp3s( $O['mp3_dir'] );
							if ( is_array($folderuris) ){
								foreach ( $folderuris as $i => $uri ) {
									$files[$i] = strrchr( $uri, "/" );
									$files[$i] = str_replace( "/", "", $files[$i] );
								}
								$c = (!empty($files)) ? count($files) : 0;
								echo "<p class=\"description\" style=\"margin: 0px 0px 14px 117px;\">This folder contains <strong>" . $c . "</strong> mp3";
								if ( $c != 1 ) { echo "'s&nbsp;"; }
								else { echo "&nbsp;"; }
								if ( $c > 0 ) {
									//echo "<a href=\"javascript:mp3jp_listtoggle('fox_folder','files');\" id=\"fox_folder-toggle\">Show files</a></p>";
									echo "<a href=\"javascript:MP3J_ADMIN.showhideit('fox_folder','files');\" id=\"fox_folder-toggle\">Show files</a></p>";
									//echo "<div id=\"fox_folder-list\" style=\"display:none;\">\n<p style=\"margin-left:0px;\">";
									echo "<div id=\"fox_folder-list\">\n<p style=\"margin-left:0px;\">";
									
									//natcasesort($files);
									
									foreach ( $files as $i => $val ) {
										echo "<span style=\"color:#aaa;font-size:11px;\">" . $n++ . "</span>&nbsp;&nbsp;" . $val . "<br />";
									}
									echo "</p>\n</div>\n";
								}
								else { echo "</p>";	}
							}
							elseif ( $folderuris == true )
								echo "<p class=\"description\" style=\"margin: 0px 0px 14px 117px;\">Unable to read or locate the folder <code>" . $O['mp3_dir'] . "</code> check the path and folder permissions</p>";
							else 
								echo "<p class=\"description\" style=\"margin: 0px 0px 14px 117px;\">No info is available on remote folders but you can play from here if you know the filenames</p>"; 
							?>						
					
							<br />
							
							<p><input type="checkbox" name="mp3foxHideExtension" value="true" <?php if ($O['hide_mp3extension'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Hide '.mp3' extension if a filename is displayed<br /><span class="description" style="margin-left:28px;">(filenames are displayed when there's no available titles)</span></p>
							<p><input type="checkbox" name="mp3foxEncodeFiles" value="true" <?php if ($O['encode_files'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Encode URI's and filenames<br /><span class="description" style="margin-left:28px;">(provides some obfuscation of your urls in the page source)</span></p>
							<p><input type="checkbox" name="mp3foxAllowRemote" value="true" <?php if ($O['allow_remoteMp3'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Allow playing of off-site mp3's<br /><span class="description" style="margin-left:28px;">(unchecking this option doesn't affect mp3's playing from a remote default folder if one is set above)</span></p>

							
						</div><!-- CLOSE FILES TAB -->
						
						
						<!-- PLAYERS TAB .......................... -->
						<div class="mp3j-tab" id="mp3j_tab_3">
							
							<div class="tab-header"><span class="description" style="font-size:18px;">Single player options</span></div>
							<p><input type="checkbox" name="volslider_onsingles" value="true" <?php if ($O['volslider_on_singles'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Volume sliders on [mp3<strong>t</strong>] players</p>
							<p><input type="checkbox" name="volslider_onmp3j" value="true" <?php if ($O['volslider_on_mp3j'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Volume sliders on [mp3<strong>j</strong>] players</p>
							<br /><br />
							
							<div class="tab-header"><span class="description" style="font-size:18px;">Playlist player options</span></div>
							<p>Width: &nbsp; <input type="text" style="width:75px;" name="mp3foxPlayerWidth" value="<?php echo $O['player_width']; ?>" /> &nbsp; <span class="description">pixels (px) or percent (%)</span></p>
							<p>Align: &nbsp;&nbsp; 
								<select name="mp3foxFloat" style="width:94px; font-size:11px; line-height:16px;">
									<option value="none" <?php if ( 'none' == $O['player_float'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Left</option>
									<option value="rel-C" <?php if ( 'rel-C' == $O['player_float'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Centre</option>
									<option value="rel-R" <?php if ( 'rel-R' == $O['player_float'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Right</option>
									<option value="left" <?php if ( 'left' == $O['player_float'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Float left</option>
									<option value="right" <?php if ( 'right' == $O['player_float'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Float right</option>
								</select></p>
							<br /><br />
							
							<!-- <p><input type="checkbox" name="mp3foxDownloadMp3" value="true" <?php //if ($O['show_downloadmp3'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Display a 'Download mp3' link</p> -->
							<h3 style="margin-left:0;"><strong>Downloads</strong></h3>
							<p style="margin-bottom:10px;">Show download link:
								<select name="mp3foxDownloadMp3" style="width:120px; font-size:11px; line-height:16px;">
									<option value="true" <?php if ( 'true' == $O['show_downloadmp3'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Yes</option>
									<option value="false" <?php if ( 'false' == $O['show_downloadmp3'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>No</option>
									<option value="loggedin" <?php if ( 'loggedin' == $O['show_downloadmp3'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>To logged in users</option>
								</select>
								&nbsp;&nbsp; 
								</p>
							
							<p class="description" style="margin:0 0 5px 30px;">When setting a player for logged in downloads, use the following options to add text/link for logged out visitors:</p> 
							<p style="margin-left:30px;">Visitor text: &nbsp; <input type="text" style="width:145px;" name="loggedout_dload_text" value="<?php echo $O['loggedout_dload_text']; ?>" /> &nbsp;<span class="description">(leave blank for no text/link)</span></p>
							<p style="margin-left:30px;">Visitor link: &nbsp; <input type="text" style="width:350px;" name="loggedout_dload_link" value="<?php echo $O['loggedout_dload_link']; ?>" /> &nbsp;<span class="description">(optional url for visitor text)</span></p>							
							
							<br />
							<!--<span class="description">(can be set per-player via shortcodes)</span>-->
							
							<p>Download link text: <input type="text" style="width:140px;" name="dload_text" value="<?php echo $O['dload_text']; ?>" /></p>
							
							<p style="margin-top:15px;"><input type="checkbox" name="force_browser_dload" value="true" <?php if ($O['force_browser_dload'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; 
								Try to force browsers to save downloads <span class="description">(no mobile support yet)</span>
								<!--&nbsp; Local files  
								<input type="radio" name="force_browser_dload_remote" value="false" <?php //if ($O['force_browser_dload_remote'] == "false") { echo 'checked="checked"'; } ?> /> 
								&nbsp; | &nbsp; 
								<input type="radio" name="force_browser_dload_remote" value="true" <?php //if ($O['force_browser_dload_remote'] == "true") { echo 'checked="checked"'; } ?>/> 
								All files -->
								</p>
								
								<p style="margin:10px 0 0 30px;"><span class="description">If you play from other domains and want to force the download, then use 
									the field<br />below to specify a path to a downloader file. <a href="<?php echo $mp3_fox->PluginFolder; ?>/remote/help.txt">See help on setting this up</a>.</span></p>
							
								<p style="margin:5px 0 0 30px;">Path to remote downloader files: <input type="text" style="width:240px;" name="dloader_remote_path" value="<?php echo $O['dloader_remote_path']; ?>" /></p>
								
							<!--<p style="margin:5px 0 0 25px;"><span class="description">(if you select 'All files' then you'll need to place a downloader file on any remote servers you want to force downloads from.
								There's a file included in the plugin for use on servers running php, see <a href="<?php //echo $mp3_fox->PluginFolder; ?>/remote/help.txt">remote setup help</a> for instructions)</span></p>-->
								
													
							<br /><br />
							<h3 style="margin-left:0;"><strong>Margins</strong></h3>	
							<p>Above players: &nbsp; <input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_top" value="<?php echo $O['paddings_top']; ?>" /> <span class="description">&nbsp; pixels (px) or percent (%)</span><br />
								Inner margin: (floated players) &nbsp; <input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_inner" value="<?php echo $O['paddings_inner']; ?>" /> <span class="description">&nbsp; pixels (px) or percent (%)</span><br />
								Below players: &nbsp; <input type="text" size="5" style="text-align:center;" name="mp3foxPaddings_bottom" value="<?php echo $O['paddings_bottom']; ?>" /> <span class="description">&nbsp; pixels (px) or percent (%)</span></p>
							
							
							<br /><br />
							<h3 style="margin-left:0;"><strong>Playlists</strong></h3>
							<p>Max playlist height: &nbsp; <input type="text" size="6" style="text-align:center;" name="mp3foxMaxListHeight" value="<?php echo $O['max_list_height']; ?>" /> px &nbsp; <span class="description">(a scroll bar will show for longer playlists, leave it blank for no limit)</span></p>							
							<p><input type="checkbox" name="mp3foxShowPlaylist" value="true" <?php if ($O['playlist_show'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Start with playlists showing</p>
							
							<div style="margin: 10px 0px 10px 0px; padding:6px; background:#f9f9f9; border:1px solid #eee;">
								<p>Playlist Separators <span class="description">- CAUTION: You'll need to manually update any existing playlists if you change the separators!</p>
								<p style="margin-left:20px;">Files: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<select name="file_separator" style="width:120px; font-size:11px; line-height:16px;">
										<option value="," <?php if ( ',' == $O['f_separator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>, (comma)</option>
										<option value=";" <?php if ( ';' == $O['f_separator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>; (semicolon)</option>
										<option value="###" <?php if ( '###' == $O['f_separator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>### (3 hashes)</option>
									</select>
									&nbsp;&nbsp;<span class="description">eg.</span> <code>tracks="fileA.mp3 <?php echo $O['f_separator']; ?> Title@fileB.mp3 <?php echo $O['f_separator']; ?> fileC.mp3"</code></p>
								
								<p style="margin-left:20px;">Captions: &nbsp;&nbsp; 
									<select name="caption_separator" style="width:120px; font-size:11px; line-height:16px;">
										<option value="," <?php if ( ',' == $O['c_separator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>, (comma)</option>
										<option value=";" <?php if ( ';' == $O['c_separator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>; (semicolon)</option>
										<option value="###" <?php if ( '###' == $O['c_separator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>### (3 hashes)</option>
									</select>
									&nbsp;&nbsp;<span class="description">eg.</span> <code>captions="Caption A <?php echo $O['c_separator']; ?> Caption B <?php echo $O['c_separator']; ?> Caption C"</code></p>
							</div>

						</div><!-- CLOSE PLAYERS TAB -->
						
						
						<!-- POPOUT TAB .......................... -->
						<div class="mp3j-tab" id="mp3j_tab_4">
							<div class="tab-header"><span class="description">&nbsp;</span></div>
							
							<p><input type="checkbox" name="mp3foxEnablePopout" value="true" <?php if ($O['enable_popout'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp; Enable the pop-out player</p>
							<p>Window width: &nbsp; <input type="text" size="4" style="text-align:center;" name="mp3foxPopoutWidth" value="<?php echo $O['popout_width']; ?>" /> px <span class="description">&nbsp; (250 - 1600)</span></p>
							<p>Window max height: &nbsp; <input type="text" size="4" style="text-align:center;" name="mp3foxPopoutMaxHeight" value="<?php echo $O['popout_max_height']; ?>" /> px <span class="description">&nbsp; (200 - 1200) &nbsp; a scroll bar will show for longer playlists</span></p>
							<p>Launch button text: &nbsp; <input type="text" style="width:200px;" name="mp3foxPopoutButtonText" value="<?php echo $O['popout_button_title']; ?>" /></p>
							
						</div><!-- CLOSE POPOUT TAB -->
						
						
						<!-- TEMPLATE TAB .......................... -->
						<div class="mp3j-tab" id="mp3j_tab_5">
							<div class="tab-header"><span class="description">&nbsp;</span></div>
							
							<?php $greyout_text = ( $O['disable_jquery_libs'] == "yes" ) ? ' style="color:#d6d6d6;"' : ''; ?>
							
							<p style="margin:10px 0 10px 0px;">Shortcode for 'Turn mp3 links into players' option:</p>
							
							<p style="margin:0px 0 20px 25px;"><textarea class="widefat" style="width:580px; height:100px;" name="make_player_from_link_shcode"><?php 
								$deslashed = str_replace('\"', '"', $O['make_player_from_link_shcode'] );
								echo $deslashed; 
								?></textarea><br /><span class="description">Can also include text/html. Placeholders:</span> <code>{TEXT}</code> <span class="description">- Link text,</span> <code>{URL}</code> <span class="description">- Link url.</span></p>
							
							
							<p<?php echo $greyout_text; ?>><input type="checkbox" name="touch_punch_js" value="true" <?php if ($O['touch_punch_js'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp;Include additional js for touch screen support<br />&nbsp; &nbsp; &nbsp; &nbsp;<span class="description"<?php echo $greyout_text; ?>>(adds jquery.ui.touch-punch.js script)</span></p>
							<p><input type="checkbox" name="mp3foxUseFixedCSS" value="true" <?php if ($O['use_fixed_css'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp;Bypass colour settings<br />&nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(colours can still be set in css)</span></p>
							<p><input type="checkbox" name="disableTemplateTag" value="true" <?php if ($O['disable_template_tag'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp;Bypass player template-tags in theme files<br />&nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(ignores mp3j_addscripts() and mp3j_put() template functions)</span></p>
							
							
							
							<p><input type="checkbox" name="mp3foxEchoDebug" value="true" <?php if ($O['echo_debug'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /> &nbsp;Turn on debug<br />&nbsp; &nbsp; &nbsp; &nbsp;<span class="description">(info appears in the source view near the bottom)</span></p>
							<?php $bgc = ( $O['disable_jquery_libs'] == "yes" ) ? "#fdd" : "#f9f9f9"; ?>
							<div style="margin: 20px 0px 10px 0px; padding:6px; background:<?php echo $bgc; ?>; border:1px solid #eee;">
								<p style="margin:0 0 5px 18px;">Disable jQuery and jQuery-UI javascript libraries? &nbsp; <input type="text" style="width:60px;" name="disableJSlibs" value="<?php echo $O['disable_jquery_libs']; ?>" /></p>
								<p style="margin: 0 0 8px 18px;"><span class="description"><strong>CAUTION!!</strong> This option will bypass the request <strong>from this plugin only</strong> for both jQuery <strong>and</strong> jQuery-UI scripts,
									you <strong>MUST</strong> be providing these scripts from an alternative source.
									<br />Type <code>yes</code> in the box and save settings to bypass jQuery and jQuery-UI.</span></p>
							</div>

						</div><!-- CLOSE TEMPLATE TAB -->
						
						
						<!-- COLOURS TAB .......................... -->
						<div class="mp3j-tab" id="mp3j_tab_1">
							<div class="tab-header"><span class="description">&nbsp;</span></div>
							
							<?php
							$greyout_field = ( $O['player_theme'] != "styleI" ) ? "background:#fcfcfc; color:#d6d6d6; border-color:#f0f0f0;" : "background:#fff; color:#000; border-color:#dfdfdf;";
							$greyout_text = ( $O['player_theme'] != "styleI" ) ? "color:#d6d6d6;" : "color:#444;";
							?>
							<!-- COLOUR / STYLE -->
							<div style="height:35px"><p style="width:55px; margin:0 0 0 0px; line-height:32px;">Players:</p></div> 
							<p style="margin:-35px 0px 0px 55px; line-height:32px;"><select name="mp3foxTheme" id="player-select" style="width:94px; font-size:11px; line-height:19px;">
									<option value="styleF" <?php if ( 'styleF' == $O['player_theme'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Silver</option>
									<option value="styleG" <?php if ( 'styleG' == $O['player_theme'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Dark</option>
									<option value="styleH" <?php if ( 'styleH' == $O['player_theme'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Text</option>
									<option value="styleI" <?php if ( 'styleI' == $O['player_theme'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Custom</option>
								</select>&nbsp;
								<span id="player-csssheet" style=" <?php echo $greyout_text; ?>"> &nbsp;uri:</span><input type="text" id="mp3fcss" style="width:480px; <?php echo $greyout_field; ?>" name="mp3foxCustomStylesheet" value="<?php echo $O['custom_stylesheet']; ?>" /></p>
							
							<br /><br />							
							<div style="position:relative; width:579px; height:20px; padding-top:2px; border-top:1px solid #eee; border-bottom:1px solid #eee; background:#f9f9f9;">
								<div style="float:left; width:90px; margin-left:9px;"><p class="description" style="margin:0px;"><strong>AREA</strong></p></div> 
								<div style="float:left; width:390px;"><p class="description" style="margin:0px;">&nbsp;Opacity&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;Colour</p></div>
							</div>
							
							<div style="position:relative; width:579px; padding-top:6px;">
								<div style="float:left; width:90px; margin-left:9px; border:0px solid #aaa;"><p style="margin:0px;line-height:32px;">Screen:<br />Loading bar:<br />Position bar:<br />Playlist:</p></div> 
								<div style="float:left; width:390px; border:0px solid #aaa;">
									<p style="margin:0px;line-height:32px;">
										<input type="text" size="4" name="mp3foxScreenOpac" value="<?php echo $current_colours['screen_opacity']; ?>" />
										&nbsp;&nbsp;<input type="text" id="opA" onkeyup="udfcol('opA','blA');" size="10" name="mp3foxScreenColour" value="<?php echo $current_colours['screen_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opA','blA');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opA');" id="blA" style="background:<?php echo $current_colours['screen_colour']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" size="4" name="mp3foxLoadbarOpac" value="<?php echo $current_colours['loadbar_opacity']; ?>" />
										&nbsp;&nbsp;<input type="text" id="opB" onkeyup="udfcol('opB','blB');" size="10" name="mp3foxLoadbarColour" value="<?php echo $current_colours['loadbar_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opB','blB');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opB');" id="blB" style="background:<?php echo $current_colours['loadbar_colour']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" size="4" name="mp3foxPosbarOpac" value="<?php echo $current_colours['posbar_opacity']; ?>" />
										&nbsp;&nbsp;<input type="text" id="opC" onkeyup="udfcol('opC','blC');" size="10" name="mp3foxPosbarColour" value="<?php echo $current_colours['posbar_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opC','blC');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opC');" id="blC" style="background:<?php echo $current_colours['posbar_colour']; ?>;">&nbsp;&nbsp;</span>
										&nbsp; &nbsp;<select name="mp3foxPosbarTint" style="width:115px; font-size:11px;">
											<option value="" <?php if ( '' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>(default)</option>
											<option value="soften" <?php if ( 'soften' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Light grad</option>
											<option value="softenT" <?php if ( 'softenT' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Tip</option>
											<option value="darken" <?php if ( 'darken' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Dark grad</option>
											<option value="none" <?php if ( 'none' == $current_colours['posbar_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>None</option>
										</select>
										<br />
										<input type="text" size="4" name="mp3foxPlaylistOpac" value="<?php echo $current_colours['playlist_opacity']; ?>" />
										&nbsp;&nbsp;<input type="text" id="opD" onkeyup="udfcol('opD','blD');" size="10" name="mp3foxPlaylistColour" value="<?php echo $current_colours['playlist_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opD','blD');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opD');" id="blD" style="background:<?php echo $current_colours['playlist_colour']; ?>;">&nbsp;&nbsp;</span>
										&nbsp; &nbsp;<select name="mp3foxPlaylistTint" style="width:115px; font-size:11px;">
											<option value="" <?php if ( '' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>(default)</option>
											<option value="lighten2" <?php if ( 'lighten2' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Light grad</option>
											<option value="lighten1" <?php if ( 'lighten1' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Soft grad</option>
											<option value="darken1" <?php if ( 'darken1' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Dark grad</option>
											<option value="darken2" <?php if ( 'darken2' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Darker grad</option>
											<option value="none" <?php if ( 'none' == $current_colours['playlist_tint'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>None</option>
										</select>
									</p>
								</div>
								<br clear="all" />
							</div>
							
							<div id="pickerwrap">
								<div id="plugHEX"></div>
								<div id="plugCUR"></div>
								<div id="plugin" onmousedown="HSVslide('drag','plugin',event); return false;"><div id="SV" onmousedown="HSVslide('SVslide','plugin',event)"><div id="SVslide" style="top:-4px; left:-4px;"><br /></div></div><div id="H" onmousedown="HSVslide('Hslide','plugin',event)"><div id="Hslide" style="top:-7px; left:-8px;"><br /></div><div id="Hmodel"></div></div></div>
							</div>
							
							<div style="position:relative;width:175px; height:150px; margin:-200px 0px 28px 405px; padding:50px 0px 0px 0px; border:0px solid #666;">
								<p style="margin:0px 0px 8px 0px; text-align:right;">Indicator:&nbsp;
									<select name="mp3foxIndicator" style="width:80px; font-size:11px;">
										<option value="" <?php if ( '' == $current_colours['indicator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>(default)</option>
										<option value="tint" <?php if ( 'tint' == $current_colours['indicator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Greyscale</option>
										<option value="colour" <?php if ( 'colour' == $current_colours['indicator'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Colour</option>
									</select></p>
								<p style="margin:0px 0px 8px 0px; text-align:right;">Volume bar:&nbsp;
									<select name="mp3foxVolGrad" style="width:80px; font-size:11px;">
										<option value="" <?php if ( '' == $current_colours['volume_grad'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>(default)</option>
										<option value="light" <?php if ( 'light' == $current_colours['volume_grad'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Light</option>
										<option value="dark" <?php if ( 'dark' == $current_colours['volume_grad'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Dark</option>
									</select></p>
								<p style="margin:0px 0px 0px 0px; text-align:right;">Dividers:&nbsp;
									<select name="mp3foxListDivider" style="width:80px; font-size:11px;">
										<option value="" <?php if ( '' == $current_colours['list_divider'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>(default)</option>
										<option value="light" <?php if ( 'light' == $current_colours['list_divider'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Light</option>
										<option value="med" <?php if ( 'med' == $current_colours['list_divider'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Medium</option>
										<option value="dark" <?php if ( 'dark' == $current_colours['list_divider'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>Dark</option>
										<option value="none" <?php if ( 'none' == $current_colours['list_divider'] ) { _e('selected="selected"', $mp3_fox->textdomain ); } ?>>None</option>
									</select></p>
							</div>
							
							<div style="position:relative; width:579px; height:20px; padding-top:2px; border-top:1px solid #eee; border-bottom:1px solid #eee; background:#f9f9f9;">
								<div style="float:left; width:90px; margin-left:9px;"><p class="description" style="margin:0px;"><strong>TEXT</strong></p></div> 
								<div style="float:left; width:430px;"><p class="description" style="margin:0px;">Colour</p></div>
								<br clear="all" />
							</div>
							
							<div style="position:relative; width:579px; padding-top:6px;">
								<div style="float:left; width:65px; margin-left:9px; border:0px solid #aaa;"><p style="margin:0px;line-height:32px;">Screen:<br />Playlist:<br />Selected:<br />Hover:</p></div>
								<div style="float:left; width:460px; border:0px solid #aaa;">
									<p style="margin:0px;line-height:32px;">
										<input type="text" id="opE" onkeyup="udfcol('opE','blE');" size="10" name="mp3foxScreenTextColour" value="<?php echo $current_colours['screen_text_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opE','blE');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opE');" id="blE" style="background:<?php echo $current_colours['screen_text_colour']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" id="opF" onkeyup="udfcol('opF','blF');" size="10" name="mp3foxListTextColour" value="<?php echo $current_colours['list_text_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opF','blF');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opF');" id="blF" style="background:<?php echo $current_colours['list_text_colour']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" id="opG" onkeyup="udfcol('opG','blG');" size="10" name="mp3foxListCurrentColour" value="<?php echo $current_colours['list_current_colour']; ?>" /> 
										<span class="addcol" onclick="putfcolour('opG','blG');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opG');" id="blG" style="background:<?php echo $current_colours['list_current_colour']; ?>;">&nbsp;&nbsp;</span>
										&nbsp; &nbsp; Background: <input type="text" id="opH" onkeyup="udfcol('opH','blH');" size="10" name="mp3foxListBGaCurrent" value="<?php echo $current_colours['listBGa_current']; ?>" />
										<span class="addcol" onclick="putfcolour('opH','blH');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opH');" id="blH" style="background:<?php echo $current_colours['listBGa_current']; ?>;">&nbsp;&nbsp;</span>
										<br />
										<input type="text" id="opI" onkeyup="udfcol('opI','blI');" size="10" name="mp3foxListHoverColour" value="<?php echo $current_colours['list_hover_colour']; ?>" />
										<span class="addcol" onclick="putfcolour('opI','blI');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opI');" id="blI" style="background:<?php echo $current_colours['list_hover_colour']; ?>;">&nbsp;&nbsp;</span>
										&nbsp; &nbsp; Background: <input type="text" id="opJ" onkeyup="udfcol('opJ','blJ');" size="10" name="mp3foxListBGaHover" value="<?php echo $current_colours['listBGa_hover']; ?>" />
										<span class="addcol" onclick="putfcolour('opJ','blJ');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opJ');" id="blJ" style="background:<?php echo $current_colours['listBGa_hover']; ?>;">&nbsp;&nbsp;</span>
									</p>
								</div>
								<br clear="all" />
							</div>
							
							<div style="position:relative; width:579px; height:20px; margin-top:30px; padding-top:2px; border-top:1px solid #eee; border-bottom:1px solid #eee; background:#f9f9f9;">
								<div style="float:left; width:90px; margin-left:9px;"><p class="description" style="margin:0px;"><strong>POP-OUT</strong></p></div> 
								<div style="float:left; width:430px;"><p class="description" style="margin:0px;">Background</p></div>
								<br clear="all" />
							</div>
							
							<div style="width:579px; padding-top:6px;">
								<div style="float:left; width:65px; margin-left:9px; border:0px solid #aaa;"><p style="margin:0px;line-height:32px;">Colour:<br />Image:</p></div>
								<div style="float:left; width:460px; border:0px solid #aaa;">
									<p style="margin:0px;line-height:32px;">
										<input type="text" id="opK" onkeyup="udfcol('opK','blK');"  size="10" name="mp3foxPopoutBackground" value="<?php echo $O['popout_background']; ?>" />
										<span class="addcol" onclick="putfcolour('opK','blK');">&nbsp;+&nbsp;</span>
										<span class="bl" onclick="sendfcolour('opK');" id="blK" style="background:<?php echo $O['popout_background']; ?>;">&nbsp;&nbsp;</span></p>
									<p style="margin:4px 0px 0px 0px;line-height:32px;">
										<input type="text" style="width:503px;" name="mp3foxPopoutBGimage" value="<?php echo $O['popout_background_image']; ?>" /></p>
								</div>
								<br clear="all" />
							</div>
							<p class="description" style="margin-top: 30px; margin-bottom: 0px;">&nbsp;&nbsp;(Opacity values from 0 to 100, leave any fields blank to use the default setting)</p>
												
							
							
						</div><!-- CLOSE COLOURS TAB -->
					
					
					</div>
					
					
					
								
					
					
					<br /><br />												
					<p style="margin-top: 4px;"><input type="submit" name="update_mp3foxSettings" class="button-primary" value="<?php _e('Update Settings', $mp3_fox->textdomain ) ?>" /> &nbsp; Remember settings if plugin is deactivated &nbsp;<input type="checkbox" name="mp3foxRemember" value="true" <?php if ($O['remember_settings'] == "true") { _e('checked="checked"', $mp3_fox->textdomain ); }?> /></p>
					<input id="fox_styling" type="hidden" name="MtogBox1" value="<?php echo $O['admin_toggle_1']; // Colour settings toggle state ?>" />
					<input type="hidden" name="mp3foxPluginVersion" value="<?php echo $mp3_fox->version_of_plugin; ?>" />
				
				</form>
				<br />				
				
				<div style="margin: 15px 120px 25px 0px; border-top: 1px solid #999; height: 30px;"><p class="description" style="margin: 0px 120px px 0px;"><a href="http://sjward.org/jplayer-for-wordpress">Plugin home page</a></p></div>
			</div>
			
			
			
			<!-- Tabs JS -->
			<script type="text/javascript">
			var MP3jP = {
				
				openTab: <?php echo $openTab; ?>,
				
				add_tab_listener: function ( j ) {
					var that = this;
					jQuery('#mp3j_tabbutton_' + j).click( function (e) {
						that.changeTab( j );
					});
				},
				
				changeTab: function ( j ) {
					if ( j !== this.openTab ) {
						jQuery('#mp3j_tab_' + this.openTab).hide();
						jQuery('#mp3j_tabbutton_' + this.openTab).removeClass('active-tab');
						jQuery('#mp3j_tab_' + j).show();
						jQuery('#mp3j_tabbutton_' + j).addClass('active-tab');
						this.openTab = j;
					}
				},
				
				init: function () {
					var that = this;
					jQuery( '.mp3j-tabbutton').each( function ( j ) {
						that.add_tab_listener( j );
						if ( j !== that.openTab ) {
							jQuery('#mp3j_tab_' + j ).hide();
						}
					});
					jQuery('#mp3j_tabbutton_' + this.openTab ).addClass('active-tab');
				}
			};
			</script>
			
			<!-- On load -->
			<script> 
			jQuery(document).ready( function () {
				MP3jP.init();
			});
			</script>

			
			
			
			
			
			
		<?php
		}
?>