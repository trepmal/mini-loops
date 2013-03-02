<?php
if ( ! defined( 'ABSPATH' ) ) die( '-1' );
/*
Plugin Name: Mini Loops
Plugin URI: http://trepmal.com/plugins/mini-loops/
Description: Query posts and display them where you want
Version: 1.2
Author: Kailey Lampert
Author URI: http://kaileylampert.com

Copyright (C) 2011-13 Kailey Lampert

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
	certain attributes may be filtered out upon saving
	declare them as safe here
	of course, you should move any of your changes to another
	files so future updates don't overwrite your changes
*/
//add_filter( 'safe_style_css', 'miniloops_more_safe_css');
function miniloops_more_safe_css( $attr ) {
	$attr[] = 'list-style-type';
	return $attr;
}

/*
	Example for customizing the output based on post format
*/
//add_filter( 'miniloops_item_format', 'miniloops_post_formats', 10, 2 );
function miniloops_post_formats( $item_format, $post_format ) {
	switch ( $post_format ) {
		case 'aside' :
			$item_format = '<li class="[class]">aside: <a href="[url]">[title]</a></li>';
			break;
		case 'gallery' :
			$item_format = '<li class="[class]">gallery: <a href="[url]">[title]</a></li>';
			break;
		case 'link' :
			$item_format = '<li class="[class]">link: <a href="[url]">[title]</a></li>';
			break;
		case 'image' :
			$item_format = '<li class="[class]">image: <a href="[url]">[title]</a></li>';
			break;
		case 'quote' :
			$item_format = '<li class="[class]">quote: <a href="[url]">[title]</a></li>';
			break;
		case 'status' :
			$item_format = '<li class="[class]">status: <a href="[url]">[title]</a></li>';
			break;
		case 'video' :
			$item_format = '<li class="[class]">video: <a href="[url]">[title]</a></li>';
			break;
		case 'audio' :
			$item_format = '<li class="[class]">audio: <a href="[url]">[title]</a></li>';
			break;
		case 'chat' :
			$item_format = '<li class="[class]">chat: <a href="[url]">[title]</a></li>';
			break;
		case 'standard' :
		case false :
			/*
				the standard format should be what you put in the widget
				but you could change it here if you really wanted
			*/
			//$item_format = '<li class="[class]">standard: <a href="[url]">[title]</a></li>';
		default :
	}
	return $item_format;
}
/*
	Demo of the 'miniloops_after_items_format' filter hook
	Adds link to custom post type archives (where applicable)
	
	Same usage for the 'miniloops_before_items_format' filter hook
*/
//add_filter( 'miniloops_after_items_format' , 'miniloops_after_items_format_filter_test', 10, 2 );
function miniloops_after_items_format_filter_test( $after, $query ) {
	$post_type = $query['post_type'];
	$post_type_obj = get_post_type_object( $post_type );
	$post_type_name = $post_type_obj->labels->name;
	$post_archive_url = get_post_type_archive_link( $post_type );
	
	$archive_link = "<p><a href='$post_archive_url'>More $post_type_name &raquo;</a></p>";
	return $after . $archive_link;
}

/*
	Modify the query args before they are run
	Can either work on all instances, or only some by checking existing args
	
	This demo changes the 'tag__in' query to 'tag__and' to limit the number of matching posts
*/
//add_filter( 'miniloops_query' , 'miniloops_query_filter_test' );
function miniloops_query_filter_test( $query ) {
	if ( $query['tag__in'] == array( 53, 82 ) ) {
		$query['tag__and'] = $query['tag__in'];
		unset( $query['tag__in'] );
	}
	return $query;
}


add_action( 'widgets_init', 'miniloops_load' );
function miniloops_load() {
	register_widget( 'miniloops' );
	register_widget( 'miniminiloops' );
}
load_plugin_textdomain( 'mini-loops', false, dirname( plugin_basename( __FILE__ ) ) .  '/lang' );

include_once( dirname( __FILE__) .'/widget.php');

include_once( dirname( __FILE__) .'/helpers.php');
