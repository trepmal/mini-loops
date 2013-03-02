<?php
if ( ! defined( 'ABSPATH' ) ) die( '-1' );

// Show recent posts function
function get_miniloops_defaults() {
	$defs = array( 	'title' => __( 'Recent Posts', 'mini-loops' ),
					'hide_title' => 0,
					'title_url' => '',
					'number_posts' => 3,
					'paged' => 1,
					'post_offset' => 0,
					'maximum_age' => 0,
					'post_type' => 'post',
					'post_status' => 'publish',
					'order_by' => 'date',
					'order' => 'DESC',
					'order_meta_key' => '',
					'reverse_order' => 0,
					'shuffle_order' => 0,
					'ignore_sticky' => 1,
					'only_sticky' => 0,
					'exclude_sticky' => 0,
					'exclude_current' => 1,
					'current_category' => 0,
					'current_single_category' => 0,
					'current_author' => 0,
					'categories' => '',
					'tags' => '',
					'post_author' => '',
					'tax' => '',
					'custom_fields' => '',
					'exclude' => '',
					'before_items' => '<ul>',
					'item_format' => '<li><a href="[url]">[title]</a><br />[excerpt]</li>',
					'after_items' => '</ul>',
			);
	return $defs;
}

function get_miniloops( $args = '' ) {
	global $wpdb, $post;
	$defaults = get_miniloops_defaults();

	$args = wp_parse_args( $args, $defaults );
	extract($args);

	//since this function can be called in the template, re-escape the parameters
	$number_posts = (int) $number_posts;
	$paged = (int) $paged;
	$post_offset = (int) $post_offset;
	$maximum_age = (int) $maximum_age;
	$post_type = esc_attr( $post_type );
	$post_status = esc_attr( $post_status );
	$order_by = esc_attr( $order_by );
	$order = esc_attr( $order );
	if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) $order = 'DESC';
	$order_meta_key = esc_attr( $order_meta_key );
	$reverse_order = (bool) $reverse_order;
	$ignore_sticky = (bool) $ignore_sticky;
	$only_sticky = (bool) $only_sticky;
	$exclude_sticky = (bool) $exclude_sticky;
	$exclude_current = (bool) $exclude_current;
	$current_category = (bool) $current_category;
	$current_single_category = (bool) $current_single_category;
	$current_author = (bool) $current_author;
	$categories = esc_attr( $categories );
	$tags = esc_attr( $tags );
		$tags = str_replace( ' ', '', $tags );
		$tags = explode( ',', $tags );
		$tags = array_filter( $tags );
	$post_author = esc_attr( $post_author );
	$tax = str_replace('&amp;', '&', esc_attr( $tax ) );
	$custom_fields = str_replace('&amp;', '&', esc_attr( $custom_fields ) );
	$exclude = explode( ',', esc_attr( $exclude ) );
	$before_items = stripslashes( wp_filter_post_kses( $before_items ) );
	$after_items = wp_filter_post_kses( $after_items );
	//$item_format //this is escaped in the loop so that the filter can be applied

	if (is_single() && $exclude_current) {
		$exclude[] = $post->ID;
	}
	if ($exclude_sticky) {
		$exclude = array_merge( $exclude, get_option('sticky_posts') );
	}

	parse_str($tax, $taxes);
	$tax_query = array();
	foreach( array_keys( $taxes ) as $k => $slug ) {
		//original code is this single commented line, if you need to revert
		//$tax_query[] = array( 'taxonomy' => $slug, 'field' => 'id', 'terms' => explode( ',', $taxes[ $slug ] ) );

		$oper = 'IN';
		$ids = explode( ',', $taxes[ $slug ] );
		if ( count( $ids ) == 1 && $ids['0'] < 0 ) {
			//if there is only one id given, and it's negative
			//let's treat it as 'posts not in'
			$ids['0'] = $ids['0'] * -1;
			$oper = 'NOT IN';
		}
		$tax_query[] = array(
			'taxonomy' => $slug,
			'field' => 'id',
			'terms' => $ids,
			'operator' => $oper );
	}

	parse_str($custom_fields, $meta_fields);
	$meta_query = array();
	foreach( $meta_fields as $k => $v ) {
		$meta_query[] = array( 'key' => $k, 'value' => $v, 'compare' => '=' );
	}

	if ( $current_category && is_category() ) {
		$categories = get_query_var( 'cat' );
	}

	if ( $current_single_category && is_single() ) {
		$categories = get_the_category();
		$categories = $categories[0]->term_id;
	}

	if ( $current_author && is_author() ) {
		$post_author = get_query_var( 'author' );
	} elseif ( $current_author && is_single() ) {
		$post_author = get_the_author_meta('ID');
	}

	$query = array(
		'cat' => $categories,
		'tag__in' => $tags,
		'tax_query' => $tax_query,
		'meta_query' => $meta_query,
		'posts_per_page' => $number_posts,
		'ignore_sticky_posts' => $ignore_sticky,
		'post__in' => ( $only_sticky ? get_option('sticky_posts') : '' ),
		'post_type' => $post_type,
		'post_status' => $post_status,
		'author' => $post_author,
		'offset' => $post_offset,
		'orderby' => $order_by,
		'order' => $order,
		'post__not_in' => $exclude,
		'paged' => $paged,
	);

	if ( in_array( $order_by, array( 'meta_value', 'meta_value_num' ) ) && ! empty( $order_meta_key ) ) {
		$query['meta_key'] = $order_meta_key;
	}

	$query = apply_filters( 'miniloops_query', $query );

	if ( $maximum_age != 0 ) {
		global $mini_loops_minimum_date;
		$mini_loops_minimum_date = date( 'Y-m-d', time() - ( $maximum_age * 24 * 60 * 60 ) );
		$maximum_age_func = create_function('$filter','global $mini_loops_minimum_date; $filter .= " AND post_date >= \'' . $mini_loops_minimum_date .'\'"; return $filter;');
		add_filter( 'posts_where', $maximum_age_func );
	}

	//for testing
	//return '<pre>'. print_r( $query, true ) .'</pre>';

	do_action( 'before_the_miniloop', $query, $args );

	//perform the query
	$miniloop = new WP_Query( $query );
	if ( $reverse_order ) $miniloop->posts = array_reverse( $miniloop->posts );
	if ( $shuffle_order ) shuffle( $miniloop->posts );

	if ( $maximum_age != 0 ) {
		remove_filter( 'posts_where', 'filter_maximum_age' );
	}

	//for testing
	// return '<pre>'. print_r( $miniloop, true ) .'</pre>';

	//begin building the list
	$postlist = '';

	if ( $miniloop->have_posts() ) : $miniloop->the_post();

		$before_items = do_shortcode( miniloops_shortcoder( stripslashes( $before_items ) ) );
		$before_items = apply_filters( 'miniloops_before_items_format', $before_items, $query, $miniloop, $args );
		$postlist .= $before_items;

		$miniloop->rewind_posts();
		while ( $miniloop->have_posts() ) : $miniloop->the_post();

			// $post_format = function_exists('get_post_format') ? get_post_format( get_the_ID() ) : 'standard';
			$post_format = current_theme_supports('post-formats') ? get_post_format( get_the_ID() ) : 'standard';

			$item_format_to_use = apply_filters( 'miniloops_item_format', $item_format, $post_format );

			$item_format_to_use = miniloops_shortcoder( $item_format_to_use );
			$postlist .= str_replace( '%%%%%', '', do_shortcode( $item_format_to_use ) );

		endwhile;

		$after_items = do_shortcode( miniloops_shortcoder( stripslashes( $after_items ) ) );
		$after_items = apply_filters( 'miniloops_after_items_format', $after_items, $query, $miniloop, $args );
		$postlist .= $after_items;

	endif;

	wp_reset_postdata();

	do_action( 'after_the_miniloop', $query );

	return $postlist;
}
function miniloops( $params ) {
	echo get_miniloops( $params );
}

