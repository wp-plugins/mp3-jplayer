=== MP3-jPlayer ===
Author URI: http://www.sjward.org
Plugin URI: http://www.sjward.org/jplayer-for-wordpress
Contributors: simon.ward
Tags: mp3, player, jplayer, music player, jplayer wordpress, mp3 player
Requires at least: 2.8 (lowest tested version)
Tested up to: 3.0.1
Stable tag: 1.3.0

Adds an mp3 player with playlist to any Wordpress pages/posts that you make a playlist for.


<br />
== Description ==

MP3-jPlayer automatically adds an mp3 player to any posts/pages that you have created a playlist for. The plugin makes it simple to play mp3’s from your Wordpress media library, or from a default folder, or from another domain, It also lets you add your own titles and captions, or can use your media library titles/captions. No setup is required, just install, activate and start adding tracks.

MP3-jPlayer integrates Happyworm's popular open source jPlayer code into an easy-to-use Wordpress plugin, it also provides you with an admin panel for some useful settings, and a simple method of building playlists on your pages and posts using Wordpress' existing custom fields that are on all post/page edit screens.


<br />
**Features**

1. Allows you to add titles and captions to tracks that aren't in your Wordpress media library.
2. Allows you to over-ride your media library titles and captions with different ones.
3. Provides an admin settings page with various player and playlist settings, a default folder/URI setting for easy playing of non-library tracks. Some position and style options.
4. Lets you to include a 'Download mp3' button on the player.
5. Is CSS styleable.


<br />
**Making A Playlist**

Add your mp3's on the page's or post's edit screen using the custom fields (below the content box). Enter them as follows:

1. Enter <code>mp3</code> into the left box of a new custom-field line.
2. Write the filename (or URI)* of the mp3 into the right box and hit 'Add custom field'.
3. Repeat the above to add more tracks, and hit the 'update/publish' button when you're done.

*You only need a full URI when the mp3 is not in either your library or your specified default folder (set at the admin settings page), otherwise just enter the filename (you can leave off the file extension).


<br />
**Adding Titles and Captions**

*Add the title in the right hand box before the filename/uri, separate with an @ sign.*
For example if your mp3 filename is happysong.mp3 and you want the title to be 'Happy Song' then write:

<code>Happy Song@happysong</code>

If no title is available (from either your Wordpress library or the custom fields) then the filename is used as the title.

*Add the caption in the left hand box after mp3, separate with a dot.*
For example if the track was by 'The Horse' then you could write:

<code>mp3.The Horse</code>

Note: Your previous caption is normally given to subsequent tracks when they don't have a caption of their own (this saves you having to write the same caption each time when all tracks on the playlist are by 'The horse'). To stop this and blank out the caption again add just the dot like so:

<code>mp3.</code>


<br />
**Ordering the Tracks**

*To control the playlist order simply number the left hand boxes, for example:*

<code>1 mp3</code>

Any numbered tracks take priority at the top of the playlist with un-numbered tracks underneath. The single space after the number is optional.

Note: The A-Z sorting option at the settings panel will over-ride any number ordering in the playlists.


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


<br />
== Screenshots ==

1. Player new style in blue, green, and neutral.
2. Player original style in cyan.
3. The admin settings page.
4. An example playlist on an edit post/page screen. 


<br />
== Frequently Asked Questions ==

= Can I have more than one player on the posts index page? =
No, only the one player will be added, it's added to the first post in the displayed list of posts that has a playlist. 

= Can I choose which mp3's will show a download button? =
Currently no, but it's on the list of additions for the next release. 

= How do I put the player in my sidebar? =
Again, this is on the list for the next release!

= I want the player to appear somewhere down the page content and not at the top, is there a shortcode? =
Not yet, it's another thing on the to do list!!

