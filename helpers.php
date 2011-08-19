<?php
// Show recent posts function
function get_miniloops_defaults() {
	$defs = array( 	'title' => __( 'Recent Posts', 'mini-loops' ),
					'hide_title' => 0,
					'title_url' => '',
					'number_posts' => 3,
					'post_offset' => 0,
					'post_type' => 'post',
					'post_status' => 'publish',
					'order_by' => 'date',
					'order' => 'DESC',
					'reverse_order' => 0,
					'shuffle_order' => 0,
					'ignore_sticky' => 1,
					'only_sticky' => 0,
					'exclude_current' => 1,
					'categories' => '',
					'tags' => '',
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
	$post_offset = (int) $post_offset;
	$post_type = esc_attr( $post_type );
	$post_status = esc_attr( $post_status );
	$order_by = esc_attr( $order_by );
	$order = esc_attr( $order );
	if (!in_array( $order, array( 'ASC', 'DESC' ) ) ) $order = 'DESC';
	$reverse_order = (bool) $reverse_order;
	$ignore_sticky = (bool) $ignore_sticky;
	$only_sticky = (bool) $only_sticky;
	$exclude_current = (bool) $exclude_current;
	$categories = esc_attr( $categories );
	$tags = esc_attr( $tags );
	$tax = str_replace('&amp;', '&', esc_attr( $tax ) );
	$custom_fields = str_replace('&amp;', '&', esc_attr( $custom_fields ) );
	$exclude = explode( ',', esc_attr( $exclude ) );
	$before_items = stripslashes( wp_filter_post_kses( $before_items ) );
	$after_items = wp_filter_post_kses( $after_items );
	//$item_format //this is escaped in the loop so that the filter can be applied

	if (is_single() && $exclude_current) {
		$exclude[] = $post->ID;
	}

	parse_str($tax, $taxes);
	$tax_query = array();
	foreach( array_keys( $taxes ) as $k => $slug ) {
		$tax_query[] = array( 'taxonomy' => $slug, 'field' => 'id', 'terms' => explode(',',$taxes[ $slug ]) );
	}

	parse_str($custom_fields, $meta_fields);
	$meta_query = array();
	foreach( $meta_fields as $k => $v ) {
		$meta_query[] = array( 'key' => $k, 'value' => $v, 'compare' => '=' );
	}

	$query = array(
		'cat' => $categories,
		'tag_id' => $tags,
		'tax_query' => $tax_query,
		'meta_query' => $meta_query,
		'posts_per_page' => $number_posts,
		'ignore_sticky_posts' => $ignore_sticky,
		'post__in' => ( $only_sticky ? get_option('sticky_posts') : '' ),
		'post_type' => $post_type,
		'post_status' => $post_status,
		'offset' => $post_offset,
		'orderby' => $order_by,
		'order' => $order,
		'post__not_in' => $exclude,
	);

	//for testing
	//return '<pre>'. print_r( $query, true ) .'</pre>';

	//run the query
	$miniloop = new WP_Query( $query );
	if ( $reverse_order ) $miniloop->posts = array_reverse( $miniloop->posts );
	if ( $shuffle_order ) shuffle( $miniloop->posts );

	//begin building the list
	$postlist = '';

	$postlist .= $before_items;

	while ( $miniloop->have_posts() ) : $miniloop->the_post();

    $post_format = function_exists('get_post_format') ? get_post_format( get_the_ID() ) : 'standard';

		$item_format_to_use = apply_filters( 'miniloops_item_format', $item_format, $post_format );

		$item_format_to_use = miniloops_shortcoder( $item_format_to_use );
		$postlist .= str_replace( '%%%%%', '', do_shortcode( $item_format_to_use ) );

	endwhile;

	wp_reset_query();

	$postlist .= $after_items;

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
add_shortcode( 'miniloop' , 'get_miniloops_sc');
function get_miniloops_sc( $atts, $content ) {
	$content = str_replace( '{', '[', str_replace('}', ']', $content ) );
	$defaults = get_miniloops_defaults();
	$atts['item_format'] = $content;
	$args = shortcode_atts($defaults, $atts );
	return get_miniloops( $args, false );

}

/*

	shortcodes for use in the Item Format
	(but in fact can be used anywhere)

*/
add_shortcode( 'ml_title' , 'miniloop_title' );
function miniloop_title() {
	global $post;
	return apply_filters( 'the_title', $post->post_title );
}

add_shortcode( 'ml_url' , 'miniloop_url' );
function miniloop_url() {
	global $post;
	return get_permalink( $post->ID );
}

add_shortcode( 'ml_excerpt' , 'miniloop_excerpt' );
function miniloop_excerpt( $atts ) {
	global $post;
	extract(shortcode_atts(array(
		'length' => 100,
		'wlength' => 0,
		'after' => '...',
		'space_between' => 0,
		'after_link' => 1,
	), $atts));
	$length = (int) $length;
	$after = esc_attr( $after );
	$after_link = (bool) $after_link;

	$ocontent = strip_tags( strip_shortcodes( get_the_content() ) );
	if ($wlength) $content = word_excerpt( $ocontent, $wlength );
	else $content = substr( $ocontent, 0, $length );

	$after = '<a href="' . get_permalink( $post->ID ) . '">' . $after . '</a>';
	if ($space_between) $after = ' '.$after;
	if (!$after_link) $after = trim( strip_tags( $after ) );
	if ( strlen( $content ) >= strlen( $ocontent ) )  $after = '';

	return $content . $after;
}

add_shortcode( 'ml_content' , 'miniloop_content' );
function miniloop_content( $atts ) {
	global $post;
	extract(shortcode_atts(array(
		'length' => 100,
		'wlength' => 0,
		'after' => '...',
		'space_between' => 0,
		'after_link' => 1,
	), $atts));
	$content = apply_filters( 'the_content', get_the_content() );

	return $content;
}

add_shortcode( 'ml_comment_count' , 'miniloop_comment_count' );
function miniloop_comment_count() {
	global $post;
	$count = get_comment_count( $post->ID );

	return $count['approved'];
}

add_shortcode( 'ml_author' , 'miniloop_author' );
function miniloop_author() {
	global $post;

	return get_userdata( $post->post_author )->display_name;
}

add_shortcode( 'ml_author_link' , 'miniloop_author_link' );
function miniloop_author_link() {
	global $post;

	return get_author_posts_url( $post->post_author );
}

add_shortcode( 'ml_date' , 'miniloop_date' );
function miniloop_date( $atts ) {
	global $post;
	extract(shortcode_atts(array(
		'format' => 'F j, Y',
	), $atts));
	$format = esc_attr( $format );

	return date_i18n( $format, strtotime($post->post_date) );
}

add_shortcode( 'ml_class' , 'miniloop_class' );
function miniloop_class( $atts ) {
	global $post;
	extract(shortcode_atts(array(
		'class' => '',
	), $atts));
	$class = esc_attr( $class );
	$classes = get_post_class( $class, $post->ID);
	$classes = array_flip( $classes );
	unset( $classes['hentry'] );
	unset( $classes['sticky'] );
	$classes = array_flip( $classes );
	return implode( ' ', $classes );
}

add_shortcode( 'ml_image' , 'miniloop_image' );
function miniloop_image( $atts ) {
	global $post;
	extract(shortcode_atts(array(
		'from' => '',
		'cfname' => '',
		'class' => '',
		'width' => 50,
		'height' => 50,
		'crop' => 0,
		'fallback' => '',
		'cache' => '',
	), $atts));

	//for testing
	//return "width: $width, height: $height";

	$img = '';
	switch ($from) {
		case 'thumb' :

			if ( 'clear' == $cache ) delete_post_meta( $post->ID, '_ml_thumb_thumb' );
			$resized = (array) get_post_meta( $post->ID, '_ml_thumb_thumb', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			elseif ( has_post_thumbnail( $post->ID ) ) {
				$id = get_post_thumbnail_id( $post->ID );
				$src = miniloops_create_thumbnail_from_id( $id, $width, $height );
				//save to meta
				$resized["{$width}x{$height}"] = $src;
				update_post_meta( $post->ID, '_ml_thumb_thumb', $resized );
			}
			if ( ! empty( $src ) )
				$img = "<img src='$src' width='$width' height='$height' class='$class' />";
			break;

		case 'attached' :

			if ( 'clear' == $cache ) delete_post_meta( $post->ID, '_ml_thumb_attached' );
			$resized = (array) get_post_meta( $post->ID, '_ml_thumb_attached', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			else {
				$atts = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment' ) );
				foreach( $atts as $a ) {
					//make sure we don't grab the wrong file type
					if ( strpos( $a->post_mime_type, 'image/' ) !== false ) {
						$src = miniloops_create_thumbnail_from_id( $a->ID, $width, $height );
						//save to meta
						$resized["{$width}x{$height}"] = $src;
						update_post_meta( $post->ID, '_ml_thumb_attached', $resized );
					}
				}
			}
			if ( ! empty( $src ) )
				$img = "<img src='$src' width='$width' height='$height' class='$class' />";
			break;

		case 'customfield' :
			if (empty($cfname)) break;

			if ( 'clear' == $cache ) delete_post_meta( $post->ID, '_ml_thumb_customfield' );
			$resized = (array) get_post_meta( $post->ID, '_ml_thumb_customfield', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			else {
				$img = get_post_meta( $post->ID, $cfname, true );
				if ( ! empty( $img ) ) {
					if ( substr( $img, 0, 4 ) != 'http' ) {
						//if no 'http'
						//assume relative to root
						$img = site_url( $img );
						$file = str_replace( content_url(), WP_CONTENT_DIR, $img );
						$src = miniloops_create_thumbnail_from_path( $file, $width, $height );
					}
					elseif ( strpos( $img, site_url() ) !== false) {
						//if match for site_url
						$file = str_replace( content_url(), WP_CONTENT_DIR, $img );
						$src = miniloops_create_thumbnail_from_path( $file, $width, $height );
					}
					else {
						//external
						//todo: real cropping for remote images
						$src = $img;
					}
					$resized["{$width}x{$height}"] = $src;
					update_post_meta( $post->ID, '_ml_thumb_customfield', $resized );
				}
			}
			if ( ! empty( $src ) )
				$img = "<img src='$src' width='$width' height='$height' class='$class' />";

			break;

		case 'first' :
		default :

			if ( 'clear' == $cache ) delete_post_meta( $post->ID, '_ml_thumb_first' );
			$resized = (array) get_post_meta( $post->ID, '_ml_thumb_first', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			else {
				preg_match('/<img[^>]+>/i', $post->post_content, $match_array );
				$img = count($match_array) > 0 ? $match_array[0] : false;
				if ($img) {
					$img = str_replace("'", '"', $img);
					//locate the first image
					//preg_match_all('/(alt|title|src)=("[^"]*")/i', $img, $img_atts);
					preg_match('/src="([^"]*)"/i', $img, $img_atts);
					$img = $img_atts[1];

					if ( substr( $img, 0, 4 ) != 'http' ) {
						//if no 'http'
						//assume relative to root
						$img = site_url( $img );
						$file = str_replace( content_url(), WP_CONTENT_DIR, $img );
						$src = miniloops_create_thumbnail_from_path( $file, $width, $height );
					}
					elseif ( strpos( $img, site_url() ) !== false) {
						//if match for site_url
						$file = str_replace( content_url(), WP_CONTENT_DIR, $img );
						$src = miniloops_create_thumbnail_from_path( $file, $width, $height );
					}
					else {
						//external
						//todo: real cropping for remote images
						$src = $img;
					}
					$resized["{$width}x{$height}"] = $src;
					update_post_meta( $post->ID, '_ml_thumb_first', $resized );
				}
			}
			if ( ! empty( $src ) )
				$img = "<img src='$src' width='$width' height='$height' class='$class' />";

		break;
	}

	$fallback = !empty($fallback) ? "<img src='$fallback' alt='' width='$width' height='$height' class='$class' />" : '';
	return (!$img || empty($img)) ? $fallback : $img;
}

function miniloops_create_thumbnail_from_id( $att_id, $width, $height ) {
	$file = wp_get_attachment_image_src( $att_id, 'fullsize' );
	$file = str_replace( content_url(), WP_CONTENT_DIR, $file[0] );
	return miniloops_create_thumbnail_from_path( $file, $width, $height );
}
function miniloops_create_thumbnail_from_path( $file, $width, $height ) {
	$new = image_resize( $file, $width, $height, true, "ml-{$width}x{$height}" );
	return str_replace( WP_CONTENT_DIR, content_url(), $new );
}
