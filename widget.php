<?php
if ( ! defined( 'ABSPATH' ) ) die( '-1' );

class miniloops extends WP_Widget {

	function miniloops() {
		$widget_ops = array('classname' => 'miniloops',
							'description' => __( 'Query posts, display them.', 'mini-loops' )
						);
		$control_ops = array( 'width' => 700 );

		parent::WP_Widget( 'miniloops', __( 'Mini Loops', 'mini-loops' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;
		$instance['title'] = empty($instance['title_url']) ? $instance['title'] : '<a href="'. $instance['title_url'] .'">'. $instance['title'] .'</a>';
		echo $instance['hide_title'] ? '' : $before_title . stripslashes( $instance['title'] ) . $after_title;

		unset($instance['title']);
		echo get_miniloops( $instance );

		echo $after_widget;

	} //end widget()

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		//get old variables
		$instance['title'] = wp_filter_post_kses( $new_instance['title'] );
		$instance['hide_title'] = (bool) $new_instance['hide_title'] ? 1 : 0;
		$instance['title_url'] = esc_url( $new_instance['title_url'] );
		$instance['number_posts'] = (int) $new_instance['number_posts'];
		$instance['post_offset'] = (int) $new_instance['post_offset'];
		$instance['maximum_age'] = esc_attr( $new_instance['maximum_age'] );
		$instance['post_type'] = esc_attr( $new_instance['post_type'] );
		$instance['post_status'] = esc_attr( $new_instance['post_status'] );
		$instance['order_by'] = esc_attr( $new_instance['order_by'] );
		$instance['order'] = esc_attr( $new_instance['order'] );
		$instance['order_meta_key'] = esc_attr( $new_instance['order_meta_key'] );
		$instance['reverse_order'] = (bool) $new_instance['reverse_order'] ? 1 : 0;
		$instance['shuffle_order'] = (bool) $new_instance['shuffle_order'] ? 1 : 0;
		$instance['ignore_sticky'] = (bool) $new_instance['ignore_sticky'] ? 1 : 0;
		$instance['only_sticky'] = (bool) $new_instance['only_sticky'] ? 1 : 0;
		$instance['exclude_sticky'] = (bool) $new_instance['exclude_sticky'] ? 1 : 0;
		$instance['exclude_current'] = (bool) $new_instance['exclude_current'] ? 1 : 0;
		$instance['current_category'] = (bool) $new_instance['current_category'] ? 1 : 0;
		$instance['current_single_category'] = (bool) $new_instance['current_single_category'] ? 1 : 0;
		$instance['current_author'] = (bool) $new_instance['current_author'] ? 1 : 0;
		$instance['categories'] = esc_attr( $new_instance['categories'] );
		$instance['tags'] = esc_attr( $new_instance['tags'] );
		$instance['post_author'] = esc_attr( $new_instance['post_author'] );
		$instance['tax'] = esc_attr( $new_instance['tax'] );
		$instance['custom_fields'] = esc_attr( $new_instance['custom_fields'] );
		$instance['exclude'] = esc_attr( $new_instance['exclude'] );
		$instance['before_items'] = wp_filter_post_kses( $new_instance['before_items'] );
		$instance['item_format'] = wp_filter_post_kses( $new_instance['item_format'] );
		$instance['after_items'] = wp_filter_post_kses( $new_instance['after_items'] );

		return $instance;

	} //end update()

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, get_miniloops_defaults() );
		extract( $instance );

		include( dirname( __FILE__) .'/form.php');

	} //end form()
}


class miniminiloops extends miniloops {

