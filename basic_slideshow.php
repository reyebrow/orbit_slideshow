<?php
/*
Plugin Name: Basic Slideshow Plugin
Description: Lightweight slideshow framework developed by Raised Eyebrow web studios for our client work. We were frustrated with the slideshow plugins available to us so we wrote our own. This plugin uses custom content types and gives a lot of flexibility when it comes to theming.
Version: 0.1
License: GPL
Author: Matt Reimer
Author URI: http://www.raisedeyebrow.com/
*/

$basic_slide_options = get_option('basic_slideshow_options');


//Make sure we've got the right kind of images set up.
add_theme_support('post-thumbnails');
add_image_size('basic_slideshow_type', $basic_slide_options['slide_width'],$basic_slide_options['slide_height'],true);
add_shortcode('basic_slideshow', 'basic_slideshow');



//Add all the JS we need to make this go.
function basic_add_scripts() {
  
  //You may have foundation loaded in your theme. If so there's no reason to do it twice.
  //print "<pre>" . print_r($wp_scripts,1) . "</pre>";
  
  wp_enqueue_script('jquery');
  
   wp_register_script('jquery.orbit-1.3.0',
         plugins_url('foundation/jquery.orbit-1.3.0.js', __FILE__),
         array('jquery'),
         '1.0' );
   wp_enqueue_style('orbit.style', plugins_url('foundation/orbit.css', __FILE__));
         
   wp_register_script('basic_slideshow_script',
         plugins_url('js/basic_slideshow.js', __FILE__),
         array('jquery.orbit-1.3.0'),
         '1.0' );
  
    wp_enqueue_script('jquery.orbit-1.3.0');
    wp_enqueue_script('basic_slideshow_script');
    
    wp_localize_script( 'basic_slideshow_script', 'slideshow_settings', get_option('basic_slideshow_options') );
  
    wp_enqueue_style('basic_slideshow_style', plugins_url('slideshow.css', __FILE__));

}
add_action('wp_enqueue_scripts', 'basic_add_scripts');


function basic_slideshow_custom_excerpt(){
  $basic_slide_options = get_option('basic_slideshow_options');
  return $basic_slide_options[teaser_length];
  
}

/***************************************************
// Shortcode Handler: Do the actual slideshow Query
****************************************************/
//This is just a shortcode handler that we can call directly.
function basic_slideshow($atts=Array() ) {

  global $wp_query;
  global $post;
  
  $basic_slide_options = get_option('basic_slideshow_options');
  
  $query_vars = array();
  
  $slideshow= "";
  //No 'Slideshow' param set. Just do the default slideshow
  if (!isset($atts['slideshow'])){  
     $query_vars['post_type'] = 'basic_slideshow_type';
     $slideshow = "default";
  }
  //'Slideshow' paramter is set: set things up for a specific slideshow
  else {    
    $query_vars['post_type'] = array();  
  	if ($basic_slide_options['type']['post']) $query_vars['post_type'][] = 'post';
  	if ($basic_slide_options['type']['page']) $query_vars['post_type'][] = 'page';
  	if ($basic_slide_options['type']['slide']) $query_vars['post_type'][] = 'basic_slideshow_type'; 
    $slideshow= $atts['slideshow'];
  	$query_vars['tax_query'] = array( array(
  		'taxonomy' => 'basic_slideshows',
  		'field' => 'slug',
  		'terms' => $atts['slideshow']
  	));
  }

  //ORder by slide weight (if given)
  $query_vars['meta_key']  = 'slide_weight';
  $query_vars['orderby'] = 'meta_value';
  $query_vars['order'] = 'ASC';  

  $basic_slide_options = get_option('basic_slideshow_options');
  
  add_filter('excerpt_length', 'basic_slideshow_custom_excerpt', 10);
  add_filter('excerpt_more', 'new_excerpt_more');


  //query_posts($query_vars);
  $slide_query = new WP_Query( $query_vars );

  //print "<pre>" . print_r($slide_query,1) . "</pre>";

  if ($slide_query->have_posts()){
    if (isset($atts['tabshow']) && $atts['tabshow'] == True ){
      basic_slideshow_do_tabshow($slide_query, $slideshow);
    }
    else {
      basic_slideshow_do_slideshow($slide_query, $slideshow);
    }
  
  }
  //Reset all the changes we've made to wp_query so that any loops below this will work properly
	wp_reset_query();
  remove_filter('excerpt_more', 'new_excerpt_more');
	remove_filter('excerpt_length','basic_slideshow_custom_excerpt');
}

