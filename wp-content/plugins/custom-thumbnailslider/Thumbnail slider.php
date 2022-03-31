<?php
/*
 
Plugin Name: Thumbnail-slider 
Description: Used by millions, Thumbnail-slider is quite possibly the best way in the world to <strong>protect your blog from spam</strong>. It keeps your site protected even while you sleep. 
Author: Automattic
 
*/

function np_init() {
    $args = array(
        'public' => true,
        'label' => 'Thumbnail-slider',
        'supports' => array(
            'title',
            'thumbnail'
        )
    );
    register_post_type('np_images', $args);
    add_shortcode('np-shortcode', 'np_function');
    
}
add_action('init', 'np_init');

function np_function($type='np_function') {
    $args = array(
        'post_type' => 'np_images',
        'posts_per_page' => 3
    );
    $result = '<div id="sync1" class="owl-carousel owl-theme">';
    
   
    //the loop
    $loop = new WP_Query($args);
    while ($loop->have_posts()) {
        $loop->the_post();
        $result .='<div>';
        $the_url = wp_get_attachment_image_src(get_post_thumbnail_id(), $type);
        $result .='<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/>';
        $result .='</div>';
    }
    //$result .='</div>';
    $result .='</div>';
    $result .= '<div id="sync2" class="owl-carousel owl-theme">';
    //$result .= '<div class="iteol">';

    while ($loop->have_posts()) {
        $loop->the_post();
        $result .='<div>';
        $the_url = wp_get_attachment_image_src(get_post_thumbnail_id(), $type);
        $result .='<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/>';
        $result .='</div>';
      
    }
    //$result .= '</div>';
    $result .='</div>';
    
    return $result;
}
add_image_size('np_function', 1600, 800, true);
add_theme_support( 'post-thumbnails' );
?>
