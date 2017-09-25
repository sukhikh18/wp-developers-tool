<?php

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
add_action( 'woocommerce_after_main_content', 'woocommerce_taxonomy_archive_description', 5 );
add_action( 'woocommerce_after_main_content', 'woocommerce_product_archive_description', 5 );