<?php

/**
 * Rewrite byline for vertical / horizontal stuff
 */
function largo_byline( $echo = true, $exclude_date = false, $post = null ) {
	if (!empty($post)) {
		if (is_object($post))
			$post_id = $post->ID;
		else if (is_numeric($post))
			$post_id = $post;
	} else
		$post_id = get_the_ID();

	$values = get_post_custom( $post_id );

	if ( function_exists( 'get_coauthors' ) && !isset( $values['largo_byline_text'] ) ) {
		$coauthors = get_coauthors( $post_id );
		foreach( $coauthors as $author ) {
			$byline_text = $author->display_name;
			if ( $org = $author->organization )
				$byline_text .= ' (' . $org . ')';

			$out[] = '<a class="url fn n" href="' . get_author_posts_url( $author->ID, $author->user_nicename ) . '" title="' . esc_attr( sprintf( __( 'Read All Posts By %s', 'largo' ), $author->display_name ) ) . '" rel="author">' . esc_html( $byline_text ) . '</a>';

		}

		if ( count($out) > 1 ) {
			end($out);
			$key = key($out);
			reset($out);
			$authors = implode( ', ', array_slice( $out, 0, -1 ) );
			$authors .= ' <span class="and">' . __( 'and', 'largo' ) . '</span> ' . $out[$key];
		} else {
			$authors = $out[0];
		}

	} else {
		$authors = largo_author_link( false, $post_id );
	}

	if ( is_single() ) {
		$teaser = __( 'Written By ', 'largo' );
	} else {
		$teaser = __( 'By ', 'largo' );
	}

	$byline_tease = $teaser;

	$output = '<span class="by-author"><span class="by">'. $byline_tease . '</span><span class="author vcard" itemprop="author">' . $authors . '</span></span>';
	if ( is_single() && ! $exclude_date ) {
		$output .= '<time class="entry-date updated dtstamp pubdate" datetime="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">' . largo_time(false, $post_id) . '</time>';
	}

	if ( is_single() && current_user_can( 'edit_post', $post_id ) ) {
		$output .= '<br /><span class="edit-link"><a href="' . get_edit_post_link( $post_id ) . '">' . __( 'Edit This Post', 'largo' ) . '</a></span>';
	}

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

/**
 * Create limited social media soup for the span3 left column of the custom two-column layout.
 *
 * @since Largo 0.5
 * @see partials/content-single-classic.php
 */
function mwen_post_social_links( $echo = true ) {
		$utilities = of_get_option( 'article_utilities' );
		$output = '<div id="mwen_post_social_links" class="post-social clearfix"><div class="left">';
		
		if ( $utilities['facebook'] === '1' ) {
			$output .= sprintf( '<span data-service="facebook" class="custom-share-button icon-facebook share-button"></span>');
		}
		
		if ( $utilities['twitter'] === '1' ) {
			$output .= sprintf( '<span data-service="twitter" class="custom-share-button icon-twitter share-button"></span>');
		}
		
		if ($utilities['email'] === '1' ) {
			$output .= '<span data-service="email" class="custom-share-button icon-mail"></span>';
		}

		if ( $utilities['print'] === '1' ) {
			$output .= '<span><a href="#" onclick="window.print()" title="' . esc_attr( __( 'Print this article', 'largo' ) ) . '" rel="nofollow"><i class="custom-share-button icon-print"></i></a></span>';
		}

		
		$output .= '</div><div class="right">';
		
		
		$output .= '</div></div>';
		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}
