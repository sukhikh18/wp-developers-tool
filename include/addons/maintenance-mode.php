<?php
/**
 * Не пускать не авторизированных админов
 */

namespace NikolayS93\Tools;

add_action('get_header', __NAMESPACE__ . '\wp_maintenance_mode', 1);
function wp_maintenance_mode(){
	if( ! current_user_can('edit_themes') || ! is_user_logged_in() ) {
		wp_die( sprintf("<h2>%s</h2>", Utils::get( 'maintenance-mode' )) );
    }
}