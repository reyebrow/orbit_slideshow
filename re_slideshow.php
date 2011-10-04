<?php
/*
Plugin Name: Raised Eyebrow Slideshow Plugin
Description: slides!!!!
Version: 0.1
License: GPL
Author: Matt Reimer
Author URI: http://www.raisedeyebrow.com/
*/

$re_slide_options = get_option('re_slideshow_options');


//Make sure we've got the right kind of images set up.
add_theme_support('post-thumbnails');
add_image_size('re_slideshow', $re_slide_options[slide_width],$re_slide_options[slide_height],true);
add_shortcode('re_slideshow', 're_slideshow');



//Add all the JS we need to make this go.
function re_add_scripts() {

  wp_enqueue_script('jquery');
  
   wp_register_script('re_jquery_cycle',
         plugins_url('js/jquery.cycle.all.min.js', __FILE__),
         array('jquery'),
         '1.0' );
   wp_register_script('re_slideshow_script',
         plugins_url('js/re_slideshow.js', __FILE__),
         array('re_jquery_cycle'),
         '1.0' );
  
    wp_enqueue_script('re_jquery_cycle');
    wp_enqueue_script('re_slideshow_script');
    wp_localize_script( 're_slideshow_script', 'slideshow_settings', get_option('re_slideshow_options') );
  
  wp_enqueue_style('re_slideshow_style', plugins_url('slideshow.css', __FILE__));

}
add_action('wp_enqueue_scripts', 're_add_scripts');
add_action( 'init', 're_create_slideshow_posttype' );

/********************************************
// Create the Custom 'Slideshow' Post Type
********************************************/

function re_create_slideshow_posttype() {
  
  $labels = array(
      'name' => _x('Slideshow', 'post type general name'),
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
      'label' => __('Slideshow'),
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
      'show_in_nav_menus' => false,
      'taxonomies' => array()
  );
    
  register_post_type( 're_slideshow', $args);

}


function re_slideshow_custom_excerpt(){
  $re_slide_options = get_option('re_slideshow_options');
  return $re_slide_options[teaser_length];
  
}

/********************************************
// Do the actual slideshow
********************************************/

function re_slideshow() {
	global $wp_Query;
  global $post;
  global $wp_embed;
  $query_vars= $wp_query->query_vars;
  $query_vars['post_type'] = 're_slideshow';
  $re_slide_options = get_option('re_slideshow_options');
  add_filter('excerpt_length', 're_slideshow_custom_excerpt', 10);
  add_filter('excerpt_more', 'new_excerpt_more');
  query_posts($query_vars);

  
?>

<div id="re_slideshow">
  <div class="list">
	<?php while (have_posts()) : the_post(); ?>
	<?php
		      $slide_meta = get_post_meta($post->ID, 'slide_meta', true);	
				$video_url = !empty($slide_meta['video_url']) ? $slide_meta['video_url'] : "";
        $slide_url = !empty($slide_meta['slide_url']) ? $slide_meta['slide_url'] : get_permalink();
         $isVideo =  (!empty($video_url) && $video_url != "")? true : false;
	?>
	
	<div class="item <?php print $isVideo ?"video-slide": "";?>">
	<?php

	     
			if ( $isVideo ){
        $post_embed = $wp_embed->run_shortcode('[embed width="' . $re_slide_options['slide_width'] . '" height="' . $re_slide_options['slide_height'] . '"]' . $video_url . '[/embed]');
        print $post_embed;
			}
			else{ ?>
        <a class="image" href="<?php print $slide_url; ?>" >
        <?php the_post_thumbnail('re_slideshow'); ?>
        </a>
        	

          <div class="meta">
            <h3><a href="<?php print $slide_url; ?>" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
            
            <?php the_excerpt(); ?>
          </div>
          <div class="meta-back">&nbsp;</div>
          
        <?php
      }
			
	?>
		<div style="clear: both"></div>
	</div>

	<?php endwhile; ?>
	</div>
</div>

<?php
	wp_reset_query();
  remove_filter('excerpt_more', 'new_excerpt_more');
	remove_filter('excerpt_length','re_slideshow_custom_excerpt');
}


function new_excerpt_more($more) {
       global $post;
      $slide_meta = get_post_meta($post->ID, 'slide_meta', true);	
      $slide_url = !empty($slide_meta['slide_url']) ? $slide_meta['slide_url'] : get_permalink();
      return '<a href="'. $slide_url . '">Read More ..</a>';
}



//With mad props to mehigh http://mehigh.biz/wordpress/adding-wmode-transparent-to-wordpress-3-media-embeds.html
function add_video_wmode_transparent($html, $url, $attr) {

//add ENABLEjsAPI to youtube videos

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


include_once('re_slideshow_admin.php');
include_once('re_slideshow_widget.php');

