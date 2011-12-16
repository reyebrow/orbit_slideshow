<?php

/**
 * Make a simple widget that prints the slideshow
 */
class ORBIT_Slideshow extends WP_Widget {
    /** constructor */
    function ORBIT_Slideshow() {
        parent::WP_Widget(false, $name = 'Orbit Slideshow');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);

        $slideshow_params = Array();
        
        
        if (isset($instance['slideshow']) && $instance['slideshow'] != "" && $instance['slideshow'] != "default"){
          $term = get_term( $instance['slideshow'], 'orbit_slideshows' );
          $slideshow_params['slideshow'] = $term->name;
        }

        //Check if tabshow is formatted correctly. Then print it if it exists
        $op = array("left", "right", "top", "bottom");
        
        if (in_array($instance['tabshow'], $op) ) {
          $slideshow_params['tabshow'] = $instance['tabshow'];
        }    
        
          echo $before_widget; 
           if ( $title ){
            echo $before_title . $title . $after_title; 
          }
          if (function_exists('orbit_slideshow')) {
            orbit_slideshow($slideshow_params);
          }
          echo $after_widget; 
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        	$instance = $old_instance;
        	$instance['title'] = strip_tags($new_instance['title']);
        	$instance['slideshow'] = strip_tags($new_instance['slideshow']);
        	$instance['tabshow'] = strip_tags($new_instance['tabshow']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $slideshow_choice = isset( $instance['slideshow'] ) ? $instance['slideshow'] : '';
        $tab_ops = array("left", "right", "top", "bottom");
        $tabshow = isset( $instance['tabshow']) ? $instance['tabshow'] : '';

        $slideshows = get_terms( 'orbit_slideshows', array( 'hide_empty' => false ) );
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('slideshow'); ?>"><?php _e('Select Slideshow:'); ?></label>
          <select id="<?php echo $this->get_field_id('slideshow'); ?>" name="<?php echo $this->get_field_name('slideshow'); ?>">
          <?php
            echo '<option'. $selected .' value="default">--Default--</option>';
            foreach ( $slideshows as $slideshow ) {
              $selected = $slideshow_choice == $slideshow->term_id ? ' selected="selected"' : '';
              echo '<option'. $selected .' value="'. $slideshow->term_id .'">'. $slideshow->name .'</option>';
            }
          ?>
          </select>
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('tabshow'); ?>"><?php _e('Show as tabs?:'); ?></label>
          <select id="<?php echo $this->get_field_id('tabshow'); ?>" name="<?php echo $this->get_field_name('tabshow'); ?>">
          <?php
            echo '<option'. $selected .' value="no">--NO--</option>';
            foreach ( $tab_ops as $tabshow_op ) {
              $selected = $tabshow == $tabshow_op ? ' selected="selected"' : '';
              echo '<option'. $selected .' value="'. $tabshow_op .'">'. $tabshow_op .'</option>';
            }
          ?>
          </select>        
          </p>        
        
        <?php 
    }

} // class FooWidget

// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("orbit_Slideshow");'));

?>
