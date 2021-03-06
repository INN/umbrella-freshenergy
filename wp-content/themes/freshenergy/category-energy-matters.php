<?php
/**
 * Template for category archive pages
 *
 * @package Largo
 * @since 0.4
 * @filter largo_partial_by_post_type
 */
get_header();

global $tags, $paged, $post, $shown_ids;

$title = single_cat_title( '', false );
$description = category_description();
$rss_link = get_category_feed_link( get_queried_object_id() );
$posts_term = of_get_option( 'posts_term_plural', 'Stories' );
$queried_object = get_queried_object();
?>

<div class="clearfix">
	<header class="archive-background clearfix">
		<a class="rss-link rss-subscribe-link" href="<?php echo $rss_link; ?>"><?php echo __( 'Subscribe', 'largo' ); ?> <i class="icon-rss"></i></a>
		<div class="hero-title">
			<?php
				$post_id = largo_get_term_meta_post( $queried_object->taxonomy, $queried_object->term_id );
				largo_hero( $post_id );
			?>
			<h1 class="page-title"><?php echo $title; ?></h1>
		</div>
		<div class="archive-description"><?php echo $description; ?></div>
		<?php do_action( 'largo_category_after_description_in_header' ); ?>
		<!--<?php get_template_part( 'partials/archive', 'category-related' ); ?>-->
	</header>

	<section class="container">

		<div class="row-fluid clearfix">
			<div class="stories span8" role="main" id="content">
			<?php
				do_action( 'largo_before_category_river' );
				if ( have_posts() ) {
					$counter = 1;
					while ( have_posts() ) {
						the_post();
						$post_type = get_post_type();
						$partial = largo_get_partial_by_post_type( 'archive', $post_type, 'archive' );
						get_template_part( 'partials/content', $partial );
						do_action( 'largo_loop_after_post_x', $counter, $context = 'archive' );
						$counter++;
					}
					largo_content_nav( 'nav-below' );
				} elseif ( count($featured_posts) > 0 ) {
					// do nothing
					// We have n > 1 posts in the featured header
					// It's not appropriate to display partials/content-not-found here.
				} else {
					get_template_part( 'partials/content', 'not-found' );
				}
				do_action( 'largo_after_category_river' );
			?>
			</div>
		</div>
	</section>

</div>

		

<?php get_footer();
