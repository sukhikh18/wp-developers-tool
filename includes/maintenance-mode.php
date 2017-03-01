<?php
add_action('get_header', 'wp_maintenance_mode', 1);
function wp_maintenance_mode(){
	if(!current_user_can('edit_themes') || !is_user_logged_in()){
		$msg = get_option(DT_PLUGIN_NAME);
		wp_die('<h2>'.$msg['maintenance-mode'].'</h2>');
	}
}