<?php

class Likeit_Widget extends WP_Widget {
	function Likeit_Widget() {
		
		$widget_options = array(
			'description' => __('A widget to show the most liked post (Like-it)')
		);
		
		parent::WP_Widget(false, $name = __('Most liked posts'), $widget_options);
	}

	function form($instance) {
		$defaults = array( 'title' => __('Most liked posts'), 'displayed' => '10' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		require('tpl/widget-config.php');
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['displayed'] = strip_tags($new_instance['displayed']);
		return $instance;
	}

	function widget($args, $instance) {
		global $wpdb, $likeit_table;
		
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;
			
		$posts = $wpdb->get_results("SELECT post_id, COUNT(post_id) as likes FROM $likeit_table GROUP BY post_id ORDER BY COUNT(post_id) DESC LIMIT {$instance['displayed']}");
		
		require_once('tpl/widget.php');

		echo $after_widget;
		
	}
}
add_action('widgets_init', create_function('', 'return register_widget("Likeit_Widget");'));