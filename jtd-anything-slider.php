<?php
/*
Plugin Name: AnythingSlider for WordPress
Plugin URI: http://wordpress.org/extend/plugins/anythingslider-for-wordpress/
Description: Integrates Chris Coyier's popular AnythingSlider jQuery plugin with WordPress. Visit the <a href="options-general.php?page=anything_slider">Settings Page</a> for more options. Follow me on <a href="http://twitter.com/#!/jacobdubail" target="_blank">Twitter</a> for plugin updates and news.
Author: Jacob Dubail
Author URI: http://jacobdubail.com
Version: 0.6.8
*/


// URL to the /js directory
define( 'JTD_INSERTJS',  plugin_dir_url( __FILE__ ) . 'js'  );
define( 'JTD_INSERTCSS', plugin_dir_url( __FILE__ ) . 'css' );



if ( is_admin() ) {
    include('insert.php');
}



// add script to the front end
add_action( 'template_redirect', 'jtd_insertjs_front' );
function jtd_insertjs_front() {

    $options = get_option( 'jtd_anything_slides_options' );
    $video   = $options['video'];
    $easing  = $options['easing'];
    $fade    = $options['fade'];

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery.anythingslider', JTD_INSERTJS . '/jquery.anythingslider.min.js', array( 'jquery' ), '1.7.26' );


    // If video is selected, load the video extension
    if ( $video !== 'false' ) {

        wp_enqueue_script( 'jquery.anythingslider.video', JTD_INSERTJS . '/jquery.anythingslider.video.min.js', array( 'jquery' ), '1.3b' );
        wp_enqueue_script( 'swfobject', JTD_INSERTJS . '/swfobject.js', '', '2.2' );

    }

    // If advanced Easing is required, load the easing library
    if ( $easing !== 'swing' && $easing !== 'linear' ) {

        wp_enqueue_script( 'jquery.easing', JTD_INSERTJS . '/jquery.easing.1.2.js', array( 'jquery' ), '1.2' );

    }

    // If fade is selected, load the FX extension
    if ( $fade !== 'false' ) {

        wp_enqueue_script( 'jquery.anythingslider.fx', JTD_INSERTJS . '/jquery.anythingslider.fx.min.js', array( 'jquery' ), '1.6' );

    }



}

// add style to the front end
add_action( 'wp_print_styles', 'jtd_insertcss_front' );
function jtd_insertcss_front() {

    //wp_register_style( 'anythingslider', JTD_INSERTCSS . '/anythingslider.css', '', '1.5.10' );

    // Check for custom theme css - load it if it exists
    if ( file_exists( get_stylesheet_directory()."/anythingslider.css" ) ) {
        wp_register_style( 'anythingslider-base', get_stylesheet_directory_uri() . '/anythingslider.css' );
    }
    elseif ( file_exists( get_template_directory()."/anythingslider.css" ) ) {
        wp_register_style( 'anythingslider-base', get_template_directory_uri() . '/anythingslider.css' );
    }
    else {
        wp_register_style( 'anythingslider-base', JTD_INSERTCSS . '/anythingslider.css' );
    }

    wp_enqueue_style( 'anythingslider-base' );

//  $options = get_option( 'jtd_anything_slides_options' );
//  $theme   = $options['theme'];

//  wp_register_style( 'anythingslider-theme', JTD_INSERTCSS . '/theme-' . $theme . '.css', '', '1.7.26' );

/*
    if ( $theme != '' && $theme != 'default' ) {
        wp_enqueue_style( 'anythingslider-theme' );
    }
*/

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
            'custom-fields',
            'page-attributes'
        ),
        'exclude_from_search' => true,
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



// Add meta box on Slide page for Navigation Formatting
add_action( 'add_meta_boxes', 'jtd_anythingslider_create_metabox' );
function jtd_anythingslider_create_metabox() {
    add_meta_box( 'jtd_anythingslider_advanced', 'Advanced Slide Functions', 'jtd_anythingslider_display_advanced_metabox', 'anything_slides', 'normal', 'high' );
}
function jtd_anythingslider_display_advanced_metabox( $post ) {

    // get the meta value if it exists
    $anything_slides_nav_format = get_post_meta( $post->ID, '_jtd_anything_slides_nav_format', true );


    echo "<table class='form-table'><tbody>
        <tr class='form-field'>
            <th scope='row'> <label for='_jtd_anything_slides_nav_format'>Navigation Formatter:</label> </th>
            <td> <input id='_jtd_anything_slides_nav_format' class='code' type='text' name='_jtd_anything_slides_nav_format' value='" . esc_attr( $anything_slides_nav_format ) . "' /> </td>
            <td> <em>Customize the text used for the slide navigation</em> </td>
        </tr>
    </tbody></table>";
}

add_action( 'save_post', 'jtd_anythingslider_save_metabox' );
function jtd_anythingslider_save_metabox( $post_id ) {
    if ( isset( $_POST['_jtd_anything_slides_nav_format'] ) ) {
        update_post_meta( $post_id, '_jtd_anything_slides_nav_format', strip_tags( $_POST['_jtd_anything_slides_nav_format'] ) );
    }

}





// register shortcode for retrieving slides
add_action( 'init', 'jtd_anythingslider_register_shortcodes' );
function jtd_anythingslider_register_shortcodes() {
    add_shortcode( 'anything_slides', 'jtd_anything_slides_shortcode' );
}

