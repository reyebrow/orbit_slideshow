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
add_image_size('basic_slideshow', $basic_slide_options[slide_width],$basic_slide_options[slide_height],true);
add_shortcode('basic_slideshow', 'basic_slideshow');



//Add all the JS we need to make this go.
function basic_add_scripts() {
  
  //You may have foundation loaded in your theme. If so there's no reason to do it twice.
  print "<pre>" . print_r($wp_scripts,1) . "</pre>";
  
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



/********************************************
// Create the Custom 'Slideshow' Post Type
********************************************/

function basic_create_slideshow_posttype() {
  
  
  $basic_slide_options = get_option('basic_slideshow_options');
  
  $labels = array(
      'name' => _x('Slides', 'post type general name'),
      'singular_name' => _x('Slide', 'post type singular name'),
      'add_new' => _x('Add Slide','resources & tools'),
      'add_new_item' => __('Add New Slide'),
      'edit_item' => __('Edit Slide'),
      'new_item' => __('New Slide'),
      'view_item' => __('View Slide'),
      'search_items' => __('Search Slides'),
      'not_found' =>  __('No Slides Found'),
      'not_found_in_trash' => __('No Slides found in Trash'),
      'parent_item_colon' => '',
  );
  
  $args = array(
      'label' => __('Slide'),
      'labels' => $labels,
      'public' => true,
      'can_export' => true,
      'show_ui' => true,
      '_builtin' => false,
      '_edit_link' => 'post.php?post=%d', // ?
      'capability_type' => 'post',
      'menu_icon' => plugins_url('/images/slide.gif', __FILE__),
      'hierarchical' => false,
      'rewrite' => array( "slug" => "slide" ),
      'supports'=> array('title', 'body', 'thumbnail', 'editor') ,
      'show_in_nav_menus' => false
  );
  
  //Add Slideshows as a taxonomy type
	$slideshows_labels = array(
	    'name' 						=> _x( 'Slideshows', 'taxonomy general name' ),
	    'singular_name' 			=> _x( 'Slideshow', 'taxonomy singular name' ),
	    'search_items' 				=> __( 'Search Slideshows' ),
	    'all_items' 				=> __( 'All Slideshows' ),
	    'parent_item' 				=> __( 'Parent Slideshow' ),
	    'parent_item_colon' 		=> __( 'Parent Slideshow:' ),
	    'edit_item' 				=> __( 'Edit Slideshows' ), 
	    'update_item' 				=> __( 'Update Slideshow' ),
	    'add_new_item' 				=> __( 'Add New Slideshow' ),
	    'new_item_name' 			=> __( 'New Slideshows Name' ),
	    'menu_name' 				=> __( 'Slideshows' ),
	  ); 	

	register_taxonomy('basic_slideshows', '', array(
	  'hierarchical' 				=> true,
	  'labels'	 					=> $slideshows_labels,
	  'show_ui' 					=> true,
	  'query_var' 					=> true,
	  'rewrite' 					=> array( 'slug' => 'slideshow' ),
	));
	

  //Register our "Slide" post type
  register_post_type( 'basic_slideshow_type', $args);
  
  
	//We want the option of adding regular posts to the slideshow
	if ($basic_slide_options['type']['post']) register_taxonomy_for_object_type('basic_slideshows', 	'post');
	if ($basic_slide_options['type']['page']) register_taxonomy_for_object_type('basic_slideshows', 	'page');
	if ($basic_slide_options['type']['slide']) register_taxonomy_for_object_type('basic_slideshows', 	'basic_slideshow_type');  

}
add_action( 'init', 'basic_create_slideshow_posttype' );


function basic_slideshow_custom_excerpt(){
  $basic_slide_options = get_option('basic_slideshow_options');
  return $basic_slide_options[teaser_length];
  
}

/********************************************
// Do the actual slideshow
********************************************/
//This is just a shortcode handler that we can call directly.
function basic_slideshow($atts=Array() ) {

  global $wp_query;
  global $post;
  global $wp_embed;
  
  //$query_vars = $wp_query->query_vars;
  $query_vars = array();
  
  
  //TODO: DEFAULT QUERY WHEN NO SLIDESHOW GIVEN
  if (!isset($args['slideshow'])){
    //Set up for the default slideshow that only uses slides (not posts or pages)
     $query_vars['post_type'] = 'basic_slideshow_type';
  }
  else {
    //set up for multiple slideshows
    $query_vars['post_type'] = '';  
  	if ($basic_slide_options['type']['post']) $query_vars['post_type'] .= 'basic_slideshow_type';
  	if ($basic_slide_options['type']['page']) $query_vars['post_type'] .= 'basic_slideshow_type';
  	if ($basic_slide_options['type']['slide']) $query_vars['post_type'] .= 'basic_slideshow_type'; 
    $query_vars['post_type'] = trim($query_vars['post_type'], ',');

  }

  $query_vars['meta_key']  = 'slide_weight';
  $query_vars['orderby'] = 'meta_value';
  $query_vars['order'] = 'ASC';  

  $basic_slide_options = get_option('basic_slideshow_options');
  
  add_filter('excerpt_length', 'basic_slideshow_custom_excerpt', 10);
  add_filter('excerpt_more', 'new_excerpt_more');

	$captions="";

  //query_posts($query_vars);
  $slide_query = new WP_Query( $query_vars );
//print_r($slide_query);

if ($slide_query->have_posts()){
  ?>
  <div id="basic_slideshow">
  	<?php while ($slide_query->have_posts()) : $slide_query->the_post(); ?>
  	<?php
      $slide_meta = get_post_meta($post->ID, 'slide_meta', true);	
      $captionTarget = "";
      $video_url = !empty($slide_meta['video_url']) ? $slide_meta['video_url'] : "";
      $slide_url = !empty($slide_meta['slide_url']) ? $slide_meta['slide_url'] : get_permalink();
      $isVideo =  (!empty($video_url) && $video_url != "")? true : false;

	  //IF this slide has a caption then print it out for inclusion later      
      if (!empty($slide_meta['slide_caption']) && $slide_meta['slide_caption'] != ""){
      	  $captionID ++;
      	  $captionTarget = "data-caption='#caption$captionID'";
	      $captions .= "<span class='orbit-caption' id='caption$captionID'>";
	      $captions .= $slide_meta['slide_caption'];
	      $captions .= "</span>";
      } ?>
  	
  	<div class="item <?php print $isVideo ?"video-slide": "";?>" <?php print $captionTarget; ?>>
  	<?php
  			if ( $isVideo ){
          $post_embed = $wp_embed->run_shortcode('[embed width="' . $basic_slide_options['slide_width'] . '" height="' . $basic_slide_options['slide_height'] . '"]' . $video_url . '[/embed]');
          print $post_embed;
  			}
  			else{ ?>
          <a class="image" href="<?php print $slide_url; ?>" >
          <?php the_post_thumbnail('basic_slideshow_type'); ?>
          </a>
          	
            <?php // HERE we print the transparent overlay and text ?>
            <div class="meta-back">&nbsp;</div>
            <div class="meta">
              <h3><a href="<?php print $slide_url; ?>" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
              
              <?php the_excerpt(); //Here's the body of the content type gets printed ?>
            </div>

            
          <?php
        }
  			
  	?>
  		<div style="clear: both"></div>
  	</div>
  
  	<?php endwhile; ?>
	<?php print $captions;?>
  </div>
  
  <?php
}
  //Reset all the changes we've made to wp_query so that any loops below this will work properly
	wp_reset_query();
  remove_filter('excerpt_more', 'new_excerpt_more');
	remove_filter('excerpt_length','basic_slideshow_custom_excerpt');
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

