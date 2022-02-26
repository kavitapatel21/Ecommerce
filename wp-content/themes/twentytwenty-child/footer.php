<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

?>
			<footer>
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="inner-content">
            <p><?php dynamic_sidebar('Footer #1');?></p>
            </div>
          </div>
        </div>
      </div>
    </footer>


    <!-- Bootstrap core JavaScript -->
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- Additional Scripts -->
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/custom.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/owl.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/slick.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/isotope.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/accordions.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


    <script language = "text/Javascript"> 
      cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
      function clearField(t){                   //declaring the array outside of the
      if(! cleared[t.id]){                      // function makes it static and global
          cleared[t.id] = 1;  // you could use true and false, but that's more typing
          t.value='';         // with more chance of typos
          t.style.color='#fff';
          }
      }
    </script>



		<?php wp_footer(); ?>

	</body>
</html>
