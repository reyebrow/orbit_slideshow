<?php

/**
 * Make a simple widget that prints the slideshow
 */
class RE_Slideshow extends WP_Widget {
    /** constructor */
    function RE_Slideshow() {
        parent::WP_Widget(false, $name = 'RE_Slideshow');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget; 
                         if ( $title ){
                          echo $before_title . $title . $after_title; 
                        }
                        if (function_exists('re_slideshow')) {
                          re_slideshow();

                        }
                        echo $after_widget; 
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        	$instance = $old_instance;
        	$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
    }

} // class FooWidget

// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("RE_Slideshow");'));

?>