function miniloops_shortcoder( $input ) {
	$input = wp_filter_post_kses( $input );
	//give our shortcodes the correct prefix
	$input = str_replace( '[', '[ml_', $input );
	$input = str_replace( '[/', '[/ml_', $input );
	//make sure we haven't doubled-up
	$input = str_replace( '[ml_ml_', '[ml_', $input );
	$input = str_replace( '[/ml_ml_', '[/ml_', $input );
	$input = str_replace( '[ml_ba_', '[ba_', $input );
	$input = str_replace( '[/ml_ba_', '[/ba_', $input );
	//a hack: 2 shortcodes touching has issues
	//%%%%% is a placeholder to be removed during output
	$input = str_replace( '][', ']%%%%%[', $input );
	$input = stripslashes( $input );
	return $input;
}

function word_excerpt( $input, $limit ) {
	$input = explode( ' ', $input );
	$input = array_splice( $input, 0, $limit );
	return trim( implode( ' ', $input ) );
}

/*

	shortcode for posts and pages

*/
add_shortcode( 'miniloops' , 'get_miniloops_sc');
add_shortcode( 'miniloop' , 'get_miniloops_sc');
function get_miniloops_sc( $atts, $content ) {
	$content = str_replace( '{', '[', str_replace('}', ']', $content ) );
	if ( strpos( $content, '[ml_format' ) !== false )
		$atts['item_format'] = do_shortcode( $content );
	elseif ( ! empty( $content) )
		$atts['item_format'] = $content;
	$args = shortcode_atts( get_miniloops_defaults(), $atts );

	return get_miniloops( $args, false );
}

