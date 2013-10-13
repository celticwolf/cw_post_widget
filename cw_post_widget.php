<?php
/*
Plugin Name: CW Post Widget
Plugin URI: http://www.celticwolf.com/
Description: Displays a random, specific, or the most recent post.
Author: Celtic Wolf, Inc.
Version: 1.0
Author URI: http://www.celticwolf.com/
*/

require('cw_display_functions.php');

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
    $specific_id = intval($instance['specific_id']);
    $category = intval($instance['category']);
    $title_link = strip_tags($instance['title_link']);

    $query_args = array();

    $query_args['post_type'] = array('post', 'page');
    $query_args['post_status'] = 'publish';
    $query_args['posts_per_page'] = 1;
    if ($display == 'random')
      $query_args['orderby'] = 'rand';
    elseif ($display == 'specific_id')
      $query_args['p'] = $specific_id;
    if ($category > -1 && ($display == 'random' || $display == 'newest'))
      $query_args['cat'] = $category;

    $query = new WP_Query($query_args);

    if ($query->have_posts())
    {
      if (!$title)
        $title = 'Quotes';
      if (strlen($title_link))
        $title = "<a href=\"$title_link\">$title</a>";
      echo $before_widget;
      echo $before_title . $title . $after_title;

      while ($query->have_posts())
      {
        $query->the_post();

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
    $instance['specific_id'] = intval(strip_tags($new_instance['specific_id']));
    $instance['category'] = intval(strip_tags($new_instance['category']));
    $instance['title_link'] = strip_tags($new_instance['title_link']);

    return $instance;
  }

  function form($instance)
  {
    $instance = wp_parse_args((array) $instance, array('title'=>'Quotes', 'display' => 'newest', 'specific_id' => 0, 'category' => 0, 'title_link' => ''));
    $display = $instance['display'];
    $specific_id = intval(strip_tags($instance['specific_id']));
    $title =  strip_tags($instance['title']);
    $category = intval($instance['category']);
    $title_link = strip_tags($instance['title_link']);

    $display_id = $this->get_field_id('display');
    $display_name = $this->get_field_name('display');
    $specific_id_id = $this->get_field_id('specific_id');
    $specific_name = $this->get_field_name('specific_id');
    $post_page_select = CWDisplayFunctions::SelectForPosts(
      $specific_id_id,
      $specific_name,
      'width: 100%;',
      $specific_id,
      array(
        'post_type' => array('post', 'page'),
        'post_status' => array('publish', 'private'),
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'title'
      )
    );

    $categories_select = wp_dropdown_categories(
      array(
        'show_option_none' => __('no category', 'cw_post_widget'),
        'orderby' => 'name',
        'id' => $this->get_field_id('category'),
        'name' => $this->get_field_name('category'),
        'selected' => $category,
        'class' => 'cw-select',
        'echo' => false,
        'hierarchical' => true
      )
    );
  ?>
  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'cw_post_widget'); ?></label>
    <input class="cw-text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('title_link'); ?>"><?php _e('Title Link:', 'cw_post_widget'); ?></label>
    <input class="cw-text" id="<?php echo $this->get_field_id('title_link'); ?>" name="<?php echo $this->get_field_name('title_link'); ?>" type="text" value="<?php echo esc_attr($title_link); ?>" />
  </p>
  <p>
    <input type="radio" class="cw-radio" id="cw_newest_<?php echo $display_id; ?>" name="<?php echo $display_name; ?>"<?php checked($display, 'newest'); ?> value="newest" />
    <label for="<?php echo $display_id; ?>"><?php _e('Newest', 'cw_post_widget'); ?></label>
    <input type="radio" class="cw-radio" id="cw_random_<?php echo $display_id; ?>" name="<?php echo $display_name; ?>"<?php checked($display, 'random'); ?> value="random" />
    <label for="<?php echo $display_id; ?>"><?php _e('Random', 'cw_post_widget'); ?></label><br />
    <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category:', 'cw_post_widget'); ?></label>
    <?php echo $categories_select; ?><br />
  </p>
  <p>
    <input type="radio" class="cw-radio" id="cw_specific_id_<?php echo $display_id; ?>" name="<?php echo $display_name; ?>"<?php checked($display, 'specific_id'); ?> value="specific_id" />
    <label for="<?php echo $display_id; ?>"><?php _e('Specific post or page', 'cw_post_widget'); ?></label>
    <?php echo $post_page_select; ?>
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