	function miniminiloops() {
		$widget_ops = array('classname' => 'miniminiloops',
							'description' => __( 'Query posts, display them.', 'mini-loops' )
						);
		$control_ops = array(  );

		parent::WP_Widget( 'miniminiloops', __( 'Mini Mini Loops', 'mini-loops' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, get_miniloops_defaults() );
		extract( $instance );

		?>
		<p><?php _e( 'Back to basics. Just recent posts. No fuss.', 'mini-loops' ); ?></p>
		<p style="width:63%;float:left;">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo stripslashes( $title ); ?>" />
			</label>
		</p>
		<p style="width:33%;float:right;padding-top:20px;height:20px;">
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hide_title'); ?>" name="<?php echo $this->get_field_name('hide_title'); ?>"<?php checked( $hide_title ); ?> />
			<label for="<?php echo $this->get_field_id('hide_title'); ?>"><?php _e('Hide Title?', 'mini-loops' );?></label>
		</p>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id( 'title_url' ); ?>"><?php _e( 'Title URL:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('title_url'); ?>" name="<?php echo $this->get_field_name('title_url'); ?>" type="text" value="<?php echo $title_url; ?>" />
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('number_posts'); ?>"><?php _e('Number of Posts:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('number_posts'); ?>" name="<?php echo $this->get_field_name('number_posts'); ?>" type="number" value="<?php echo $number_posts; ?>" />
			</label>
		</p>
		<input name="<?php echo $this->get_field_name('post_offset'); ?>" type="hidden" value="<?php echo $post_offset; ?>" />
		<input name="<?php echo $this->get_field_name('maximum_age'); ?>" type="hidden" value="<?php echo $maximum_age; ?>" />
		<input name="<?php echo $this->get_field_name('post_type'); ?>" type="hidden" value="<?php echo $post_type; ?>" />
		<input name="<?php echo $this->get_field_name('post_status'); ?>" type="hidden" value="<?php echo $post_status; ?>" />
		<input name="<?php echo $this->get_field_name('order_by'); ?>" type="hidden" value="<?php echo $order_by; ?>" />
		<input name="<?php echo $this->get_field_name('order'); ?>" type="hidden" value="<?php echo $order; ?>" />
		<input name="<?php echo $this->get_field_name('reverse_order'); ?>" type="hidden" value="<?php echo $reverse_order; ?>" />
		<input name="<?php echo $this->get_field_name('shuffle_order'); ?>" type="hidden" value="<?php echo $shuffle_order; ?>" />
		<input name="<?php echo $this->get_field_name('ignore_sticky'); ?>" type="hidden" value="<?php echo $ignore_sticky; ?>" />
		<input name="<?php echo $this->get_field_name('only_sticky'); ?>" type="hidden" value="<?php echo $only_sticky; ?>" />
		<input name="<?php echo $this->get_field_name('exclude_sticky'); ?>" type="hidden" value="<?php echo $exclude_sticky; ?>" />
		<input name="<?php echo $this->get_field_name('exclude_current'); ?>" type="hidden" value="<?php echo $exclude_current; ?>" />
		<input name="<?php echo $this->get_field_name('current_category'); ?>" type="hidden" value="<?php echo $current_category; ?>" />
		<input name="<?php echo $this->get_field_name('current_author'); ?>" type="hidden" value="<?php echo $current_author; ?>" />
		<input name="<?php echo $this->get_field_name('categories'); ?>" type="hidden" value="<?php echo $categories; ?>" />
		<input name="<?php echo $this->get_field_name('tags'); ?>" type="hidden" value="<?php echo $tags; ?>" />
		<input name="<?php echo $this->get_field_name('post_author'); ?>" type="hidden" value="<?php echo $post_author; ?>" />
		<input name="<?php echo $this->get_field_name('tax'); ?>" type="hidden" value="<?php echo $tax; ?>" />
		<input name="<?php echo $this->get_field_name('custom_fields'); ?>" type="hidden" value="<?php echo $custom_fields; ?>" />
		<input name="<?php echo $this->get_field_name('exclude'); ?>" type="hidden" value="<?php echo $exclude; ?>" />
		<textarea class="hidden widefat" id="<?php echo $this->get_field_id('before_items'); ?>" name="<?php echo $this->get_field_name('before_items'); ?>"><?php echo htmlspecialchars( stripslashes( $before_items ) ); ?></textarea>
		<textarea class="hidden widefat" id="<?php echo $this->get_field_id('after_items'); ?>" name="<?php echo $this->get_field_name('after_items'); ?>"><?php echo htmlspecialchars( stripslashes( $after_items ) ); ?></textarea>
		<textarea class="hidden widefat" rows="5" id="<?php echo $this->get_field_id('item_format'); ?>" name="<?php echo $this->get_field_name('item_format'); ?>"><?php echo stripslashes( $item_format ); ?></textarea>
		<?php
	} //end form()
}