/*

	shortcodes for use in the Item Format
	(but in fact can be used anywhere)

*/
add_shortcode( 'ml_format' , 'miniloop_item_format' );
function miniloop_item_format( $atts, $content ) {
	extract( shortcode_atts( array(
		'name' => 'ml_format',
	), $atts ) );
	return get_post_meta( get_the_ID(), $name, true );
}

add_shortcode( 'ml_title' , 'miniloop_title' );
function miniloop_title( $atts ) {
	extract( shortcode_atts( array(
		'link' => 0,
		'length' => 0, //characters
		'before' => '',
		'after' => '',
	), $atts ) );
	$title = apply_filters( 'the_title', get_the_title() );

	if ($length)
		$title = substr( $title, 0, $length );

	$title = $before . $title . $after;

	if ( $link ) {
		$link = get_permalink();
		$title = "<a href='$link'>$title</a>";
	}

	return $title;
}

add_shortcode( 'ml_url' , 'miniloop_url' );
function miniloop_url( $atts ) {
	extract( shortcode_atts( array(
		'length' => 0, //characters
		'before' => '',
		'after' => '',
	), $atts ) );
	$link = get_permalink( );

	if ($length)
		$link = substr( $link, 0, $length );

	$link = $before . $link . $after;

	return $link;
}

