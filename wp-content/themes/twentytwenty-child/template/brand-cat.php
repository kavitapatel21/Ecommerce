<?php
/*
Template Name: brand-cat
Template Post Type: post, page, my-post-type;
*/
get_header();
?>

<div  style="padding-top: 100px;" >
<?php
$terms = get_terms( 'category' ); 
$term_ids = wp_list_pluck( $terms, 'term_id' );
$args = 
array(
    'post_type' => 'custom_products',
    'tax_query' => array(
        array(
            'taxonomy' => 'category',
            'field' => 'term_id',
            'terms' => $_GET ? $_GET['category'] : $term_ids,
        ),
        'relation' => 'AND',
    ),
);

?>
<form  method="POST" id="form">

<div class="container-fluid" style="padding-top: 30px;" >
  <div class="row">
	  <div class="col-xl-3 col-lg-3 col-md-12">
        <?php  
              if( $brands = get_terms( array( 'taxonomy' => 'category' ) ) ) :
                    echo '<ul class="brands-list">';
                foreach( $brands as $brand ) :
                    echo '<input type="checkbox" class="chkbox" id="brand_' . $brand->term_id . '" value="'. $brand->term_id .'" name="brand_' . $brand->term_id . '" />
                    <label for="brand_' . $brand->term_id . '">' . $brand->name . '</label>';
                    if ($brand !== end($brands)) { echo '<li class="list-spacer"></li>'; }
                endforeach;
                    echo '</ul>';
            endif;
        ?>
    
    <input class="btn btn-dark" type="button" id="filter" value="Filter">
        <input type="hidden" name="action" value="myfilter">
        
        </form>
        </div>

        <div class="row col-xl-9 col-lg-9 col-md-12" id="response">
            <div class="row">
        <?php 
         $args = array(  
            'post_type' => 'custom_products',
            'post_status' => 'publish',
            'posts_per_page' => -1, 
            'orderby' => 'title', 
            'order' => 'ASC',
        );
    
        $loop = new WP_Query( $args ); 
        while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <div class="col-md-3">
  <?php $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'thumbnail' ); ?>  
  <img src="<?php echo $url ?>" width="200" height="200" style="padding-top: 20px;" alt=""/>
  <h2 style="padding-top: 5px;text-align:left;"><?php the_title(); ?></h2>
  <h4 style="padding-top: 5px;text-align:left;"><?php the_content(); ?></h4>
  <button type="button" class="btn btn-dark" style="align-items:center">Add to cart</button>
  </div> 
        <?php endwhile; ?>
    </div>
    </div>
        </div>
</div>
    
<?php
wp_reset_postdata();
get_footer();
?>


