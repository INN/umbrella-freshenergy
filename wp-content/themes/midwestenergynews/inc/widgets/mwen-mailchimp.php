<?php

class mwen_mailchimp_signup_widget extends WP_Widget {

	function mwen_mailchimp_signup_widget() {
		$widget_opts = array(
			'classname' => 'mailchimp-signup-widget',
			'description'=> __('Display a simple mailchimp signup form.', 'largo')
		);
		$this->WP_Widget('mailchimp_signup_widget', __('MWEN MailChimp Signup Widget', 'largo'),$widget_opts);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __('Subscribe to our newsletter', 'largo') : $instance['title'], $instance, $this->id_base);

		// Because $before_widget is a whole thing of HTML, just copy it all to add the 'rev' class.
		if (isset($instance['reverse']) && $instance['reverse'] == true) {
			$before_widget = '<div class="widget mailchimp-signup-widget rev">';
		}

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		?>
			<div class="newsletter-widget">
				<?php if (is_home() || is_archive()) { ?>
					<h3 class="pitch">Get our <span>daily headlines</span> in your inbox.</h3>
				<?php } else { ?>
					<h3 class="pitch">Don't miss the latest headlines. <span>Sign up for our Daily Digests.</span></h3>
				<?php } ?>
				<form action="//midwestenergynews.us7.list-manage.com/subscribe/post?u=ae5d3a0c6088cad29d71bf0d0&amp;id=efa0033ba9" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="row validate" target="_blank" novalidate>
					<div id="mailchimp-input-wrap">
						<input type="email" placeholder="Your email" name="EMAIL" class="required email" id="mce-EMAIL">

					<?php if( !is_home() && !is_archive() ) { ?>
						<input type="text" placeholder="Zip code" name="ZIPCODE" class="required zipcode" id="mce-ZIPCODE">
					<?php } ?>
					</div>

					<ul>
						<li>
							<input type="checkbox" value="1" name="group[6829][1]" id="mce-group[6829]-6829-1" checked>
							<label for="mce-group[6829]-6829-1">Midwest <?php if (!is_home() && !is_archive()) echo 'Headlines'; ?></label>
						</li>
						<li>
							<input type="checkbox" value="4" name="group[6829][4]" id="mce-group[6829]-6829-2" checked>
							<label for="mce-group[6829]-6829-2">National <?php if (!is_home() && !is_archive()) echo 'Headlines'; ?></label>
						</li>
					</ul>

					<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class=" btn">

					<div id="mce-responses" class="clear">
						<div class="response" id="mce-error-response" style="display:none"></div>
						<div class="response" id="mce-success-response" style="display:none"></div>
					</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
					<div class="hidden"><input type="text" name="b_ae5d3a0c6088cad29d71bf0d0_efa0033ba9" tabindex="-1" value=""></div>
				</form>
			</div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' 	=> __('Subscribe to our newsletter', 'largo'),
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>">
		</p>
		
		<?php
	}

}
