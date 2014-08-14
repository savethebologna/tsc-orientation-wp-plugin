<?php
get_header();
?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
<?php
	if( current_user_can( 'delete_pages' ) ) {
		while ( have_posts() ) : the_post();
			$modules = load_module_results($post);
?>
<article id="post-86" class="post-86 consultant type-consultant status-publish hentry">
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>
		<div class="entry-content">
<?php
			the_content();
			if( !create_module_dropdown($modules) ){ echo 'No results to report yet.'; }
			echo "<div id='tscmod_results'></div>";
?>
		</div>
			
	</article>
<?php
		endwhile;
	} else {
?>
				<p>You do not have sufficient privileges to view this page.</p>
				<br>
				<p>See an administrator if you believe this is in error. View source for more information.</p>
				<!--You must have at least and "Editor" role. Otherwise there has been an error in the TSC Testing Plugin (single-consultant.php).-->
<?php
	}
?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php
get_footer();
echo create_module_dropdown_script($modules,'tscmod_results',false);
?>