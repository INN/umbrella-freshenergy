<?php

$thumbnail = get_the_post_thumbnail( $post->ID, 'full' );
?>

<div class="row-fluid">
	<div <?php post_class( $post->ID ); ?> >
		<a href="<?php echo get_permalink(); ?>"><?php echo the_post_thumbnail( 'large' ); ?></a>
		<div class="text-wrapper">
			<a href="<?php echo get_permalink(); ?>" class="blocklink"></a>
			<h5 class="top-tag"><?php largo_top_term( array( 'post' => $post->ID ) ); ?></h5>
			<h4><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></h4>
			<span class="byline"><?php echo largo_byline(false); ?></span>
			<?php largo_excerpt( null, 1 ); ?>
		</div>
	</div>
</div>
