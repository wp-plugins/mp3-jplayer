=== MP3-jPlayer ===
Author URI: http://www.sjward.org
Plugin URI: http://www.sjward.org/jplayer-for-wordpress
Contributors: simon.ward
Tags: mp3, audio, player, music, jplayer, integration, music player, mp3 player, playlist, media, jquery, javascript, plugin, shortcode, css 
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 1.3.2

Auto adds an mp3 audio player to pages/posts that you make a playlist on.



== Description ==

This version adds shortcode support

features:
- Easy playing of mp3's from your media library, a default folder, another domain
- Use custom fields to make ordered playlists
- Add titles and captions, use or overide media library titles and captions
- No setup
- Simple admin panel
- Uses jPlayer
 
The player has a loader bar and position/vol sliders, status info, hideable playlist, and optional download mp3 button.

[See a Demo here](http://sjward.org/jplayer-for-wordpress)


**Shortcode**

Using the shortcode is optional, it lets you position the player within the content rather than at the top, and has 2 optional attributes for position and download control on each page. The shortcode is:
<code>[mp3-jplayer]</code>

It's optional attributes are 'pos' (with values of left, right, none), and 'dload' (true, false), eg:
<code>[mp3-jplayer pos="right" dload="true"]</code> 


**Making a Playlist**

Add tracks on the page/post edit screen using the custom fields (below the content box). Enter them as follows:

1. In the left box of a new custom-field line enter:

<code>mp3</code>

2. Write the filename (or URI)* of the mp3 into the right box and hit 'Add custom field'. Repeat the above to add more tracks, and hit the 'update/publish' button when you're done.

*NOTE - You only need a full URI when the mp3 is not in the library/default folder , otherwise just the filename will do (can leave off the file extension).



**Adding a Title and Caption**

Add the title in the right hand box before the filename(or uri), separate with an @ sign, eg:

<code>MyTitle@myfilename</code>



**Adding Captions**

Add the caption in the left hand box after <code>mp3</code>, separate with a dot.

<code>mp3.My Caption</code>



**Play Order**

To control the playlist order number the left hand boxes, eg:
<code>1 mp3</code>
<code>2 mp3.some caption</code>
<code>3 mp3.Another Caption</code>



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



== Screenshots ==

1. Player style
2. Admin settings page
3. Playlist example 



== Changelog ==
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