= The player doesn't show up, how do I know that the player is working on my site? =
The player will not appear if it doesn't think there's at least one mp3 to play on the page. You can test it as follows:
1. Using the player's default settings, go to any post/page edit screen.
2. In a new custom field write 'mp3' into the left box, and write 'test' in the right hand box.
3. Save the page and view it.

The player should now appear and will try to play a test track from the plugin author's domain. 

= How do I restore the default settings? =
Go to the settings page, uncheck the box labeled 'Remember my settings when plugin is deactivated' and click 'Update Settings', go to the 'Plugins' menu and de-activate MP3-jPlayer, then activate it again to restore the defaults.
 

<br />
== Changelog ==

= 1.3.0 =
* Updated jquery.jplayer.min.js to version 1.2.0 (including the new .swf file). The plugin should now work on the iPad.
* Fixed admin side broken display of the uploads folder path that occured when a path had been specified but didn't yet exist.
* Fixed the broken link to the (new) media settings page when running in Wordpress 3.
* Changed the 'Use my media library titles...' option logic to allow any titles or captions to independently over-ride the library by default. The option is now 'Always use my media library titles...' which when ticked will give preference to library titles/captions over those in the custom fields.
* Modified the css for compatibility with Internet Explorer 6. The player should now display almost the same in IE6 as in other browsers.

= 1.2.12 = 
* Added a new player style option on the settings page, and 3 colour variations including a neutral grey.
* Changed download button display states to give a better visual cue.

= 1.2.11 =
* Added an option to include a 'download mp3' link on the player.

= 1.2.10 =
* Fixed bug (created in v1.2.9) where player was breaking if remote tracks were filtered from the playlist.  

= 1.2.9 =
* Added a play order setting method to allow ordering of some or all tracks on a playlist. Any numbered custom keys (eg "1 mp3") appear in order at the top of the playlist.
* Added ability to show/hide the playlist, and to choose it's default state from the admin settings page.
* Added a 'connecting' state and animated gifs to player status for more visual indication that player is functioning when the connection is slow.
* Restructured/improved the code a little. When on a single page/post now grabbing the meta keys/values while in the head to avoid enqueueing scripts or writing js if there's no tracks on a page. This should eventually be done for posts index page as well. Currently the adding of the the player on the index is left until the posts loop runs which should be fail-safe against any other plugin modifying the displayed posts during the loop. 
* Updated jquery to version 1.4.2 (this shouldn't affect WP versions proir to 3 as WP uses it's included jquery version in preference).
* The 'Default folder' option can now be a remote uri to a folder, if it is then it doesn't get filtered from the playists when 'allow remote' is unticked. 

= 1.2.0 =
* Added media library integration to allow adding of mp3's in the same way as from the default folder (ie. by entering just a filename). If the track is in the library then using it's full uri and conditionally using the titles and captions. User does not have to specify where the tracks reside (recognises library file, default folder file, and local or remote uri's). 
* Added an admin option to filter out any off-site mp3's from the playlists if desired.
* Added an admin option to hide the file extension should a filename be displayed.
* Fixed bug where using @ signs in a title would break the track (the filename/uri was written incorrectly). Titles can now include @ signs. Similarly fixed bug where using dots in a caption truncated the caption.
* The plugin now clears out it's settings from the database by default upon deactivation. This can be changed from the settings page.
* The A-Z sort now ignores capitalisation.
* It's no longer necessary to include the file extension when writing filenames.

= 1.1.0 =
* Added detecting of urls in the playlist allowing over-rides of default folder and playing of off-site mp3's.
* Added status info in player display to show player's current state (ready, buffering, playing, stopped, paused).
* Added admin option to put a player on posts index page, the player is now added to the first post that has a playlist when on the index.
* Added ability to give mp3's a caption along with the title.
* Added some basic player positioning options.
* Added field checking/correction when the admin default folder value is updated.
* Added a-z sort option.
* Fixed bug where using unescaped double quotes in a title broke the playlist, quotes are now escaped automatically and can be used.

= 1.0 =
* First release
