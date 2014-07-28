<?php
/*
Plugin Name: RSS Reviews
Plugin URI: https://github.com/PearSea/RSS-Reviews
Description: Use this plugin to receive your latests reviews from sites like TripAdvisor.
Version: 1.2 
Author: Gregory Pearcey
Author URI: http://gregorypearcey.com/
License: Creative Commons Attribution-ShareAlike 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

//Add the stylesheet into the header
wp_enqueue_style("rss.reviews",WP_PLUGIN_URL."/rss-reviews/css/responsiveslides.css");

//Add the scripts in the footer
wp_enqueue_script("jquery");

wp_enqueue_script(
"rss.reviews", WP_PLUGIN_URL."/rss-reviews/js/responsiveslides.js",
array("jquery"), "1.3.1",1);

wp_enqueue_script(
"rss.reviewssetup", WP_PLUGIN_URL."/rss-reviews/js/rss-reviews.js",
array("jquery","rss.reviews"), "",1);

// Add Widget area
class rss_reviews extends WP_Widget {

	// constructor
	function rss_reviews() {
        parent::WP_Widget(false, $name = __('RSS Reviews', 'rss_reviews') );
    }

	// widget form creation
	function form($instance) {
	
	// Check values
	if( $instance) {
	     $title = esc_attr($instance['title']);
	     $text = esc_attr($instance['text']);
	     $amount = esc_textarea($instance['amount']);
	} else {
	     $title = '';
	     $text = '';
	     $amount = '';
	}
	?>
	
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'rss_reviews'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	
	<p>
	<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('RSS Feed URL:', 'rss_reviews'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
	</p>
	
	<p>
	<label for="<?php echo $this->get_field_id('amount'); ?>"><?php _e('Amount of Reviews:', 'rss_reviews'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('amount'); ?>" name="<?php echo $this->get_field_name('amount'); ?>" type="text" value="<?php echo $amount; ?>" />
	</p>
	<?php
	}

	// update widget
	function update($new_instance, $old_instance) {
	      $instance = $old_instance;
	      // Fields
	      $instance['title'] = strip_tags($new_instance['title']);
	      $instance['text'] = strip_tags($new_instance['text']);
	      $instance['amount'] = strip_tags($new_instance['amount']);
	     return $instance;
	}

// display widget front end
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   $text = $instance['text'];
	   $amount = $instance['amount'];
	   echo $before_widget;
	   
	   if(function_exists('fetch_feed')) {
		// fetch feed items
		$rss = fetch_feed($text);
		if(!is_wp_error($rss)) : // error check
			$maxitems = $rss->get_item_quantity($amount); // number of items
			$rss_items = $rss->get_items(0, $maxitems);
		endif;   
	   
	   // Display the widget
	   echo '<div id="slider" class="widget-text wp_widget_plugin_box">';
	
	   // Check if title is set
	   if ( $title ) {
	      echo $before_title . $title . $after_title;
	   }
	   
	   echo '<ul class="rslides">';
	   if($maxitems == 0) echo '<p>Feed not available.</p>'; // if empty
	   else foreach ($rss_items as $item) : 

		echo '<li>'.$item->get_description().'</li>';
	
		endforeach; 
		echo '</ul>';
		echo '</div>';
	} 
	   echo $after_widget;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("rss_reviews");'));

//Short Code
function rssreviews($atts, $content=null){  
  
    extract(shortcode_atts( array('id' => ''), $atts));  
	   
	   if(function_exists('fetch_feed')) {
		// fetch feed items
		$rss = fetch_feed($id);
		if(!is_wp_error($rss)) : // error check
			$maxitems = $rss->get_item_quantity($amount); // number of items
			$rss_items = $rss->get_items(0, $maxitems);
		endif;
	   
	   if($maxitems == 0){
			return '<p>Feed not available.</p>'; // if empty
	   } else {
		   
		   $middle = '';
		   foreach ($rss_items as $item){ 
			   $middle .= '<li>
			   <a href="'.$item->get_permalink().'"title="'.$item->get_date('j F Y @ g:i a').'"><h3>'.$item->get_title().'</h3></a>'
			   .$item->get_description().'</li><hr>';
			}
			$returnfeed = '<ul class="nobullets">'.$middle.'</ul>';
		return $returnfeed;				 
	   }
		
	}  
  
}  
add_shortcode('rssreviews', 'rssreviews');


?>