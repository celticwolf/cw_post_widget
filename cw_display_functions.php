<?php

class CWDisplayFunctions
{
  public static function SelectForPosts($id, $name, $style, $selected_id = -1, $args = array())
  {
    $ret = '';

    $query = new WP_Query($args);
    while ($query->have_posts())
    {
      $query->the_post();
      $selected = ($query->post->ID == $selected_id) ? 'selected="selected"' : '';
      $option = "<option value=\"{$query->post->ID}\"$selected>{$query->post->post_title}</option>";
      $ret .= "$option\n";
    }

    wp_reset_postdata();

    return "<select id=\"$id\" name=\"$name\" style=\"$style\">\n$ret</select>";
  }
}

?>
