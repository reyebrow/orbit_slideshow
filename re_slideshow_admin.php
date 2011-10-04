<?php

/********************************************
// Add in a Special metaboxes on our conten pages
********************************************/


// Describe the actual metabox
function re_video_url_meta_box()
{
	global $post;
	$slide_meta = get_post_meta($post->ID, 'slide_meta', true);	
	
	?>
	<label for="slide_meta[video_url]">Video URL (optional):</label><br />
	<input type="text" size="100" name="slide_meta[video_url]" id="video_url" value="<?php echo $slide_meta['video_url']; ?>" /><br /><br />
	Note: only use this field if you intend this to be a video slide. It will be ignored otherwise.
	
	<label for="slide_meta[slide_url]">Link (optional):</label><br />
	<input type="text" size="100" name="slide_meta[slide_url]" id="slide_url" value="<?php echo $slide_meta['slide_url']; ?>" /><br /><br />
	This slide will link to its own post by default. Put something else in here or simply put &lt;none&gt; for no link.	
	<?php
}


//Add the metabox to the slide type
function re_video_meta_boxes()
{
global $post;
  add_meta_box('post-video-url', __('Extra Slide Settings'), 're_video_url_meta_box', 're_slideshow', 'normal', 'high');

}
add_action('add_meta_boxes', 're_video_meta_boxes');


//Save metabox info
function re_save_page_info_meta_box()
{
  if (isset($_REQUEST['slide_meta']))
		update_post_meta($_REQUEST['post_ID'], 'slide_meta', $_REQUEST['slide_meta']);	
		
}
add_action( 'save_post', 're_save_page_info_meta_box');



/********************************************
// Add in a Settings menu for this module.
********************************************/

add_action('admin_menu', 're_slideshow_plugin_menu');
add_action( 'admin_init', 're_slideshow_register_settings' );

function re_slideshow_plugin_menu() {
  add_submenu_page('edit.php?post_type=re_slideshow', 'RE WP Slideshow', 'Settings', 'manage_options', 're-slideshow-settings', 're_slideshow_plugin_options');
	
}

