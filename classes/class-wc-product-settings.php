<?php
class WCProductSettings {
	protected $fields = array();

	function add_field($args = array(), $product_type = 'simple'){
		if (!empty($args['id'])){
			if( $product_type == 'simple' ){
				$args['wrapper_class'] = 'show_if_simple';
			}
			$this->fields[] = $args;
		}
	}
	function set_fields(){
		// Display Fields
		add_action( 'woocommerce_product_options_general_product_data',
			array( $this, 'woo_add_custom_general_fields') );

		// Save Fields
		add_action( 'woocommerce_process_product_meta',
			array( $this, 'woo_custom_general_fields_save') );
	}

	// activate inputs params
	function woo_add_custom_general_fields() {
		// global $woocommerce, $post;

		echo '<div class="options_group">';
		foreach ($this->fields as $field) {

			switch ($field['type']) {
				case 'text':
				case 'number':
					woocommerce_wp_text_input($field);
					break;

				case 'checkbox':
					woocommerce_wp_checkbox($field);
					break;
			}
		}
		echo '</div>';
	}

	function woo_custom_general_fields_save( $post_id ){
		foreach ($this->fields as $field){
			$field_value = $_POST[ $field['id'] ];

			if(!empty($field_value))
				update_post_meta( $post_id, $field['id'], esc_attr( $field_value ) );
			else
				delete_post_meta( $post_id, $field['id']);
		}
	}
}