=== MP3-jPlayer ===
Author URI: http://www.sjward.org
Plugin URI: http://www.sjward.org/jplayer-for-wordpress
Contributors: simon.ward
Tags: mp3, audio, player, music, jplayer, integration, music player, mp3 player, playlist, media, jquery, javascript, plugin, shortcode, css, post, page, sidebar 
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 1.3.4

Auto adds an mp3 audio player to pages / posts that you make a playlist on. Can be customised into themes.



== Description ==
Version 1.3.4 adds a set of template tags for sidebar/header players etc, new shortcode options including centering and download setting, and a smaller player size option. 

Features:

* No setup.
* Play mp3's from your Media Library, a default folder, another domain.
* Add titles and captions to any mp3.
* Set playlists for download.
* Use or overide the library titles/captions.
* Optional shortcode.
* Simple admin panel.
* Template tags.
* CSS styleable.
* Integrates jquery jPlayer, works on the iPad.


<br />
[See a Demo here](http://sjward.org/jplayer-for-wordpress)

<br />
The player can be added to the most recent post on the post index (that has a playlist), or using template tags it can be put in sidebars/headers etc, fed a playlist, appear on archive pages, and set a stylesheet. Player has sliders, loader bar, status info, and optional download button.
  

<br /><br />
**Making a Playlist**


Add tracks on page/post edit screens using the custom fields (below the content box), as follows:

1. In the left box of a new custom-field enter:

<code>mp3</code>


2. Write the filename or URI* of the mp3 into the right box and hit 'Add custom field'.


Repeat the above to add more tracks, and hit the 'update/publish' button when you're done.

<br />
*Use a full URI when the mp3 is not in either a) the library or b) from the default folder/uri. You'll need to set the default folder that you want to use on the settings page.


<br /><br />
**Adding a Title**

Add titles in the right box, before the filename (or uri), separate with an @ sign, eg:

<code>Title@filename</code>


<br /><br />
**Adding aCaption**

Add the caption in the left hand box after 'mp3', separate with a dot, eg.

<code>mp3.Caption</code>


