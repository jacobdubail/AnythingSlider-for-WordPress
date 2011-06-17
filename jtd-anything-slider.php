<?php
/*
Plugin Name: AnythingSlider for WordPress
Plugin URI: http://wordpress.org/extend/plugins/yadayada
Description: The famous AnythingSlider
Author: Jacob Dubail
Author URI: http://jacobdubail.com
Version: 0.2
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
	$options = get_option( 'jtd_anything_slides_options' );
	$theme   = $options['theme'];
	wp_register_style( 'anythingslider', JTD_INSERTCSS . '/anythingslider.css', '', '1.5.10' );
	wp_register_style( 'anythingslider-theme', JTD_INSERTCSS . '/theme-' . $theme . '.css', array( 'anythingslider' ), '1.5.10' );
	wp_enqueue_style( 'anythingslider' );
	
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
	
	$options      = get_option( 'jtd_anything_slides_options' );
	$height       = ( isset( $attr['height'] ) )          ? $attr['height']                : ( ( $options['height'] )     ? $options['height']     : 300 );
	$width        = ( isset( $attr['width'] ) )           ? $attr['width']                 : ( ( $options['width'] )      ? $options['width']      : 570 );
	$delay        = ( isset( $attr['delay'] ) )           ? $attr['delay']                 : ( ( $options['delay'] )      ? $options['delay']      : 5000 );  
	$resume       = ( isset( $attr['resume'] ) )          ? $attr['resume']                : ( ( $options['resume'] )     ? $options['resume']     : 9000 ); 
	$animation    = ( isset( $attr['animation'] ) )       ? $attr['animation']             : ( ( $options['animation'] )  ? $options['animation']  : 800 ); 
	$arrows       = ( isset( $options['arrows'] ) )       ? $options['arrows']             : 'true';
	$toggleArrows = ( isset( $options['toggleArrows'] ) ) ? $options['toggleArrows']       : 'false';
	$navigation   = ( isset( $options['navigation'] ) )   ? $options['navigation']         : 'true';
	$theme        = ( isset( $options['theme'] ) )        ? $options['theme']              : 'default';
	
	$toggleArrows = ( $toggleArrows == 1 )                ? 'true'                         : 'false';
	$arrows       = ( $arrows       == 1 )                ? 'true'                         : 'false';
	$navigation   = ( $navigation   == 1 )                ? 'true'                         : 'false';
		
		
	// do we have results
	if ( $loop->have_posts() ) {
		
		$rand          = rand(5, 500);
		
		$output        = "<ul id='slider-{$rand}' class='anythingSlider'>";
		
		while ( $loop->have_posts() ) {
		
			$loop->the_post();
			
			//global $post;
			//$thumb      = get_the_post_thumbnail( $post->ID, array( $width, $height*4 ) );
			$content    = get_the_content();
									
			$output    .= "<li>";
			
			if ( $content ) {
				$output .= "<div class='content'> {$content} </div>";
			}
			
			$output    .= "</li>";
		
		}
		
		$output       .= "</ul>";
		
		// output the jquery plugin code
		$output .= "<script> 
			jQuery('#slider-{$rand}').anythingSlider({
				width           : {$width},
				height          : {$height},
				buildArrows     : {$arrows},
				buildNavigation : {$navigation},
				delay           : {$delay},      
				resumeDelay     : {$resume}, 
				animationTime   : {$animation},
				theme           : '{$theme}',
				toggleControls  : false,
				toggleArrows    : {$toggleArrows},
				hashTags        : false
			}); 
		</script>";
		
	}
	
	return $output;
	
}





//Admin Options Page
add_action( 'admin_menu', 'jtd_anything_slides_create_settings_menu' );
function jtd_anything_slides_create_settings_menu() {
	add_options_page( 'Anything Slider Settings', 'AnythingSlider', 'manage_options', 'anything_slider', 'jtd_anything_slides_settingspage' );
}

function jtd_anything_slides_settingspage() {
	?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Anything Slider for WordPress</h2>
	<form action="options.php" method="post">

		<?php settings_fields('jtd_anything_slides_options'); ?>
		<?php do_settings_sections('anything_slider'); ?>
			
		<input name="Submit" type="submit" value="Save Changes" class="button-primary" />
	</form>
	</div>
	<?php
	
}



// Regsiter Admin Settings
add_action( 'admin_init', 'jtd_anything_slides_admin_settings' );
function jtd_anything_slides_admin_settings() {
	register_setting( 
		'jtd_anything_slides_options', 
		'jtd_anything_slides_options', 
		'jtd_anything_slides_validate_options' 
	);
	add_settings_section( 
		'jtd_anything_slides_option_section', 
		'Anything Slider for WordPress', 
		'jtd_anything_slides_option_section_text', 
		'anything_slider' 
	);
	add_settings_field( 
		'jtd_anything_slides-width', 
		'<label for="width">Width</label>', 
		'jtd_anything_slides_width_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
	add_settings_field( 
		'jtd_anything_slides-height', 
		'<label for="height">Height</label>', 
		'jtd_anything_slides_height_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
	add_settings_field( 
		'jtd_anything_slides-delay', 
		'<label for="delay">Delay</label>', 
		'jtd_anything_slides_delay_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
	add_settings_field( 
		'jtd_anything_slides-resume', 
		'<label for="resume">Resume Delay</label>', 
		'jtd_anything_slides_resume_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
	add_settings_field( 
		'jtd_anything_slides-animation', 
		'<label for="animation">Animation Time</label>', 
		'jtd_anything_slides_animation_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
	add_settings_field( 
		'jtd_anything_slides-theme', 
		'<label for="theme">Theme</label>', 
		'jtd_anything_slides_theme_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
	add_settings_field( 
		'jtd_anything_slides-navigation', 
		'<label for="navigation">Navigation</label>', 
		'jtd_anything_slides_navigation_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
	add_settings_field( 
		'jtd_anything_slides-arrows', 
		'<label for="arrows">Arrows</label>', 
		'jtd_anything_slides_arrows_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
	add_settings_field( 
		'jtd_anything_slides-toggleArrows', 
		'<label for="toggleArrows">Toggle Arrows</label>', 
		'jtd_anything_slides_toggleArrows_callback', 
		'anything_slider', 
		'jtd_anything_slides_option_section' 
	);
}

// Draw the section header
function jtd_anything_slides_option_section_text() {
	echo '<p>Enter the desired settings for your slider';
}

// Display and fill the form field
function jtd_anything_slides_width_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$width    = $options['width'];
	echo "<input id='width' name='jtd_anything_slides_options[width]' type='number' value='$width' />";
	echo "<span class='description'>Override the default CSS width</span>";
}
function jtd_anything_slides_height_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$height   = $options['height'];
	echo "<input id='height' name='jtd_anything_slides_options[height]' type='number' value='$height' />";
	echo "<span class='description'>Override the default CSS height</span>";
}
function jtd_anything_slides_delay_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$delay    = $options['delay'];
	echo "<input id='delay' name='jtd_anything_slides_options[delay]' type='number' value='$delay' />";
	echo "<span class='description'>How long between slideshow transitions in AutoPlay mode (in milliseconds)</span>";
}
function jtd_anything_slides_resume_callback() {
	$options  = get_option( 'jtd_anything_slides_options' );
	$resume   = $options['resume'];
	echo "<input id='resume' name='jtd_anything_slides_options[resume]' type='number' value='$resume' />";
	echo "<span class='description'>Resume slideshow after user interaction, only if autoplayLocked is true (in milliseconds)</span>";
}
function jtd_anything_slides_animation_callback() {
	$options   = get_option( 'jtd_anything_slides_options' );
	$animation = $options['animation'];
	echo "<input id='animation' name='jtd_anything_slides_options[animation]' type='number' value='$animation' />";
	echo "<span class='description'>How long the slideshow transition takes (in milliseconds)</span>";
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
function jtd_anything_slides_navigation_callback() {
	$options    = get_option( 'jtd_anything_slides_options' );
	$navigation = $options['navigation'];
	echo "<label>Yes <input id='navigation' name='jtd_anything_slides_options[navigation]' type='radio' value='1'" . checked( $navigation, 1, false ) . " /></label><br />";
	echo "<label>No  <input id='navigation' name='jtd_anything_slides_options[navigation]' type='radio' value='0'" . checked( $navigation, 0, false ) . " /></label>";
	echo "<span class='description'>If true, builds a list of anchor links to link to each panel</span>";
}
function jtd_anything_slides_arrows_callback() {
	$options    = get_option( 'jtd_anything_slides_options' );
	$arrows     = $options['arrows'];
	echo "<label>Yes <input id='arrows' name='jtd_anything_slides_options[arrows]' type='radio' value='1'" . checked( $arrows, 1, false ) . " /></label><br />";
	echo "<label>No  <input id='arrows' name='jtd_anything_slides_options[arrows]' type='radio' value='0'" . checked( $arrows, 0, false ) . " /></label>";
	echo "<span class='description'>If true, builds the forwards and backwards buttons</span>";
}
function jtd_anything_slides_toggleArrows_callback() {
	$options      = get_option( 'jtd_anything_slides_options' );
	$toggleArrows = $options['toggleArrows'];
	echo "<label>Yes <input id='toggleArrows' name='jtd_anything_slides_options[toggleArrows]' type='radio' value='1'" . checked( $toggleArrows, 1, false ) . " /></label><br />";
	echo "<label>No  <input id='toggleArrows' name='jtd_anything_slides_options[toggleArrows]' type='radio' value='0'" . checked( $toggleArrows, 0, false ) . " /></label>";
	echo "<span class='description'>if true, side navigation arrows will slide out on hovering &amp; hide @ other times</span>";
}


// Validate user input (we want numbers only)
function jtd_anything_slides_validate_options($input) {
	$themes                = array( 'construction', 'cs-portfolio', 'metallic', 'minimalist-round', 'minimalist-square' );
	$valid                 = array();
	$valid['width']        = preg_replace( '/[^0-9]/', '', $input['width'] );
	$valid['height']       = preg_replace( '/[^0-9]/', '', $input['height'] );
	$valid['delay']        = preg_replace( '/[^0-9]/', '', $input['delay'] ); 
	$valid['resume']       = preg_replace( '/[^0-9]/', '', $input['resume'] ); 
	$valid['animation']    = preg_replace( '/[^0-9]/', '', $input['animation'] ); 
	$valid['theme']        = ( in_array( mysql_real_escape_string($input['theme']), $themes ) ) ? $input['theme'] : 'default';
	$valid['navigation']   = preg_replace( '/[^0-1]/', '', $input['navigation'] );
	$valid['arrows']       = preg_replace( '/[^0-1]/', '', $input['arrows'] );
	$valid['toggleArrows'] = preg_replace( '/[^0-1]/', '', $input['toggleArrows'] );
		
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















