<?php
/**
 * Plugin Name: Feeder MB
 * Plugin URI: http://www.mortgagebrokerapp.com.au
 * Description: A plugin that will enable mortgagebrokerapp.com.au client to feed rss live from mortgagebrokerapp.com.au.
 * Version: 1.0.1
 * Author: Arung Isyadi
 * Author URI: https://www.odesk.com/users/~019c027c426b2c4d50
 * License: GPL2
	
	Copyright 2012  Arung Isyadi  (email : arungisyadi@outlook.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include('mb-options.php');

//enable shorcode to used in widget "text"
add_filter('widget_text', 'do_shortcode');

//registering styles
function mb_styles(){
    wp_register_style('mb_basic', plugins_url('/css/style.css', __FILE__), array(), '1.0', 'all');
    wp_enqueue_style('mb_basic'); // Enqueue it!
}
add_action('wp_enqueue_scripts', 'mb_styles');

add_shortcode('list_feeds', 'list_feeds');

function list_feeds($atts, $content = null){
	$maxnum = get_option('mb_max_num');
	
	if(empty($maxnum)){
		$maxnum = 5;
	}
		
	//call for feed functions inside WP
	include_once( ABSPATH . WPINC . '/feed.php' );
	
	$source = 'http://www.mortgagebrokerapp.com.au/feed/?feedkey=';
	
	$unique = get_option('mb_unique_id');
	
	$feed_url = $source . $unique;
	
	//pull the rss feed
	$mb_rss = fetch_feed( $feed_url );
	$maxitems = $mb_rss->get_item_quantity( $maxnum );
	
	$output .= '<ul id="mb_list">';
	
	for($x = 0; $x <= $maxnum-1; $x++){
		//add_filter( 'wp_feed_cache_transient_lifetime', create_function('$a', 'return '. ($cachetime * 20).';' ));
		$item = $mb_rss->get_item( $x );
		if($item && $item->get_title() != ''){
			$output .= '<li>';
			$output .= '<h1>' . $item->get_title() . '</h1>';
			$output .= '<p>' . substr(strip_tags($item->get_content(), '<a><br><img>'), 0 , 260) . ' [...]</p>';
			$opt_more = get_option('mb_read_more');
			if(empty($opt_more)){
				$readmore = '<a href="' . get_option('siteurl') . '/feed-article/?id=' . $x . '" target="_blank">Read More</a>';
			}else{
				$readmore = '<a href="' . get_option('siteurl') . '/feed-article/?id=' . $x . '" target="_blank">' . $opt_more .'</a>';
			}
			$output .= '<p>' . $readmore . '</p>';
			$output .= '</li>';
		}
	}
	
	$output .= '</ul>';
	//$output .= 'You chose maximum: <strong>' . $feed_url . ' feeds</strong>';
	
	return $output;
	
}

add_shortcode('feed_article', 'feed_article');

function feed_article($atts, $content = null){
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		
		//call for feed functions inside WP
		include_once( ABSPATH . WPINC . '/feed.php' );
		
		$source = 'http://www.mortgagebrokerapp.com.au/feed/?feedkey=';
		
		$unique = get_option('mb_unique_id');
		
		$feed_url = $source . $unique;
		
		//pull the rss feed
		$mb_rss = fetch_feed( $feed_url );
		
		$maxitems = $mb_rss->get_item_quantity( 1 );
		
		$item = $mb_rss->get_item( $id );
		
		if($item && $item->get_title() != ''){
			$output .= '<h1>' . $item->get_title() . '</h1>';
			$output .= $item->get_content();
		}else{
			$output = 'No Article found under that id';
		}
		
	}else{
		$output = '<h1><strong>You cannot access this page directly!!! Access this page from RSS FEED List!!!</strong></h1>';
	}
	
	return $output;
}

if ( is_admin() ) {
	add_action( 'admin_init', 'admin_init' );
}

function admin_init(){
	$permalink = get_option('siteurl') . '/feed-article/';
	$post_id = url_to_postid( $permalink );
	
	global $wpdb;
	$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
	//print_r($post_exists);
	
	if(!$post_exists){
		// Create post object
		$my_post = array(
		  'post_title'    => 'Feed Article',
		  'post_content'  => '[feed_article]',
		  'post_name' => 'feed-article',
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_type' => 'page'
		  //'post_category' => array(8,39)
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );	
	}else{
		$my_post = array(
			  'ID'           => $post_id,
			  'post_content' => '[feed_article]'
		);
		wp_update_post( $my_post );
	}
}

?>