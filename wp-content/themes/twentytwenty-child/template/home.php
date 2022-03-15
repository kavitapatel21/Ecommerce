<?php
/*
Template Name: home
Template Post Type: post, page, my-post-type;
*/
get_header();
?>



<!-- Page Content -->
    <!-- Banner Starts Here -->
    <div class="banner header-text">
      <div class="owl-banner owl-carousel">
      <?php
          $args = array( 'post_type' => 'dynamic-slider',
          'post_status' => 'publish',
          'posts_per_page' => -1,
          'order'    => 'ASC'); 
          $loop = new WP_Query( $args );
          while ( $loop->have_posts() ) : $loop->the_post(); 
      ?>
      <?php $thumb = get_the_post_thumbnail_url(); ?>
        <div class="banner-item-01" style="background-image: url('<?php echo $thumb;?>')">
          <div class="text-content">
            <h4><?php the_title(); ?></h4>
            <h2><?php the_content(); ?></h2>
          </div>
        </div>
        <?php
          endwhile;
        ?>
      </div>
    </div>
    <!-- Banner Ends Here -->
   <!--<?php echo do_shortcode('[contact-form-7 id="364" title="Contact form 1"]'); ?>-->
   
   <!--products searchbar-->
        <div style="margin-top: 30px; margin-bottom:30px;">
            <?php echo do_shortcode('[fibosearch]'); ?> 
        </div>  

  
    <div class="latest-products" id="latest-products">
      	<div class="container">
        	<div class="row">
         		<div class="col-md-12">
            		<div class="section-heading">
              			<h2>Latest Products</h2>
              			<a href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ) . "?showall=1"; ?>">view all products <i class="fa fa-angle-right"></i></a>
            		</div>
            	</div>
			  		<?php echo do_shortcode('[products limit="8" columns="4"]'); ?>
        	</div>
        </div>
    </div>

    <div class="best-features">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
              <h2>About Sixteen Clothing</h2>
            </div>
          </div>
          <div class="col-md-6">
            <div class="left-content">
              <h4>Looking for the best products?</h4>
              <p>
              Sixteen clothing is a one stop shop for all your fashion and lifestyle needs. Being India's largest e-commerce store for fashion and lifestyle products, Sixteen Clothing aims at providing a hassle free and enjoyable shopping experience to shoppers across the country with the widest range of brands and products on this website.
              </p>
             <!-- <ul class="featured-list">
                <li><a href="#">Lorem ipsum dolor sit amet</a></li>
                <li><a href="#">Consectetur an adipisicing elit</a></li>
                <li><a href="#">It aquecorporis nulla aspernatur</a></li>
                <li><a href="#">Corporis, omnis doloremque</a></li>
                <li><a href="#">Non cum id reprehenderit</a></li>
              </ul>-->
              <?php
              //$post   = get_post( 102 );
              //$output =  apply_filters( 'the_content', $post->post_content );
              ?>
              <a href="<?php echo get_permalink(102);?>" class="filled-button">Read More</a>
            </div>
          </div>
          <div class="col-md-6">
            <div class="right-image">
              <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/feature-image.jpg" alt="">
            </div>
          </div>
        </div>
      </div>
    </div>
 
<!--<?php 
 $page_id = woocommerce_get_page_id('edit-address');
 echo $page_id;
?>
-->
<?php
get_footer();
?>