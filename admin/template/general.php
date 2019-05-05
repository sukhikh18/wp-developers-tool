<?php

namespace NikolayS93\Tools;

use NikolayS93\WPAdminForm\Form as Form;

$data = get_general_settings();

$form = new Form( $data, $is_table = true );
$form->display();

submit_button( 'Сохранить', 'primary', 'save_changes' );

function get_general_settings() {
	$args = array(
		array(
			'id'      => 'orign-image-resize',
			'type'    => 'select',
			'label'   => __('Compress original images', DOMAIN),
			'desc'    => __('If the size of the uploaded image exceeds this, the original will be deleted, and instead the image with the specified size will be renamed to the original.', DOMAIN),
			'options' => array(
				''        => __('Do not compress', DOMAIN),
				'default' => __('Default compress (max-width: 1600)', DOMAIN),
			),
		),
		array(
			'id'      => 'second-title',
			'type'    => 'select',
			'label'   => __('Enable second titles', DOMAIN),
			'desc'    => __('Change title ..', DOMAIN),
			'options' => array(
				''       => __('Do not use', DOMAIN),
				'loop'   => __('For "The loop"', DOMAIN),
				'detail' => __('For detail only', DOMAIN),
			),
			'custom_attributes' => array(
				'cols' => 80,
				'rows' => 3,
			)
		),
		array(
			'id'      => 'record-views',
			'type'    => 'select',
			'label'   => __('Record views', DOMAIN),
			'desc'    => __('Record views data to meta field "total_views"', DOMAIN),
			'options' => array(
				''        => __('Do not use', DOMAIN),
				'all'     => __('For all post types', DOMAIN),
				'post'    => __('For "post" only', DOMAIN),
				'page'    => __('For "page" only', DOMAIN),
				'product' => __('For "product" only', DOMAIN),
			),
		),
		array(
			'id'      => 'remove-images',
			'type'    => 'select',
			'label'   => __('Remove attached images', DOMAIN),
			'desc'    => __('Remove attached images, when post will be deleted', DOMAIN),
			'options' => array(
				''        => __( 'Do not remove', DOMAIN ),
				'all'     => __( 'Remove all', DOMAIN ),
				'post'    => __( 'For post only', DOMAIN ),
				'page'    => __( 'For page only', DOMAIN ),
				'product' => __( 'For products only', DOMAIN ),
			),
		),
		array(
			'id'          => 'empty-content',
			'type'        => 'text',
			'label'       => __('Empty page message', DOMAIN),
			'desc'        => __('Set message when page is empty', DOMAIN),
			'placeholder' => __('<h3>Page be under development</h3>', DOMAIN),
			'input_class' => 'widefat',
		),
		array(
			'id'          => 'maintenance-mode',
			'type'        => 'textarea',
			'label'       => __('Enable maintenance', DOMAIN),
			'desc'        => __('Maintenance for temporary close your site', DOMAIN),
			'placeholder' => __("The website is in maintenance.\nPlease check back later.", DOMAIN),
			'input_class' => 'widefat',
		),
	);

	return apply_filters( 'get_general_settings', $args );
}