add_shortcode( 'ml_excerpt' , 'miniloop_excerpt' );
function miniloop_excerpt( $atts ) {
	extract( shortcode_atts( array(
		'length' => 100,
		'wlength' => 0,
		'after' => '...',
		'space_between' => 0,
		'after_link' => 1,
		'custom' => 0,
		'strip_tags' => 1,
		'strip_shortcodes' => 1,
		'up_to_more' => 0,
		'after_with_more' => 1,
	), $atts ) );
	$length = (int) $length;
	$after = esc_attr( $after );
	$space_between = (bool) $space_between;
	$after_link = (bool) $after_link;
	$custom = (bool) $custom;

	$after = '<a href="' . get_permalink( ) . '">' . $after . '</a>';
	if ( $space_between ) $after = ' ' . $after;
	if ( ! $after_link ) $after = trim( strip_tags( $after ) );

	//if using 'custom' excerpts (generated by WP or customized by author)
	if ( $custom )
		return get_the_excerpt() . $after;

	//just get the post content
	$contents = get_the_content();

	$used_more = false;
	if ( $up_to_more ) {
		global $post;
		$contents = explode( '<!--more-->', $post->post_content );
		if ( count( $contents ) > 1 ) $used_more = true;
		$contents = $contents[0];
	}

	//strip it (if needed)
	if ( $strip_shortcodes ) $contents = strip_shortcodes( $contents );
	else $contents = do_shortcode( $contents );
	if ( $strip_tags ) $contents = strip_tags( $contents );

	//back it up, we'll compare the trimmed version to this to see if the '...' is needed
	$ocontent = $contents;

	//trim it
	if ( ! $used_more ) {
		if ($wlength) $content = word_excerpt( $ocontent, $wlength );
		else $content = $length >= 0 ? substr( $ocontent, 0, $length ) : substr( $ocontent, 0 );
	}
	else {
		$content = $contents;
	}

	//if our trimmed content is the same as the original content, we don't need a '...'
	if ( ! $up_to_more || ( $used_more && ! $after_with_more ) ) {
		if ( strlen( $content ) >= strlen( $ocontent ) )  $after = '';
	}

	return $content . $after;
}

add_shortcode( 'ml_content' , 'miniloop_content' );
function miniloop_content( $atts ) {
	extract( shortcode_atts( array(
		'length' => 100,
		'wlength' => 0,
		'after' => '...',
		'space_between' => 0,
		'after_link' => 1,
	), $atts ) );
	$content = apply_filters( 'the_content', get_the_content() );

	return $content;
}

add_shortcode( 'ml_comment_count' , 'miniloop_comment_count' );
function miniloop_comment_count() {
	$count = get_comment_count( get_the_ID() );

	return $count['approved'];
}

add_shortcode( 'ml_author' , 'miniloop_author' );
function miniloop_author() {

	return get_the_author();
}

add_shortcode( 'ml_author_link' , 'miniloop_author_link' );
function miniloop_author_link() {

	return get_author_posts_url( get_the_author_meta('ID') );
}

add_shortcode( 'ml_author_avatar' , 'miniloop_author_avatar' );
function miniloop_author_avatar( $atts ) {
	extract( shortcode_atts( array(
		'size' => 96,
		'default' => '',
		'alt' => false
	), $atts ) );
	// if ( empty( $name ) ) return;

	return get_avatar( get_the_author_meta('ID'), $size, $default, $alt );
}

add_shortcode( 'ml_field' , 'miniloop_field' );
function miniloop_field( $atts ) {
	extract( shortcode_atts( array(
		'name' => '',
		'single' => 1,
		'separator' => ', ',
		'reverse' => 0,
	), $atts ) );
	if ( empty( $name ) ) return;

	if ( $single ) :
		$return = get_post_meta( get_the_ID(), $name, $single );
	else :
		$meta = get_post_meta( get_the_ID(), $name, $single );
		if ( $reverse ) $meta = array_reverse( $meta );
		$return = implode( $separator, $meta );
	endif;

	return $return;

}

add_shortcode( 'ml_category' , 'miniloop_category' );
function miniloop_category( $atts ) {
	$atts = shortcode_atts( array(
		'separator' => ', ',
		'link' => false,
		'justone' => false,
		'reverse' => 0,
		'taxonomy' => 'category',
	), $atts );

	return miniloop_taxonomy( $atts );
}

add_shortcode( 'ml_tag' , 'miniloop_tag' );
function miniloop_tag( $atts ) {
	$atts = shortcode_atts( array(
		'separator' => ', ',
		'link' => false,
		'justone' => false,
		'reverse' => 0,
		'taxonomy' => 'post_tag',
	), $atts );

	return miniloop_taxonomy( $atts );
}