function re_slideshow_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
  ?>
  <div class="wrap">
  <h2>RE Slideshow Settings Page</h2>
  <form method="post" action="options.php">
      <?php settings_fields( 're-slideshow-settings-group' ); ?>
      <?php do_settings_fields( 're-slideshow-settings-group' ); ?>
      <table class="form-table">
          <tr valign="top">
          <td scope="row">Slide Dimensions</td>
          <td>
          <?php    
          $params = get_option('re_slideshow_options');   
          
          $width = $params[slide_width] > 0 ? $params[slide_width] : 550;
          $height = $params[slide_height] > 0 ? $params[slide_height] : 330;
          $tease = $params[teaser_length] > 0 ? $params[teaser_length] : 50;

          $slide_time = $params[slide_time] > 0 ? $params[slide_time] : 5;
          $transition_speed = $params[transition_speed] > 0 ? $params[transition_speed] : 500;
          $transition_type = $params[transition_type] > 0 ? $params[transition_type] : 500;

          $effectType[$params['transition_type']] = "selected=\"selected\"";
          
          $effects_list = Array('blindX','blindY','blindZ','cover','curtainX','curtainY','fade','fadeZoom','growX','growY','none','scrollUp','scrollDown','scrollLeft','scrollRight','scrollHorz','scrollVert','shuffle','slideX','slideY','toss','turnUp','turnDown','turnLeft','turnRight','uncover','wipe','zoom');
          
          ?>
          
            <input size="5" type="text" name="re_slideshow_options[slide_width]" value="<?php print $width; ?>" /> X 
            <input size="5" type="text" name="re_slideshow_options[slide_height]" value="<?php print $height; ?>" />
            <select name="re_slideshow_options[slide_unit]">
              <option value="px">px</option>
              <option value="%">%</option>
              <option value="em">em</option>
            </select>
            <div><em>Note: You can override this in the CSS if you want. Also you might want to get the thumbnail regenerate plugin since this will help you resize images for slides that already exist</em></div>
            </td>
          </tr>    
          
          
          <tr valign="top">
          <td scope="row">Teaser Length</td>
          <td>

            <input size="5" type="text" name="re_slideshow_options[teaser_length]" value="<?php print $tease; ?>" /> words (10 minimum).                    
            <div><em>(How much of the slide's body do you want on each slide?)</em></div>
            </td>
          </tr>              

          <tr valign="top">
          <td scope="row">Slide Time</td>
          <td>

            <input size="5" type="text" name="re_slideshow_options[slide_time]" value="<?php print $slide_time; ?>" /> display slides for this amount of time (in seconds).                    
            </td>
          </tr>   

          <tr valign="top">
          <td scope="row">Transition Speed</td>
          <td>

            <input size="5" type="text" name="re_slideshow_options[transition_speed]" value="<?php print $transition_speed; ?>" /> how much time for a transition (in ms).                    
            </td>
          </tr>   

          <tr valign="top">
          <td scope="row">Transition Type</td>
          <td>

              <select name="re_slideshow_options[transition_type]">
                <?php foreach ($effects_list as $effect){
                  print "<option $effectType[$effect] value=\"$effect\">$effect</option>";
                } ?>
              </select>
            </td>
          </tr>   
  
  
   </table>
      
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
  <h3>Using Slideshow in your theme</h3>
  <p>You can use this theme as a widget but that might not be flexible enough for you. If you want to do things manually drop the following code into your theme and it should give you what you want.</p>
  <pre>
    &lt;?php if (function_exists('re_slideshow_featured_posts')) re_slideshow_featured_posts(); ?&gt;
  </pre>
  
  
  <h3>CSS Settings</h3>
  <p>This plugin exists with the expectation that you WILL need to style it. Below is the CSS that you might want to change. Drop this code into one of your theme's CSS files and you should be good to go.</p>
  <textarea style="width: 100%; height: 400px; background: yellow;">
  <?php include('slideshow.css');?>
  </textarea>
  
  
  
  </form>
  </div><?php
	
}


function re_slideshow_register_settings() {
	//register our settings
	register_setting( 're-slideshow-settings-group', 're_slideshow_options', 're_slideshow_sanitize' );
}



// Sanitize and validate input. Accepts an array, return a sanitized array.
function re_slideshow_sanitize($input){

  //Validate width
  $width = $input['slide_width'];
  if ( !is_numeric($width) || $width <=0 ){
    add_settings_error('re_slideshow',esc_attr('settings_updated'),__('width must be a positive integer.'),'error');
    return false;
  }
  

  //Validate height
  $test = $input['slide_height'];
  if ( !is_numeric($test) || $test <=0 ){
    add_settings_error('re_slideshow',esc_attr('settings_updated'),__('Height must be a positive integer.'),'error');
    return false;
  }

  //validate teaser length
  $test = $input['teaser_length'];
  if ( !is_numeric($test) || $test < 1 ){
    add_settings_error('re_slideshow',esc_attr('settings_updated'),__('Teaser Length must be greater than 1'),'error');
    return false;
  }
  
    //validate teaser length
  $test = $input['transition_speed'];
  if ( !is_numeric($test) || $test < 1 ){
    add_settings_error('re_slideshow',esc_attr('settings_updated'),__('Teaser Length must be greater than 1'),'error');
    return false;
  }
  
    //validate teaser length
  $test = $input['slide_time'];
  if ( !is_numeric($test) || $test < 1 ){
    add_settings_error('re_slideshow',esc_attr('settings_updated'),__('Teaser Length must be greater than 1'),'error');
    return false;
  }
  
  // Say our second option must be safe text with no HTML tags
  //$input['sometext'] =Ê wp_filter_nohtml_kses($input['sometext']);
  
  add_settings_error('re_slideshow',esc_attr('settings_updated'),__('Settings saved.'),'updated');
  return $input;

}



function re_slideshow_errors() {
    settings_errors( 're_slideshow' );
}
add_action( 'admin_notices', 're_slideshow_errors' );

?>