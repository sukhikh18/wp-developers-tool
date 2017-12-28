<?php
/**
 * Не пускать не авторизированных админов
 */

namespace CDevelopers\tool;

add_action('get_header', __NAMESPACE__ . '\wp_maintenance_mode', 1);
function wp_maintenance_mode(){
	if( ! current_user_can('edit_themes') || ! is_user_logged_in() ) {
		wp_die( sprintf("<h2>%s</h2>", DTools::get( 'maintenance-mode' )) );
    }
}