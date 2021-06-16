<?php
if ( ! defined( 'ABSPATH' ) ) die( '-1' );

class miniloops extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'miniloops',
			'description' => __( 'Query posts, display them.', 'mini-loops' )
		);
		$control_ops = array( 'width' => 700 );

		WP_Widget::__construct( 'miniloops', __( 'Mini Loops', 'mini-loops' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		 // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

		echo $args['before_widget'];
		if ( ! $instance['hide_title'] ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			$title = empty( $instance['title_url'] ) ? wp_kses_post( $title ) : '<a href="'. esc_url( $instance['title_url'] ) .'">'. wp_kses_post( $title ) .'</a>';
			echo $args['before_title'] . $title . $args['after_title'];
		}

		unset($instance['title']);
		echo get_miniloops( $instance );

		echo $args['after_widget'];

		 // phpcs:enable

	} //end widget()

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		//get old variables
		$instance['title']                   = wp_kses_post( $new_instance['title'] );
		$instance['hide_title']              = (bool) isset( $new_instance['hide_title'] ) && $new_instance['hide_title'] ? 1 : 0;
		$instance['title_url']               = esc_url( $new_instance['title_url'] );
		$instance['number_posts']            = (int) $new_instance['number_posts'];
		$instance['post_offset']             = (int) $new_instance['post_offset'];
		$instance['maximum_age']             = esc_attr( $new_instance['maximum_age'] );
		$instance['post_type']               = esc_attr( $new_instance['post_type'] );
		$instance['post_status']             = esc_attr( $new_instance['post_status'] );
		$instance['order_by']                = esc_attr( $new_instance['order_by'] );
		$instance['order']                   = esc_attr( $new_instance['order'] );
		$instance['order_meta_key']          = esc_attr( $new_instance['order_meta_key'] );
		$instance['reverse_order']           = (bool) isset( $new_instance['reverse_order'] ) && $new_instance['reverse_order'] ? 1 : 0;
		$instance['shuffle_order']           = (bool) isset( $new_instance['shuffle_order'] ) && $new_instance['shuffle_order'] ? 1 : 0;
		$instance['ignore_sticky']           = (bool) isset( $new_instance['ignore_sticky'] ) && $new_instance['ignore_sticky'] ? 1 : 0;
		$instance['only_sticky']             = (bool) isset( $new_instance['only_sticky'] ) && $new_instance['only_sticky'] ? 1 : 0;
		$instance['exclude_sticky']          = (bool) isset( $new_instance['exclude_sticky'] ) && $new_instance['exclude_sticky'] ? 1 : 0;
		$instance['exclude_current']         = (bool) isset( $new_instance['exclude_current'] ) && $new_instance['exclude_current'] ? 1 : 0;
		$instance['current_category']        = (bool) isset( $new_instance['current_category'] ) && $new_instance['current_category'] ? 1 : 0;
		$instance['current_single_category'] = (bool) isset( $new_instance['current_single_category'] ) && $new_instance['current_single_category'] ? 1 : 0;
		$instance['current_author']          = (bool) isset( $new_instance['current_author'] ) && $new_instance['current_author'] ? 1 : 0;
		$instance['categories']              = esc_attr( $new_instance['categories'] );
		$instance['tags']                    = esc_attr( $new_instance['tags'] );
		$instance['post_author']             = esc_attr( $new_instance['post_author'] );
		$instance['tax']                     = esc_attr( $new_instance['tax'] );
		$instance['custom_fields']           = esc_attr( $new_instance['custom_fields'] );
		$instance['exclude']                 = esc_attr( $new_instance['exclude'] );
		$instance['before_items']            = wp_kses_post( $new_instance['before_items'] );
		$instance['item_format']             = wp_kses_post( $new_instance['item_format'] );
		$instance['after_items']             = wp_kses_post( $new_instance['after_items'] );

		return $instance;

	} //end update()

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, get_miniloops_defaults() );
		extract( $instance );
		include( dirname( __FILE__) .'/form.php');

	} //end form()
}


