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
		<?php //get_template_part( 'partials/archive', 'category-related' ); ?>
	</header>

	<section class="container">

	<?php if ( $paged < 2 && of_get_option( 'hide_category_featured' ) == '0' ) {
		$args = array_merge( array( 'cat' => get_queried_object_id() ), array( 'cat' => '-9' ) );
		$featured_posts = fe_get_featured_posts_in_category( get_queried_object_id() );

		if ( count( $featured_posts ) > 0 ) {
			$secondary_featured = $featured_posts;
			if ( count( $secondary_featured ) > 0 ) { ?>
				<div class="secondary-featured-post">
					<div class="row-fluid clearfix"><?php
						foreach ( $secondary_featured as $idx => $featured_post ) {
								$shown_ids[] = $featured_post->ID;
								largo_render_template(
									'partials/archive',
									'category-featured',
									array( 'featured_post' => $featured_post )
								);
						} ?>
					</div>
					<a href="/category/news/"><button>More News</button></a>
				</div>
			<?php }
		}
	} ?>
	</section>
</div>

<div id="fe-staff-circles" class="row-fluid clearfix">
	<div class="widget widget-1 odd default span12">
		<h3 class=""><span>Program Staff</span></h3>
		<div class="menu-staff-container">
			<ul id="menu-staff" class="menu">
				<?php
					$currentid = get_queried_object_id();
					$args = array( 
						'category__and' => array( $currentid, 1089 ),  // 1089 is the Staff category
						'post_type' => 'page'
					);
					query_posts( $args );

					while ( have_posts() ) : the_post();
						echo '<li style="background-image:url(';
						the_post_thumbnail_url( 'large' );
						echo ')"><a href="' . get_permalink() . '"><span>' . get_the_title() . '</span></a></li>';
					endwhile;
				?>
			</ul>
		</div>
	</div>
</div>




<?php
	$currentid = get_queried_object_id();
	$currentslug = get_queried_object()->slug;
	if ( have_posts() ) { ?>

		<?php 
			function get_id_by_slug($page_slug) {
			    $page = get_page_by_path($page_slug);
			    if ($page) {
			        return $page->ID;
			    } else {
			        return null;
			    }
			} 
			$pageid = get_id_by_slug( $currentslug . '-publications' );

			if ( $pageid )
			{ ?>

				<div id="fe-reports">
					<div class="widget widget-1 odd default span12">
						<h3 class=""><span>Publications</span></h3>
						<div class="row-fluid">
							<?php 
							    //echo $currentslug;
							    //echo $pageid;
							    echo do_shortcode(get_post_field('post_content', $pageid));
							?>
						</div>
					</div>
				</div>

			<?php } ?>

	<?php } ?>

<?php wp_reset_query();?>
		

<?php get_footer();