function jtd_anything_slides_shortcode( $attr ) {

    // Add support for ordering the slides
    $order         = isset ( $attr['order'] )   ? $attr['order']   : 'ASC';
    $orderby       = isset ( $attr['orderby'] ) ? $attr['orderby'] : 'menu_order';
    $cat           = isset ( $attr['cat'] ) ? $attr['cat'] : '';
    $navformat     = isset ( $attr['navformat'] ) ? $attr['navformat'] : '';

    // setup slide query
    $loop = new WP_Query(
        array(
            'post_type'      => 'anything_slides',
            'orderby'        => $orderby,
            'order'          => $order,
            'posts_per_page' => -1,
            'slide_cat'      => $cat
        )
    );


    $options        = get_option( 'jtd_anything_slides_options' );

    // Appearance
    $width          = ( isset( $attr['width'] ) )             ? $attr['width']                 : ( ( $options['width'] )          ? $options['width']          : 570 );
    $height         = ( isset( $attr['height'] ) )            ? $attr['height']                : ( ( $options['height'] )         ? $options['height']         : 300 );
    $theme          = ( isset( $attr['theme'] ) )             ? $attr['theme']                 : ( ( $options['theme'] )          ? $options['theme']          : 'default' );
    $expand         = ( isset( $attr['expand'] ) )            ? $attr['expand']                : ( ( $options['expand'] )         ? $options['expand']         : 'false'   );
   $resizeContents = ( isset( $attr['resizeContents'] ) )    ? $attr['resizeContents']        : ( ( $options['resizeContents'] ) ? $options['resizeContents'] : 'true'    );
   $vertical       = ( isset( $attr['vertical'] ) )          ? $attr['vertical']              : ( ( $options['vertical'] )       ? $options['vertical']       : 'false'   );
   $showMultiple   = ( isset( $attr['showMultiple'] ) )      ? $attr['showMultiple']          : ( ( $options['showMultiple'] )   ? $options['showMultiple']   : 'false'   );
   $easing         =    ( isset( $attr['easing'] ) )            ? $attr['easing']                : ( ( $options['easing'] )         ? $options['easing']         : 'swing'   );

    $buildArrows    = ( isset( $options['arrows'] ) )         ? $options['arrows']             : 'true';
   $buildNav       = ( isset( $options['navigation'] ) )     ? $options['navigation']         : 'true';
   $startStopped   = ( isset( $options['startStop'] ) )      ? $options['startStop']          : 'false';

   $toggleArrows   = ( isset( $options['toggleArrows'] ) )   ? $options['toggleArrows']       : 'false';
   $toggleControls = ( isset( $options['toggleControls'] ) ) ? $options['toggleControls']     : 'false';
   $startText      = ( isset( $options['startText'] ) )      ? $options['startText']          : 'Start';
   $stopText       = ( isset( $options['stopText'] ) )       ? $options['stopText']           : 'Stop';
   $forwardText    = ( isset( $options['forwardText'] ) )    ? $options['forwardText']        : '&raquo;';
   $backText       = ( isset( $options['backText'] ) )       ? $options['backText']           : '&laquo;';
   $tooltipClass   = ( isset( $attr['tooltipClass'] ) )      ? $attr['tooltipClass']          : ( ( $options['tooltipClass'] )   ? $options['tooltipClass']   : 'tooltip' );



    // Function Settings
    $enableArrows   = ( isset( $options['enableArrows'] ) )   ? $options['enableArrows']       : 'true';
    $enableNav      = ( isset( $options['enableNav'] ) )      ? $options['enableNav']          : 'true';
    $enablePlay     = ( isset( $options['enablePlay'] ) )     ? $options['enablePlay']         : 'true';
    $enableKeyboard = ( isset( $options['keyboard'] ) )       ? $options['keyboard']           : 'true';




    // Navigation Settings
    $startPanel     =   ( isset( $attr['startPanel'] ) )        ? $attr['startPanel']            : ( ( $options['startPanel'] )     ? $options['startPanel']       : '1');
    $changeBy       = ( isset( $attr['changeBy'] ) )          ? $attr['changeBy']              : ( ( $options['changeBy'] )       ? $options['changeBy']         : '1');
   $hashTags       = ( isset( $options['hashTags'] ) )       ? $options['hashTags']           : 'false';
   $infiniteSlides = ( isset( $options['infinite'] ) )       ? $options['infinite']           : 'true';




    // Slideshow options
   $autoPlay        = ( isset( $attr['autoPlay'] ) )           ? $attr['autoPlay']             : ( ( $options['autoPlay'] )     ? $options['autoPlay']          : 'true');
   $autoPlayLocked  = ( isset( $options['autoPlayLocked'] ) )  ? $options['autoPlayLocked']    : 'false';
   $autoPlayDelayed = ( isset( $options['autoPlayDelayed'] ) ) ? $options['autoPlayDelayed']   : 'false';
   $pauseOnHover    = ( isset( $options['pauseOnHover'] ) )    ? $options['pauseOnHover']      : 'true';
   $stopAtEnd       = ( isset( $options['stopAtEnd'] ) )       ? $options['stopAtEnd']         : 'false';
   $playRtl         = ( isset( $options['playRtl'] ) )         ? $options['playRtl']           : 'false';




   // Timing Settings
    $delay          = ( isset( $attr['delay'] ) )               ? $attr['delay']                : ( ( $options['delay'] )      ? $options['delay']      : 5000 );
    $resumeDelay    = ( isset( $attr['resume'] ) )              ? $attr['resume']               : ( ( $options['resume'] )     ? $options['resume']     : 9000 );
    $animation      = ( isset( $attr['animation'] ) )           ? $attr['animation']            : ( ( $options['animation'] )  ? $options['animation']  : 800 );



    // Interactivity Settings
    $clickForwardArrow = ( isset( $options['clickForwardArrow'] ) ) ? $options['clickForwardArrow'] : 'click';
    $clickBackArrow    = ( isset( $options['clickBackArrow'] ) )    ? $options['clickBackArrow']    : 'click';
    $clickControls     = ( isset( $options['clickControls'] ) )     ? $options['clickControls']     : 'click';
    $clickSlideshow    = ( isset( $options['clickSlideshow'] ) )    ? $options['clickSlideshow']    : 'click focusin';



    // Video
    $video          = ( isset( $options['video'] ) )          ? $options['video']              : 'false';
    $addWmode       = ( isset( $options['wmode'] ) )          ? $options['wmode']              : 'NULL';



    // FX
    $fade           = ( isset( $attr['fade'] ) )              ? $attr['fade']                     : ( ( $options['fade'] )       ? $options['fade']       : 'false' );
    $animation      = ( $fade !== 'false' )                   ? 0                                 : $animation;
    $fadeTiming     = ( isset( $attr['fadeTiming'] ) && $fade !== 'false' ) ? $attr['fadeTiming'] : ( ( $options['fadeTiming'] && $fade !== 'false' ) ? $options['fadeTiming'] : 0 );


    if ( $theme !== '' && $theme !== 'default' ) {
        wp_register_style( 'anythingslider-theme', JTD_INSERTCSS . '/theme-' . $theme . '.css', '', '1.7.26' );
        wp_enqueue_style( 'anythingslider-theme' );
    }


    // do we have results
    if ( $loop->have_posts() ) {

        $i               = 0;
        $id              = ( isset( $attr['id'] ) ) ? $attr['id'] : rand(5, 500);
        //$rand            = rand(5, 500);
        $output          = "<ul id='slider-{$id}' class='anythingSlider'>";

        while ( $loop->have_posts() ) {

            $loop->the_post();

            // setup the NavigationFormatter object based on the custom field value for each slide
            if ( $navformat ) {

                global $post;

                if ( get_post_meta( $post->ID, '_jtd_anything_slides_nav_format', true ) ) {
                    $navArray[$i] = get_post_meta( $post->ID, '_jtd_anything_slides_nav_format', true );
                } else {
                    $navArray[$i] = get_the_title();
                }

                $setupNavFormat  = true;

            } else { // if no the navFormat attribute is not present in the shortcode, return NULL

                $navFormat       = "'NULL'";

                $setupNavFormat  = false;

            }

            $i++;

            $content      = get_the_content();

            $output      .= "<li>";

            if ( $content ) {
                $output   .= "<div class='content clearfix'>" .  do_shortcode($content) . "</div>";
            }

            $output      .= "</li>";

        }

        // Have to build the NavigationFormatter function outside of the Loop
        if ( $setupNavFormat ) {
            $navArray  = json_encode($navArray);

            $navFormat = "function(index, panel){
                return {$navArray}[index - 1];
            }";
        }


        $output        .= "</ul>";

        // output the jquery plugin code

        if ( $fade !== 'false' ) {

            $output .= "
            <style>
                /* This CSS is needed for the fading transition */
                #slider-{$id} .panel             { filter: alpha(opacity=00); opacity: 0; }
                #slider-{$id} .panel.activePage  { filter: alpha(opacity=99); opacity: 1; } /* make sure active panel is visible */
            </style>
            ";
        }

        $output .= "
        <style>
            #slider-{$id} { width: {$width}px; height: {$height}px; }
        </style>
        <script>
        jQuery(function() {
            jQuery('#slider-{$id}').anythingSlider({

                theme               : '{$theme}',
                expand              : {$expand},
                resizeContents      : {$resizeContents},
                vertical            : {$vertical},
                showMultiple        : {$showMultiple},
                easing              : '{$easing}',

                buildArrows         : {$buildArrows},
                buildNavigation     : {$buildNav},
                buildStartStop      : {$startStopped},


                toggleArrows        : {$toggleArrows},
                toggleControls      : {$toggleControls},

                startText           : '{$startText}',
                stopText            : '{$stopText}',
                forwardText         : '{$forwardText}',
                backText            : '{$backText}',
                tooltipClass        : '{$tooltipClass}',


                enableArrows        : {$enableArrows},
                enableNavigation    : {$enableNav},
                enableStartStop     : {$enablePlay},
                enableKeyboard      : {$enableKeyboard},


                startPanel          : '{$startPanel}',
                changeBy            : '{$changeBy}',
                hashTags            : {$hashTags},
                infiniteSlides      : {$infiniteSlides},
                navigationFormatter : {$navFormat},


                autoPlay            : {$autoPlay},
                autoPlayLocked      : {$autoPlayLocked},
                autoPlayDelayed     : {$autoPlayDelayed},
                pauseOnHover        : {$pauseOnHover},
                stopAtEnd           : {$stopAtEnd},
                playRtl             : {$playRtl},


                delay               : {$delay},
                resumeDelay         : {$resumeDelay},
                animationTime       : {$animation},
                delayBeforeAnimate  : {$fadeTiming},


                clickForwardArrow   : '{$clickForwardArrow}',
                clickBackArrow      : '{$clickBackArrow}',
                clickControls       : '{$clickControls}',
                clickSlideshow      : '{$clickSlideshow}',


                resumeOnVideoEnd    : {$video},
                addWmodeToObject    : '{$addWmode}'

            });
        });
        </script>";

        if ( $fade !== 'false' ) {

            $output .= "
                <script>
                jQuery(function() {
                    jQuery('#slider-{$id}').anythingSliderFx({
                        '.panel' : [ 'fade', '', {$fadeTiming}, '{$easing}' ]
                });
            });
            </script>
            ";

        }

    } wp_reset_query();

    $output .= do_action( "add_anything_slider_FX", $id );
    $output .= do_action( "add_anything_slider_FX_{$id}", $id );


    return $output;

    //do_action( "add_anything_slider_FX", $id );
    //do_action( "add_anything_slider_FX_{$id}", $id );

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
    wp_enqueue_script( 'anything_slider_admin', JTD_INSERTJS . '/admin.js' );

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
            <?php do_settings_sections('anything_slider_function'); ?>
            <?php do_settings_sections('anything_slider_navigation'); ?>
            <?php do_settings_sections('anything_slider_options'); ?>
            <?php do_settings_sections('anything_slider_timing'); ?>
            <?php do_settings_sections('anything_slider_interactivity'); ?>
            <?php do_settings_sections('anything_slider_video'); ?>
            <?php do_settings_sections('anything_slider_fx'); ?>
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
    add_settings_section( 'jtd_anything_slides_option_section', 'Appearance Settings <span class="sidebar-name-arrow"></span>',                  'jtd_anything_slides_option_appearance_text',    'anything_slider_appearance' );
    add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>Function Settings <span class="sidebar-name-arrow"></span></h3>',     'jtd_anything_slides_option_function_text',      'anything_slider_function' );
    add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>Navigation Settings <span class="sidebar-name-arrow"></span></h3>',   'jtd_anything_slides_option_navigation_text',    'anything_slider_navigation' );
    add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>Slideshow Options <span class="sidebar-name-arrow"></span></h3>',     'jtd_anything_slides_option_options_text',       'anything_slider_options' );
    add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>Timing Options <span class="sidebar-name-arrow"></span></h3>',        'jtd_anything_slides_option_timing_text',        'anything_slider_timing' );
    add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>Interactivity Settings (advanced and beta)<span class="sidebar-name-arrow"></span></h3>', 'jtd_anything_slides_option_interactivity_text', 'anything_slider_interactivity' );
    add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>Video Settings <span class="sidebar-name-arrow"></span></h3>',        'jtd_anything_slides_option_video_text',         'anything_slider_video' );
    add_settings_section( 'jtd_anything_slides_option_section', '</div><h3>FX Settings <span class="sidebar-name-arrow"></span></h3>',           'jtd_anything_slides_option_fx_text', 'anything_slider_fx' );




    // Register each of the fields that will be displayed on the options page -> set a callback for each setting to spit out the actual form field
    // Appearance Settings
    add_settings_field( 'jtd_anything_slides-width',         '<label for="width">Width</label>',                     'jtd_anything_slides_width_callback',        'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-height',        '<label for="height">Height</label>',                   'jtd_anything_slides_height_callback',       'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-theme',         '<label for="theme">Theme</label>',                     'jtd_anything_slides_theme_callback',        'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-expand',        '<label for="expand">Expand</label>',                   'jtd_anything_slides_expand_callback',       'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-resizeContents','<label for="resizeContents">Resize</label>',           'jtd_anything_slides_resize_callback',       'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-vertical',      '<label for="vertical">Vertical</label>',               'jtd_anything_slides_vertical_callback',     'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-showMultiple',  '<label for="showMultiple">Show Multiple</label>',      'jtd_anything_slides_showMultiple_callback', 'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-easing',        '<label for="easing">Easing</label>',                   'jtd_anything_slides_easing_callback',       'anything_slider_appearance', 'jtd_anything_slides_option_section' );

    add_settings_field( 'jtd_anything_slides-arrows',       '<label for="arrows">Build Arrows</label>',               'jtd_anything_slides_arrows_callback',      'anything_slider_appearance',  'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-navigation',   '<label for="navigation">Build Navigation</label>',       'jtd_anything_slides_navigation_callback',  'anything_slider_appearance',  'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-startStop',    '<label for="startStop">Build Start/Stop</label>',        'jtd_anything_slides_startStop_callback',   'anything_slider_appearance',  'jtd_anything_slides_option_section' );

    add_settings_field( 'jtd_anything_slides-toggleArrows', '<label for="toggleArrows">Toggle Arrows</label>',        'jtd_anything_slides_toggleArrows_callback', 'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-toggleContr',  '<label for="toggleContr">Toggle Controls</label>',       'jtd_anything_slides_toggleContr_callback',  'anything_slider_appearance', 'jtd_anything_slides_option_section' );

    add_settings_field( 'jtd_anything_slides-startText',    '<label for="startText">Start Text</label>',              'jtd_anything_slides_startText_callback',    'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-stopText',     '<label for="stopText">Stop Text</label>',                'jtd_anything_slides_stopText_callback',     'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-forwardText',  '<label for="forwardText">Forward Text</label>',          'jtd_anything_slides_forwardText_callback',  'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-backText',     '<label for="backText">Back Text</label>',                'jtd_anything_slides_backText_callback',     'anything_slider_appearance', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-tooltipClass', '<label for="tooltipClass">Tooltip Class</label>',        'jtd_anything_slides_tooltipClass_callback', 'anything_slider_appearance', 'jtd_anything_slides_option_section' );
/*
  // ** Appearance **
  theme               : "default", // Theme name
  expand              : false,     // If true, the entire slider will expand to fit the parent element
  resizeContents      : true,      // If true, solitary images/objects in the panel will expand to fit the viewport
  showMultiple        : false,     // Set this value to a number and it will show that many slides at once
  easing              : "swing",   // Anything other than "linear" or "swing" requires the easing plugin or jQuery UI

  buildArrows         : true,      // If true, builds the forwards and backwards buttons
  buildNavigation     : true,      // If true, builds a list of anchor links to link to each panel
  buildStartStop      : true,      // ** If true, builds the start/stop button

  toggleArrows        : false,     // If true, side navigation arrows will slide out on hovering & hide @ other times
  toggleControls      : false,     // if true, slide in controls (navigation + play/stop button) on hover and slide change, hide @ other times

  startText           : "Start",   // Start button text
  stopText            : "Stop",    // Stop button text
  forwardText         : "&raquo;", // Link text used to move the slider forward (hidden by CSS, replaced with arrow image)
  backText            : "&laquo;", // Link text used to move the slider back (hidden by CSS, replace with arrow image)
  tooltipClass        : "tooltip", // Class added to navigation & start/stop button (text copied to title if it is hidden by a negative text indent)
*/


    // Function Settings
    add_settings_field( 'jtd_anything_slides-enableArrows',   '<label for="enableArrows">Enable Arrows</label>',        'jtd_anything_slides_enableArrows_callback',   'anything_slider_function',  'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-enableNav',      '<label for="enableNav">Enable Navigation</label>',       'jtd_anything_slides_enableNav_callback',      'anything_slider_function',  'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-enablePlay',     '<label for="enablePlay">Enable Start Stop</label>',      'jtd_anything_slides_enablePlay_callback',     'anything_slider_function',  'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-keyboard',       '<label for="keyboard">Enable Keyboard</label>',          'jtd_anything_slides_keyboard_callback',       'anything_slider_function',  'jtd_anything_slides_option_section' );
/*
  // Function
  enableArrows        : true,      // if false, arrows will be visible, but not clickable.
  enableNavigation    : true,      // if false, navigation links will still be visible, but not clickable.
  enableStartStop     : true,      // if false, the play/stop button will still be visible, but not clickable. Previously "enablePlay"
  enableKeyboard      : true,      // if false, keyboard arrow keys will not work for this slider.
*/


    // Navigation Settings
    add_settings_field( 'jtd_anything_slides-startPanel',   '<label for="startPanel">Start Panel</label>',            'jtd_anything_slides_startPanel_callback',   'anything_slider_navigation', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-changeBy',     '<label for="changeBy">Change Slides By</label>',         'jtd_anything_slides_changeBy_callback',     'anything_slider_navigation', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-hashTags',     '<label for="hashTags">Display Hash Tags</label>',        'jtd_anything_slides_hashTags_callback',     'anything_slider_navigation', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-infinite',     '<label for="infinite">Infinite Slides</label>',          'jtd_anything_slides_infinite_callback',     'anything_slider_navigation', 'jtd_anything_slides_option_section' );
//  add_settings_field( 'jtd_anything_slides-navFormatter', '<label for="navFormatter">Navigation Formatter</label>', 'jtd_anything_slides_navFormatter_callback', 'anything_slider_navigation', 'jtd_anything_slides_option_section' );
/*
  // Navigation
  startPanel          : 1,         // This sets the initial panel
  changeBy            : 1,         // Amount to go forward or back when changing panels.
  hashTags            : true,      // Should links change the hashtag in the URL?
  infiniteSlides      : true,      // if false, the slider will not wrap & not clone any panels
  navigationFormatter : null,      // Details at the top of the file on this use (advanced use)
*/


    // Slideshow Options
    add_settings_field( 'jtd_anything_slides-autoPlay',        '<label for="autoPlay">Auto Play</label>',                'jtd_anything_slides_autoPlay_callback',        'anything_slider_options', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-autoPlayLocked',  '<label for="autoPlayLocked">Auto Play Locked</label>',   'jtd_anything_slides_autoPlayLocked_callback',  'anything_slider_options', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-autoPlayDelayed', '<label for="autoPlayDelayed">Auto Play Delayed</label>', 'jtd_anything_slides_autoPlayDelayed_callback', 'anything_slider_options', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-pauseOnHover',    '<label for="pauseOnHover">Pause on Hover</label>',       'jtd_anything_slides_pauseOnHover_callback',    'anything_slider_options', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-stopAtEnd',       '<label for="stopAtEnd">Stop at end of slides</label>',   'jtd_anything_slides_stopAtEnd_callback',       'anything_slider_options', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-playRtl',         '<label for="playRtl">Play Right to Left</label>',        'jtd_anything_slides_playRtl_callback',         'anything_slider_options', 'jtd_anything_slides_option_section' );
/*
  // Slideshow options
  autoPlay            : false,     // If true, the slideshow will start running; replaces "startStopped" option
  autoPlayLocked      : false,     // If true, user changing slides will not stop the slideshow
  autoPlayDelayed     : false,     // If true, starting a slideshow will delay advancing slides; if false, the slider will immediately advance to the next slide when slideshow starts
  pauseOnHover        : true,      // If true & the slideshow is active, the slideshow will pause on hover
  stopAtEnd           : false,     // If true & the slideshow is active, the slideshow will stop on the last page. This also stops the rewind effect when infiniteSlides is false.
  playRtl             : false,     // If true, the slideshow will move right-to-left
*/


    // Timing Options
    add_settings_field( 'jtd_anything_slides-delay',          '<label for="delay">Delay</label>',                     'jtd_anything_slides_delay_callback',          'anything_slider_timing', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-resume',         '<label for="resume">Resume Delay</label>',             'jtd_anything_slides_resume_callback',         'anything_slider_timing', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-animation',      '<label for="animation">Animation Time</label>',        'jtd_anything_slides_animation_callback',      'anything_slider_timing', 'jtd_anything_slides_option_section' );
/*
  // Times
  delay               : 3000,      // How long between slideshow transitions in AutoPlay mode (in milliseconds)
  resumeDelay         : 15000,     // Resume slideshow after user interaction, only if autoplayLocked is true (in milliseconds).
  animationTime       : 600,       // How long the slideshow transition takes (in milliseconds)
*/


    // Interactivity Options -- Very Beta
    add_settings_field( 'jtd_anything_slides-clickForwardArrow', '<label for="clickForwardArrow">Forward Arrow Click Event</label>', 'jtd_anything_slides_clickForwardArrow_callback', 'anything_slider_interactivity', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-clickBackArrow',    '<label for="clickBackArrow">Back Arrow Click Event</label>',       'jtd_anything_slides_clickBackArrow_callback',    'anything_slider_interactivity', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-clickControls',     '<label for="clickControls">Navigation Click Event</label>',        'jtd_anything_slides_clickControls_callback',     'anything_slider_interactivity', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-clickSlideshow',    '<label for="clickSlideshow">Slideshow Click Event</label>',        'jtd_anything_slides_clickSlideshow_callback',    'anything_slider_interactivity', 'jtd_anything_slides_option_section' );
/*
  // Interactivity
  clickForwardArrow   : "click",         // Event used to activate forward arrow functionality (e.g. add jQuery mobile's "swiperight")
  clickBackArrow      : "click",         // Event used to activate back arrow functionality (e.g. add jQuery mobile's "swipeleft")
  clickControls       : "click focusin", // Events used to activate navigation control functionality
  clickSlideshow      : "click",         // Event used to activate slideshow play/stop button
*/


    // Video Options
//  add_settings_field( 'jtd_anything_slides-resumeOnVideo',  '<label for="resumeOnVideo">Resume After Video</label>','jtd_anything_slides_resumeOnVideo_callback',  'anything_slider', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-video',          '<label for="video">Video</label>',                     'jtd_anything_slides_video_callback',          'anything_slider_video', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-wmode',          '<label for="wmode">Wmode</label>',                     'jtd_anything_slides_wmode_callback',          'anything_slider_video', 'jtd_anything_slides_option_section' );
/*
    // Video
    resumeOnVideoEnd    : true,      // If true & the slideshow is active & a supported video is playing, it will pause the autoplay until the video is complete
    addWmodeToObject    : "opaque",  // If your slider has an embedded object, the script will automatically add a wmode parameter with this setting
    isVideoPlaying      : function(base){ return false; } // return true if video is playing or false if not - used by video extension
*/



    // FX Options
//  add_settings_field( 'jtd_anything_slides-resumeOnVideo',  '<label for="resumeOnVideo">Resume After Video</label>','jtd_anything_slides_resumeOnVideo_callback',  'anything_slider', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-fade',           '<label for="fade">Fade</label>',                       'jtd_anything_slides_fade_callback',           'anything_slider_fx', 'jtd_anything_slides_option_section' );
    add_settings_field( 'jtd_anything_slides-fadeTiming',     '<label for="fadeTiming">Fade Timing</label>',          'jtd_anything_slides_fadeTiming_callback',     'anything_slider_fx', 'jtd_anything_slides_option_section' );





} // End settings




// Draw the section header
function jtd_anything_slides_option_appearance_text() {
    echo '<div class="inside">';
}
function jtd_anything_slides_option_function_text() {
    echo '<div class="inside">';
}
function jtd_anything_slides_option_navigation_text() {
    echo '<div class="inside">';
}
function jtd_anything_slides_option_options_text() {
    echo '<div class="inside">';
}
function jtd_anything_slides_option_timing_text() {
    echo '<div class="inside">';
}
function jtd_anything_slides_option_interactivity_text() {
    echo '<div class="inside">';
}
function jtd_anything_slides_option_video_text() {
    echo '<div class="inside">';
}
function jtd_anything_slides_option_fx_text() {
    echo '<div class="inside">';
}




// Display and fill the form field

/*
********************
Appearance Settings
********************
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
function jtd_anything_slides_theme_callback() {
    $options   = get_option( 'jtd_anything_slides_options' );
    $theme     = $options['theme'];
    echo "<select id='theme' name='jtd_anything_slides_options[theme]'>
        <option value=''" .                  selected( $theme, '', false )                  . ">Default</option>
        <option value='construction'" .      selected( $theme, 'construction', false )      . ">Construction</option>
        <option value='cs-portfolio'" .      selected( $theme, 'cs-portfolio', false )      . ">CS Portfolio</option>
        <option value='metallic'" .          selected( $theme, 'metallic', false )          . ">Metallic</option>
        <option value='minimalist-round'" .  selected( $theme, 'minimalist-round', false )  . ">Minimalist Round</option>
        <option value='minimalist-square'" . selected( $theme, 'minimalist-square', false ) . ">Minimalist Square</option>
    </select>";
    echo "<span class='description'>Select a theme, or leave blank for default</span>";
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
function jtd_anything_slides_vertical_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $vertical = $options['vertical'];
    echo "<label>Yes <input id='vertical' name='jtd_anything_slides_options[vertical]' type='radio' value='true'"  . checked( $vertical, 'true', false ) . "/></label><br />";
    echo "<label>No  <input id='vertical' name='jtd_anything_slides_options[vertical]' type='radio' value='false'" . checked( $vertical, 'false', false ) . "/></label>";
    echo "<span class='description'>If yes, all panels will slide vertically; they slide horizontally otherwise</span>";
}
function jtd_anything_slides_showMultiple_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $multiple = $options['showMultiple'];
    echo "<input id='showMultiple' name='jtd_anything_slides_options[showMultiple]' type='number' min=0 value='$multiple' />";
    echo "<span class='description'>Set this value to a number and it will show that many slides at once</span>";
}

function jtd_anything_slides_easing_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $easing   = $options['easing'];
    echo "<select id='easing' name='jtd_anything_slides_options[easing]'>
        <option value='swing'" .            selected( $easing, 'swing', false )            . ">Swing</option>
        <option value='linear'" .           selected( $easing, 'linear', false )           . ">Linear</option>
        <option value='easeInQuad'" .       selected( $easing, 'easeInQuad', false )       . ">easeInQuad</option>
        <option value='easeOutQuad'" .      selected( $easing, 'easeOutQuad', false )      . ">easeOutQuad</option>
        <option value='easeInOutQuad'" .    selected( $easing, 'easeInOutQuad', false )    . ">easeInOutQuad</option>
        <option value='easeInCubic'" .      selected( $easing, 'easeInCubic', false )      . ">easeInCubic</option>
        <option value='easeOutCubic'" .     selected( $easing, 'easeOutCubic', false )     . ">easeOutCubic</option>
        <option value='easeInOutCubic'" .   selected( $easing, 'easeInOutCubic', false )   . ">easeInOutCubic</option>
        <option value='easeInQuart'" .      selected( $easing, 'easeInQuart', false )      . ">easeInQuart</option>
        <option value='easeOutQuart'" .     selected( $easing, 'easeOutQuart', false )     . ">easeOutQuart</option>
        <option value='easeInOutQuart'" .   selected( $easing, 'easeInOutQuart', false )   . ">easeInOutQuart</option>
        <option value='easeInQuint'" .      selected( $easing, 'easeInQuint', false )      . ">easeInQuint</option>
        <option value='easeOutQuint'" .     selected( $easing, 'easeOutQuint', false )     . ">easeOutQuint</option>
        <option value='easeInOutQuint'" .   selected( $easing, 'easeInOutQuint', false )   . ">easeInOutQuint</option>
        <option value='easeInSine'" .       selected( $easing, 'easeInSine', false )       . ">easeInSine</option>
        <option value='easeOutSine'" .      selected( $easing, 'easeOutSine', false )      . ">easeOutSine</option>
        <option value='easeInOutSine'" .    selected( $easing, 'easeInOutSine', false )    . ">easeInOutSine</option>
        <option value='easeInExpo'" .       selected( $easing, 'easeInExpo', false )       . ">easeInExpo</option>
        <option value='easeOutExpo'" .      selected( $easing, 'easeOutExpo', false )      . ">easeOutExpo</option>
        <option value='easeInOutExpo'" .    selected( $easing, 'easeInOutExpo', false )    . ">easeInOutExpo</option>
        <option value='easeInCirc'" .       selected( $easing, 'easeInCirc', false )       . ">easeInCirc</option>
        <option value='easeOutCirc'" .      selected( $easing, 'easeOutCirc', false )      . ">easeOutCirc</option>
        <option value='easeInOutCirc'" .    selected( $easing, 'easeInOutCirc', false )    . ">easeInOutCirc</option>
        <option value='easeInElastic'" .    selected( $easing, 'easeInElastic', false )    . ">easeInElastic</option>
        <option value='easeOutElastic'" .   selected( $easing, 'easeOutElastic', false )   . ">easeOutElastic</option>
        <option value='easeInOutElastic'" . selected( $easing, 'easeInOutElastic', false ) . ">easeInOutElastic</option>
        <option value='easeOutBack'" .      selected( $easing, 'easeOutBack', false )      . ">easeInBack</option>
        <option value='easeOutBack'" .      selected( $easing, 'easeOutBack', false )      . ">easeOutBack</option>
        <option value='easeInOutBack'" .    selected( $easing, 'easeInOutBack', false )    . ">easeInOutBack</option>
        <option value='easeInBounce'" .     selected( $easing, 'easeInBounce', false )     . ">easeInBounce</option>
        <option value='easeOutBounce'" .    selected( $easing, 'easeOutBounce', false )    . ">easeOutBounce</option>
        <option value='easeInOutBounce'" .  selected( $easing, 'easeInOutBounce', false )  . ">easeInOutBounce</option>
    </select>";
    echo "<span class='description'>Anything other than 'linear' or 'swing' requires the easing plugin or jQuery UI</span>";
}

function jtd_anything_slides_arrows_callback() {
    $options    = get_option( 'jtd_anything_slides_options' );
    $arrows     = $options['arrows'];
    echo "<label>Yes <input id='arrows' name='jtd_anything_slides_options[arrows]' type='radio' value='true'"  . checked( $arrows, 'true', false ) . " /></label><br />";
    echo "<label>No  <input id='arrows' name='jtd_anything_slides_options[arrows]' type='radio' value='false'" . checked( $arrows, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes, builds the forwards and backwards buttons</span>";
}
function jtd_anything_slides_navigation_callback() {
    $options    = get_option( 'jtd_anything_slides_options' );
    $navigation = $options['navigation'];
    echo "<label>Yes <input id='navigation' name='jtd_anything_slides_options[navigation]' type='radio' value='true'"  . checked( $navigation, 'true', false ) . " /></label><br />";
    echo "<label>No  <input id='navigation' name='jtd_anything_slides_options[navigation]' type='radio' value='false'" . checked( $navigation, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes, builds a list of anchor links to link to each panel</span>";
}
function jtd_anything_slides_startStop_callback() {
    $options    = get_option( 'jtd_anything_slides_options' );
    $startStop  = $options['startStop'];
    echo "<label>Yes <input id='startStop' name='jtd_anything_slides_options[startStop]' type='radio' value='true'"  . checked( $startStop, 'true', false ) . " /></label><br />";
    echo "<label>No  <input id='startStop' name='jtd_anything_slides_options[startStop]' type='radio' value='false'" . checked( $startStop, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes, builds the start/stop button</span>";
}

function jtd_anything_slides_toggleArrows_callback() {
    $options      = get_option( 'jtd_anything_slides_options' );
    $toggleArrows = $options['toggleArrows'];
    echo "<label>Yes <input id='toggleArrows' name='jtd_anything_slides_options[toggleArrows]' type='radio' value='true'"  . checked( $toggleArrows, 'true', false ) . " /></label><br />";
    echo "<label>No  <input id='toggleArrows' name='jtd_anything_slides_options[toggleArrows]' type='radio' value='false'" . checked( $toggleArrows, 'false', false ) . " /></label>";
    echo "<span class='description'>if yes, side navigation arrows will slide out on hovering &amp; hide @ other times</span>";
}
function jtd_anything_slides_toggleContr_callback() {
    $options     = get_option( 'jtd_anything_slides_options' );
    $toggleContr = $options['toggleContr'];
    echo "<label>Yes <input id='toggleContr' name='jtd_anything_slides_options[toggleContr]' type='radio' value='true'"  . checked( $toggleContr, 'true', false ) . " /></label><br />";
    echo "<label>No  <input id='toggleContr' name='jtd_anything_slides_options[toggleContr]' type='radio' value='false'" . checked( $toggleContr, 'false', false ) . " /></label>";
    echo "<span class='description'>if yes, slide in controls (navigation + play/stop button) on hover and slide change, hide @ other times</span>";
}

function jtd_anything_slides_startText_callback() {
    $options     = get_option( 'jtd_anything_slides_options' );
    $startText   = $options['startText'];
    echo "<input id='startText' name='jtd_anything_slides_options[startText]' type='text' value='$startText' />";
    echo "<span class='description'>Start button text</span>";
}
function jtd_anything_slides_stopText_callback() {
    $options    = get_option( 'jtd_anything_slides_options' );
    $stopText   = $options['stopText'];
    echo "<input id='stopText' name='jtd_anything_slides_options[stopText]' type='text' value='$stopText' />";
    echo "<span class='description'>Stop button text</span>";
}
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
function jtd_anything_slides_tooltipClass_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $tooltip  = $options['tooltipClass'];
    echo "<input id='tooltipClass' name='jtd_anything_slides_options[tooltipClass]' type='text' value='$tooltip' />";
    echo "<span class='description'>Set this value to a number and it will show that many slides at once</span>";
}




/*
******************
Function Settings
******************
*/
function jtd_anything_slides_enableArrows_callback() {
    $options      = get_option( 'jtd_anything_slides_options' );
    $enableArrows = $options['enableArrows'];
    echo "<label>Yes <input id='enableArrows' name='jtd_anything_slides_options[enableArrows]' type='radio' value='true'"  . checked( $enableArrows, 'true',  false ) . " /></label><br />";
    echo "<label>No  <input id='enableArrows' name='jtd_anything_slides_options[enableArrows]' type='radio' value='false'" . checked( $enableArrows, 'false', false ) . " /></label>";
    echo "<span class='description'>if no, arrows will be visible, but not clickable.</span>";
}
function jtd_anything_slides_enableNav_callback() {
    $options   = get_option( 'jtd_anything_slides_options' );
    $enableNav = $options['enableNav'];
    echo "<label>Yes <input id='enableNav' name='jtd_anything_slides_options[enableNav]' type='radio' value='true'"  . checked( $enableNav, 'true',  false ) . " /></label><br />";
    echo "<label>No  <input id='enableNav' name='jtd_anything_slides_options[enableNav]' type='radio' value='false'" . checked( $enableNav, 'false', false ) . " /></label>";
    echo "<span class='description'>if no, navigation links will still be visible, but not clickable.</span>";
}
function jtd_anything_slides_enablePlay_callback() {
    $options    = get_option( 'jtd_anything_slides_options' );
    $enablePlay = $options['enablePlay'];
    echo "<label>Yes <input id='enablePlay' name='jtd_anything_slides_options[enablePlay]' type='radio' value='true'"  . checked( $enablePlay, 'true', false ) . " /></label><br />";
    echo "<label>No  <input id='enablePlay' name='jtd_anything_slides_options[enablePlay]' type='radio' value='false'" . checked( $enablePlay, 'false', false ) . " /></label>";
    echo "<span class='description'>if no, the play/stop button will still be visible, but not clickable.</span>";
}
function jtd_anything_slides_keyboard_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $keyboard = $options['keyboard'];
    echo "<label>Yes <input id='keyboard' name='jtd_anything_slides_options[keyboard]' type='radio' value='true'"  . checked( $keyboard, 'true', false ) . "/></label><br />";
    echo "<label>No  <input id='keyboard' name='jtd_anything_slides_options[keyboard]' type='radio' value='false'" . checked( $keyboard, 'false', false ) . "/></label>";
    echo "<span class='description'>If no, keyboard arrow keys will not work for the current panel</span>";
}




/*
********************
Navigation Settings
********************
*/
function jtd_anything_slides_startPanel_callback() {
    $options    = get_option( 'jtd_anything_slides_options' );
    $startPanel = $options['startPanel'];
    echo "<input id='startPanel' name='jtd_anything_slides_options[startPanel]' type='text' value='$startPanel' />";
    echo "<span class='description'>This sets the initial panel</span>";
}
function jtd_anything_slides_changeBy_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $changeBy = $options['changeBy'];
    echo "<input id='changeBy' name='jtd_anything_slides_options[changeBy]' type='number' min=0 value='$changeBy' />";
    echo "<span class='description'>Amount to go forward or back when changing panels.</span>";
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





/*
*****************
Slidshow Options
*****************
*/
function jtd_anything_slides_autoPlay_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $autoPlay = $options['autoPlay'];
    echo "<label>Yes <input id='autoPlay' name='jtd_anything_slides_options[autoPlay]' type='radio' value='true'"  . checked( $autoPlay, 'true',  false ) . " /></label><br />";
    echo "<label>No  <input id='autoPlay' name='jtd_anything_slides_options[autoPlay]' type='radio' value='false'" . checked( $autoPlay, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes, the slideshow will start running</span>";
}
function jtd_anything_slides_autoPlayLocked_callback() {
    $options        = get_option( 'jtd_anything_slides_options' );
    $autoPlayLocked = $options['autoPlayLocked'];
    echo "<label>Yes <input id='autoPlayLocked' name='jtd_anything_slides_options[autoPlayLocked]' type='radio' value='true'"  . checked( $autoPlayLocked, 'true',  false ) . " /></label><br />";
    echo "<label>No  <input id='autoPlayLocked' name='jtd_anything_slides_options[autoPlayLocked]' type='radio' value='false'" . checked( $autoPlayLocked, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes, user changing slides will not stop the slideshow</span>";
}
function jtd_anything_slides_autoPlayDelayed_callback() {
    $options         = get_option( 'jtd_anything_slides_options' );
    $autoPlayDelayed = $options['autoPlayDelayed'];
    echo "<label>Yes <input id='autoPlayDelayed' name='jtd_anything_slides_options[autoPlayDelayed]' type='radio' value='true'"  . checked( $autoPlayDelayed, 'true',  false ) . " /></label><br />";
    echo "<label>No  <input id='autoPlayDelayed' name='jtd_anything_slides_options[autoPlayDelayed]' type='radio' value='false'" . checked( $autoPlayDelayed, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes, starting a slideshow will delay advancing slides; if false, the slider will immediately advance to the next slide when slideshow starts</span>";
}
function jtd_anything_slides_pauseOnHover_callback() {
    $options      = get_option( 'jtd_anything_slides_options' );
    $pauseOnHover = $options['pauseOnHover'];
    echo "<label>Yes <input id='pauseOnHover' name='jtd_anything_slides_options[pauseOnHover]' type='radio' value='true'"  . checked( $pauseOnHover, 'true',  false ) . " /></label><br />";
    echo "<label>No  <input id='pauseOnHover' name='jtd_anything_slides_options[pauseOnHover]' type='radio' value='false'" . checked( $pauseOnHover, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes & the slideshow is active, the slideshow will pause on hover</span>";
}
function jtd_anything_slides_stopAtEnd_callback() {
    $options   = get_option( 'jtd_anything_slides_options' );
    $stopAtEnd = $options['stopAtEnd'];
    echo "<label>Yes <input id='stopAtEnd' name='jtd_anything_slides_options[stopAtEnd]' type='radio' value='true'"  . checked( $stopAtEnd, 'true',  false ) . " /></label><br />";
    echo "<label>No  <input id='stopAtEnd' name='jtd_anything_slides_options[stopAtEnd]' type='radio' value='false'" . checked( $stopAtEnd, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes & the slideshow is active, the slideshow will stop on the last page. This also stops the rewind effect when infiniteSlides is false.</span>";
}
function jtd_anything_slides_playRtl_callback() {
    $options = get_option( 'jtd_anything_slides_options' );
    $playRtl = $options['playRtl'];
    echo "<label>Yes <input id='playRtl' name='jtd_anything_slides_options[playRtl]' type='radio' value='true'"  . checked( $playRtl, 'true',  false ) . " /></label><br />";
    echo "<label>No  <input id='playRtl' name='jtd_anything_slides_options[playRtl]' type='radio' value='false'" . checked( $playRtl, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes, the slideshow will move right-to-left</span>";
}






/*
***************
Timing Options
***************
*/
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





/*
**********************
Interactivity Options
**********************
*/
function jtd_anything_slides_clickForwardArrow_callback() {
    $options           = get_option( 'jtd_anything_slides_options' );
    $clickForwardArrow = $options['clickForwardArrow'];

    echo "<input id='clickForwardArrow' name='jtd_anything_slides_options[clickForwardArrow]' type='text' value='$clickForwardArrow' />";
    echo "<span class='description'>Event used to activate forward arrow functionality (e.g. add jQuery mobile's 'swiperight')</span>";
}
function jtd_anything_slides_clickBackArrow_callback() {
    $options        = get_option( 'jtd_anything_slides_options' );
    $clickBackArrow = $options['clickBackArrow'];

    echo "<input id='clickBackArrow' name='jtd_anything_slides_options[clickBackArrow]' type='text' value='$clickBackArrow' />";
    echo "<span class='description'>Event used to activate back arrow functionality (e.g. add jQuery mobile's 'swipeleft')</span>";
}
function jtd_anything_slides_clickControls_callback() {
    $options        = get_option( 'jtd_anything_slides_options' );
    $clickControls  = $options['clickControls'];

    echo "<input id='clickControls' name='jtd_anything_slides_options[clickControls]' type='text' value='$clickControls' />";
    echo "<span class='description'>Events used to activate navigation control functionality</span>";
}
function jtd_anything_slides_clickSlideshow_callback() {
    $options         = get_option( 'jtd_anything_slides_options' );
    $clickSlideshow  = $options['clickSlideshow'];

    echo "<input id='clickSlideshow' name='jtd_anything_slides_options[clickSlideshow]' type='text' value='$clickSlideshow' />";
    echo "<span class='description'>Event used to activate slideshow play/stop button</span>";
}






/*
***************
Video Settings
***************
*/

function jtd_anything_slides_video_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $video    = $options['video'];
    echo "<label>Yes <input id='video' name='jtd_anything_slides_options[video]' type='radio' value='true'"  . checked( $video, 'true', false ) . " /></label><br />";
    echo "<label>No  <input id='video' name='jtd_anything_slides_options[video]' type='radio' value='false'" . checked( $video, 'false', false ) . " /></label>";
    echo "<span class='description'>If yes & the slideshow is active & a supported video is playing, it will pause the autoplay until the video is complete. visit <a target='_blank' href='http://proloser.github.com/AnythingSlider/video.html'>GitHub</a> for more info.</span>";
}
function jtd_anything_slides_wmode_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $wmode    = $options['wmode'];
    echo "<select id='wmode' name='jtd_anything_slides_options[wmode]'>
                <option value=''" . selected( $wmode, '', false ) . ">Disabled</option>
                <option value='opaque'" . selected( $wmode, 'opaque', false ) . ">Opaque</option>
                <option value='transparent'" . selected( $wmode, 'transparent', false ) . ">Transparent</option>
        </select>";
    echo "<span class='description'>If your slider has an embedded object, the script will automatically add a wmode parameter with this setting</span>";
}










/*
***************
FX Settings
***************
*/

function jtd_anything_slides_fade_callback() {
    $options  = get_option( 'jtd_anything_slides_options' );
    $fade     = $options['fade'];
    echo "<label>Yes <input id='fade' name='jtd_anything_slides_options[fade]' type='radio' value='true'"  . checked( $fade, 'true', false ) . " /></label><br />";
    echo "<label>No  <input id='fade' name='jtd_anything_slides_options[fade]' type='radio' value='false'" . checked( $fade, 'false', false ) . " /></label>";
    echo "<span class='description'>If true, slide transition will be zero and slides will fade in/out.</span>";
}
function jtd_anything_slides_fadeTiming_callback() {
    $options    = get_option( 'jtd_anything_slides_options' );
    $fadeTiming = $options['fadeTiming'];
    echo "<input id='fadeTiming' name='jtd_anything_slides_options[fadeTiming]' type='number' min=0 value='$fadeTiming' />";
    echo "<span class='description'>How long would you like the fade in/out to take?</span>";
}














// Validate user input (we want numbers only)
function jtd_anything_slides_validate_options($input) {
    $easing                  = array( 'swing', 'linear', 'easeInQuad', 'easeOutQuad', 'easeInOutQuad', 'easeInCubic', 'easeOutCubic', 'easeInOutCubic', 'easeInQuart', 'easeOutQuart', 'easeInOutQuart', 'easeInQuint', 'easeOutQuint', 'easeInOutQuint', 'easeInSine', 'easeOutSine', 'easeInOutSine', 'easeInExpo', 'easeOutExpo', 'easeInOutExpo', 'easeInCirc', 'easeOutCirc', 'easeInOutCirc', 'easeInElastic', 'easeOutElastic', 'easeInOutElastic', 'easeInBack', 'easeOutBack', 'easeInOutBack', 'easeInBounce', 'easeOutBounce', 'easeInOutBounce' );
    $themes                  = array( 'construction', 'cs-portfolio', 'metallic', 'minimalist-round', 'minimalist-square' );
    $wmodes                  = array( '', 'opaque', 'transparent' );
    $boolean                 = array( 'true', 'false' );
    $allowed                 = array(  );
    $valid                   = array();

    // Appearance Settings
    $valid['width']          = preg_replace( '/[^0-9]/',        '', $input['width'] );
    $valid['height']         = preg_replace( '/[^0-9]/',        '', $input['height'] );
    $valid['theme']          = ( in_array( mysql_real_escape_string($input['theme']),          $themes ) )  ? $input['theme']           : 'default';
    $valid['expand']         = ( in_array( mysql_real_escape_string($input['expand']),         $boolean ) ) ? $input['expand']          : 'false';
    $valid['resizeContents'] = ( in_array( mysql_real_escape_string($input['resizeContents']), $boolean ) ) ? $input['resizeContents']  : 'false';
    $valid['vertical']       = ( in_array( mysql_real_escape_string($input['vertical']),       $boolean ) ) ? $input['vertical']        : 'false';
    $valid['showMultiple']   = preg_replace( '/[^0-9]/',        '', $input['showMultiple'] );
    $valid['easing']         = ( in_array( mysql_real_escape_string($input['easing']),         $easing ) )  ? $input['easing']          : 'swing';
    //$valid['easing']         = esc_html( $input['easing'] );

    $valid['arrows']         = ( in_array( mysql_real_escape_string($input['arrows']),         $boolean ) ) ? $input['arrows']          : 'false';
    $valid['navigation']     = ( in_array( mysql_real_escape_string($input['navigation']),     $boolean ) ) ? $input['navigation']      : 'false';
    $valid['startStop']      = ( in_array( mysql_real_escape_string($input['startStop']),      $boolean ) ) ? $input['startStop']       : 'false';

    $valid['toggleArrows']   = ( in_array( mysql_real_escape_string($input['toggleArrows']),   $boolean ) ) ? $input['toggleArrows']    : 'false';
    $valid['toggleContr']    = ( in_array( mysql_real_escape_string($input['toggleContr']),    $boolean ) ) ? $input['toggleContr']     : 'false';
    $valid['startText']      = esc_html( $input['startText'] );
    $valid['stopText']       = esc_html( $input['stopText'] );
    $valid['forwardText']    = esc_html( $input['forwardText'] );
    $valid['backText']       = esc_html( $input['backText'] );
    $valid['tooltipClass']   = preg_replace( '/[^A-Za-z0-9_]/', '', $input['tooltipClass'] );



    // Function Settings
    $valid['enableArrows']   = ( in_array( mysql_real_escape_string($input['enableArrows']),   $boolean ) ) ? $input['enableArrows']    : 'false';
    $valid['enableNav']      = ( in_array( mysql_real_escape_string($input['enableNav']),      $boolean ) ) ? $input['enableNav']       : 'false';
    $valid['enablePlay']     = ( in_array( mysql_real_escape_string($input['enablePlay']),     $boolean ) ) ? $input['enablePlay']      : 'false';
    $valid['keyboard']       = ( in_array( mysql_real_escape_string($input['keyboard']),       $boolean ) ) ? $input['keyboard']        : 'false';




    // Navigation Settings
    $valid['startPanel']     = preg_replace( '/[^0-9]/',        '', $input['startPanel'] );
    $valid['changeBy']       = preg_replace( '/[^0-9]/',        '', $input['changeBy'] );
    $valid['hashTags']       = ( in_array( mysql_real_escape_string($input['hashTags']),       $boolean ) ) ? $input['hashTags']        : 'false';
    $valid['infinite']       = ( in_array( mysql_real_escape_string($input['infinite']),       $boolean ) ) ? $input['infinite']        : 'false';



    // Slideshow options
    $valid['autoPlay']        = ( in_array( mysql_real_escape_string($input['autoPlay']),        $boolean ) ) ? $input['autoPlay']        : 'false';
    $valid['autoPlayLocked']  = ( in_array( mysql_real_escape_string($input['autoPlayLocked']),  $boolean ) ) ? $input['autoPlayLocked']  : 'false';
    $valid['autoPlayDelayed'] = ( in_array( mysql_real_escape_string($input['autoPlayDelayed']), $boolean ) ) ? $input['autoPlayDelayed'] : 'false';
    $valid['pauseOnHover']    = ( in_array( mysql_real_escape_string($input['pauseOnHover']),    $boolean ) ) ? $input['pauseOnHover']    : 'false';
    $valid['stopAtEnd']       = ( in_array( mysql_real_escape_string($input['stopAtEnd']),       $boolean ) ) ? $input['stopAtEnd']       : 'false';
    $valid['playRtl']         = ( in_array( mysql_real_escape_string($input['playRtl']),         $boolean ) ) ? $input['playRtl']         : 'false';



    // Timing Settings
    $valid['delay']           = preg_replace( '/[^0-9]/',        '', $input['delay'] );
    $valid['resume']          = preg_replace( '/[^0-9]/',        '', $input['resume'] );
    $valid['animation']       = preg_replace( '/[^0-9]/',        '', $input['animation'] );



    // Interactivity Settings
    $valid['clickForwardArrow'] = esc_html( $input['clickForwardArrow'] );
    $valid['clickBackArrow']    = esc_html( $input['clickBackArrow'] );
    $valid['clickControls']     = esc_html( $input['clickControls'] );
    $valid['clickSlideshow']    = esc_html( $input['clickSlideshow'] );



    // Video Settings
    $valid['video']          = ( in_array( mysql_real_escape_string($input['video']),           $boolean ) ) ? $input['video']          : 'false';
    $valid['wmode']          = ( in_array( mysql_real_escape_string($input['wmode']),           $wmodes  ) ) ? $input['wmode']          : 'false';



    // FX Settings
    $valid['fade']           = ( in_array( mysql_real_escape_string($input['fade']),            $boolean ) ) ? $input['fade']          : 'false';
    $valid['fadeTiming']     = preg_replace( '/[^0-9]/', '', $input['fadeTiming'] );


    return $valid;
}














// Set default options at activation
function jtd_anything_slides_set_defaults() {

    $tmp = get_option( 'jtd_anything_slides_options' );

    if ( ( !is_array( $tmp ) ) ) {
        $o = array(
                'width'               => '570',
                'height'              => '190',
                'theme'               => "default",
                'expand'              => 'false',
                'resizeContents'      => 'true',
                'vertical'            => 'false',
                'showMultiple'        => '1',
                'easing'              => "swing",

                'arrows'              => 'true',
                'navigation'          => 'true',
                'startStop'           => 'true',

                'toggleArrows'        => 'false',
                'toggleContr'         => 'false',

                'startText'           => "Start",
                'stopText'            => "Stop",
                'forwardText'         => "&raquo;",
                'backText'            => "&laquo;",
                'tooltipClass'        => "tooltip",


                'enableArrows'        => 'true',
                'enableNav'           => 'true',
                'enablePlay'          => 'true',
                'keyboard'            => 'true',


                'startPanel'          => '1',
                'changeBy'            => '1',
                'hashTags'            => 'true',
                'infinite'            => 'true',

                'autoPlay'            => 'false',
                'autoPlayLocked'      => 'false',
                'autoPlayDelayed'     => 'false',
                'pauseOnHover'        => 'true',
                'stopAtEnd'           => 'false',
                'playRtl'             => 'false',


                'delay'               => 3000,
                'resume'              => 15000,
                'animation'           => 750,


                'clickForwardArrow'   => "click",
                'clickBackArrow'      => "click",
                'clickControls'       => "click focusin",
                'clickSlideshow'      => "click",


                'video'               => 'true',
                'wmode'               => "opaque"

        );

    }

   update_option( 'jtd_anything_slides_options', $o );

}
register_activation_hook(__FILE__, 'jtd_anything_slides_set_defaults');








// Add an upgrade warning
/*
if ( is_admin() ) {
    add_action( 'in_plugin_update_message-' . plugin_basename(__FILE__), 'jtd_anything_slides_upgrade_notice' );
}
function jtd_anything_slides_upgrade_notice() {
    $info = __( '&nbsp; ATTENTION! After updating this plugin, you will need to update your settings on the the <a href="options-general.php?page=anything_slider">Settings Page</a>.', MY_TEXTDOMAIN );
    echo '<span style="color:red;">' . strip_tags( $info, '<br><a><b><i><span>' ) . '</span>';
}
*/


