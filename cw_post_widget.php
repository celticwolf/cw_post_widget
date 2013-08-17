<?php
/*
Plugin Name: CW Post Widget
Plugin URI: http://www.celticwolf.com/
Description: Displays a random, specific, or the most recent post.
Author: Celtic Wolf, Inc.
Version: 0.1.0
Author URI: http://www.celticwolf.com/
*/

// get flickr images
class CWPostWidget extends WP_Widget
{

  function CWPostWidget()
  {
    $widget_ops = array('classname' => 'cw_post_widget', 'description' => __('Displays a random, specific, or the most recent post.', 'cw_post_widget'));
    $this->WP_Widget('CWPostWidget', __('CW Post Widget', 'cw_post_widget'), $widget_ops);
  }

  function widget($args, $instance)
  {
    extract($args);

    $title = apply_filters('widget_title', empty($instance['title']) ? __('Quotes', 'cw_post_widget') : $instance['title'], $instance, $this->id_base);

    $display = $instance['display'];
    $post_page_id = intval(strip_tags($instance['post_page_id']));
    $category = intval(strip_tags($instance['category']));
    $header_link = strip_tags($instance['header_link']);

    $query_args = array();

    $query_args['post_type'] = 'post';
    $query_args['post_status'] = 'publish';
    $query_args['posts_per_page'] = 1;
    if ($display == 'random')
      $query_args['orderby'] = 'rand';
    else
      $query_args['p'] = $post_page_id;
    if ($category && 0 === $post_page_id)
      $query_args['cat'] = $category;

    $tbQuery = new WP_Query($query_args);

    if ($tbQuery->have_posts())
    {
      if (!$title)
        $title = 'Quotes';
      if (strlen($header_link))
        $title = "<a href=\"$header_link\">$title</a>";
      echo $before_widget;
      echo $before_title . $title . $after_title;

      while ($tbQuery->have_posts())
      {
        $tbQuery->the_post();

        // Respect the "more" tag on all pages
        global $more;
        $more = 0;
        the_content();
      }
      echo $after_widget;
    }

    wp_reset_postdata();
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['display'] = $new_instance['display'];
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['post_page_id'] = intval(strip_tags($new_instance['post_page_id']));
    $instance['category'] = intval(strip_tags($new_instance['category']));
    $instance['header_link'] = strip_tags($new_instance['header_link']);

    return $instance;
  }

  function form($instance)
  {
    $instance = wp_parse_args((array) $instance, array('title'=>'Quotes', 'display' => 'random', 'post_page_id' => 0, 'category' => 0, 'header_link' => ''));
    $display = $instance['display'];
    $post_page_id = intval(strip_tags($instance['post_page_id']));
    $title =  strip_tags($instance['title']);
    $category = intval(strip_tags($instance['category']));
    $header_link = strip_tags($instance['header_link']);
  ?>

  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'cw_post_widget'); ?></label>
    <input class="cw-text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </p>
  <p>
    <input type="radio" class="cw-radio" id="random_<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>"<?php checked($display, 'random'); ?> value="random" />
    <label for="random_<?php echo $this->get_field_id('display'); ?>"><?php _e('Random order', 'cw_post_widget'); ?></label>
    <input type="radio" class="cw-radio" id="post_page_id_<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>"<?php checked($display, 'post_page_id'); ?> value="post_page_id" />
    <label for="post_id_<?php echo $this->get_field_id('display'); ?>"><?php _e('Post or page id', 'cw_post_widget'); ?></label>
    <input class="cw-text" id="<?php echo $this->get_field_id('post_page_id'); ?>" name="<?php echo $this->get_field_name('post_page_id'); ?>" type="text" value="<?php echo esc_attr($post_page_id); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category:', 'cw_post_widget'); ?></label>
    <input class="cw-text" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo esc_attr($category); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('header_link'); ?>"><?php _e('Header Link:', 'cw_post_widget'); ?></label>
    <input class="cw-text" id="<?php echo $this->get_field_id('header_link'); ?>" name="<?php echo $this->get_field_name('header_link'); ?>" type="text" value="<?php echo esc_attr($header_link); ?>" />
  </p>

  <?php
  }
}

function cw_register_post_widget()
{
  register_widget('CWPostWidget');
  do_action('widgets_init');
}

add_action('init', 'cw_register_post_widget', 1);

?>