You can blank out a library caption (or a caption that's been carried over from a previous track) by using just the dot (ie. 'mp3.')


<br /><br />
**Play Order**

To control the playlist order number the left hand boxes, eg:

<code>1 mp3</code>


<code>2 mp3.Caption</code>


<code>3 mp3.Another Caption</code>



<br /><br />
**Shortcode**

Using the shortcode is optional, it lets you position the player within the content rather than at the top of it, and has 4 optional attributes for controlling the position (pos), download setting (dload), autoplay (play), and show playlist (list) on each page. The shortcode is:

**<code>[mp3-jplayer]</code>**


The attributes are:

pos: left, right, rel (or none), rel-C, rel-R, absolute

dload: true, false

play: true, false

list: true, false


<br />
for example

**<code>[mp3-jplayer play="true" pos="rel-C" dload="true"]</code>**



<br /><br />
**Template Tags**

**Quick example:**
**Make the player move to sidebar on the posts index and play 5 random tracks from your library**

Put this in index.php before the posts loop starts:

`<?php if ( function_exists('mp3j_flag') ) { mp3j_flag(); } ?>`


<br />
Put this in sidebar.php somewhere below the opening div(s):

`<?php 
if ( function_exists( 'mp3j_grab_library' ) ) { 
	$lib = mp3j_grab_library();
	$files = $lib['filenames'];
	shuffle( $files );
	$files = array_slice( $files, 0, 5 );
	mp3j_set_meta( $files );
	mp3j_put( 'feed' );
} 	
?>`


<br />
To use the smaller player stylesheet on the above example put this in header.php above wp_head(): 

`<?php 
if ( function_exists('mp3j_addscripts') ) { 
	if ( is_home() ) {
		mp3j_addscripts('/wp-content/plugins/mp3-jplayer/css/mp3jplayer-blu-sidebar.css'); 
	}
}
?>`


<br /><br />
**Tag Details**

Note: there's an admin option to ignore the tags which needs to be unticked when you want to use them!

**<code>mp3j_addscripts( $style )</code>**


 - Forces the player's javascript/CSS to be loaded and allows you to change stylesheet. Scripts aren't automatically enqueued on archive pages and any singular that has no playlist of it's own. When used this tag must be placed above wp_head().

 - $style can be either a uri to a stylesheet, or  'styleA', 'styleB', 'styleC', 'styleD' to use one of the included. Defaults to admin setting if not specified.


<br />

**<code>mp3j_flag( $set )</code>**


 - Tells the plugin to ignore content and shortcodes and to wait for an mp3j_put tag. The flag tag can be anywhere and can be used more than once. 

 - $set can be either 1 (set the flag) or 0 (unset the flag), and is 1 if not specified.


<br />

**<code>mp3j_grab_library( $format )</code>**


 - returns an array of all the mp3's in the library with their 'filenames', 'urls', 'titles', 'excerpts', and 'descriptions'. Can be used anywhere.

 - $format can be either 1 (gives back the above fields in indexed arrays) or 0 (gives back the arrays as returned from the select query), defaults to 1.


<br />

**<code>mp3j_set_meta( $tracks, $captions )</code>**
 
 
 - Sets an on-the-fly playlist for the mp3j_put tag to pick up. Can be used anywhere to create a playlist. The arrays you feed in go through the same sorting/filtering routine as if the tracks had been pulled from a page or post, and still respond to the admin settings like 'hide file extension' or 'play in alphabetical order'.
 
 - $tracks must be an indexed array of any mix of either filenames (from default folder or library) or full uri's, and can include a prefixed title using an '@' as a separator same as the fields do. As the admin settings are still applied, if 'always use library titles..' is ticked and it's a library 'filename' that you're using then any corresponding caption in the $captions array won't make it through, to get control of titles and captions for library files use their 'urls' in the $tracks array.   
 
 - $captions is an optional array, the indexes should correspond to the indexes of their files in the $tracks array.
 
 
<br />

**<code>mp3j_put( $mode, $position, $dload, $autoplay, $playlist )</code>**


 - Puts the player on the page (but only if mp3j_flag is set and what you're asking it to play results in some tracks!). Can be used multiple times and must be within the &lt;body&gt;&lt;/body&gt; section of a page. 

 - $mode can be: A post id to grab tracks from; 'first' to pick up the tracks from the first content encountered that had a playlist (see note below); 'feed' to pick up an alternative playlist created with mp3j_set_meta; or not set ('') to pick up tracks from any current id;
 
 - $pos can be 'left', 'right' for float; 'none', 'rel-C', 'rel-R' for relative position; or 'absolute'). Defaults to admin setting

 - $dload - show download button, 'true' or 'false'. defaults to admin setting.

 - $autoplay - 'true' or 'false'. defaults to admin setting.
 
 - $playlist - start with playlist showing, 'true' or 'false'. defaults to admin setting.
 
 - Note on 'first': Typically you'd use this on an index page when the player is in the sidebar (ie. when the put tag comes after the loop has run) and you want to play the most recent tracks post. If there is no first id to collect (when no posts have a playlist) the player would not be added, to set a backup use another put tag directly underneath the first with $mode set to some id you want to pick up tracks from, or set to 'feed' to pick up an alternative playlist you've created using mp3j_set_meta. 
 
 - Another note on 'first': Because it actually waits for the content and has a look for tracks, it won't do anything if the put tag using 'first' is above the loop. To get header players to play the first post with tracks you either have to put the put-tag in a div after the loop and css absolute position it, or query the upcoming posts and use the id.  
 
<br />

**<code>mp3j_debug($info)</code>**


 - Prints some info and variables from the plugin to the browser's source view (CTRL+U or Page->view source) about what content and tags appeared on the page that just ran. Can be used more than once to get info at different points in page. Can be useful for debugging when customising templates.

 - $info can be 'vars' to see info only or 'all' to also see meta and library arrays (a potentially long list), defaults to vars.


<br /><br />
Best to use function_exists() to make sure the tags exist before running them, eg:

`<?php if ( function_exists('mp3j_addscripts') ) { mp3j_addscripts('styleD'); } ?>`

Running them without checking when the plugin is not activated will throw an error.


<br />
== Installation ==

To install using Wordpress:

1. Download the zip file to your computer.
2. Log in to your Wordpress admin and go to 'plugins' -> 'Add New'.
3. Click 'Upload' at the top of the page then browse' for the zip file on your computer and hit the 'Install' button, Wordpress should install it for you.
4. Once installed go to your Wordpress 'Plugins' menu and activate MP3-jPlayer.

To Install manually:

1. Download the zip file and unzip it. 
2. Open the unzipped folder and upload the entire contents (1 folder and it's files and subfolders) to your `/wp-content/plugins` directory on the server.
3. Activate the plugin via your Wordpress 'Plugins' menu.



== Frequently Asked Questions ==

= Can the player go in the header/sidebar? =
Yes from this version 1.3.4 if you use template tags. It's not a widget yet so you can't do it from the admin area currently.


= Why is the default folder set to your domain? =
It's not anymore, it was going to be for testing a default install without needing to know any mp3's. So you could activate, go to a page and use 'test' as the filename and it would play that file from my domain, but a)i forgot to mention it, and b)it's prob not that useful. On fresh install it's now set to the root of your domain. 


