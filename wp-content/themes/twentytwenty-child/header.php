<?php
/**
 * Header file for the Twenty Twenty WordPress default theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<?php wp_head(); ?>
		<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    <title>Sixteen Clothing HTML Template</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo get_stylesheet_directory_uri(); ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!--

TemplateMo 546 Sixteen Clothing

https://templatemo.com/tm-546-sixteen-clothing

-->

    <!-- Additional CSS Files 
			<link rel="icon" href="/favicon-32x32.png" />-->
			<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icon.png" />
			<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/fontawesome.css">
			<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/owl.css">
			<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(). '/style.css' ?>">
			<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/templatemo-sixteen.css"/>
	<body <?php body_class(); ?>>

		<?php
		wp_body_open();
		?>

		 <!-- Header -->
		 <header class="">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
        <?php if ( function_exists( 'the_custom_logo' ) ) {
    		the_custom_logo();
			}?>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
			<?php
		$menu = '27';
		$args        = array(
		'order'       => 'ASC',
		'orderby'     => 'menu_order',
		'post_type'   => 'nav_menu_item',
		'post_status' => 'publish',
		'output'      => ARRAY_A,
		'output_key'  => 'menu_order',
		'nopaging'    => true,
		);
		$items=wp_get_nav_menu_items( $menu, $args);
		foreach( $items as $item ){
        // set up title and url
        $title = $item->title;
        $link = $item->url;
		?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo $link; ?>"><?php echo $title; ?></a>
		</li>
		<?php } ?>
            </ul>
          </div>
        </div>
      </nav>
    </header>
		<?php
		// Output the menu modal.
		get_template_part( 'template-parts/modal-menu' );
