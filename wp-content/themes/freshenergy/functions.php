<?php
/**
 * Child theme functions for the freshenergy theme
 */

/**
 * Include theme files
 *
 * Based off of how Largo loads files: https://github.com/INN/Largo/blob/master/functions.php#L358
 *
 * 1. hook function Largo() on after_setup_theme
 * 2. function Largo() runs Largo::get_instance()
 * 3. Largo::get_instance() runs Largo::require_files()
 *
 * This function is intended to be easily copied between child themes, and for that reason is not prefixed with this child theme's normal prefix.
 *
 * @link https://github.com/INN/Largo/blob/master/functions.php#L145
 */
function largo_child_require_files() {
	$includes = array(
		'/homepages/layouts/freshenergy.php',
		'/inc/widgets.php',
		'/inc/images.php',
		'/inc/events.php',
		'/inc/events-list-shortcode.php',
	);
	foreach ( $includes as $include ) {
		require_once( get_stylesheet_directory() . $include );
	}
}
add_action( 'after_setup_theme', 'largo_child_require_files' );

/**
 * Include compiled style.css
 */
function fe_styles() {
	wp_dequeue_style( 'largo-child-styles' );
	$suffix = (LARGO_DEBUG)? '' : '.min';
	wp_enqueue_style(
		'fe',
		get_stylesheet_directory_uri() . '/css/child' . $suffix . '.css',
		array(),
		filemtime( get_stylesheet_directory() . '/css/child' . $suffix . '.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'fe_styles', 20 );

/**
 * Change thumbnail image size to rectangles 4 by 6
 */
function fe_thumbnail_size() {
	set_post_thumbnail_size( 210, 140, true ); // thumbnail
	add_image_size( 'thumbnail', 210, 140, true );
}
add_action( 'after_setup_theme', 'fe_thumbnail_size', 100 ); // needs to come after Largo's configuration at 10

function fe_js() {
	wp_enqueue_script(
		'typekit_js',
		get_stylesheet_directory_uri() . '/js/typekit.js',
		array(),
		'1.0',
		false
	);
	wp_enqueue_script(
		'fe_js',
		get_stylesheet_directory_uri() . '/js/fe.js',
		array( 'jquery' ),
		'1.0',
		true
	);
	wp_enqueue_script(
		'twitter',
		'//platform.twitter.com/widgets.js',
		array(),
		'3',
		true
	);
}

add_action('wp_enqueue_scripts', 'fe_js');

function add_taxonomies_to_pages() {
	register_taxonomy_for_object_type( 'post_tag', 'page' );
	register_taxonomy_for_object_type( 'category', 'page' );
}
add_action( 'init', 'add_taxonomies_to_pages' );
if ( ! is_admin() ) {
	add_action( 'pre_get_posts', 'category_and_tag_archives' );

}

function category_and_tag_archives( $wp_query ) {
	$my_post_array = array('post','page');

	if ( $wp_query->get( 'category_name' ) || $wp_query->get( 'cat' ) )
	$wp_query->set( 'post_type', $my_post_array );

	if ( $wp_query->get( 'tag' ) )
	$wp_query->set( 'post_type', $my_post_array );
}


/**
 * Get posts marked as "Featured in category" for a given category name.
 *
 * @param string $category_name the category to retrieve featured posts for.
 * @param integer $number total number of posts to return, backfilling with regular posts as necessary.
 */
function fe_get_featured_posts_in_category( $category_id, $number = 4 ) {
	$args = array(
		'cat' => $category_id,
		'category__not_in' => array( 9, 1089 ), // 9=publications, 1089=staff
		'numberposts' => $number,
		'post_status' => 'publish'
	);

	$tax_query = array(
		'tax_query' => array(
			array(
				'taxonomy' => 'prominence',
				'field' => 'slug',
				'terms' => 'category-featured'
			)
		)
	);

	// Get the featured posts
	$featured_posts = get_posts( array_merge( $args, $tax_query ) );

	// Backfill with regular posts if necessary
	if ( count( $featured_posts ) < (int) $number ) {
		$needed = (int) $number - count( $featured_posts );
		$regular_posts = get_posts( array_merge( $args, array(
			'numberposts' => $needed,
			'post__not_in' => array_map( function( $x ) { return $x->ID; }, $featured_posts )
		)));
		$featured_posts = array_merge( $featured_posts, $regular_posts );
	}

	return $featured_posts;
}

/**
 * Get posts marked as "Featured in category" for a given category name.
 *
 * @param string $category_name the category to retrieve featured posts for.
 * @param integer $number total number of posts to return, backfilling with regular posts as necessary.
 */
function fe_get_featured_posts_in_publication( $category_id, $number = 4 ) {
	$args = array(
		'cat' => $category_id,
		'numberposts' => $number,
		'post_status' => 'publish'
	);

	$tax_query = array(
		'tax_query' => array(
			array(
				'taxonomy' => 'prominence',
				'field' => 'slug',
				'terms' => 'category-featured'
			)
		)
	);

	// Get the featured posts
	$featured_posts = get_posts( array_merge( $args, $tax_query ) );

	// Backfill with regular posts if necessary
	if ( count( $featured_posts ) < (int) $number ) {
		$needed = (int) $number - count( $featured_posts );
		$regular_posts = get_posts( array_merge( $args, array(
			'numberposts' => $needed,
			'post__not_in' => array_map( function( $x ) { return $x->ID; }, $featured_posts )
		)));
		$featured_posts = array_merge( $featured_posts, $regular_posts );
	}

	return $featured_posts;
}

/**
 * Helper for getting posts in a category archive, excluding featured posts.
 * 
 * @param WP_Query $query
 * @uses fe_get_featured_posts_in_category
 */
function fe_category_archive_posts( $query ) {
	// don't muck with admin, non-categories, etc
	if ( ! $query->is_category() || ! $query->is_main_query() || is_admin() ) {
		return;
	}

	// Don't muck with these categories specifically
	// Also remove the largo thing that mucks with them
	if (
		$query->is_category( '26' ) || // powering progress
		$query->is_category( '25' ) || // energy matters
		$query->is_category( 'news' ) // news
	) {
		remove_action( 'pre_get_posts', 'largo_category_archive_posts', 15 );
		return;
	}

	// If this has been disabled by an option, do nothing
	// @see fe_do_hide_category_featured
	if ( of_get_option( 'hide_category_featured' ) == true ) {
		return;
	}

	// get the featured posts
	$featured_posts = fe_get_featured_posts_in_category( $query->get( 'cat' ) );

	// get the IDs from the featured posts
	$featured_post_ids = array();
	foreach ( $featured_posts as $fpost )
		$featured_post_ids[] = $fpost->ID;

	$query->set( 'post__not_in', $featured_post_ids );
	$query->set('cat','-1089'); // Exclude Staff category
}
add_action( 'pre_get_posts', 'fe_category_archive_posts' );

/**
 * Make thumbnails actual thumbnails, not featured for featured content
 *
 * @since Largo 0.5.5.3
 * @filter largo_content_partial_arguments
 * @param Array $args arguments for Largo's partials/content.php
 * @see largo/partials/content.php
 */
function fe_disable_featured_image_presentation_on_archives( $args, $qo = null ) {
	if ( is_archive() ) {
		$args['featured'] = false;
	}
	return $args;
}
add_action( 'largo_content_partial_arguments', 'fe_disable_featured_image_presentation_on_archives', 10, 2 );
