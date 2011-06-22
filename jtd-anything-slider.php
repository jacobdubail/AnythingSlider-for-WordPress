<?php
/*
Plugin Name: AnythingSlider for WordPress
Plugin URI: http://wordpress.org/extend/plugins/anythingslider-for-wordpress/
Description: Integrates Chris Coyier's popular AnythingSlider jQuery plugin with WordPress. Visit the <a href="/wp-admin/options-general.php?page=anything_slider">Settings Page</a> for more options.
Author: Jacob Dubail
Author URI: http://jacobdubail.com
Version: 0.3
*/


// URL to the /js directory
define( 'JTD_INSERTJS',  plugin_dir_url( __FILE__ ) . 'js'  );
define( 'JTD_INSERTCSS', plugin_dir_url( __FILE__ ) . 'css' );



// add script to the front end
add_action( 'template_redirect', 'jtd_insertjs_front' );
function jtd_insertjs_front() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery.anythingslider', JTD_INSERTJS . '/jquery.anythingslider.min.js', array( 'jquery' ), '1.5.21' );
}

// add style to the front end
add_action( 'wp_print_styles', 'jtd_insertcss_front' );
function jtd_insertcss_front() {
	
	//wp_register_style( 'anythingslider', JTD_INSERTCSS . '/anythingslider.css', '', '1.5.10' );
	
	// Check for custom theme css - load it if it exists
	if ( file_exists( get_stylesheet_directory()."/anything-slider.css" ) ) {
		wp_register_style( 'anythingslider-base', get_stylesheet_directory_uri() . '/anythingslider.css' );
	}
	elseif ( file_exists( get_template_directory()."/anything-slider.css" ) ) {
		wp_register_style( 'anythingslider-base', get_template_directory_uri() . '/anythingslider.css' );
	}
	else {
		wp_register_style( 'anythingslider-base', JTD_INSERTCSS . '/anythingslider.css' );
	}
	
	
	wp_enqueue_style( 'anythingslider-base' );
		
	
	$options = get_option( 'jtd_anything_slides_options' );
	$theme   = $options['theme'];
			
	wp_register_style( 'anythingslider-theme', JTD_INSERTCSS . '/theme-' . $theme . '.css', '', '1.5.10' );
	
	if ( $theme != '' && $theme != 'default' ) {
		wp_enqueue_style( 'anythingslider-theme' );
	} 

}






// Add theme support for Post Thumbnails
add_theme_support( 'post-thumbnails' );
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'jtd_anythingslide_thumb', 960, 280, true );
}




// register the post type 
add_action( 'init', 'jtd_anythingslider_register_cpt' );
function jtd_anythingslider_register_cpt() {
	$slide_args            = array (
		'public'           => true,
		'query_var'        => 'anything_slides',
		'supports'         => array(
			'title',
			'editor',
			'custom-fields'
		),
		'labels'           => array(
			'name'          => 'Slides',
			'singular_name' => 'Slide',
			'add_new'       => 'Add New Slide',
			'add_new_item'  => 'Add New Slide',
			'edit_item'     => 'Edit Slide',
			'new_item'      => 'New Slide',
			'view_item'     => 'View Slide',
			'search_items'  => 'Search Slides',
			'not_found'     => 'No Slides Found'
		)
	);
	
	register_post_type( 'anything_slides', $slide_args );
}




// register custom taxonomy for slide categorization
add_action( 'init', 'jtd_anythingslider_register_tax' );
function jtd_anythingslider_register_tax() {
	$tax_args             = array(
		'hierarchical'    => true,
		'query_var'       => 'slide_cat',
		'labels'          => array(
			'name'         => 'Slide Categories',
			'edit_item'    => 'Edit Category',
			'add_new_item' => 'Add New Slide Category',
			'all_items'    => 'All Slide Categories'
		)
	);
	register_taxonomy( 'slide_cat', array( 'anything_slides' ), $tax_args );
}




// register shortcode for retrieving slides
add_action( 'init', 'jtd_anythingslider_register_shortcodes' );
function jtd_anythingslider_register_shortcodes() {
	add_shortcode( 'anything_slides', 'jtd_anything_slides_shortcode' );
}

