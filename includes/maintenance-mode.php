<?php
namespace DTools;

add_action('get_header', 'DTools\wp_maintenance_mode', 1);
function wp_maintenance_mode(){
	if(!current_user_can('edit_themes') || !is_user_logged_in())
		wp_die("<h2>" . DevelopersTools::$settings['maintenance-mode'] . "</h2>");
}