/********************************************
// Helper Function to do the Actual Slideshow
********************************************/
function basic_slideshow_do_slideshow($slide_query, $slideshow="") {

  //TODO: Add better structure for foundation-friendly 
  global $wp_embed;
  
  $captions="";

  ?>
  <div id="slideshow-<?php print $slideshow; ?>" class="basic_slideshow">
  	<?php while ($slide_query->have_posts()) : $slide_query->the_post();
      $slide_meta = get_post_meta($slide_query->post->ID, 'slide_meta', true);	
      $captionTarget = "";
      $video_url = !empty($slide_meta['video_url']) ? $slide_meta['video_url'] : "";
      $image_only = !empty($slide_meta['image_only']) && $slide_meta['image_only'] == 1 ? True : false;
      $slide_url = !empty($slide_meta['slide_url']) ? $slide_meta['slide_url'] : get_permalink();
      $isVideo =  (!empty($video_url) && $video_url != "")? true : false;

	    //IF this slide has a caption then print it out for inclusion later      
      if (!empty($slide_meta['slide_caption']) && $slide_meta['slide_caption'] != ""){
      	  $captionID ++;
      	  $captionTarget = "data-caption='#".$slideshow."-Caption$captionID'";
	      $captions .= "<span class='orbit-caption' id='".$slideshow."-Caption$captionID'>";
	      $captions .= $slide_meta['slide_caption'];
	      $captions .= "</span>";
      } ?>
  	
  	<div class="slide <?php print $isVideo ?"video-slide": "";?>" <?php print $captionTarget; ?>>
  	<?php
  			if ( $isVideo ){
          $post_embed = $wp_embed->run_shortcode('[embed width="' . $basic_slide_options['slide_width'] . '" height="' . $basic_slide_options['slide_height'] . '"]' . $video_url . '[/embed]');
          print $post_embed;
  			}
  			else{ ?>
          <a class="image" href="<?php print $slide_url; ?>" >
          <?php the_post_thumbnail('basic_slideshow_type'); ?>
          </a>
          	<?php if (!$image_only){ ?>
	            <?php // HERE we print the transparent overlay and text ?>
	            <div class="meta-back">&nbsp;</div>
	            <div class="meta">
	              <h3><a href="<?php print $slide_url; ?>" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
	              
	              <?php the_excerpt(); //Here's the body of the content type gets printed ?>
	            </div>
            <?php } ?>
          <?php
        }
  			
  	?>
  	</div>
  
  	<?php endwhile; ?>
	<?php print $captions;?>
  </div> <?
}

/********************************************
// Helper Function to do the TabShow
********************************************/
function basic_slideshow_do_tabshow($slide_query, $slideshow= "default"){

  //TODO: Add better structure for foundation-friendly 
  global $wp_embed;
  $basic_slide_options = get_option('basic_slideshow_options');
  $tabID = 0;
    ?>

<div id="tabshow-<?php print $slideshow; ?>" class="row basic_tabshow">
  <div class="eight columns">
    <ul class="tabs-content" style="height: <?php print $basic_slide_options['slide_height']; ?>px;">
    
    <?php while ($slide_query->have_posts()) : $slide_query->the_post(); 

        $slide_meta = get_post_meta($slide_query->post->ID, 'slide_meta', true);	
        $image_only = !empty($slide_meta['image_only']) && $slide_meta['image_only'] == 1 ? True : false;
        $video_url = !empty($slide_meta['video_url']) ? $slide_meta['video_url'] : "";
        $slide_url = !empty($slide_meta['slide_url']) ? $slide_meta['slide_url'] : get_permalink();
        $isVideo =  (!empty($video_url) && $video_url != "")? true : false;
  
        //if ( $tabID == 1 ) print_r(get_defined_vars());
  	    //We need title tabs  
        $tabID ++;
        $active = $tabID == 1 ? "active " : "";
        $tabsTarget = $slideshow."-tab".$tabID."Tab";
	      $tabs .= "<dd><a href='#".$slideshow."-tab$tabID'>";
	      $tabs .= "<h3>" . get_the_title() . "</h3>";
	      $tabs .= $slide_meta['slide_caption'];//isset($slide_meta['slide_caption']) ? $slide_meta['slide_caption'] : "";
	      $tabs .= "</a></dd>"; ?>
        
    
        <li id="<?php print $tabsTarget; ?>" class="tab-content <?php print $active; print $isVideo ?"video-slide": "";?>">
          <?php
      			if ( $isVideo ){
              $post_embed = $wp_embed->run_shortcode('[embed width="' . $basic_slide_options['slide_width'] . '" height="' . $basic_slide_options['slide_height'] . '"]' . $video_url . '[/embed]');
              print $post_embed;
      			}
      			else{ ?>
              <a class="image" href="<?php print $slide_url; ?>" style="height: <?php print $basic_slide_options['slide_height']; ?>px;">
              <?php the_post_thumbnail('basic_slideshow_type'); ?>
              </a>
              	
          	<?php if (!$image_only){ ?>
	            <?php // HERE we print the transparent overlay and text ?>
	            <div class="meta-back">&nbsp;</div>
	            <div class="meta">
	              <h3><a href="<?php print $slide_url; ?>" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
	              
	              <?php the_excerpt(); //Here's the body of the content type gets printed ?>
	            </div>
            <?php } 
            } ?>
          	
        </li>
        
    <?php endwhile; ?>

      
    </ul>    
  </div>
  <div class="four columns">
      <dl class="nice vertical tabs" style="margin-bottom:0">
        <?php print $tabs; ?>
		  </dl>
  </div>
</div>


<?php
}

/********************************************
// Change the excerpt size
********************************************/
function new_excerpt_more($more) {
       global $post;
      $slide_meta = get_post_meta($post->ID, 'slide_meta', true);	
      $slide_url = !empty($slide_meta['slide_url']) ? $slide_meta['slide_url'] : get_permalink();
      return '<a href="'. $slide_url . '">Read More ..</a>';
}


//add ENABLEjsAPI to youtube videos
//With mad props to mehigh http://mehigh.biz/wordpress/adding-wmode-transparent-to-wordpress-3-media-embeds.html
function add_video_wmode_transparent($html, $url, $attr) {

	$pattern = '/(youtube.com\/)(v\/\w+\?version=\d+)/i';
	$add = '&enablejsapi=1';
	$new_pattern = "$1$2$add$3";
	$html = preg_replace($pattern, $new_pattern, $html);


   if (strpos($html, "<embed src=" ) !== false) {
    	$html = str_replace('</param><embed', '</param><param name="enablejsapi" value="1"></param><param name="wmode" value="transparent"></param><embed wmode="transparent" ', $html);
   		return $html;
   } else {
        return $html;
   }
}
add_filter('embed_oembed_html', 'add_video_wmode_transparent', 10, 3);


include_once('basic_slideshow_admin.php');
include_once('basic_slideshow_widget.php');

