<?php
/*
 
Plugin Name: Thumbnail-slider
 
Description: Used by millions, Thumbnail-slider is quite possibly the best way in the world to <strong>protect your blog from spam</strong>. It keeps your site protected even while you sleep. 

Author: Automattic
 
*/
?>
<style>
    body{
  background:#ccc;
}
.main {
  font-family:Arial;
  width:500px;
  display:block;
  margin:0 auto;
}
img{
    background: #fff;
    width:auto;
    margin: 10px;
    padding: 2%;
    position: relative;
    text-align: center;
}
</style>
<?php
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
    $result = '<div class="main">';
    $result .= '<div class="slider slider-for">';
   
    //the loop
    $loop = new WP_Query($args);
    while ($loop->have_posts()) {
        $loop->the_post();
   
        $the_url = wp_get_attachment_image_src(get_post_thumbnail_id(), $type);
        $result .='<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/>';
    }
    $result .='</div>';
    $result .= '<div class="slider slider-nav">';

    while ($loop->have_posts()) {
        $loop->the_post();
        $the_url = wp_get_attachment_image_src(get_post_thumbnail_id(), $type);
        $result .='<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt=""/>';
       $result .= '</div>';
    }
    $result .='</div>';
    return $result;
}
add_image_size('np_function', 1600, 800, true);
add_theme_support( 'post-thumbnails' );
?>
