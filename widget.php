<?php

class miniloops extends WP_Widget {

	function miniloops() {
		$widget_ops = array('classname' => 'miniloops',
							'description' => __( 'Query posts, display them.', 'mini-loops' )
						);
		$control_ops = array( 'width' => 300, 'id_base' => 'miniloops' );

		parent::WP_Widget( 'miniloops', __( 'Mini Loops', 'mini-loops' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo $before_widget;
		$instance['title'] = empty($instance['title_url']) ? $instance['title'] : '<a href="'. $instance['title_url'] .'">'. $instance['title'] .'</a>';
		echo $instance['hide_title'] ? '' : $before_title . $instance['title'] . $after_title;

		unset($instance['title']);
		echo get_miniloops( $instance );

		echo $after_widget;

	} //end widget()

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		//get old variables
		$instance['title'] = esc_attr( $new_instance['title'] );
		$instance['hide_title'] = (bool) $new_instance['hide_title'] ? 1 : 0;
		$instance['title_url'] = esc_url( $new_instance['title_url'] );
		$instance['number_posts'] = (int) $new_instance['number_posts'];
		$instance['post_offset'] = (int) $new_instance['post_offset'];
		$instance['post_type'] = esc_attr( $new_instance['post_type'] );
		$instance['post_status'] = esc_attr( $new_instance['post_status'] );
		$instance['order_by'] = esc_attr( $new_instance['order_by'] );
		$instance['order'] = esc_attr( $new_instance['order'] );
		$instance['reverse_order'] = (bool) $new_instance['reverse_order'] ? 1 : 0;
		$instance['shuffle_order'] = (bool) $new_instance['shuffle_order'] ? 1 : 0;
		$instance['ignore_sticky'] = (bool) $new_instance['ignore_sticky'] ? 1 : 0;
		$instance['only_sticky'] = (bool) $new_instance['only_sticky'] ? 1 : 0;
		$instance['exclude_current'] = (bool) $new_instance['exclude_current'] ? 1 : 0;
		$instance['categories'] = esc_attr( $new_instance['categories'] );
		$instance['tags'] = esc_attr( $new_instance['tags'] );
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

		include('form.php');

	} //end form()
}