add_shortcode( 'ml_tax' , 'miniloop_taxonomy' );
add_shortcode( 'ml_taxonomy' , 'miniloop_taxonomy' );
function miniloop_taxonomy( $atts ) {
	extract( shortcode_atts( array(
		'taxonomy' => '',
		'separator' => ', ',
		'link' => false,
		'justone' => false,
		'reverse' => 0,
	), $atts ) );
	$terms = wp_get_object_terms( get_the_ID(), $taxonomy );
	$terms_ = array();

	foreach( $terms as $t ) {
		$url = get_term_link( $t->slug, $taxonomy );
		$a = "<a href='$url'>{$t->name}</a>";
		if ( ! $link ) $a = strip_tags( $a );
		$terms_[ $t->term_id ] = $a;
		if ( $justone ) break;
	}
		if ( $reverse ) $terms_ = array_reverse( $terms_ );

	return implode( $separator, $terms_ );
}

// BETA - not fully tested, may not work under some conditions
// 'ba' prefix for 'before/after' - special hackish use
add_shortcode( 'ba_archive' , 'miniloop_archive' );
function miniloop_archive( $atts ) {
	extract( shortcode_atts( array(
		'before' => '<p>',
		'after' => '</p>',
	), $atts ) );

	return "{$before}##replace|archive##{$after}";
}
// For use with the BETA [ba_archive] shortcode
add_filter( 'miniloops_before_items_format', 'ml_hackish_filter', 10, 3 );
add_filter( 'miniloops_after_items_format', 'ml_hackish_filter', 10, 3 );
function ml_hackish_filter( $before_items, $query, $miniloop ) {
	// if there is nothing to replace, carry on...
	if ( strpos( $before_items, '##replace' ) === false ) return $before_items;

	// if the miniloop query is an archive, swap the placeholder for an archive link
	if ( $miniloop->is_archive() ) {
		$obj = $miniloop->get_queried_object();

		if ( is_a( $obj, 'WP_User' ) ) {
			$name = $obj->data->display_name;
			$url = get_author_posts_url( $obj->data->ID );
		} else if ( is_a( $obj, 'stdClass' ) ) { // taxonomy
			$name = $obj->name;
			$url = get_term_link( $name, $obj->taxonomy );
		}
		$before_items = str_replace('##replace|archive##', "<a href='$url'>$name</a>", $before_items );
	}
	else
		$before_items = str_replace('##replace|archive##', '', $before_items );

	return $before_items;
}

add_shortcode( 'ml_date' , 'miniloop_date' );
function miniloop_date( $atts ) {
	extract( shortcode_atts( array(
		'format' => 'F j, Y',
	), $atts ) );
	$format = esc_attr( $format );

	return get_the_date( $format );
}

add_shortcode( 'ml_post_type', 'miniloop_post_type' );
function miniloop_post_type( $atts ) {
	extract( shortcode_atts( array(
		'label' => 'name', //probably 'name' or 'singular_name'. also accepted: add_new, add_new_item, edit_item, new_item, view_item, search_items, not_found, not_found_in_trash, parent_item_colon, all_items, menu_name, name_admin_bar
	), $atts ) );

 	$post_type_obj = get_post_type_object( get_post_type() );
 	$post_type_name = $post_type_obj->labels->$label;
	return $post_type_name;
}

add_shortcode( 'ml_post_type_archive_link', 'miniloop_post_type_archive_link' );
function miniloop_post_type_archive_link( $atts ) {
	extract( shortcode_atts( array(
	), $atts ) );

	$post_archive_url = get_post_type_archive_link( get_post_type() );
	return $post_archive_url;
}

add_shortcode( 'ml_class' , 'miniloop_class' );
function miniloop_class( $atts ) {
	extract( shortcode_atts( array(
		'class' => '',
	), $atts ) );
	$class = esc_attr( $class );
	$classes = get_post_class( $class, get_the_ID() );
	$classes = array_flip( $classes );
	unset( $classes['hentry'] );
	unset( $classes['sticky'] );
	$classes = array_flip( $classes );
	return implode( ' ', $classes );
}

