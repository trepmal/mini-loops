<?php
/*
Plugin Name: Mini Loops
Plugin URI: http://trepmal.com/plugins/mini-loops/
Description: Query posts and display them where you want
Version: 0.1
Author: Kailey Lampert
Author URI: http://kaileylampert.com
*/

global $allowedposttags;
$allowedposttags['ol']['style'] = array();
//die( print_r($allowedposttags,true));
add_filter( 'safe_style_css', 'more_safe_css');
function more_safe_css( $attr ) {
	$attr[] = 'list-style-type';
	return $attr;
}

//Example for customizing the output based on post format
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
			//$item_format = '<li class="[class]">standard: <a href="[url]">[title]</a></li>';
		default :
	}
	return $item_format;
}

//add_filter( 'the_title', 'testing_title_filter');
function testing_title_filter( $input ) {
	return $input.'!';
}

add_action( 'widgets_init', 'miniloops_load' );
function miniloops_load() { 
	register_widget( 'miniloops' );
}

include_once('widget.php');

include_once('helpers.php');