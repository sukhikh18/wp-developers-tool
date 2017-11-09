<?php

namespace CDevelopers\tool;

class DTools_Page
{
    function __construct()
    {
        $page = new WP_Admin_Page( DTools::OPTION );
        $page->set_args( apply_filters( 'dtools_page_args', array(
            'parent' => 'options-general.php',
            'title' => __('Advanced settings', DOMAIN),
            'menu' => __('Advance', DOMAIN),
            'permissions' => 'manage_options',
            'tab_sections' => array(
                DTools::PREFIX . 'general' => __('Main', DOMAIN),
                DTools::PREFIX . 'scripts' => __('Scripts', DOMAIN),
            ),
            'callback' => array(
                DTools::PREFIX . 'general' => array(__CLASS__, 'general_settings_tab'),
                DTools::PREFIX . 'scripts' => array(__CLASS__, 'scripts_settings_tab'),
            ),
            'validate'    => array(__CLASS__, 'validate_options'),
            'columns'     => 1,
            ) ) );

        $page->set_assets( array($this, '_assets') );

        add_action($page->page . '_after_form_inputs', 'submit_button' );
    }

    function _assets()
    {
        wp_enqueue_script( DTools::PREFIX . 'admin_js', Dtools::get_plugin_url('assets/admin.js'),
            array('jquery'), false, true );
    }

    static function general_settings_tab()
    {
        echo (new WP_Admin_Forms( DTools::get_settings('general.php'), true ))->render();
    }

    static function scripts_settings_tab(){
        echo (new WP_Admin_Forms( DTools::get_settings('scripts.php'), true ))->render();
    }

    static function validate_options( $inputs ){
        // $inputs = array_map_recursive( 'sanitize_text_field', $inputs );
        $inputs = WP_Admin_Page::array_filter_recursive($inputs);

        return $inputs;
    }
}
new DTools_Page();
