<?php
/*/*
Template Name: about us
Template Post Type: post, page, my-post-type;
*/
get_header();
?>
<!-- Page Content -->
<div class="page-heading about-heading header-text" id="aboutus">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>about us</h4>
              <h2>our company</h2>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="best-features about-features">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
              <h2>Our Background</h2>
            </div>
          </div>
          <div class="col-md-6">
            <div class="right-image">
              <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/feature-image.jpg" alt="">
            </div>
          </div>
          <div class="col-md-6">
            <div class="left-content">
              <h4>Who we are &amp; What we do?</h4>
              <p>Sixteen clothing is one of the Top E-commerce company. It is a leading online fashion store offering products for men and women both in all ranges. The company is headquartered in Bengaluru, Karnataka. It was launched in the year 2009. It has gained success in a short span of time. The company was founded in 2007 to sell personalized gift items.</p>
              <ul class="social-icons">
                <li><a href="https://www.facebook.com "><i class="fa fa-facebook"></i></a></li>
                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="team-members">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
              <h2>Our Team Members</h2>
            </div>
          </div>
          <?php
          $args = array(
'post_type'=> 'post',
'orderby'    => 'ID',
'post_status' => 'publish',
'order'    => 'ASC',
'posts_per_page' => -1 // this will retrive all the post that is published 
);
$result = new WP_Query( $args );
if ( $result-> have_posts() ) : ?>
<?php while ( $result->have_posts() ) : $result->the_post(); ?>
          <div class="col-md-4">
            <div class="team-member">
              <div class="thumb-container">
                <img src="<?php  the_post_thumbnail(); ?>
                <div class="hover-effect">
                  <div class="hover-content">
                    <ul class="social-icons">
                      <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                      <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                      <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                      <li><a href="#"><i class="fa fa-behance"></i></a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="down-content">
                <h4><?php the_title(); ?> </h4>
                <p><?php the_content(); ?> </p>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
<?php endif; wp_reset_postdata(); ?>
        </div>
      </div>
    </div>

<!--
    <div class="services">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <div class="service-item">
              <div class="icon">
                <i class="fa fa-gear"></i>
              </div>
              <div class="down-content">
                <h4>Product Management</h4>
                <p>Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat.</p>
                <a href="#" class="filled-button">Read More</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="service-item">
              <div class="icon">
                <i class="fa fa-gear"></i>
              </div>
              <div class="down-content">
                <h4>Customer Relations</h4>
                <p>Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat.</p>
                <a href="#" class="filled-button">Details</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="service-item">
              <div class="icon">
                <i class="fa fa-gear"></i>
              </div>
              <div class="down-content">
                <h4>Global Collection</h4>
                <p>Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat.</p>
                <a href="#" class="filled-button">Read More</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="happy-clients">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
              <h2>Happy Partners</h2>
            </div>
          </div>
          <div class="col-md-12">
            <div class="owl-clients owl-carousel">
              <div class="client-item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/client-01.png" alt="1">
              </div>
              
              <div class="client-item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/client-01.png" alt="2">
              </div>
              
              <div class="client-item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/client-01.png" alt="3">
              </div>
              
              <div class="client-item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/client-01.png" alt="4">
              </div>
              
              <div class="client-item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/client-01.png" alt="5">
              </div>
              
              <div class="client-item">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/client-01.png" alt="6">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>-->

<?php
get_footer();
?>