<?php
$options = get_option( DT_PLUGIN_NAME );

if(isset($options['bestsellers'])){
	if(is_admin()){
		if($options['bestsellers'] == 'personal'){
			$wc_fields = new WCProductSettings();
			$wc_fields->add_field( array(
				'type'				=> 'checkbox',
				'id'                => 'top_sale_product',
				'label'             => 'Популярный товар',
				'description'       => 'Этот товар будет показываться в блоке популярных товаров',
				) );
		}
		
		if(isset($wc_fields))
			$wc_fields->set_fields();
	}

	if($options['bestsellers'] == 'views'){
		function add_woo_view_count(){
			global $post;

			$views = get_post_meta( $post->ID, 'total_views', true );
			$views++;

			update_post_meta( $post->ID, 'total_views', $views );

			if(WP_DEBUG)
				print_r('<pre>(Режим отладки)Популярность товара: '.$views.'</pre>');
		}
		add_action( 'woocommerce_after_single_product', 'add_woo_view_count', 50);
	}
}

	// todo:

	// $wc_fields->add_field(array(
	// 	'type'				=> 'text',
	// 	'id'                => 'pr_value',
	// 	'label'             => 'Ед. измерения',
	// 	'desc_tip'    		=> 'true', // for large desc value
	// 	'placeholder'       => 'К примеру: "шт."', 
	// 	'description'       => 'На сайте это будет отображаться как Цена р. / за шт.',
	// 	) );

	// add_filter( 'woocommerce_sale_price_html', array($this, 'add_price_value'), 10, 2 );
	// add_filter( 'woocommerce_price_html', array($this, 'add_price_value'), 10, 2 );
	// add_filter( 'woocommerce_variable_sale_price_html', array($this, 'add_price_value'), 10, 2 );
	// add_filter( 'woocommerce_variable_price_html', array($this, 'add_price_value'), 10, 2 );
	// function add_price_value( $price, $product ) {
	// 	$affix = get_post_meta( $product->id, 'pr_value', true );
	// 	if($affix)
	// 		$price.= '/' . $affix;
	// 	return $price;
	// 	}