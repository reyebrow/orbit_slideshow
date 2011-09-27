<?php
/*
Plugin Name: Raised Eyebrow Slideshow Plugin
Description: slides!!!!
Version: 0.1
License: GPL
Author: Matt Reimer
Author URI: http://www.raisedeyebrow.com/
*/

function re_add_scripts() {


wp_enqueue_script('jquery');

 wp_register_script('re_jquery_cycle',
       plugins_url('jquery.cycle.all.min.js', __FILE__),
       array('jquery'),
       '1.0' );
 wp_register_script('re_slideshow_script',
       plugins_url('re_slideshow.js', __FILE__),
       array('re_jquery_cycle'),
       '1.0' );

  wp_enqueue_script('re_jquery_cycle');
  wp_enqueue_script('re_slideshow_script');

wp_enqueue_style('re_slideshow_style', plugins_url('slideshow.css', __FILE__));

  
}
add_action('wp_enqueue_scripts', 're_add_scripts');

add_theme_support('post-thumbnails');
add_image_size('re_slideshow',550,330,true);

/*
  

<div id="slideshow-wrapper">
<?php slideshow_featured_posts(); ?>
</div>

*/


add_action( 'init', 're_create_slideshow_posttype' );

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
      'menu_icon' => get_bloginfo('stylesheet_directory').'/images/slide.png',
      'hierarchical' => false,
      'rewrite' => array( "slug" => "slide" ),
      'supports'=> array('title', 'body', 'thumbnail', 'editor') ,
      'show_in_nav_menus' => false,
      'taxonomies' => array()
  );
    
  register_post_type( 're_slideshow', $args);

}


/**
 * Sets the length of the content in a slideshow.
 */
function re_slideshow_excerpt_featured_length($length) {
	return 50;
}


function re_slideshow_featured_posts() {
	global $wp_Query;
  $query_vars= $wp_query->query_vars;
  $query_vars['post_type'] = 're_slideshow';
	add_filter('excerpt_length','re_slideshow_excerpt_featured_length');
  query_posts($query_vars);
  
  
?>

<div id="re_slideshow">
  <div class="list">
	<?php while (have_posts()) : the_post(); ?>
	<div class="item">
		<a class="image" href="<?php the_permalink(); ?>" title="Permanent Link to <?php the_title_attribute(); ?>">
		<?php the_post_thumbnail('re_slideshow'); ?>
		</a>
		<div class="meta">
			<h3><a href="<?php the_permalink(); ?>" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
			<?php the_excerpt(); ?>
		</div>
			<div class="meta-back">&nbsp;</div>
		<div style="clear: both"></div>
	</div>

	<?php endwhile; ?>
	</div  >
</div>
 
<?php
	wp_reset_query();
	remove_filter('excerpt_length','re_slideshow_excerpt_featured_length');
}