add_shortcode( 'ml_image' , 'miniloop_image' );
function miniloop_image( $atts ) {
	extract( shortcode_atts( array(
		'from' => '',
		'cfname' => '',
		'cfnamealt' => '',
		'class' => '',
		'width' => 50,
		'height' => 50,
		'crop' => 0,
		'fallback' => '',
		'alttext' => '',
		'cache' => '',
	), $atts ) );

	//for testing
	//return "width: $width, height: $height";

	$img = '';
	$upl = wp_upload_dir();
	$from = explode( ',', $from );
	foreach( $from as $from_where ) {
	switch ( $from_where ) {
		case 'thumb' :

			if ( 'clear' == $cache ) {
				delete_post_meta( get_the_ID(), '_ml_thumb_thumb' );
				delete_post_meta( get_the_ID(), '_ml_thumb_thumb_alt' );
			}
			$resized = (array) get_post_meta( get_the_ID(), '_ml_thumb_thumb', true );
			$alt = get_post_meta( get_the_ID(), '_ml_thumb_thumb_alt', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			elseif ( has_post_thumbnail( get_the_ID() ) ) {
				$id = get_post_thumbnail_id( get_the_ID() );
				$src = miniloops_create_thumbnail_from_id( $id, $width, $height );
				$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
				//save to meta
				$resized["{$width}x{$height}"] = $src;
				update_post_meta( get_the_ID(), '_ml_thumb_thumb', $resized );
				update_post_meta( get_the_ID(), '_ml_thumb_thumb_alt', $alt );
			}
			break;

		case 'attached' :

			if ( 'clear' == $cache ) {
				delete_post_meta( get_the_ID(), '_ml_thumb_attached' );
				delete_post_meta( get_the_ID(), '_ml_thumb_attached_alt' );
			}
			$resized = (array) get_post_meta( get_the_ID(), '_ml_thumb_attached', true );
			$alt = get_post_meta( get_the_ID(), '_ml_thumb_attached_alt', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			else {
				$atts = get_posts( array( 'post_parent' => get_the_ID(), 'post_type' => 'attachment', 'orderby' => 'menu_order', 'order' => 'ASC' ) );
				foreach( $atts as $a ) {
					//make sure we don't grab the wrong file type
					if ( wp_attachment_is_image( $a->ID ) ) {
						$src = miniloops_create_thumbnail_from_id( $a->ID, $width, $height );
						$alt = get_post_meta( $a->ID, '_wp_attachment_image_alt', true );
						//save to meta
						$resized["{$width}x{$height}"] = $src;
						break;
					}
				}
				update_post_meta( get_the_ID(), '_ml_thumb_attached', $resized );
				update_post_meta( get_the_ID(), '_ml_thumb_attached_alt', $alt );
			}
			break;

		case 'customfield' :
			if (empty($cfname)) break;

			if ( 'clear' == $cache ) delete_post_meta( get_the_ID(), '_ml_thumb_customfield' );
			$resized = (array) get_post_meta( get_the_ID(), '_ml_thumb_customfield', true );

			if ( ! empty( $cfnamealt ) )
				$alt = get_post_meta( get_the_ID(), $cfnamealt, true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			else {
				$img = get_post_meta( get_the_ID(), $cfname, true );
				if ( ! empty( $img ) ) {
					if ( substr( $img, 0, 4 ) != 'http' ) {
						//if no 'http'
						//assume relative to root
						$img = site_url( $img );
						$file = str_replace( $upl['baseurl'], $upl['basedir'], $img );
						$src = miniloops_create_thumbnail_from_path( $file, $width, $height );
					}
					elseif ( strpos( $img, site_url() ) !== false) {
						//if match for site_url
						$file = str_replace( $upl['baseurl'], $upl['basedir'], $img );
						$src = miniloops_create_thumbnail_from_path( $file, $width, $height );
					}
					else {
						//external
						//todo: real cropping for remote images
						$src = $img;
					}
					$resized["{$width}x{$height}"] = $src;
					update_post_meta( get_the_ID(), '_ml_thumb_customfield', $resized );
				}
			}
			break;

		case 'first' :
		default :

			if ( 'clear' == $cache ) delete_post_meta( get_the_ID(), '_ml_thumb_first' );
			$resized = (array) get_post_meta( get_the_ID(), '_ml_thumb_first', true );
			$alt = get_post_meta( get_the_ID(), '_ml_thumb_first_alt', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			else {
				preg_match('/<img[^>]+>/i', get_the_content(), $match_array );
				$img = count($match_array) > 0 ? $match_array[0] : false;
				if ($img) {
					$img = str_replace("'", '"', $img);
					//locate the first image
					//preg_match_all('/(alt|title|src)=("[^"]*")/i', $img, $img_atts);
					preg_match('/src="([^"]*)"/i', $img, $img_src);
					preg_match('/alt="([^"]*)"/i', $img, $img_alt);

					$img = $img_src[1];
					$alt = isset( $img_alt[1] ) ? $img_alt[1] : '';

					if ( substr( $img, 0, 4 ) != 'http' ) {
						//if no 'http'
						//assume relative to root
						$img = site_url( $img );
						$file = str_replace( $upl['baseurl'], $upl['basedir'], $img );
						$src = miniloops_create_thumbnail_from_path( $file, $width, $height );
					}
					elseif ( strpos( $img, site_url() ) !== false) {
						//if match for site_url
						$file = str_replace( $upl['baseurl'], $upl['basedir'], $img );
						$src = miniloops_create_thumbnail_from_path( $file, $width, $height );
					}
					else {
						//external
						//todo: real cropping for remote images
						$src = $img;
					}
					$resized["{$width}x{$height}"] = $src;
					update_post_meta( get_the_ID(), '_ml_thumb_first', $resized );
					update_post_meta( get_the_ID(), '_ml_thumb_first_alt', $alt );
				}
			}
		break;
	}//end switch
	if ( ! empty( $src ) ) break;
	}//end foreach

	$alt = ! empty( $alt ) ? $alt : $alttext;

	//if the above has resulted in an image
	if ( ! empty( $src ) )
		$img = "<img src='$src' width='$width' height='$height' class='$class' alt='$alt' />";

	//build the fallback
	$fallback = ! empty( $fallback ) ? "<img src='$fallback' alt='$alt' width='$width' height='$height' class='$class' />" : '';

	//if no/empty image, use fallback
	return ( ! $img || empty( $img ) ) ? $fallback : $img;
}

function miniloops_create_thumbnail_from_id( $att_id, $width, $height ) {
	$upl = wp_upload_dir();
	$file = wp_get_attachment_image_src( $att_id, 'fullsize' );
	$file = str_replace( $upl['baseurl'], $upl['basedir'], $file[0] );
	return miniloops_create_thumbnail_from_path( $file, $width, $height );
}
function miniloops_create_thumbnail_from_path( $file, $width, $height ) {
	$upl = wp_upload_dir();
	// deprecated method
	// $new = image_resize( $file, $width, $height, true, "ml-{$width}x{$height}" );
	// if ( is_wp_error( $new ) )
	// 	//if the image could not be resized, return the original url
	// 	return str_replace( $upl['basedir'], $upl['baseurl'], $file );
	// return str_replace( $upl['basedir'], $upl['baseurl'], $new );

	// robbed from the depracted image_resize() function
	$editor = wp_get_image_editor( $file );
	if ( is_wp_error( $editor ) )
		// return $editor;
		return str_replace( $upl['basedir'], $upl['baseurl'], $file );
	$editor->set_quality( 90 );

	$resized = $editor->resize( $width, $height, true );
	if ( is_wp_error( $resized ) )
		// return $resized;
		return str_replace( $upl['basedir'], $upl['baseurl'], $file );

	$dest_file = $editor->generate_filename( "ml-{$width}x{$height}", null );
	$saved = $editor->save( $dest_file );

	if ( is_wp_error( $saved ) )
		// return $saved;
		return str_replace( $upl['basedir'], $upl['baseurl'], $file );

	return str_replace( $upl['basedir'], $upl['baseurl'], $dest_file );

}