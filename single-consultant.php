<?php get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php if( current_user_can( 'delete_pages' ) ) {
				while ( have_posts() ) : the_post();
					$mod = load_module_results($post);
					get_template_part( 'content', 'page' );
					foreach ( $mod as $key => $result )
						{ echo $key . " Module: " . $result . "<br>"; }
				endwhile;
			} else { ?>
				<p>You do not have sufficient privileges to view this page.</p>
				<br>
				<p>See an administrator if you believe this is in error. View source for more information.</p>
				<!--You must have at least and "Editor" role, otherwise there is an error in the TSC Testing Plugin (single-consultant.php).-->
			<?php } ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>
