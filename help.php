<?php
		function mp3j_print_help_page() { 
			
			global $mp3_fox;
			$O = $mp3_fox->getAdminOptions();
			$colours_array = array();
			$openTab = '0';
			?>
			
			
			<div class="wrap">

			
							<h2>&nbsp;</h2>
							<h1>Help 
								<span class="description" style="font-size:10px;"> &nbsp; MP3-jPlayer &nbsp;|&nbsp; Version <?php echo $mp3_fox->version_of_plugin; ?></span>
								<?php 
								if ( $O['disable_jquery_libs'] == "yes" )
								{ 
									echo '&nbsp;<span style="font-size: 11px; font-weight:700; color:#f66;">(jQuery and UI scripts are turned off)</span>';
								} 
								?>
							</h1>
							<p>&nbsp;</p>
							
							
							
							<p>Add players using <code>[mp3j]</code> <code>[mp3t]</code> <code>[mp3-jplayer]</code> <code>[mp3-popout]</code> shortcodes, links to mp3s, <a href="widgets.php">widgets</a>, and <a href="<?php echo $mp3_fox->PluginFolder; ?>/template-tag-help.htm">Template Tags</a>.</p>
							
							<br />
							<h3 style="margin-left:0;">Shortcode Parameters</h3>
							<div class="Ahelp1">
								
								<h4><code>[mp3-jplayer]</code> <span class="description">Playlist player</span></h4>
								
								<h5>Parameters:</h5>
								<p><code>tracks</code> <span class="description">filenames/URIs/FEEDs (<code>,</code> separated)</span><br />
									<code>captions</code> <span class="description">caption text (<code>;</code> separated)</span><br />
									<code>vol</code> <span class="description">0 - 100</span><br />
									<code>autoplay</code> <span class="description">y/n</span><br />
									<code>loop</code> <span class="description">y/n</span><br />
									<code>dload</code> <span class="description">y / n / loggedin (show download link)</span><br />
									<code>list</code> <span class="description">y/n (show playlist)</span><br />
									<code>pick</code> <span class="description">a number (picks random selection)</span><br />
									<code>shuffle</code> <span class="description">y/n (shuffle track order)</span><br />
									<code>title</code> <span class="description">text above player</span><br />
									<code>pos</code> <span class="description">rel-L, rel-C, rel-R, left, right</span><br />
									<code>width</code> <span class="description">in px or %</span><br />
									<code>height</code> <span class="description">in px (player height excluding list)</span><br />
									<code>pn</code> <span class="description">y/n (show prev/next buttons)</span><br />
									<code>stop</code> <span class="description">y/n (show stop button)</span><br />
									<code>id</code> <span class="description">a page id (to read the custom fields from)</span><br />
									<code>images</code> <span class="description">comma separated list of track image urls</span><br />
									<!--<code>imglinks</code> <span class="description">comma separated list of arbitrary links</span><br />-->
									<code>fsort</code> <span class="description">asc/desc (folder feed direction)</span><br />
									<code>style</code> <span class="description">modify player style</span> <strong><a href="<?php echo $mp3_fox->PluginFolder; ?>/style-param-help.htm">Help</a></strong></p>
								
							</div>
							
							<div class="Ahelp1">
								<h4><code>[mp3j]</code> &amp; <code>[mp3t]</code> <span class="description">Single-track players</span></h4>
								
								<h5>Parameters:</h5>
								<p><code>track</code> <span class="description">filename or URI</span><br />
									<code>caption</code> <span class="description">caption text (right of title)</span><br />
									<code>vol</code> <span class="description">0 - 100</span><br />
									<code>volslider</code> <span class="description">y/n</span><br />
									<code>autoplay</code> <span class="description">y/n</span><br />
									<code>loop</code> <span class="description">y/n</span><br />
									<code>title</code> <span class="description">text (replaces both title and caption)</span><br />
									<code>bold</code> <span class="description">y/n</span><br />
									<code>flip</code> <span class="description">y/n (play/pause)</span><br />
									<code>ind</code> <span class="description">y/n (hide indicator and time)</span><br />
									<code>flow</code> <span class="description">y/n (don't line break)</span><br />
									<code>style</code> <span class="description">modify player style</span> <strong><a href="<?php echo $mp3_fox->PluginFolder; ?>/style-param-help.htm">Help</a></strong></p>
								
								<h5>Also for <code>[mp3t]</code></h5>
								<p><code>play</code> play button text<br /><code>stop</code> pause button text</p>
								
							</div>
							
							<div class="Ahelp1">
								<h4><code>[mp3-popout]</code> <span class="description">Link to pop-out player</span></h4>
								
								<h5>Parameters:</h5>
								<p><code>tracks</code> <span class="description">files/URIs/FEEDs (<code>,</code> separated)</span><br />
									<code>captions</code> <span class="description">caption text  (<code>;</code> separated)</span><br />
									<code>vol</code> <span class="description">0 - 100</span><br />
									<code>autoplay</code> <span class="description">y / n</span><br />
									<code>loop</code> <span class="description">y / n</span><br />
									<code>dload</code> <span class="description">y / n / loggedin (show download link)</span><br />
									<code>list</code> <span class="description">y/n (show popout playlist)</span><br />
									<code>pick</code> <span class="description">number (pick random selection)</span><br />
									<code>shuffle</code> <span class="description">y/n (shuffle track order)</span><br />
									<code>title</code> <span class="description">title for the popout window</span><br />
									<code>pos</code> <span class="description">rel-L, rel-C, rel-R, left, right (link position)</span><br />
									<code>text</code> <span class="description">text for the link</span><br />
									<code>height</code> <span class="description">px (popout player height excluding it's list)</span><br />
									<code>id</code> <span class="description">a page id (to read the custom fields from)</span><br />
									<code>tag</code> <span class="description">html tag for text (eg. <code>h2</code>, Default is <code>p</code>)</span><br />
									<code>image</code> <span class="description">image for the popout link</span><br />
									<code>images</code> <span class="description">comma separated list of track image urls</span><br />
									<!--<code>imglinks</code> <span class="description">comma separated list of arbitrary links</span><br />-->
									<code>fsort</code> <span class="description">asc/desc (folder feed direction)</span><br />
									<code>style</code> <span class="description">modify player style</span> <strong><a href="<?php echo $mp3_fox->PluginFolder; ?>/style-param-help.htm">Help</a></strong></p>
							</div>
														
							<!-- Not in this release
							<div class="Ahelp1">
								<h5><code>[mp3-link]</code> Play from a playlist player</h5>
								<p><code>player</code> number of the player to operate<br /><code>track</code> the track number<br /><code>text</code> link text, defaults to <code>Play</code><br /><code>bold</code> y/n</p>
							</div>
							-->
							
							<br class="clearB" /><br />
							
							<p><strong>Eg</strong>. <span class="description">Play a url:</span> <code>[mp3j track="http://somedomain.com/myfile.mp3"]</code><br />
								<strong>Eg</strong>. <span class="description">Play a file from default folder (set below) or library:</span> <code>[mp3j track="myfile.mp3"]</code><br />
								<strong>Eg</strong>. <span class="description">Make a playlist player:</span> <code>[mp3-jplayer tracks="fileA.mp3, http://somedomain.com/fileB.mp3, fileC.mp3"]</code><br />
								<strong>Eg</strong>. <span class="description">Add titles:</span> <code>[mp3-jplayer tracks="My Title@fileA.mp3, My Title@fileB.mp3, My Title@fileC.mp3"]</code><br />
								<strong>Eg</strong>. <span class="description">Add captions:</span> <code>[mp3-jplayer tracks="fileA.mp3, fileB.mp3" captions="Caption A; Caption B"]</code></p>
							<p><a href="http://sjward.org/jplayer-for-wordpress" target="_blank">More shortcode examples</a></p>
							<br />
							
							<p><strong>Use these commands with [mp3-jplayer] in the <code>tracks</code> parameter to playlist entire folders or the library:</strong></p>
							<p><code>FEED:LIB</code> playlists all mp3s in the library.<br />
								<code>FEED:DF</code> playlists your default folder.<br />
								<code>FEED:/my/music</code> playlists a folder or subfolder (relative to root of domain, not the WP install)</p>
							<p><strong>Eg</strong>. Play 5 random tracks from the library: <code>[mp3-jplayer tracks="FEED:LIB" pick="5"]</code><br />
								<strong>Eg</strong>. Play everything in the folder called 'tunes': <code>[mp3-jplayer tracks="FEED:/tunes"]</code></p>
								
							<p>The <code>tracks</code> parameter can contain a mix of FEEDs and filenames/urls, eg. <code>[mp3-jplayer tracks="myfileA.mp3, FEED:/tunes, Title@myfileB.mp3, FEED:DF"]</code>.
								When just a filename is used the file must be in either your media library or in the default folder (set on this page). You can also specify a file in a sub 
								directory in the default folder eg. <code>tracks=&quot;subfolder/file.mp3&quot;</code>.</p>
							
							<br />
														
							<h3 style="margin-left:0;">Custom Fields</h3>
							<p>You can write playlists into the custom fields that are on page and post edit screens (check your 'screen options' at top-right 
								if they're not visible). They can be picked up with any of the shortcodes (from any page/post, or with the template tag, or by the widgets). See below for how 
								to set them up and some example uses:</p>
							
							<p class="description">1. Enter <code>mp3</code> into the left hand box (the 'key' box).<br />2. Write the filename, URI, or 'FEED' (see above) into the right hand box (the 'value' box) and hit 'add custom field'</p>
							<p>Add each track or 'FEED' in a new field pair.</p>
							<p>To add titles and captions in the custom fields use the following format:</p>
							<p class="description">1. Add a dot, then the caption in the left hand box, eg: <code>mp3.My Caption</code>
								<br />2. Add the title, then an '@' before the filename in the right box, eg: <code>My Title@filename</code></p>
							<p>The keys (left boxes) can be numbered, eg:<code>1 mp3</code> will be first on the playlist.</p>
							
							<p><strong>Eg</strong>. If a custom field key / value pair is set as <code>mp3</code> / <code>FEED:LIB</code>, then the library is available to any shortcodes, so:<br />
								
								<code>[mp3j]</code> or <code>[mp3t]</code> <span class="description">plays the next track from the library in a single player</span> <br />
								<code>[mp3j track="3"]</code> or <code>[mp3t track="3"]</code> <span class="description">plays track 3 from the library in a single player</span> <br />
								<code>[mp3-jplayer]</code> <span class="description">playlists all the custom fields in a playlist player</span><br /></p>
							
							<br />
							
							
							<h3 style="margin-left:0;">Widgets</h3>
							<p class="description">
								MP3j-sh - <span class="description">Adds players by writing shortcodes.</span><br />
								MP3j-ui - <span class="description">Adds a playlist player by using tick boxes and modes. Note that some features such as manually written captions, and additional style (css classes) cannot be set with this widget, use the mp3j-sh widget instead.</span>
								</p>
							<br />
							<h3 style="margin-left:0;">Template Tags</h3>
							<p class="description">For use in theme files, See <a href="<?php echo $mp3_fox->PluginFolder; ?>/template-tag-help.htm">Template Tag Help</a> for more details.</p>
							<p style="line-height:22px;"><code style="font-size:13px;">mp3j_addscripts( $style )</code><br /><code style="font-size:13px;">mp3j_put( $shortcodes )</code><br /><code style="font-size:13px;">mp3j_grab_library( $format )</code><br/><code style="font-size:13px;">mp3j_debug()</code></p>

							<br />
							
							<h3 style="margin-left:0;">Testing</h3>
							<p><strong>Make sure you're using default plugin settings for these tests.</strong></p>
							<p>You can test the plugin with a valid mp3 by copy/pasting the following shortcode into a page or post:<br />
								<code>[mp3-jplayer tracks="http://sjward.org/seven.mp3"]</code></p>
								
							<p>To test link replacement copy/paste one of these links, if you're not sure which one then paste them both (one of them should work):</p>
							<p>If you use the visual (default) editor: <code><a href="http://sjward.org/seven.mp3">Test link</a></code><br />
							If you use the text editor: <code>&lt;a href="http://sjward.org/seven.mp3"&gt;Test link&lt;/a&gt;</code></p>
				
							<p><br />Links to info:<br /><a href="http://sjward.org/jplayer-for-wordpress">Demo page</a><br /><a href="<?php echo $mp3_fox->PluginFolder; ?>/style-param-help.htm">Style Parameter Help</a><br /><a href="<?php echo $mp3_fox->PluginFolder; ?>/remote/help.txt">Forcing Remote Downloads</a><br /><a href="<?php echo $mp3_fox->PluginFolder; ?>/template-tag-help.htm">Template Tag Help</a></p>
							<br /><br />

			
			
			</div>
			
			
		<?php	
		}
?>