function jtd_anything_slides_shortcode( $attr ) {
	
	
	// setup slide query
	$loop = new WP_Query(
		array(
			'post_type'      => 'anything_slides',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'slide_cat'      => $attr['cat']
		)
	);
	
	$options        = get_option( 'jtd_anything_slides_options' );
	
	//echo "<pre>";
	//print_r($options);
	//echo "</pre>";
	
	// Appearance
	$width          = ( isset( $attr['width'] ) )             ? $attr['width']                 : ( ( $options['width'] )          ? $options['width']          : 570 );
	$height         = ( isset( $attr['height'] ) )            ? $attr['height']                : ( ( $options['height'] )         ? $options['height']         : 300 );
	$expand         = ( isset( $attr['expand'] ) )            ? $attr['expand']                : ( ( $options['expand'] )         ? $options['expand']         : 'false' );
   $resizeContents = ( isset( $attr['resizeContents'] ) )    ? $attr['resizeContents']        : ( ( $options['resizeContents'] ) ? $options['resizeContents'] : 'true' );
   $showMultiple   = ( isset( $attr['showMultiple'] ) )      ? $attr['showMultiple']          : ( ( $options['showMultiple'] )   ? $options['showMultiple']   : 'false' );
   $tooltipClass   = ( isset( $attr['tooltipClass'] ) )      ? $attr['tooltipClass']          : ( ( $options['tooltipClass'] )   ? $options['tooltipClass']   : 'tooltip' );
	$theme          = ( isset( $options['theme'] ) )          ? $options['theme']              : 'default';
	
	// Navigation
   $startPanel     =	( $options['startPanel'] != NULL )      ? $options['startPanel']         : '1';
   $hashTags       = ( isset( $options['hashTags'] ) )       ? $options['hashTags']           : 'false';
   $infiniteSlides = ( isset( $options['infinite'] ) )       ? $options['infinite']           : 'true';
   $enableKeyboard = ( $options['keyboard']   != NULL )      ? $options['keyboard']           : 'true';
	$buildArrows    = ( isset( $options['arrows'] ) )         ? $options['arrows']             : 'true';
	$toggleArrows   = ( isset( $options['toggleArrows'] ) )   ? $options['toggleArrows']       : 'false';
	$buildNav       = ( isset( $options['navigation'] ) )     ? $options['navigation']         : 'true';
   $enableNav      = ( isset( $options['enableNav'] ) )      ? $options['enableNav']          : 'true';
   $toggleControls = ( $options['toggleControls'] != NULL )  ? $options['toggleControls']     : 'false';
// $appendControls = ( isset( $options['appendControls'] ) ) ? $options['appendControls']     : 'NULL';
   $navFormatter   = ( isset( $options['navFormatter'] ) )   ? $options['navFormatter']       : 'NULL';
   $forwardText    = ( $options['forwardText'] != NULL )     ? $options['forwardText']        : '&raquo;';
   $backText       =	( $options['backText'] != NULL )        ? $options['backText']           : '&laquo;';
	
	// Slideshow options
   $enablePlay     = ( isset( $options['enablePlay'] ) )     ? $options['enablePlay']         : 'true';
   $autoPlay       = ( isset( $options['autoPlay'] ) )       ? $options['autoPlay']           : 'true';
   $autoPlayLocked = ( isset( $options['autoPlayLocked'] ) ) ? $options['autoPlayLocked']     : 'false';
   $startStopped   = ( isset( $options['startStopped'] ) )   ? $options['startStopped']       : 'false';
   $pauseOnHover   = ( isset( $options['pauseOnHover'] ) )   ? $options['pauseOnHover']       : 'true';
   $resumeOnVideo  = ( isset( $options['resumeOnVideo'] ) )  ? $options['resumeOnVideo']      : 'true';
   $stopAtEnd      = ( isset( $options['stopAtEnd'] ) )      ? $options['stopAtEnd']          : 'false';
   $playRtl        = ( isset( $options['playRtl'] ) )        ? $options['playRtl']            : 'false';
   $startText      = ( isset( $options['startText'] ) )      ? $options['startText']          : 'Start';
   $stopText       = ( isset( $options['stopText'] ) )       ? $options['stopText']           : 'Stop';
	$delay          = ( isset( $attr['delay'] ) )             ? $attr['delay']                 : ( ( $options['delay'] )      ? $options['delay']      : 5000 );  
	$resumeDelay    = ( isset( $attr['resume'] ) )            ? $attr['resume']                : ( ( $options['resume'] )     ? $options['resume']     : 9000 ); 
	$animation      = ( isset( $attr['animation'] ) )         ? $attr['animation']             : ( ( $options['animation'] )  ? $options['animation']  : 800 ); 
// $easing         =	( isset( $options['easing'] ) )         ? $options['easing']             : 'swing';
	
		
		
	// do we have results
	if ( $loop->have_posts() ) {
		
		$rand          = rand(5, 500);
		
		$output        = "<ul id='slider-{$rand}' class='anythingSlider'>";
		
		while ( $loop->have_posts() ) {
		
			$loop->the_post();
			
			//global $post;

			$content    = get_the_content();
									
			$output    .= "<li>";
			
			if ( $content ) {
				$output .= "<div class='content clearfix'> {$content} </div>";
			}
			
			$output    .= "</li>";
		
		}
		
		$output       .= "</ul>";
		
		// output the jquery plugin code
		$output .= "<script> 
			jQuery('#slider-{$rand}').anythingSlider({
				
				// Appearance
				width               : {$width},
				height              : {$height},
				expand              : {$expand},
				resizeContents      : {$resizeContents},
				showMultiple        : {$showMultiple},
				tooltipClass        : '{$tooltipClass}',
				theme               : '{$theme}',
				
				// Navigation
				startPanel          : {$startPanel},
				hashTags            : {$hashTags},
				infiniteSlides      : {$infiniteSlides},
				enableKeyboard      : {$enableKeyboard},
				buildArrows         : {$buildArrows},
				toggleArrows        : {$toggleArrows},
				buildNavigation     : {$buildNav},
				enableNavigation    : {$enableNav},
				toggleControls      : {$toggleControls},
				navigationFormatter : '{$navFormatter}',
				forwardText         : '{$forwardText}',
				backText            : '{$backText}',
				
				// Slideshow Options
				enablePlay          : {$enablePlay},      
				autoPlay            : {$autoPlay},     
				autoPlayLocked      : {$autoPlayLocked},    
				startStopped        : {$startStopped},   
				pauseOnHover        : {$pauseOnHover},     
				resumeOnVideoEnd    : {$resumeOnVideo},     
				stopAtEnd           : {$stopAtEnd},    
				playRtl             : {$playRtl},    
				startText           : '{$startText}',  
				stopText            : '{$stopText}',   
				delay               : {$delay},      
				resumeDelay         : {$resumeDelay}, 
				animationTime       : {$animation},      
				easing              : 'swing',  
							
				// Extra Options	
				maxOverallWidth     : 32766
			}); 
		</script>";
		
	}
	
	return $output;
	
}





// Add Admin Options Page
add_action( 'admin_menu', 'jtd_anything_slides_create_settings_menu' );
function jtd_anything_slides_create_settings_menu() {
	
	$settings = add_options_page( 'Anything Slider Settings', 'AnythingSlider', 'manage_options', 'anything_slider', 'jtd_anything_slides_settingspage' );

	// Add admin CSS
	add_action( 'load-'.$settings, 'jtd_anything_slides_insert_admin_css' );

}