class miniminiloops extends miniloops {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'miniminiloops',
			'description' => __( 'Query posts, display them.', 'mini-loops' )
		);
		$control_ops = array( );
		WP_Widget::__construct( 'miniminiloops', __( 'Mini Mini Loops', 'mini-loops' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, get_miniloops_defaults() );
		extract( $instance );
		?>
		<p><?php esc_html_e( 'Back to basics. Just recent posts. No fuss.', 'mini-loops' ); ?></p>
		<p style="width:63%;float:left;">
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</label>
		</p>
		<p style="width:33%;float:right;padding-top:20px;height:20px;">
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hide_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_title') ); ?>"<?php checked( $hide_title ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id('hide_title' ) ); ?>"><?php esc_html_e('Hide Title?', 'mini-loops' );?></label>
		</p>
		<p style="width:48%;float:left;">
			<label for="<?php echo esc_attr( $this->get_field_id( 'title_url' )  ); ?>"><?php esc_html_e( 'Title URL:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('title_url') ); ?>" type="text" value="<?php echo esc_attr( $title_url ); ?>" />
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo esc_attr( $this->get_field_id('number_posts' ) ); ?>"><?php esc_html_e('Number of Posts:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('number_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('number_posts') ); ?>" type="number" value="<?php echo esc_attr( $number_posts ); ?>" />
			</label>
		</p>
		<input name="<?php echo esc_attr( $this->get_field_name('post_offset') ); ?>" type="hidden" value="<?php echo esc_attr( $post_offset ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('maximum_age') ); ?>" type="hidden" value="<?php echo esc_attr( $maximum_age ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('post_type') ); ?>" type="hidden" value="<?php echo esc_attr( $post_type ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('post_status') ); ?>" type="hidden" value="<?php echo esc_attr( $post_status ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('order_by') ); ?>" type="hidden" value="<?php echo esc_attr( $order_by ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('order') ); ?>" type="hidden" value="<?php echo esc_attr( $order ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('order_meta_key') ); ?>" type="hidden" value="<?php echo esc_attr( $order_meta_key ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('reverse_order') ); ?>" type="hidden" value="<?php echo esc_attr( $reverse_order ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('shuffle_order') ); ?>" type="hidden" value="<?php echo esc_attr( $shuffle_order ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('ignore_sticky') ); ?>" type="hidden" value="<?php echo esc_attr( $ignore_sticky ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('only_sticky') ); ?>" type="hidden" value="<?php echo esc_attr( $only_sticky ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('exclude_sticky') ); ?>" type="hidden" value="<?php echo esc_attr( $exclude_sticky ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('exclude_current') ); ?>" type="hidden" value="<?php echo esc_attr( $exclude_current ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('current_category') ); ?>" type="hidden" value="<?php echo esc_attr( $current_category ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('current_author') ); ?>" type="hidden" value="<?php echo esc_attr( $current_author ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('categories') ); ?>" type="hidden" value="<?php echo esc_attr( $categories ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('tags') ); ?>" type="hidden" value="<?php echo esc_attr( $tags ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('post_author') ); ?>" type="hidden" value="<?php echo esc_attr( $post_author ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('tax') ); ?>" type="hidden" value="<?php echo esc_attr( $tax ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('custom_fields') ); ?>" type="hidden" value="<?php echo esc_attr( $custom_fields ); ?>" />
		<input name="<?php echo esc_attr( $this->get_field_name('exclude') ); ?>" type="hidden" value="<?php echo esc_attr( $exclude ); ?>" />
		<textarea class="hidden widefat" id="<?php echo esc_attr( $this->get_field_id('before_items' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('before_items') ); ?>"><?php echo esc_textarea( $before_items ); ?></textarea>
		<textarea class="hidden widefat" id="<?php echo esc_attr( $this->get_field_id('after_items' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('after_items') ); ?>"><?php echo esc_textarea( $after_items ); ?></textarea>
		<textarea class="hidden widefat" rows="5" id="<?php echo esc_attr( $this->get_field_id('item_format' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('item_format') ); ?>"><?php echo esc_textarea( $item_format ); ?></textarea>
		<?php
	} //end form()
}
