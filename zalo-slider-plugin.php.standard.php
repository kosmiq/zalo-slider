<?php
/**
 * Plugin Name: Zalo Cases Slider
 * Plugin URI:
 * Description: Plugin for creating the cases slider
 * Version: 0.1
 * Author: Tor Raswill
 * Author URI: http://tor.raswill.se
 * License:
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

//* Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'add_slider_scripts' );
function add_slider_scripts() {

  $pluginversion = '0.2';
  if ( is_home() ) {
    wp_enqueue_style( 'zalo-slider-slick', plugins_url( 'slick.css', __FILE__ ), array(), $pluginversion, false );
    wp_enqueue_style( 'zalo-slider', plugins_url( 'zalo-slider.css', __FILE__ ), array(), $pluginversion, false );

    wp_enqueue_script( 'zalo-slider-slick', plugins_url( 'slick.min.js', __FILE__ ), array( 'jquery' ), $pluginversion, true );
    //wp_enqueue_script( 'zalo-imagesloaded', plugins_url( 'imagesloaded.js', __FILE__ ), array( 'jquery' ), $pluginversion, false );
    wp_enqueue_script( 'zalo-slider-settings', plugins_url( 'zalo-slider-functions.js', __FILE__ ), array( 'jquery', 'zalo-slider-slick', 'zalo-imagesloaded', 'BJLL' ), $pluginversion, true );
  }
}

add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'zalo_slider',
    array(
      'supports' => array( 'title', 'editor', 'comments', 'excerpt', 'custom-fields', 'thumbnail' ),
      'labels' => array(
        'name' => __( 'Cases' ),
        'singular_name' => __( 'Case' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'cases'),
      'menu_icon' => plugins_url( 'images/icon.png', __FILE__ ),
    )
  );
}


class ZaloSliderWidget extends WP_Widget
{
  function ZaloSliderWidget()
  {
    $widget_ops = array('classname' => 'zalosliderwidget featuredpage', 'description' => 'Displays the Zalo case slider' );
    $this->WP_Widget('ZaloSliderWidget', 'Zalo case slider widget', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
    echo '<p><label for="' . $this->get_field_id('title') . '">Title: <input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . attribute_escape($title) . '" /></label></p>';
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);

    echo $before_widget;
    //$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $title = empty($instance['title']) ? ' ' : $instance['title'];

    echo '<div id="zalo-cases" class="zalo-section-target"></div>';

    if (!empty($title))
      //echo $before_title . $title . $after_title;
      echo '<header class="entry-header"><h2 class="entry-title">' . $title . '</h2></header>';

    $output = '';
    //$carousel_query = new WP_Query('post_type=zalo_slider&showposts=10&orderby=\'ID\'&order=\'ASC\'');
    $carousel_query = new WP_Query( array ( 'post_type' => 'zalo_slider', 'orderby' => 'ID', 'order' => 'ASC' ) );
    if ($carousel_query->have_posts()) :
      $output .= '<div id="slider" class="zalo-cases-slider"><div class="slickslider">';

      while ($carousel_query->have_posts()) : $carousel_query->the_post();
        $output .= '<div class="slide">';
        $content = apply_filters( 'the_content', get_the_content() );
        $content = str_replace( ']]>', ']]&gt;', $content );

        $excerpt = get_the_excerpt();

        $placeholder = plugins_url( 'images/placeholder.png', __FILE__ );

        $output .= '<div class="slider-image">';
        if(has_post_thumbnail()) {

          //$output .= get_the_post_thumbnail($post->ID,'featured_image', array( 'class' => 'lazy-ignore' ));
          $output .= get_the_post_thumbnail($post->ID,'featured_image');
        } else {
          //$output .= '<img width="1500" height="700" src="' . $placeholder . '" class="lazy-ignore attachment-featured_image wp-post-image" alt="" />';
          //$output .= '<img width="1500" height="700" src="' . $placeholder . '" class="attachment-featured_image wp-post-image" alt="" />';
          $img_html = '<img width="1500" height="700" src="' . $placeholder . '" class="attachment-featured_image wp-post-image" alt="" />';
          $img_html = apply_filters( 'bj_lazy_load_html', $img_html );
          $output .= $img_html;
        }
        //$output .= '<header class="title one">' . get_the_title() . '</header>';
        $output .= '</div>';
        //$output .= '<div class="slider-excerpt title two">' . $excerpt . '</div>';
        $output .= '<div class="slider-content title two">';
        $output .= '<header class="title one">' . get_the_title() . '</header>';
        $output .= $content;
        $output .= '</div>';
        $output .= '</div>';
      endwhile;

      $output .= '</div></div>';
    endif;
    wp_reset_postdata();

    echo $output;

    echo $after_widget;
  }

}
add_action( 'widgets_init', create_function('', 'return register_widget("ZaloSliderWidget");') );

function slider_func() {
  $output = '';
  //$carousel_query = new WP_Query('post_type=zalo_slider&showposts=10&orderby=\'ID\'&order=\'ASC\'');
  $carousel_query = new WP_Query( array ( 'post_type' => 'zalo_slider', 'orderby' => 'ID', 'order' => 'ASC' ) );
  if ($carousel_query->have_posts()) :
    $output .= '<div id="zalo-cases">';
    $output .= '<div id="slider" class="zalo-cases-slider"><div class="slickslider">';

    while ($carousel_query->have_posts()) : $carousel_query->the_post();
      $output .= '<div class="slide">';
      $content = apply_filters( 'the_content', get_the_content() );
      $content = str_replace( ']]>', ']]&gt;', $content );

      $excerpt = get_the_excerpt();

      $placeholder = plugins_url( 'images/placeholder.png', __FILE__ );

      $output .= '<div class="slider-image">';
      if(has_post_thumbnail()) {

        $output .= get_the_post_thumbnail($post->ID,'featured_image');
      } else {

        $img_html = '<img width="1500" height="700" src="' . $placeholder . '" class="attachment-featured_image wp-post-image" alt="" />';
        $img_html = apply_filters( 'bj_lazy_load_html', $img_html );
        $output .= $img_html;

        //$output .= '<img width="1500" height="700" src="' . $placeholder . '" class="attachment-featured_image wp-post-image" alt="" />';
      }
      //$output .= '<header class="title one">' . get_the_title() . '</header>';
      $output .= '</div>';
      $output .= '<header class="title one">' . get_the_title() . '</header>';
      //$output .= '<div class="slider-excerpt title two">' . $excerpt . '</div>';
      $output .= '<div class="slider-content title two">' . $content . '</div>';
      $output .= '</div>';
    endwhile;

    $output .= '</div></div></div>';
  endif;
  wp_reset_postdata();

  return $output;

}
add_shortcode("slider", "slider_func");


/* Stop Adding Functions Below this Line */
?>