function jtd_anything_slides_insert_admin_css() {
	
	wp_enqueue_style( 'anything_slider_admin', JTD_INSERTCSS . '/admin.css' );
	
}



function jtd_anything_slides_settingspage() {
	?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Anything Slider for WordPress</h2>
	<form action="options.php" method="post">
	
		<div class="anythingslider-holder">
			<?php settings_fields('jtd_anything_slides_options'); ?>
			<?php do_settings_sections('anything_slider_appearance'); ?>
			<?php do_settings_sections('anything_slider_navigation'); ?>
			<?php do_settings_sections('anything_slider_slideshow'); ?>
			</div><!-- end .inside -->

		</div><!-- end metabox-holder -->
		
		<input name="Submit" type="submit" value="Save Changes"      class="button-primary" />
		<!-- <input name="reset"  type="reset"  value="RESET ALL OPTIONS" class="button-secondary" /> -->
	</form>
	</div>
	<?php
	
}




// Regsiter Admin Settings and Options
add_action( 'admin_init', 'jtd_anything_slides_admin_settings' );
function jtd_anything_slides_admin_settings() {
	
	// Register the Option so we can store all of the settings into the DB
	register_setting( 'jtd_anything_slides_options', 'jtd_anything_slides_options', 'jtd_anything_slides_validate_options' );
	
	// Register a settings section into which we can print all of the options fields
	add_settings_section( 'jtd_anything_slides_option_section', 'Appearance Settings', 'jtd_anything_slides_option_appearance_text', 'anything_slider_appearance' );
	add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>Navigation Settings</h3>', 'jtd_anything_slides_option_navigation_text', 'anything_slider_navigation' );
	add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>Slideshow Options</h3>',   'jtd_anything_slides_option_slideshow_text',  'anything_slider_slideshow' );
	
	// Register each of the fields that will be displayed on the options page -> set a callback for each setting to spit out the actual form field
	// Appearance Settings
	add_settings_field( 'jtd_anything_slides-width',         '<label for="width">Width</label>',                     'jtd_anything_slides_width_callback',        'anything_slider_appearance', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-height',        '<label for="height">Height</label>',                   'jtd_anything_slides_height_callback',       'anything_slider_appearance', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-expand',        '<label for="expand">Expand</label>',                   'jtd_anything_slides_expand_callback',       'anything_slider_appearance', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-resizeContents','<label for="resizeContents">Resize</label>',           'jtd_anything_slides_resize_callback',       'anything_slider_appearance', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-showMultiple',  '<label for="showMultiple">Show Multiple</label>',      'jtd_anything_slides_showMultiple_callback', 'anything_slider_appearance', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-tooltipClass',  '<label for="tooltipClass">Tooltip Class</label>',      'jtd_anything_slides_tooltipClass_callback', 'anything_slider_appearance', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-theme',         '<label for="theme">Theme</label>',                     'jtd_anything_slides_theme_callback',        'anything_slider_appearance', 'jtd_anything_slides_option_section' );
	
	
	// Navigation Settings
	add_settings_field( 'jtd_anything_slides-startPanel',   '<label for="startPanel">Start Panel</label>',            'jtd_anything_slides_startPanel_callback',   'anything_slider_navigation', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-hashTags',     '<label for="hashTags">Display Hash Tags</label>',        'jtd_anything_slides_hashTags_callback',     'anything_slider_navigation', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-infinite',     '<label for="infinite">Infinite Slides</label>',          'jtd_anything_slides_infinite_callback',     'anything_slider_navigation', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-keyboard',     '<label for="keyboard">Enable Keyboard</label>',          'jtd_anything_slides_keyboard_callback',     'anything_slider_navigation', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-arrows',       '<label for="arrows">Arrows</label>',                     'jtd_anything_slides_arrows_callback',       'anything_slider_navigation', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-toggleArrows', '<label for="toggleArrows">Toggle Arrows</label>',        'jtd_anything_slides_toggleArrows_callback', 'anything_slider_navigation', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-navigation',   '<label for="navigation">Build Navigation</label>',       'jtd_anything_slides_navigation_callback',   'anything_slider_navigation', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-enableNav',    '<label for="enableNav">Enable Navigation</label>',       'jtd_anything_slides_enableNav_callback',    'anything_slider_navigation', 'jtd_anything_slides_option_section' );	
	add_settings_field( 'jtd_anything_slides-toggleContr',  '<label for="toggleContr">Toggle Controls</label>',       'jtd_anything_slides_toggleContr_callback',  'anything_slider_navigation', 'jtd_anything_slides_option_section' );	
//	add_settings_field( 'jtd_anything_slides-appendContr',  '<label for="appendContr">Append Controls</label>',       'jtd_anything_slides_appendContr_callback',  'anything_slider_navigation', 'jtd_anything_slides_option_section' );	
//	add_settings_field( 'jtd_anything_slides-navFormatter', '<label for="navFormatter">Navigation Formatter</label>', 'jtd_anything_slides_navFormatter_callback', 'anything_slider_navigation', 'jtd_anything_slides_option_section' );	
	add_settings_field( 'jtd_anything_slides-forwardText',  '<label for="forwardText">Forward Text</label>',          'jtd_anything_slides_forwardText_callback',  'anything_slider_navigation', 'jtd_anything_slides_option_section' );	
	add_settings_field( 'jtd_anything_slides-backText',     '<label for="backText">Back Text</label>',                'jtd_anything_slides_backText_callback',     'anything_slider_navigation', 'jtd_anything_slides_option_section' );		
	
	// Slideshow Options
	add_settings_field( 'jtd_anything_slides-enablePlay',     '<label for="enablePlay">Enable Play</label>',          'jtd_anything_slides_enablePlay_callback',     'anything_slider_slideshow', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-autoPlay',       '<label for="autoPlay">Auto Play</label>',              'jtd_anything_slides_autoPlay_callback',       'anything_slider_slideshow', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-autoPlayLocked', '<label for="autoPlayLocked">Auto Play Locked</label>', 'jtd_anything_slides_autoPlayLocked_callback', 'anything_slider_slideshow', 'jtd_anything_slides_option_section' );
//	add_settings_field( 'jtd_anything_slides-startStopped',   '<label for="startStopped">Start Stopped</label>',      'jtd_anything_slides_startStopped_callback',   'anything_slider', 'jtd_anything_slides_option_section' );
//	add_settings_field( 'jtd_anything_slides-pauseOnHover',   '<label for="pauseOnHover">Paust on Hover</label>',     'jtd_anything_slides_pauseOnHover_callback',   'anything_slider', 'jtd_anything_slides_option_section' );
//	add_settings_field( 'jtd_anything_slides-resumeOnVideo',  '<label for="resumeOnVideo">Resume After Video</label>','jtd_anything_slides_resumeOnVideo_callback',  'anything_slider', 'jtd_anything_slides_option_section' );
//	add_settings_field( 'jtd_anything_slides-stopAtEnd',      '<label for="stopAtEnd">Stop at end of slides</label>', 'jtd_anything_slides_stopAtEnd_callback',      'anything_slider', 'jtd_anything_slides_option_section' );
//	add_settings_field( 'jtd_anything_slides-playRtl',        '<label for="playRtl">Play Right to Left</label>',      'jtd_anything_slides_playRtl_callback',        'anything_slider', 'jtd_anything_slides_option_section' );
//	add_settings_field( 'jtd_anything_slides-startText',      '<label for="startText">Start Text</label>',            'jtd_anything_slides_startText_callback',      'anything_slider', 'jtd_anything_slides_option_section' );	
//	add_settings_field( 'jtd_anything_slides-stopText',       '<label for="stopText">Stop Text</label>',              'jtd_anything_slides_stopText_callback',       'anything_slider', 'jtd_anything_slides_option_section' );	
	add_settings_field( 'jtd_anything_slides-delay',          '<label for="delay">Delay</label>',                     'jtd_anything_slides_delay_callback',          'anything_slider_slideshow', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-resume',         '<label for="resume">Resume Delay</label>',             'jtd_anything_slides_resume_callback',         'anything_slider_slideshow', 'jtd_anything_slides_option_section' );
	add_settings_field( 'jtd_anything_slides-animation',      '<label for="animation">Animation Time</label>',        'jtd_anything_slides_animation_callback',      'anything_slider_slideshow', 'jtd_anything_slides_option_section' );
//	add_settings_field( 'jtd_anything_slides-easing',         '<label for="easing">Easing</label>',                   'jtd_anything_slides_easing_callback',         'anything_slider', 'jtd_anything_slides_option_section' );		
	
	
}






// Draw the section header
function jtd_anything_slides_option_appearance_text() {
	echo '<div class="inside">';
}
function jtd_anything_slides_option_navigation_text() {
	echo '<div class="inside">';
}
function jtd_anything_slides_option_slideshow_text() {
	echo '<div class="inside">';
}







// Display and fill the form field
/*
** Appearance
*/
/* Appearance
  width               : null,      // Override the default CSS width
  height              : null,      // Override the default CSS height
  expand              : false,     // If true, the entire slider will expand to fit the parent element
  resizeContents      : true,      // If true, solitary images/objects in the panel will expand to fit the viewport
  showMultiple        : false,     // Set this value to a number and it will show that many slides at once
  tooltipClass        : 'tooltip', // Class added to navigation & start/stop button (text copied to title if it is hidden by a negative text indent)
  theme               : 'default', // Theme name - adds a class name to the base element "anythingSlider-{theme}" so the loaded theme will work.
*/
function jtd_anything_slides_width_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$width    = $options['width'];
	echo "<input id='width' name='jtd_anything_slides_options[width]' type='number' min=0 value='$width' />";
	echo "<span class='description'>Override the default CSS width</span>";
}
function jtd_anything_slides_height_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$height   = $options['height'];
	echo "<input id='height' name='jtd_anything_slides_options[height]' type='number' min=0 value='$height' />";
	echo "<span class='description'>Override the default CSS height</span>";
}
function jtd_anything_slides_expand_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$expand   = $options['expand'];
	echo "<label>Yes <input id='expand' name='jtd_anything_slides_options[expand]' type='radio' value='true'"  . checked( $expand, 'true', false ) . "/></label><br />";
	echo "<label>No  <input id='expand' name='jtd_anything_slides_options[expand]' type='radio' value='false'" . checked( $expand, 'false', false ) . "/></label>";
	echo "<span class='description'>If yes, the entire slider will expand to fit the parent element</span>";
}
function jtd_anything_slides_resize_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$resize   = $options['resizeContents'];
	echo "<label>Yes <input id='resizeContents' name='jtd_anything_slides_options[resizeContents]' type='radio' value='true'"  . checked( $resize, 'true', false ) . "/></label><br />";
	echo "<label>No  <input id='resizeContents' name='jtd_anything_slides_options[resizeContents]' type='radio' value='false'" . checked( $resize, 'false', false ) . "/></label>";
	echo "<span class='description'>If yes, solitary images/objects in the panel will expand to fit the viewport</span>";
}
function jtd_anything_slides_showMultiple_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$multiple = $options['showMultiple'];
	echo "<input id='showMultiple' name='jtd_anything_slides_options[showMultiple]' type='number' min=0 value='$multiple' />";
	echo "<span class='description'>Set this value to a number and it will show that many slides at once</span>";
}
function jtd_anything_slides_tooltipClass_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$tooltip  = $options['tooltipClass'];
	echo "<input id='tooltipClass' name='jtd_anything_slides_options[tooltipClass]' type='text' value='$tooltip' />";
	echo "<span class='description'>Set this value to a number and it will show that many slides at once</span>";
}
function jtd_anything_slides_theme_callback() {
	$options   = get_option( 'jtd_anything_slides_options' );
	$theme     = $options['theme'];
	echo "<select id='theme' name='jtd_anything_slides_options[theme]'>
		<option value=''" . selected( $theme, '', false ) . ">Default</option>
		<option value='construction'" . selected( $theme, 'construction', false ) . ">Construction</option>
		<option value='cs-portfolio'" . selected( $theme, 'cs-portfolio', false ) . ">CS Portfolio</option>
		<option value='metallic'" . selected( $theme, 'metallic', false ) . ">Metallic</option>
		<option value='minimalist-round'" . selected( $theme, 'minimalist-round', false ) . ">Minimalist Round</option>
		<option value='minimalist-square'" . selected( $theme, 'minimalist-square', false ) . ">Minimalist Square</option>
	</select>";
	echo "<span class='description'>Select a theme, or leave blank for default</span>";
} 


