=== AnythingSlider for WordPress ===

Contributors: jacobdubail, Chris Coyier, ProLoser, Mottie, Doug Neiner
Plugin Name: AnythingSlider for WordPress
Plugin URI: http://jacobdubail.com/anythingslider-for-wordpress
Donate Link: http://jacobdubail.com
Tags: wp, jquery, slider
Author URI: http://jacobdubail.com/
Author: Jacob Dubail
Requires at least: 3.0
Tested up to: 3.2RC1
Stable tag: 0.2
Version: 0.2

Integrates Chris Coyier's popular AnythingSlider jQuery plugin with WordPress.  

== Description ==

AnythingSlider for WordPress integrates Chris Coyier's popular AnythingSlider jQuery plugin with WordPress.  

Based on the hard work of folks like Doug Neiner, ProLoser and Mottie, this plugin adds a plethora of flexible functionality to any WordPress theme.  By using the [anything_slides] shortcode anywhere in your theme, you can add a unique and highly customizable slideshow to any post or page.

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

Easy. Just use the standard method of embedding shortcodes into theme files: `<?php do_shortcode( "[anything_slides]" ); ?>`


== Donations ==

I happily made this plugin for the community.  As I improve it and add more features, I may ask for donations.  In the meantime, enjoy!