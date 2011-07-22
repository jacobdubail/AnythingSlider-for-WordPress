<?php 




//Adding "embed form" button
add_action('media_buttons_context', 'jtd_anythingslider_insert_button' );
function jtd_anythingslider_insert_button($context){

	$image_btn = plugin_dir_url( __FILE__ ) .  'favicon.ico';
	$out       = '<a href="#TB_inline?width=660&height=900&inlineId=anythingSlider_insert_slideshow" class="thickbox" title="Add a Slideshow"><img src="'.$image_btn.'" alt="Add a Slideshow" /></a>';
	
	return $context . $out;

}


if( !defined( "AS_CURRENT_PAGE" ) )
    define( "AS_CURRENT_PAGE", basename( $_SERVER['PHP_SELF'] ) );

if( in_array( AS_CURRENT_PAGE, array( 'post.php', 'page.php', 'page-new.php', 'post-new.php' ) ) ){
   add_action( 'admin_footer',  'jtd_anythingslider_insert_form_popup' );
}

function jtd_anythingslider_insert_form_popup() {

	$options = get_option( 'jtd_anything_slides_options' ); 

?>
	<script>
		function InsertSlideshow(){
		    
		    var cat_id    = ( jQuery("#add_cat_id").val() ) ? 'cat=' + jQuery("#add_cat_id").val() : '';
		    var category  = jQuery("#add_cat_id option[value='" + cat_id + "']").text().replace(" ", "");
		    var width     = jQuery("#width").val();
		    var height    = jQuery("#height").val();
		    var delay     = jQuery("#delay").val();
		    var resume    = jQuery("#resume").val();
		    var animation = jQuery("#animation").val();
		    var navFormat = ( jQuery("#nav_format").is(":checked") ) ? 'navFormat=true' : '';
		
		
		    var win = window.dialogArguments || opener || parent || top;
		    win.send_to_editor("[anything_slides " + cat_id + " width=" + width + " height=" + height + " " + navFormat + " delay=" + delay + " resume=" + resume + " animation=" + animation + "]");
		}
	</script>

<div id="anythingSlider_insert_slideshow" style="display:none;">
<div class="wrap">


	<div>
		<h3>Insert A SlideShow</h3>                        
	</div>
	
	<table class="form-table"><tbody>
	
	
		<tr class="form-field">
			<th scope="row"> <label for="add_cat_id">Select a Category</label> </th>
			<td> 
				<select id="add_cat_id">
					<option value="">Select a Category</option>
					<?php
					$cats = get_terms( 'slide_cat' );
					foreach( $cats as $cat ){
					?>
					<option value="<?php echo $cat->slug; ?>"><?php echo esc_html( $cat->name ); ?></option>
					<?php
					//print_r($cat);
					}
					?>
				</select> 
			</td>
			<th scope="row"> <em>optional</em> </th>
		</tr>
		
		<tr class="form-field">
			<th scope="row"><label for="width">Width</label></th>
			<td><input type="number" id="width" name="width" value="<?php echo $options['width']; ?>" /></td>
			<th scope="row"><em>Custom width for this slideshow?</em></th>
		</tr>
		
		<tr class="form-field">
			<th scope="row"><label for="height">Height</label></th>
			<td><input type="number" id="height" name="height" value="<?php echo $options['height']; ?>" /></td>
			<th scope="row"><em>Custom height for this slideshow?</em></th>
		</tr>
		
		<tr class="form-field">
			<th scope="row"><label for="delay">Slide Delay</label></th>
			<td><input type="number" id="delay" name="delay" value="<?php echo $options['delay']; ?>" /></td>
			<th scope="row"><em>How long between slideshow transitions in AutoPlay mode (in milliseconds)</em></th>
		</tr>
		
		<tr class="form-field">
			<th scope="row"><label for="resume">Resume Delay</label></th>
			<td><input type="number" id="resume" name="resume" value="<?php echo $options['resume']; ?>" /></td>
			<th scope="row"><em>Resume slideshow after user interaction (in milliseconds).</em></th>
		</tr>
		
		<tr class="form-field">
			<th scope="row"><label for="animation">Animation Time</label></th>
			<td><input type="number" id="animation" name="animation" value="<?php echo $options['animation']; ?>" /></td>
			<th scope="row"><em>How long the slideshow transition takes (in milliseconds)</em></th>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="form-field">
			<th scope="row"><label for="nav_format">Navigation Formatting?</label></th>
			<td><input type="checkbox" id="nav_format" /></td>
			<th scope="row"><em>Not sure? leave it blank.</em></th>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
		</tr>

		
		<tr class="form-field">
			<td><input type="button" class="button-primary" value="Insert Slideshow" onclick="InsertSlideshow();"/></td>
			<td><a class="button" href="#" onclick="tb_remove(); return false;">Cancel</a></td>
		</tr>
		
		
	</tbody></table>
	


</div>
</div>

<?php
	}


?>