== Screenshots ==

1. Player
2. Admin settings page
3. Playlist example 



== Changelog ==

= 1.3.4 =
* Added template tags.
* Added new shortcode attributes play and list, and added more values for pos.
* Added new default position options on settings page
* Added a smaller player option


= 1.3.3 =
* Fixed the CSS that caused player to display poorly in some themes.

= 1.3.2 =
* Added the shortcode [mp3-jplayer] and attributes: pos (left, right, none), dload (true, false) which over-ride the admin-panel position and download settings on that post/page. Eg. [mp3-jplayer pos="right" dload="true"]
* Tweaked transport button graphic a wee bit.

= 1.3.1 =
* Fixed image rollover on buttons when wordpress not installed in root of site.

= 1.3.0 =
* Updated jquery.jplayer.min.js to version 1.2.0 (including the new .swf file). The plugin should now work on the iPad.
* Fixed admin side broken display of the uploads folder path that occured when a path had been specified but didn't yet exist.
* Fixed the broken link to the (new) media settings page when running in Wordpress 3.
* Changed the 'Use my media library titles...' option logic to allow any titles or captions to independently over-ride the library by default. The option is now 'Always use my media library titles...' which when ticked will give preference to library titles/captions over those in the custom fields.
* Modified the css for compatibility with Internet Explorer 6. The player should now display almost the same in IE6 as in other browsers.

= 1.2.12 = 
* Added play order setting, a 'download mp3' link option, show/hide playlist and option, a connecting state, a new style.  
* The 'Default folder' option can now be a remote uri to a folder, if it is then it doesn't get filtered from the playists when 'allow remote' is unticked. 

= 1.2.0 =
* Added playing of media library mp3's in the same way as from the default folder (ie. by entering just a filename). User does not have to specify where the tracks reside (recognises library file, default folder file, and local or remote uri's). 
* Added filter option to remove off-site mp3's from the playlists.
* The plugin now clears out it's settings from the database by default upon deactivation. This can be changed from the settings page.
* It's no longer necessary to include the file extension when writing filenames.

= 1.1.0 =
* Added captions, player status info, a-z sort option, basic player positioning, detecting of urls/default folder
* Fixed bug where using unescaped double quotes in a title broke the playlist, quotes are now escaped automatically and can be used.

= 1.0 =
* First release