/*
** Navigation
*/
/* Navigation
  startPanel          : 1,         // This sets the initial panel
  hashTags            : true,      // Should links change the hashtag in the URL?
  infiniteSlides      : true,      // if false, the slider will not wrap
  enableKeyboard      : true,      // if false, keyboard arrow keys will not work for the current panel.
  buildArrows         : true,      // If true, builds the forwards and backwards buttons
  toggleArrows        : false,     // if true, side navigation arrows will slide out on hovering & hide @ other times
  buildNavigation     : true,      // If true, builds a list of anchor links to link to each panel
  enableNavigation    : true,      // if false, navigation links will still be visible, but not clickable.
  toggleControls      : false,     // if true, slide in controls (navigation + play/stop button) on hover and slide change, hide @ other times
  appendControlsTo    : null,      // A HTML element (jQuery Object, selector or HTMLNode) to which the controls will be appended if not null
  navigationFormatter : null,      // Details at the top of the file on this use (advanced use)
  forwardText         : "&raquo;", // Link text used to move the slider forward (hidden by CSS, replaced with arrow image)
  backText            : "&laquo;", // Link text used to move the slider back (hidden by CSS, replace with arrow image)
 */

function jtd_anything_slides_startPanel_callback() {
	$options    = get_option( 'jtd_anything_slides_options' );
	$startPanel = $options['startPanel'];
	echo "<input id='startPanel' name='jtd_anything_slides_options[startPanel]' type='text' value='$startPanel' />";
	echo "<span class='description'>This sets the initial panel</span>";
}
function jtd_anything_slides_hashTags_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$hashTags = $options['hashTags'];
	echo "<label>Yes <input id='hashTags' name='jtd_anything_slides_options[hashTags]' type='radio' value='true'"  . checked( $hashTags, 'true', false ) . "/></label><br />";
	echo "<label>No  <input id='hashTags' name='jtd_anything_slides_options[hashTags]' type='radio' value='false'" . checked( $hashTags, 'false', false ) . "/></label>";
	echo "<span class='description'>Should links change the hashtag in the URL?</span>";
}
function jtd_anything_slides_infinite_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$infinite = $options['infinite'];
	echo "<label>Yes <input id='infinite' name='jtd_anything_slides_options[infinite]' type='radio' value='true'"  . checked( $infinite, 'true', false ) . "/></label><br />";
	echo "<label>No  <input id='infinite' name='jtd_anything_slides_options[infinite]' type='radio' value='false'" . checked( $infinite, 'false', false ) . "/></label>";
	echo "<span class='description'>if no, the slider will not wrap</span>";
}
function jtd_anything_slides_keyboard_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$keyboard = $options['keyboard'];
	echo "<label>Yes <input id='keyboard' name='jtd_anything_slides_options[keyboard]' type='radio' value='true'"  . checked( $keyboard, 'true', false ) . "/></label><br />";
	echo "<label>No  <input id='keyboard' name='jtd_anything_slides_options[keyboard]' type='radio' value='false'" . checked( $keyboard, 'false', false ) . "/></label>";
	echo "<span class='description'>If no, keyboard arrow keys will not work for the current panel</span>";
}
function jtd_anything_slides_arrows_callback() {
	$options    = get_option( 'jtd_anything_slides_options' );
	$arrows     = $options['arrows'];
	echo "<label>Yes <input id='arrows' name='jtd_anything_slides_options[arrows]' type='radio' value='true'"  . checked( $arrows, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='arrows' name='jtd_anything_slides_options[arrows]' type='radio' value='false'" . checked( $arrows, 'false', false ) . " /></label>";
	echo "<span class='description'>If yes, builds the forwards and backwards buttons</span>";
}
function jtd_anything_slides_toggleArrows_callback() {
	$options      = get_option( 'jtd_anything_slides_options' );
	$toggleArrows = $options['toggleArrows'];
	echo "<label>Yes <input id='toggleArrows' name='jtd_anything_slides_options[toggleArrows]' type='radio' value='true'"  . checked( $toggleArrows, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='toggleArrows' name='jtd_anything_slides_options[toggleArrows]' type='radio' value='false'" . checked( $toggleArrows, 'false', false ) . " /></label>";
	echo "<span class='description'>if yes, side navigation arrows will slide out on hovering &amp; hide @ other times</span>";
}
function jtd_anything_slides_navigation_callback() {
	$options    = get_option( 'jtd_anything_slides_options' );
	$navigation = $options['navigation'];
	echo "<label>Yes <input id='navigation' name='jtd_anything_slides_options[navigation]' type='radio' value='true'"  . checked( $navigation, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='navigation' name='jtd_anything_slides_options[navigation]' type='radio' value='false'" . checked( $navigation, 'false', false ) . " /></label>";
	echo "<span class='description'>If yes, builds a list of anchor links to link to each panel</span>";
}
function jtd_anything_slides_enableNav_callback() {
	$options   = get_option( 'jtd_anything_slides_options' );
	$enableNav = $options['enableNav'];
	echo "<label>Yes <input id='enableNav' name='jtd_anything_slides_options[enableNav]' type='radio' value='true'"  . checked( $enableNav, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='enableNav' name='jtd_anything_slides_options[enableNav]' type='radio' value='false'" . checked( $enableNav, 'false', false ) . " /></label>";
	echo "<span class='description'>if no, navigation links will still be visible, but not clickable.</span>";
}
function jtd_anything_slides_toggleContr_callback() {
	$options     = get_option( 'jtd_anything_slides_options' );
	$toggleContr = $options['toggleContr'];
	echo "<label>Yes <input id='toggleContr' name='jtd_anything_slides_options[toggleContr]' type='radio' value='true'"  . checked( $toggleContr, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='toggleContr' name='jtd_anything_slides_options[toggleContr]' type='radio' value='false'" . checked( $toggleContr, 'false', false ) . " /></label>";
	echo "<span class='description'>if yes, slide in controls (navigation + play/stop button) on hover and slide change, hide @ other times</span>";
}
/*
function jtd_anything_slides_navFormatter_callback() {
	$options      = get_option( 'jtd_anything_slides_options' );
	$navFormatter = $options['navFormatter'];
	echo "<label>Yes <input id='navFormatter' name='jtd_anything_slides_options[navFormatter]' type='radio' value='true'" . checked( $autoPlayLocked, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='navFormatter' name='jtd_anything_slides_options[navFormatter]' type='radio' value='false'" . checked( $autoPlayLocked, 'false', false ) . " /></label>";
	echo "<span class='description'>Details at the top of the file on this use (advanced use).</span>";
}
*/
function jtd_anything_slides_forwardText_callback() {
	$options     = get_option( 'jtd_anything_slides_options' );
	$forwardText = $options['forwardText'];
	echo "<input id='forwardText' name='jtd_anything_slides_options[forwardText]' type='text' value='$forwardText' />";
	echo "<span class='description'>Link text used to move the slider forward (hidden by CSS, replaced with arrow image)</span>";
}
function jtd_anything_slides_backText_callback() {
	$options    = get_option( 'jtd_anything_slides_options' );
	$backText = $options['backText'];
	echo "<input id='backText' name='jtd_anything_slides_options[backText]' type='text' value='$backText' />";
	echo "<span class='description'>Link text used to move the slider back (hidden by CSS, replace with arrow image)</span>";
}


/*
** Slideshow options
*/
/* Slideshow options
  enablePlay          : true,      // if false, the play/stop button will still be visible, but not clickable.
  autoPlay            : true,      // This turns off the entire slideshow FUNCTIONALY, not just if it starts running or not
  autoPlayLocked      : false,     // If true, user changing slides will not stop the slideshow
  
  startStopped        : false,     // If autoPlay is on, this can force it to start stopped
  pauseOnHover        : true,      // If true & the slideshow is active, the slideshow will pause on hover
  resumeOnVideoEnd    : true,      // If true & the slideshow is active & a youtube video is playing, the autoplay will pause until the video completes
  stopAtEnd           : false,     // If true & the slideshow is active, the slideshow will stop on the last page. This also stops the rewind effect when infiniteSlides is false.
  playRtl             : false,     // If true, the slideshow will move right-to-left
  startText           : "Start",   // Start button text
  stopText            : "Stop",    // Stop button text
  
  delay               : 3000,      // How long between slideshow transitions in AutoPlay mode (in milliseconds)
  resumeDelay         : 15000,     // Resume slideshow after user interaction, only if autoplayLocked is true (in milliseconds).
  animationTime       : 600,       // How long the slideshow transition takes (in milliseconds)
  easing              : "swing",   // Anything other than "linear" or "swing" requires the easing plugin
*/
function jtd_anything_slides_enablePlay_callback() {
	$options    = get_option( 'jtd_anything_slides_options' );
	$enablePlay = $options['enablePlay'];
	echo "<label>Yes <input id='enablePlay' name='jtd_anything_slides_options[enablePlay]' type='radio' value='true'"  . checked( $enablePlay, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='enablePlay' name='jtd_anything_slides_options[enablePlay]' type='radio' value='false'" . checked( $enablePlay, 'false', false ) . " /></label>";
	echo "<span class='description'>if no, the play/stop button will still be visible, but not clickable.</span>";
}
function jtd_anything_slides_autoPlay_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$autoPlay = $options['autoPlay'];
	echo "<label>Yes <input id='autoPlay' name='jtd_anything_slides_options[autoPlay]' type='radio' value='true'"  . checked( $autoPlay, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='autoPlay' name='jtd_anything_slides_options[autoPlay]' type='radio' value='false'" . checked( $autoPlay, 'false', false ) . " /></label>";
	echo "<span class='description'>This turns off the entire slideshow functionality, not just if it starts running or not</span>";
}
function jtd_anything_slides_autoPlayLocked_callback() {
	$options        = get_option( 'jtd_anything_slides_options' );
	$autoPlayLocked = $options['autoPlayLocked'];
	echo "<label>Yes <input id='autoPlayLocked' name='jtd_anything_slides_options[autoPlayLocked]' type='radio' value='true'"  . checked( $autoPlayLocked, 'true', false ) . " /></label><br />";
	echo "<label>No  <input id='autoPlayLocked' name='jtd_anything_slides_options[autoPlayLocked]' type='radio' value='false'" . checked( $autoPlayLocked, 'false', false ) . " /></label>";
	echo "<span class='description'>If yes, user changing slides will not stop the slideshow</span>";
}




function jtd_anything_slides_delay_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$delay    = $options['delay'];
	echo "<input id='delay' name='jtd_anything_slides_options[delay]' type='number' min=0 value='$delay' />";
	echo "<span class='description'>How long between slideshow transitions in AutoPlay mode (in milliseconds)</span>";
}
function jtd_anything_slides_resume_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$resume   = $options['resume'];
	echo "<input id='resume' name='jtd_anything_slides_options[resume]' type='number' min=0 step='250' value='$resume' />";
	echo "<span class='description'>Resume slideshow after user interaction, only if autoplayLocked is true (in milliseconds)</span>";
}
function jtd_anything_slides_animation_callback() {
	$options   = get_option( 'jtd_anything_slides_options' );
	$animation = $options['animation'];
	echo "<input id='animation' name='jtd_anything_slides_options[animation]' type='number' min=0 value='$animation' />";
	echo "<span class='description'>How long the slideshow transition takes (in milliseconds)</span>";
}























// Validate user input (we want numbers only)
function jtd_anything_slides_validate_options($input) {
	$themes                  = array( 'construction', 'cs-portfolio', 'metallic', 'minimalist-round', 'minimalist-square' );
	$boolean                 = array( 'true', 'false' );
	$valid                   = array();
	
	// Appearance
	$valid['width']          = preg_replace( '/[^0-9]/',        '', $input['width'] );
	$valid['height']         = preg_replace( '/[^0-9]/',        '', $input['height'] );
	$valid['expand']         = ( in_array( mysql_real_escape_string($input['expand']),         $boolean ) ) ? $input['expand']          : 'false'; 
	$valid['resizeContents'] = ( in_array( mysql_real_escape_string($input['resizeContents']), $boolean ) ) ? $input['resizeContents']  : 'false'; 
	$valid['showMultiple']   = preg_replace( '/[^0-9]/',        '', $input['showMultiple'] );
	$valid['tooltipClass']   = preg_replace( '/[^A-Za-z0-9_]/', '', $input['tooltipClass'] );
	$valid['theme']          = ( in_array( mysql_real_escape_string($input['theme']),          $themes ) ) ? $input['theme']            : 'default';
	
	
	// Navigation
	$valid['startPanel']     = preg_replace( '/[^0-9]/',        '', $input['startPanel'] ); 
	$valid['hashTags']       = ( in_array( mysql_real_escape_string($input['hashTags']),       $boolean ) ) ? $input['hashTags']        : 'false'; 
	$valid['infinite']       = ( in_array( mysql_real_escape_string($input['infinite']),       $boolean ) ) ? $input['infinite']        : 'false';  
	$valid['keyboard']       = ( in_array( mysql_real_escape_string($input['keyboard']),       $boolean ) ) ? $input['keyboard']        : 'false';  
	$valid['arrows']         = ( in_array( mysql_real_escape_string($input['arrows']),         $boolean ) ) ? $input['arrows']          : 'false'; 
	$valid['toggleArrows']   = ( in_array( mysql_real_escape_string($input['toggleArrows']),   $boolean ) ) ? $input['toggleArrows']    : 'false'; 
	$valid['navigation']     = ( in_array( mysql_real_escape_string($input['navigation']),     $boolean ) ) ? $input['navigation']      : 'false'; 
	$valid['enableNav']      = ( in_array( mysql_real_escape_string($input['enableNav']),      $boolean ) ) ? $input['enableNav']       : 'false'; 
	$valid['toggleContr']    = ( in_array( mysql_real_escape_string($input['toggleContr']),    $boolean ) ) ? $input['toggleContr']     : 'false'; 
	$valid['forwardText']    = preg_replace( '/[^A-Za-z0-9_]/', '', $input['forwardText'] );
	$valid['backText']       = preg_replace( '/[^A-Za-z0-9_]/', '', $input['backText'] );
	
	// Slideshow options
	$valid['enablePlay']     = ( in_array( mysql_real_escape_string($input['enablePlay']),      $boolean ) ) ? $input['enablePlay']     : 'false'; 
	$valid['autoPlay']       = ( in_array( mysql_real_escape_string($input['autoPlay']),        $boolean ) ) ? $input['autoPlay']       : 'false'; 
	$valid['autoPlayLocked'] = ( in_array( mysql_real_escape_string($input['autoPlayLocked']),  $boolean ) ) ? $input['autoPlayLocked'] : 'false'; 	
	$valid['delay']          = preg_replace( '/[^0-9]/',        '', $input['delay'] ); 
	$valid['resume']         = preg_replace( '/[^0-9]/',        '', $input['resume'] ); 
	$valid['animation']      = preg_replace( '/[^0-9]/',        '', $input['animation'] ); 
	
	
	
		
	
	
	
	
	
	
	
	/*
  // Slideshow options
  startStopped        : false,     // If autoPlay is on, this can force it to start stopped
  pauseOnHover        : true,      // If true & the slideshow is active, the slideshow will pause on hover
  resumeOnVideoEnd    : true,      // If true & the slideshow is active & a youtube video is playing, the autoplay will pause until the video completes
  stopAtEnd           : false,     // If true & the slideshow is active, the slideshow will stop on the last page. This also stops the rewind effect when infiniteSlides is false.
  playRtl             : false,     // If true, the slideshow will move right-to-left
  startText           : "Start",   // Start button text
  stopText            : "Stop",    // Stop button text
  delay               : 3000,      // How long between slideshow transitions in AutoPlay mode (in milliseconds)
  resumeDelay         : 15000,     // Resume slideshow after user interaction, only if autoplayLocked is true (in milliseconds).
  animationTime       : 600,       // How long the slideshow transition takes (in milliseconds)
  easing              : "swing",   // Anything other than "linear" or "swing" requires the easing plugin

*/

	
	if( $valid['width'] != $input['width'] ) {
		add_settings_error(
			'jtd_anything_slides-width',
			'jtd_anything_slides_texterror',
			'Incorrect value entered!',
			'error'
		);		
	}
	if( $valid['height'] != $input['height'] ) {
		add_settings_error(
			'jtd_anything_slides-height',
			'jtd_anything_slides_texterror',
			'Incorrect value entered!',
			'error'
		);		
	}
	if( $valid['delay'] != $input['delay'] ) {
		add_settings_error(
			'jtd_anything_slides-delay',
			'jtd_anything_slides_texterror',
			'Incorrect value entered!',
			'error'
		);		
	}
	if( $valid['resume'] != $input['resume'] ) {
		add_settings_error(
			'jtd_anything_slides-resume',
			'jtd_anything_slides_texterror',
			'Incorrect value entered!',
			'error'
		);		
	}
	if( $valid['animation'] != $input['animation'] ) {
		add_settings_error(
			'jtd_anything_slides-animation',
			'jtd_anything_slides_texterror',
			'Incorrect value entered!',
			'error'
		);		
	}

	return $valid;
}














// Set default options at activation
function jtd_anything_slides_set_defaults() {

	$tmp = get_option( 'jtd_anything_slides_options' );

	if ( ( !is_array( $tmp ) ) ) {
		$o = array(
			'width'               => '570',       // Override the default CSS width
			'height'              => '190',       // Override the default CSS height
			'expand'              => 'false',     // If true, the entire slider will expand to fit the parent element
			'resizeContents'      => 'true',      // If true, solitary images/objects in the panel will expand to fit the viewport
			'showMultiple'        => 1,           // Set this value to a number and it will show that many slides at once
			'tooltipClass'        => 'tooltip',   // Class added to navigation & start/stop button (text copied to title if it is hidden by a negative text indent)
			'theme'               => 'default',
			    
			'startPanel'          => 1,           // This sets the initial panel
			'hashTags'            => 'true',      // Should links change the hashtag in the URL?
			'infinite'            => 'true',      // if false, the slider will not wrap
			'keyboard'            => 'true',      // if false, keyboard arrow keys will not work for the current panel.
			'arrows'              => 'true',      // If 'true', builds the forwards and backwards buttons
			'toggleArrows'        => 'false',     // if 'true', side navigation arrows will slide out on hovering & hide @ other times
			'navigation'          => 'true',      // If 'true', builds a list of anchor links to link to each panel
			'enableNav'           => 'true',      // if 'false', navigation links will still be visible, but not clickable.
			'toggleControls'      => 'false',     // if 'true', slide in controls (navigation + play/stop button) on hover and slide change, hide @ other times
			'appendControlsTo'    => null,        // A HTML element (jQuery Object, selector or HTMLNode) to which the controls will be appended if not null
			'navigationFormatter' => null,        // Details at the top of the file on this use (advanced use)
			'forwardText'         => "&raquo;",   // Link text used to move the slider forward (hidden by CSS, replaced with arrow image)
			'backText'            => "&laquo;",   // Link text used to move the slider back (hidden by CSS, replace with arrow image)
			
			'enablePlay'          => 'true',      // if 'false', the play/stop button will still be visible, but not clickable.
			'autoPlay'            => 'true',      // This turns off the entire slideshow FUNCTIONALY, not just if it starts running or not
			'autoPlayLocked'      => 'false',     // If 'true', user changing slides will not stop the slideshow
			'startStopped'        => 'false',     // If autoPlay is on, this can force it to start stopped
			'pauseOnHover'        => 'true',      // If 'true' & the slideshow is active, the slideshow will pause on hover
			'resumeOnVideo'       => 'true',      // If 'true' & the slideshow is active & a youtube video is playing, the autoplay will pause until the video completes
			'stopAtEnd'           => 'false',     // If true & the slideshow is active, the slideshow will stop on the last page. This also stops the rewind effect when infiniteSlides is 'false'.
			'playRtl'             => 'false',     // If true, the slideshow will move right-to-left
			'startText'           => "Start",     // Start button text
			'stopText'            => "Stop",      // Stop button text
			'delay'               => 3000,        // How long between slideshow transitions in AutoPlay mode (in milliseconds)
			'resume'              => 15000,       // Resume slideshow after user interaction, only if autoplayLocked is true (in milliseconds).
			'animation'           => 600,         // How long the slideshow transition takes (in milliseconds)
			'easing'              => "swing"      // Anything other than "linear" or "swing" requires the easing plugin
	
		);
		
	}
  
   update_option( 'jtd_anything_slides_options', $o );
   
}
register_activation_hook(__FILE__, 'jtd_anything_slides_set_defaults');








// Add an upgrade warning
if ( is_admin() ) {
	add_action( 'in_plugin_update_message-' . plugin_basename(__FILE__), 'jtd_anything_slides_upgrade_notice' );
}
function jtd_anything_slides_upgrade_notice() {
	$info = __( '&nbsp; ATTENTION! After updating this plugin, you will need to update your settings on the the <a href="options-general.php?page=anything_slider">Settings Page</a>.', MY_TEXTDOMAIN );
	echo '<span style="color:red;">' . strip_tags( $info, '<br><a><b><i><span>' ) . '</span>';
}


