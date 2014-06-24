<?php
/*
Template Name: Orientation Modules
*/
?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			
			<?php if( is_user_logged_in() ){
				while ( have_posts() ) : the_post();
					get_template_part( 'content', 'page' );
				endwhile;
			}else{ ?>
				<p>Training modules are only available to logged in users. Please <a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">log in</a> first.</p>
			<?php } ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>