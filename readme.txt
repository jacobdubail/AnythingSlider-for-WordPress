=== AnythingSlider for WordPress ===

Contributors: jacobdubail, Chris Coyier, ProLoser, Mottie, Doug Neiner
Plugin Name: AnythingSlider for WordPress
Plugin URI: http://jacobdubail.com/anythingslider-for-wordpress
Donate Link: http://jacobdubail.com
Tags: wp, jquery, slider
Author URI: http://jacobdubail.com/
Author: Jacob Dubail
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 0.6.3


Integrates Chris Coyier's popular AnythingSlider jQuery plugin with WordPress.  

== Description ==

AnythingSlider for WordPress integrates Chris Coyier's popular AnythingSlider jQuery plugin with WordPress.  

Based on the hard work of folks like Doug Neiner, ProLoser and Mottie, this plugin adds a plethora of flexible functionality to any WordPress theme.  By using the [anything_slides] shortcode anywhere in your theme, you can add a unique and highly customizable slideshow to any post or page.

For more details and a few video tutorials, visit <a href="http://missingpiecestudios.com" target="_blank">Missing Piece Studios</a>.

Check out the <a href="http://wordpress.org/extend/plugins/anythingslider-for-wordpress/faq/">FAQ</a> for more details.


= Changelog =

= 0.6.3 =

* Added an ajax form to auto-insert a slideshow into a post or page *
* Updated plugin script to verstion 1.7.4 *

= 0.6.2 =

* Update plugin scripts to version 1.7.2 *
* Added the new options available in the latest version of the jQuery plugin *
* Added a touch of JS to the admin options page to make it a little more manageable *
* Added Easing options *

= 0.6.1 =

* Bug fix for the way the Navigation Formatter runs *

= 0.6 =

* Added a new option for Navigation Formatting! Check out the FAQ for more details. *
* Update plugin scripts/css to version 1.7.1 *
* Fixed a bug with the video extension script loader *
* Updated a few options that are included or deprecated in version 1.7.1 of the jQuery plugin *

= 0.5.1 =

* Update JS to latest version of the jQuery plugin *

= 0.5 = 

* Fixed an error when loading custom CSS from Theme directory.  Add anythingslider.css to your theme, if you'd like to use a custom CSS file.
* Fixed an error with the new Video script.

= 0.4 =

* Upgraded to AnythingSlider 1.6
* Added support for Video Playback options

= 0.3 =

* More bug fixes
* Support for custom CSS file - Add anythingslider.css to your theme folder or your theme's CSS folder (overrides the default CSS)
* Cleaned up the settings page descriptions to be more clear/intuitive
* Added a ton of options to the settings page
* Added a few options to the shortcode
* Improved documentation

= 0.2 = 

* Many bug fixes
* Added a variety of new global options

= 0.1 =

Initial release.  Many improvements to come!

== Installation ==

You can install AnythingSlider for WordPress directly from the WordPress admin panel. Visit the Plugins/Add New page and search for 'AnythingSlider for WordPress'. Click to automatically install.

Once installed and activated visit the AnythingSlider admin page (Settings/AnythingSlider) to customize the global AnythingSlider settings.

If you're old school, you can download the plugin, upload the files into the wp-content/plugins directory of your server via FTP, and then visit the Plugins page of the admin panel to activate it.

== Upgrade Notice ==

test

== Screenshots ==

1. Global settings admin page
2. Add new slides
3. Adding the shortcode to a post or page with all of the optional options
4. A slideshow on the homepage

== Changelog ==

= 0.6.3 =

* Added an ajax form to auto-insert a slideshow into a post or page *
* Updated plugin script to verstion 1.7.4 *

= 0.6.2 =

* Update plugin scripts to version 1.7.2 *
* Added the new options available in the latest version of the jQuery plugin *
* Added a touch of JS to the admin options page to make it a little more manageable *
* Added Easing options *

= 0.6.1 =

* Bug fix for the way the Navigation Formatter runs *

= 0.6 =

* Added a new option for Navigation Formatting! Check out the FAQ for more details. *
* Update plugin scripts/css to version 1.7.1 *
* Fixed a bug with the video extension script loader *
* Updated a few options that are included or deprecated in version 1.7.1 of the jQuery plugin *

= 0.5.1 =

* Update JS to latest version of the jQuery plugin *

= 0.5 = 

* Fixed an error when loading custom CSS from Theme directory.  Add anythingslider.css to your theme, if you'd like to use a custom CSS file.
* Fixed an error with the new Video script.

= 0.4 =

* Upgraded to AnythingSlider 1.6
* Added support for Video Playback options

= 0.3 =

* More bug fixes
* Support for custom CSS file - Add anythingslider.css to your theme folder or your theme's CSS folder (overrides the default CSS)
* Cleaned up the settings page descriptions to be more clear/intuitive
* Added a ton of options to the settings page
* Added a few options to the shortcode
* Improved documentation

= 0.2 = 

* Many bug fixes
* Added a variety of new global options

= 0.1 =

Initial release.  Many improvements to come!

== Frequently Asked Questions ==

= How do I use this plugin? =

It's easy! Create a few slides using the Slides custom post type. Then, on a post or page of your choosing, use the [anything_slides] shortcode to embed the slideshow.

= How do I display one category, specifically, in my slideshow? =

There are a variety of options you can add to the [anything_slides] shortcode:

* cat *the category slug of your desired category*
* width or height *the width or height of the slideshow - this overrides the global settings*
* delay *the time between slides*
* resume *time between user interaction and slideshow resume*
* animation *how long the slide animation lasts*

= How can I add this to a theme file? =

Easy. Just use the standard method of embedding shortcodes into theme files: `<?php echo do_shortcode( "[anything_slides]" ); ?>`

For more information about this plugin, and to watch a few videos, check out <a href="http://missingpiecestudios.com" target="_blank">Missing Piece Studios</a>.

For more information about the jQuery plugin, visit <a href="http://proloser.github.com/AnythingSlider/" target="_blank">this GitHub repo</a>.

= How do I customize the CSS for my slideshows? =

Simply add the file 'anythingslider.css' to your theme directory, or your theme's CSS directory.  That's it. The plugin will detect the file's existence and do the heavy lifting for you.

= How do I use the new Navigation Formatter feature =

You'll notice on the Slides post/page there is a new metabox for Navigation Formatter.  Add the text you'd like to use into this box.

Next add the new attribute to the shortcode wherever you'd like to use custom navigation formatting.

The shortcode has a new attribute `navFormat`.  The existence of this attribute in your shortcode call will grab the text you just entered and use that for the navigation title.

If that's too confusing... which it probably is, check out a how-to video at <a href="http://missingpiecestudios.com" target="_blank">Missing Piece Studios</a>.


== Donations ==

I happily made this plugin for the community.  As I improve it and add more features, I may ask for donations.  In the meantime, enjoy!