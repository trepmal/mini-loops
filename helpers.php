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
					'exclude_sticky' => 0,
					'exclude_current' => 1,
					'current_category' => 0,
					'current_single_category' => 0,
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
	if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) $order = 'DESC';
	$reverse_order = (bool) $reverse_order;
	$ignore_sticky = (bool) $ignore_sticky;
	$only_sticky = (bool) $only_sticky;
	$exclude_sticky = (bool) $exclude_sticky;
	$exclude_current = (bool) $exclude_current;
	$current_category = (bool) $current_category;
	$current_single_category = (bool) $current_single_category;
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
	if ($exclude_sticky) {
		$exclude = array_merge( $exclude, get_option('sticky_posts') );
	}

	parse_str($tax, $taxes);
	$tax_query = array();
	foreach( array_keys( $taxes ) as $k => $slug ) {
		//original code is this single commented line, if you need to revert
		//$tax_query[] = array( 'taxonomy' => $slug, 'field' => 'id', 'terms' => explode(',',$taxes[ $slug ]) );

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
	$before_items = do_shortcode( miniloops_shortcoder( stripslashes( $before_items ) ) );
	$before_items = apply_filters( 'miniloops_before_items_format', $before_items, $query );
	$postlist .= $before_items;

	while ( $miniloop->have_posts() ) : $miniloop->the_post();

    	$post_format = function_exists('get_post_format') ? get_post_format( get_the_ID() ) : 'standard';

		$item_format_to_use = apply_filters( 'miniloops_item_format', $item_format, $post_format );

		$item_format_to_use = miniloops_shortcoder( $item_format_to_use );
		$postlist .= str_replace( '%%%%%', '', do_shortcode( $item_format_to_use ) );

	endwhile;

	wp_reset_query();

	$after_items = do_shortcode( miniloops_shortcoder( stripslashes( $after_items ) ) );
	$after_items = apply_filters( 'miniloops_after_items_format', $after_items, $query );
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
function miniloop_title() {
	return apply_filters( 'the_title', get_the_title() );
}

add_shortcode( 'ml_url' , 'miniloop_url' );
function miniloop_url() {
	return get_permalink( get_the_ID() );
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
	$atts = shortcode_atts(array(
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
	$atts = shortcode_atts(array(
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

add_shortcode( 'ml_date' , 'miniloop_date' );
function miniloop_date( $atts ) {
	extract( shortcode_atts( array(
		'format' => 'F j, Y',
	), $atts ) );
	$format = esc_attr( $format );

	return get_the_date( $format );
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
		'class' => '',
		'width' => 50,
		'height' => 50,
		'crop' => 0,
		'fallback' => '',
		'cache' => '',
	), $atts ) );

	//for testing
	//return "width: $width, height: $height";

	$img = '';
	$upl = wp_upload_dir();
	switch ( $from ) {
		case 'thumb' :

			if ( 'clear' == $cache ) delete_post_meta( get_the_ID(), '_ml_thumb_thumb' );
			$resized = (array) get_post_meta( get_the_ID(), '_ml_thumb_thumb', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			elseif ( has_post_thumbnail( get_the_ID() ) ) {
				$id = get_post_thumbnail_id( get_the_ID() );
				$src = miniloops_create_thumbnail_from_id( $id, $width, $height );
				//save to meta
				$resized["{$width}x{$height}"] = $src;
				update_post_meta( get_the_ID(), '_ml_thumb_thumb', $resized );
			}
			break;

		case 'attached' :

			if ( 'clear' == $cache ) delete_post_meta( get_the_ID(), '_ml_thumb_attached' );
			$resized = (array) get_post_meta( get_the_ID(), '_ml_thumb_attached', true );

			if ( isset( $resized["{$width}x{$height}"] ) ) {
				//thumb exists
				$src = $resized["{$width}x{$height}"];
			}
			else {
				$atts = get_children( array( 'post_parent' => get_the_ID(), 'post_type' => 'attachment' ) );
				foreach( $atts as $a ) {
					//make sure we don't grab the wrong file type
					if ( strpos( $a->post_mime_type, 'image/' ) !== false ) {
						$src = miniloops_create_thumbnail_from_id( $a->ID, $width, $height );
						//save to meta
						$resized["{$width}x{$height}"] = $src;
						update_post_meta( get_the_ID(), '_ml_thumb_attached', $resized );
					}
				}
			}
			break;

		case 'customfield' :
			if (empty($cfname)) break;

			if ( 'clear' == $cache ) delete_post_meta( get_the_ID(), '_ml_thumb_customfield' );
			$resized = (array) get_post_meta( get_the_ID(), '_ml_thumb_customfield', true );

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
					preg_match('/src="([^"]*)"/i', $img, $img_atts);
					$img = $img_atts[1];

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
				}
			}
		break;
	}
	if ( ! empty( $src ) )
		$img = "<img src='$src' width='$width' height='$height' class='$class' alt= '' />";

	$fallback = ! empty( $fallback ) ? "<img src='$fallback' alt='' width='$width' height='$height' class='$class' />" : '';
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
	$new = image_resize( $file, $width, $height, true, "ml-{$width}x{$height}" );
	if ( is_wp_error( $new ) ) 
		//if the image could not be resized, return the original url
		return str_replace( $upl['basedir'], $upl['baseurl'], $file );
	return str_replace( $upl['basedir'], $upl['baseurl'], $new );
}
