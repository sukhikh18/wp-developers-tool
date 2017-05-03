<?php
if ( ! defined( 'ABSPATH' ) ) exit; // disable direct access

define('SCSS_DIR', get_template_directory() . '/assets/');

add_filter( 'remove_cyrillic', 'remove_cyrillic_filter', 10, 1 );
function remove_cyrillic_filter($str){
  $pattern = "/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu";
  $str = preg_replace( $pattern, "", $str );

  return $str;
}

function use_scss(){
  $suffix = '.min';
  if( is_wp_debug() !== false )
    $suffix = '';

  $scss_cache = get_option( 'scss_cache' );

  // from
  $file = get_template_directory() . '/style.scss';
  // to, suffix maybe has .min
  $out_file = '/style'.$suffix.'.css';
  $role = isset(wp_get_current_user()->roles[0]) ? wp_get_current_user()->roles[0] : '';
  if($role == 'administrator'){
    if (file_exists( $file ) && filemtime($file) !== $scss_cache){
      $scss = new scssc();
      $scss->setImportPaths(function($path) {
        if (!file_exists(SCSS_DIR.$path)) return null;
        return SCSS_DIR.$path;
      });

      if(!is_wp_debug())
        $scss->setFormatter('scss_formatter_compressed');

      $compiled = $scss->compile( apply_filters( 'remove_cyrillic', file_get_contents($file) ) );
      if(!empty($compiled)){
        file_put_contents(get_template_directory().$out_file, $compiled );
        update_option( 'scss_cache', filemtime($file) );
        $scss_cache = filemtime($file);
      }
    }
  } // is user admin
}
add_action('wp_enqueue_scripts', 'use_scss', 999 );