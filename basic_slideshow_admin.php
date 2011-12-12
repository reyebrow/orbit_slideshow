<?php

/********************************************
// Add in a Special metaboxes on our 
// content pages
********************************************/


// Describe the actual metaboxes
function basic_video_url_meta_box()
{
	global $post;
	$slide_meta = get_post_meta($post->ID, 'slide_meta', true);	
	
	?>
	<label for="slide_meta[slide_caption]">Slide Caption:</label><br />
	<textarea type="text" style="width: 90%;" name="slide_meta[slide_caption]" id="slide_caption"/><?php echo $slide_meta['slide_caption']; ?></textarea><br /><br />
	<p>Foundation's Orbit plugin includes support for captions. Captions can include HTML</p>
	
	<label for="slide_meta[video_url]">Video URL (optional):</label><br />
	<input type="text" style="width: 90%;" name="slide_meta[video_url]" id="video_url" value="<?php echo $slide_meta['video_url']; ?>" /><br /><br />
	<p><em>Note: only use this field if you intend this to be a video slide. Making a slide into a video slide means the title and the body text won't show.</em><br/>
	Any <a href="http://codex.wordpress.org/Embeds" target="_blank">oembed</a> url should work here. Youtube videos work great if you use the shortenned share url (http://youtu.be/xxxxxxxxx). <br/>
	Vimeo works but won't pause properly when you change slides so be aware of that.</p>
	
	<label for="slide_meta[slide_url]">Link (optional):</label><br />
	<input type="text" style="width: 90%;"  name="slide_meta[slide_url]" id="slide_url" value="<?php echo $slide_meta['slide_url']; ?>" /><br /><br />
	This slide will link to its own post by default. Put something else in here or simply put &lt;none&gt; for no link.	
	<?php
}


function basic_video_weighting_meta_box()
{
	global $post;
	$slide_weight = get_post_meta($post->ID, 'slide_weight', true);
	
	?>
  	<label for="slide_weight">Slide Weight (optional):</label><br />
  	<input type="text" name="slide_weight" id="slide_weight" value="<?php echo $slide_weight; ?>" />
  	<p>Higher numbers will put the slide later in the queue.</p>
	<?php
}


//Add the metabox to the slide type
function basic_video_meta_boxes()
{
global $post;
  add_meta_box('post-video-url', __('Extra Slide Settings'), 'basic_video_url_meta_box', 'basic_slideshow_type', 'normal', 'high');
  add_meta_box('post-slide-weight', __('Slide Order'), 'basic_video_weighting_meta_box', 'basic_slideshow_type', 'side', 'high');

}
add_action('add_meta_boxes', 'basic_video_meta_boxes');


//Save metabox info
function basic_save_page_info_meta_box()
{
  if (isset($_REQUEST['slide_meta']))
    update_post_meta($_REQUEST['post_ID'], 'slide_meta', $_REQUEST['slide_meta']);	
		
   if (isset($_REQUEST['slide_weight']) && !is_numeric($_REQUEST['slide_weight']) ){
    $_REQUEST['slide_weight'] = 0;
   }
    update_post_meta($_REQUEST['post_ID'], 'slide_weight', $_REQUEST['slide_weight']);	

   
}
add_action( 'save_post', 'basic_save_page_info_meta_box');

/********************************************
// Add a column in the manage posts page
********************************************/

add_filter('manage_edit-basic_slideshow_columns', 'slide_columns');
function slide_columns($columns) {
    $columns['slide_order'] = 'Slide Order';
    return $columns;
}


add_action('manage_posts_custom_column',  'my_show_slide_columns');
function my_show_slide_columns($name) {
    global $post;
    switch ($name) {
        case 'slide_order':
            $views = get_post_meta($post->ID, 'slide_weight', true);
            echo $views;
    }
}



/********************************************
// Add in a Settings menu for this module.
********************************************/

add_action('admin_menu', 'basic_slideshow_plugin_menu');
add_action( 'admin_init', 'basic_slideshow_register_settings' );

function basic_slideshow_plugin_menu() {
  add_submenu_page('edit.php?post_type=basic_slideshow_type', 'Basic Slideshow Settings', 'Settings', 'manage_options', 're-slideshow-settings', 'basic_slideshow_plugin_options');
	
}

function basic_slideshow_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
  ?>
  <div class="wrap">
  <h2>Basic Slideshow Settings Page</h2>
  <form method="post" action="options.php">
      <?php settings_fields( 're-slideshow-settings-group' ); ?>
      <?php //do_settings_fields( 're-slideshow-settings-group' ); ?>
      <table class="form-table">
          <tr valign="top">
          <td scope="row">Slide Dimensions</td>
          <td>
          <?php    
          $params = get_option('basic_slideshow_options');   
          
          $width = $params['slide_width'] > 0 ? $params['slide_width'] : 550;
          $height = $params['slide_height'] > 0 ? $params['slide_height'] : 330;
          $tease = $params['teaser_length'] > 0 ? $params['teaser_length'] : 50;
          
          //print "<pre>".print_r($params,1)."</pre>";
          
          $slide_type = $params['type']['slide'] == 1 ? "checked=\"checked\"" : "";
          $post_type = $params['type']['post'] == 1 ? "checked=\"checked\"" : "";
          $page_type = $params['type']['page'] == 1 ? "checked=\"checked\"" : "";

          $slide_time = $params['slide_time'] > 0 ? $params['slide_time'] : 5;
          $transition_speed = $params['transition_speed'] > 0 ? $params['transition_speed'] : 500;
          $transition_type = $params['transition_type'] > 0 ? $params['transition_type'] : 500;
          
          $pauseOnHover = $params['pauseOnHover'] == 1 ?  "checked=\"checked\"" : "";
          

          $effectType[$params['transition_type']] = "selected=\"selected\"";
          
          $effects_list = Array('fade', 'horizontal-slide', 'vertical-slide', 'horizontal-push');
          
          ?>
          
            <input size="5" type="text" name="basic_slideshow_options[slide_width]" value="<?php print $width; ?>" /> X 
            <input size="5" type="text" name="basic_slideshow_options[slide_height]" value="<?php print $height; ?>" />
            <select name="basic_slideshow_options[slide_unit]">
              <option value="px">px</option>
            </select>
            <div><em>Note: For now pixels is the only unit you can use. You can override this in the CSS or with your own jquery plugin if you want.</em></div>
            </td>
          </tr>    
          
          
          <tr valign="top">
          <td scope="row">Teaser Length</td>
          <td>

            <input size="5" type="text" name="basic_slideshow_options[teaser_length]" value="<?php print $tease; ?>" /> words                    
            <div><em>(How much of the slide's body text do you want on each slide?)</em></div>
            </td>
          </tr>              

          <tr valign="top">
          <td scope="row">Slide Time</td>
          <td>

            <input size="5" type="text" name="basic_slideshow_options[slide_time]" value="<?php print $slide_time; ?>" /> display slides for this amount of time (in seconds).                    
            </td>
          </tr>   
          
          <tr valign="top">
          <td scope="row">Pause on hover</td>
          <td>

            <input type="checkbox" name="basic_slideshow_options[pauseOnHover]" <?php print $pauseOnHover; ?> value="1" /> Pause the slideshow when the mouse hovers over it?                  
            </td>
          </tr>  

          <tr valign="top">
          <td scope="row">Types allowed in the Slideshow</td>
          <td>
            <input type="checkbox" name="basic_slideshow_options[type][slide]" <?php print $slide_type; ?> value="1" /><label for="basic_slideshow_options[type][slide]">Slide</label>
            <input type="checkbox" name="basic_slideshow_options[type][post]" <?php print $post_type; ?> value="1" /><label for="basic_slideshow_options[type][post]">Posts</label>
            <input type="checkbox" name="basic_slideshow_options[type][page]" <?php print $page_type; ?> value="1" /><label for="basic_slideshow_options[type][page]">Pages</label>               
            <p>In addition to a special "slide" type you can also add pages and posts to the slideshow if you want.</p>
            </td>
          </tr> 

          <tr valign="top">
          <td scope="row">Transition Speed</td>
          <td>

            <input size="5" type="text" name="basic_slideshow_options[transition_speed]" value="<?php print $transition_speed; ?>" /> how much time for a transition (in ms).                    
            </td>
          </tr>   

          <tr valign="top">
          <td scope="row">Transition Type</td>
          <td>

              <select name="basic_slideshow_options[transition_type]">
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
    &lt;?php if (function_exists('basic_slideshow_featured_posts')) basic_slideshow_featured_posts(); ?&gt;
  </pre>
  
  
  <h3>Help! My images aren't resizing properly.</h3>
  <p>If you change the size of your slideshow above you're going to need to resize your thumbnails. Luckily a guy named Alex (Viper007Bond) wrote a plugin called "<a href="http://www.viper007bond.com/wordpress-plugins/regenerate-thumbnails/" target="_blank">Regenerate Thumbnails"</a> so you can install that and run it every time you resize the slideshow.</p>

  <h3>How do I get two slideshows working?</h3>
  <p>You don't. Not yet anyway. Maybe in a future version.</p>

  
  <h3>CSS Settings</h3>
  <p>This plugin exists with the expectation that you WILL need to style it. Below is the CSS that you might want to change. Drop this code into one of your theme's CSS files and you should be good to go.</p>
  <textarea style="width: 100%; height: 400px; background: yellow;">
<?php include('slideshow.css');?>
  </textarea>
  
  
  
  </form>
  </div><?php
	
}


function basic_slideshow_register_settings() {
	//register our settings
	register_setting( 're-slideshow-settings-group', 'basic_slideshow_options', 'basic_slideshow_sanitize' );
}


/********************************************
// Sanitize and validate input. 
// Accepts an array, return a sanitized array.
********************************************/

function basic_slideshow_sanitize($input){

  //Validate width
  $width = $input['slide_width'];
  if ( !is_numeric($width) || $width <=0 ){
    add_settings_error('basic_slideshow_type',esc_attr('settings_updated'),__('width must be a positive integer.'),'error');
    return false;
  }
  

  //Validate height
  $test = $input['slide_height'];
  if ( !is_numeric($test) || $test <=0 ){
    add_settings_error('basic_slideshow_type',esc_attr('settings_updated'),__('Height must be a positive integer.'),'error');
    return false;
  }

  //validate teaser length
  $test = $input['teaser_length'];
  if ( !is_numeric($test) || $test < 1 ){
    add_settings_error('basic_slideshow_type',esc_attr('settings_updated'),__('Teaser Length must be greater than 1'),'error');
    return false;
  }
  
    //validate transition speed
  $test = $input['transition_speed'];
  if ( !is_numeric($test) || $test < 1 ){
    add_settings_error('basic_slideshow_type',esc_attr('settings_updated'),__('transition speed must be greater than 1ms'),'error');
    return false;
  }
  
    //validate slide time
  $test = $input['slide_time'];
  if ( !is_numeric($test) || $test < 1 ){
    add_settings_error('basic_slideshow_type',esc_attr('settings_updated'),__('Slide time must be greater than 1 second'),'error');
    return false;
  }

  // Say our second option must be safe text with no HTML tags
  add_settings_error('basic_slideshow_type',esc_attr('settings_updated'),__('Settings saved.'),'updated');
  return $input;

}



function basic_slideshow_errors() {
    settings_errors( 'basic_slideshow_type' );
}
add_action( 'admin_notices', 'basic_slideshow_errors' );

?>