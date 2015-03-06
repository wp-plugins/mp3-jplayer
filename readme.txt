=== MP3-jPlayer ===
Author URI: http://sjward.org
Plugin URI: http://mp3-jplayer.com
Contributors: simon.ward
Donate link: http://www.sjward.org/jplayer-for-wordpress
Tags: audio, audio player, audio playlist, mp3 player, music player, media, mobile, iphone, ipad, integration, multisite, playlist player, media player, audio widget, audio download, html5 audio, music player, mp3, music, html5, flash, jplayer, playlist, jquery, shortcode, widget, css, post, page, sidebar, html 5
License: GPLv2 or later
Requires at least: 2.9
Tested up to: 4.1
Stable tag: 2.3.1

Easy, Flexible Audio for WordPress.

== Description ==

= Mobile friendly HTML5 audio players and audio playlist players =

* Adds style and colour options for audio players.
* Enhanced music playlist players and single-file audio players
* Additional popout players and popout links, audio widget players, and audio downloads.
* Works with all built-in WordPress options for adding your music, including the recently introduced *Create Audio Playlist* drag & drop interface.


[Player Demos](http://mp3-jplayer.com/player-skins/) <br>
[How to Add Your Players](http://mp3-jplayer.com/adding-players/) <br>
[Help & Docs](http://mp3-jplayer.com/help-docs/)


MP3-jPlayer will expand WP's native shortcodes with new functions and options, giving you a lot of choice in how to set up your music playlists. Here's a few of the features:

* Flexible multi-player audio plugin, add unlimited music players to pages, posts, sidebars, and template files.
* Offer audio downloads to visitors or logged-in users.
* Playlist folders with one simple feed folder command.
* Can be selectively integrated with WordPress default audio players.
* Customise the colour scheme, fonts, title and image placements and more on the Player Design page.
* A fully integrated Pop-out player that can be launched from playlist players, or from a stand-alone popout link.
* Shortcode parameters to give you individual control of player heights, widths, volumes, downloads, styling, and allow you to play from your library, local folders or urls.
* You can use custom fields to manage playlists.
* Supports playback via HTML5 wherever possible, and falls back to Flash automatically if necessary.
* Supports mp3, m4a, mp4, webm, oga, ogg, and wav files.
* Very easy file counterparting, just upload.
* Plays Icecast and Shoutcast audio streams.
* Great compatibility across browsers / platforms. Works on iPhone, iPad, Android.
* Editable player designs via CSS.
* Multisite compatible.



See [the plugin's home page](http://mp3-jplayer.com) for info, demos, documentation, and help articles.


== Installation ==

Install using WordPress:

1. Log in and go to 'plugins' -> 'Add New'.
3. Search for 'mp3-jplayer' and hit the 'Install now' link in the results, Wordpress will install it.
4. Activate the plugin.

Install manually:

1. Download the zip file and unzip it. 
2. Open the unzipped folder and upload the entire contents (1 folder and it's files and subfolders) to your `/wp-content/plugins` directory on the server.
3. Activate the plugin through the WordPress 'Plugins' menu.


== Frequently Asked Questions ==

Make sure you check out the [Support Site](http://mp3-jplayer.com/help-docs/) for lots of helpful info!


= Supported file formats ? =
mp3, m4a, mp4, oga, ogg, wav, webm, webma.

= Theme requirements ? =
Themes need the standard wp_head() and wp_footer() calls in them.

= Can't locate audio message ? =
Check your filename/url spelling if you're writing them manually. Remove any accented letters from mp3 filenames (Delete the originals and re-upload if they're from the library).

= Player doesn't show up ? =
This will happen if the playlist you've asked for doesn't result in anything to play, for example if you're using 'FEED' and the folder path is incorrect or remote, or if you're playing remote urls and the option 'allow mp3s from other domains' is unticked.

= Header and footer players ? =
Use widget areas if available, or you can use the do_shortcode() function in template files.

= Player shows but doesn't work ? =
Most times it's because of a hard-coded javascript that's in your theme or another plugin. Try switching to a default WordPress theme and see if the player works. Try deactivating other plugins one-by-one and check each time to see if the player works. 

= Report bugs/issues ? =
Either on the [forum at Wordpress](https://wordpress.org/support/plugin/mp3-jplayer), or [here](http://sjward.org/contact).


== Screenshots ==

1. An example audio playlist player.
2. A Popout playlist on a desktop.
3. A single file music player and a music playlist.
4. The plugin's Settings screen.
5. The plugin's Design screen.
6. Single-file Button audio players.
7. Single-file Text music players. 
8. A Popout player playing on an Android phone.


== Changelog ==

= 2.3.1 =
* Added developer methods for hooking into the player's Javascript events, these also add support for some of the up-coming extensions.
* Corrected the help message admin-side that shows on the widget when it's set to an invalid path.

= 2.3 =
* Fixed a major issue on index/cat/search type pages that could incorrectly assign the playlists or break players in some scenarios.
* Added support for custom js (for skin extensions) on the player Design page.
* Renamed the MP3-jPlayer plugin class instance to MP3JP.

= 2.2 =
* Simplified error handling to try and eliminate the false triggering of messages that was occurring on some devices.
* Added an option to control the frontend display of player error messages, the options are: Never / To admins only / To all. The setting is under advanced tab, default is admins only.
* Fixed the bug on index/cat pages that broke players in some scenarios when using the 'Show in full content' and 'Show in excerpts' options.
* Fixed the functioning of 'Show in excerpts' option (this option is still for manually written excerpts only).
* Added developer methods for contolling css output and design page visibility.
* Added support in the popout player for the stats collection modules (allows play and download via the popout to be captured).
* Some css hardening on jQuery ui components.

= 2.1 =
* Fixed plugin compatibility with old versions of WP pre 3.6.
* Fixed the widget's folder feed (it was picking up all audio regardless of chosen formats), and the admin-side info message that shows the track count.
* Added a 'Show images' tick option on the widget (this controls the display of any featured images that you've set for the audio).
* Made player's download link immediately visible upon page load (previously users needed to start a track in order to make the first download link available).  

= 2.0 =
* Improved plugin integration with core Wordpress media handling operations, such as those accessed with the 'Add Media' button, featured images for audio, embeded audio, attached audio, and pasted urls.
* Added support for all common file formats, the plugin now supports mp3, m4a, mp4, oga, ogg, wav, webm, webma files. Caution - Please see [Audio Format Advice](http://mp3-jplayer.com/audio-format-advice/) for help with choosing good formats for the web.
* Added new folder feed options, format controls, sorting by upload date, and auto-counterparting.
* Added a player design page with many new options for controlling font faces, sizes, alignments, and image size and alignment and more within the players.
* Added optional auto-counterparting feature that will work for FEEDs as well as individually playlisted local files.
* Added new skins that are more mobile friendly.
* Fixed autoplay behavior so that it doesn't try to activate on handheld devices (currently most mobile devices disallow autoplay).
* Added integration options allowing for selective replacement of core WP features and players.
* Added feedback messages for catching broken urls easily, and when a device cannot play the supplied file(s). 
* Many more improvements and enhancements both admin-side and frontend.

= 1.8.12 =
* Security update.

= 1.8.11 =
* Fixed a routine that could throw PHP warnings on servers running old versions of PHP (lower than 5.3).
* Added support for WP's <code>do_shortcode()</code> function. You need to be running WordPress 3.3 or higher for it to work. This also means that from WP 3.3 the plugin's template tags <code>mp3j_addScripts()</code> and <code>mp3j_put()</code> are no longer needed (though they are still supported for backwards compatibility).  

= 1.8.10 =
* Fix popout.
* Add support for players in hidden elements (it's now handled automatically).
* Start admin improvements (settings is now a top level menu). 

= 1.8.9 =
* Fix for Android/Chrome. For info on the fix see Mark Panaghiston's posts at the bottom of [this jPlayer thread](https://groups.google.com/forum/#!msg/jplayer/BoVUNok0yl4/LU8q2wggQaYJ). 

= 1.8.8 =
* Updated jQuery.jPlayer to 2.6.0.
* Improved admin-side security.
* Fixed compatibility with 'Scripts to Footer' plugin.
* Fixed a routine that could throw a php warning.

= 1.8.7 =
* Fixed autoplay in the popout when using the popout shortcode [mp3-popout].
* Improved widget detection for scripts to allow for core's wp_convert_widget_settings.
* Sorted the overlapping field on widget interface.

= 1.8.6 =
* Correct popout script references (apologies, missed in the last update meaning the popout didn't function).

= 1.8.5 =
* Updated jQuery.jPlayer to 2.5.0.
* Security updates.

= 1.8.4 =
* Updated jQuery.jPlayer to 2.3.0 (security fixes).

= 1.8.3 =
* Moved to jQuery.jPlayer 2.2.0 (fixes plugin problems with recent flash release (v11.6) in browsers like IE and Firefox (time was displaying as 'NaN', tracks not advancing/autoplaying)).
* Fixed the auto number option for arbitrary single players (they were all numbered 1!).
* Fixed quotes in captions (they were unescaped still and would break players), thanks to Chris for reporting.
* Fixed a couple of routines that could throw php warnings, thanks to Rami for reporting.
* Added the much requested option to try force browsers into saving mp3 downloads (instead of playing them in some kinda built-in player). Maintains right click save-ability. No mobile support just yet. Switched on for local files by default. Can also be set up for remote files (see the help). Option is under 'Playlist player options' on the settings page. Please feedback any issues.
* Added option to turn any mp3 links in a page into players, which means you can now add players using the 'Add media' button on the page/post edit screens. It has as an editable shortcode on the player settings page (under template options). Option is on by default. Switch it off near top of settings page.  
* Added the 'style' parameter onto the MP3j-ui widget.

= 1.8.1 =
* Some css corrections - missing image for the buttons on the 'custom' style, and the smaller font sizes when using the 'mods' option. 

= 1.8 =
* Fixed bug in javascript that caused problems in WordPress 3.5 (players broke after a couple of clicks).
* Fixed bug when single quotes ended up in a popout title (it broke players).
* Fixed bug in widget when it was set with a non-existent page id (it broke players).
* Fixed bug with mp3j_put function (it could pick up the adjacent post's tracks in some scenarios).  
* Fixed bug with https urls.
* Fixed bug in pick parameter.
* Fixed bug with 'Allow mp3s from other domains' option (it affected single players when it was unticked).
* Fixed display of hours on long mp3s (player will display the hours only when needed).
* Fixed css that was hiding playlists in Opera browser.
* Fixed titles running into captions.
* Fixed titles obscuring slider motion (not IE proof). 
* Added 'images' parameter on [mp3-jplayer], they can be set per track and are carried to the popout.
* Added easier styling option via a 'style' parameter that can be used in shortcodes (takes class names separated by spaces). Some classes are included as follows: bigger1 bigger2 bigger3 bigger4 bigger5 smaller outline dark text bars100 bars150 bars200 bars250 nolistbutton nopopoutbutton nostop nopn wtransbars btransbars. See examples on the demo page.
* Added new download option 'loggedin' which shows alternative text/link if visitor is not logged in.
* Added shortcode [mp3-popout] which creates a link to a popout player.
* Added volume slider option and shortcode parameter for [mp3t] and [mp3j] players.
* Added order control of library mp3s (when using 'FEED:LIB'), options are (asc/desc) by upload date, title, filename, or caption/filename, this is a global setting (not per player).
* Added new shortcode parameter (fsort="desc") for reversing folder playlist order.
* Added option to run player shortcodes in manually written excerpts.
* Added template tag - mp3j_div() for use in theme files when using players in hidden/collapsable tabs, lightboxes etc (allows players to function ok in hidden elements if flash gets used).
* Added option to bypass jQuery / jQueryUI script requests.
* Added choice of separators to use when writing playlists in shortcodes/widgets.
* Added touchpunch.js for useable sliders on touch screen devices. 
* Many more improvements and minor fixes.

= 1.7.3 =
* Stopped files of audio/mpeg MIME type other than mp3 from showing on the player's library file list on the settings page. They won't appear in playlists when using 'FEED:LIB' now.  
* Corrected graphics error introduced last update on the popout button, thanks to Peter for reporting.

= 1.7.2 =
* Fixed bug in the case where sidebars_widgets array was not defined (was throwing a php warning), thanks to Craig for reporting.
* Fixed bug on search pages where full post content was being used (players in posts were breaking unless a player widget was present), thanks to Marco for reporting.
* Fixed loop parameter in single players (wasn't responding to 'n' or '0'). Thanks to George for reporting.
* Corrected the template tag handling so that it can auto pick-up mp3's from post fields on index/archive/search pages. 
* Fixed the 'text' player's colour pickup for the popout, and refined it's layout a little.
* Changed from using depreciated wp user-levels to capabilities for options page setup (was throwing a wp_debug warning).
* Corrected typos in the plugin help (invasion of capitalised L's).

= 1.7.1 =
* Fixed widgets on search pages, and added 'search' as an include/exclude value for the page filter. Thanks to Flavio for reporting.
* Fixed pick-up of default colours when using template tags, and the indicator on single players.

= 1.7 =
* Added multiple players ability, backwards compatible (see notes below).
* Added single-file players.
* Added pop-out.
* Added colour picker to settings.
* Added player width and height settings, captions (or titles) will word-wrap.
* Added shortcodes widget.
* Updated jQuery UI and fixed script enqueuing.
* Fixed page filter for widget, added index and archive options.
* Changed ul transport to div (for better stability across themes).
* General improvements and bug fixes.
* NOTE 1: File extensions must be used (previously it was optional).
* NOTE 2: Shortcodes are needed to add players within the content (previously it was optional). 
* NOTE 3: CSS has changed (id's changed to classes, most renamed), old sheets won't work without modification.

= 1.4.3 =
* Fixed player buttons for Modularity Lite and Portfolio Press themes (they were disappearing / misaligned when player was in sidebar), thanks to Nate, Jeppe, and Nicklas for the reports.
* Fixed the bug in stylesheet loading when using the mp3j_addscripts() template tag (style was not being loaded in some cases), thanks to biggordonlips for reporting. 

= 1.4.2 =
* Fixed error in the scripts handling for the widget, thanks to Kathy for reporting.
* Fixed the non-showing library captions when using widget modes 2/3 to play library files.
* Fixed (hopefully) the mis-aligned buttons that were still happening in some themes.

= 1.4.1 =
* Added a repeat play option on settings page.
* Fixed text-player buttons css in Opera.
* Fixed initial-volume setting error where only the slider was being set and not the volume. Thanks to Darkwave for reporting.

= 1.4.0 =
* Added a widget.
* Improvements to admin including library and default folder mp3 lists, custom stylesheet setting, and some new options.  
* Added new shortcode attributes shuffle, slice, id. New values for list
* Added a way to play whole folders, the entire library, to grab the tracks from another page.
* Added a simpler text-only player style that adopts theme link colours.
* Improved admin help.
* Some minor bug fixes.
* Some minor css improvements and fixes.

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
* First release on Wordpress.org
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
