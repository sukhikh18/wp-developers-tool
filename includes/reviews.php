<?php
// todo: review form shortcode
// remove str_replace;
// editble forms

class DTReview
{
	const REVIEW_TYPE = 'review';

	function __construct(){
		$this->_actions();
		if(is_admin()){
			$this->_admin_actions();
		}
	}

	static function _review_fields(){
		$review_fields = array(
			array(
				'id' => '_your_name',
				'type' => 'text',
				'label' => 'Имя автора',
				),
			array(
				'id' => '_your_number',
				'type' => 'text',
				'label' => 'Номер телефона',
				),
			array(
				'id' => '_your_email',
				'type' => 'text',
				'label' => 'Емэйл',
				),
			// array(
			// 	'id' => '_your_city',
			// 	'type' => 'text',
			// 	'label' => 'Город',
			// 	),
			// array(
			// 	'id' => 'your_review_rating',
			// 	'type' => 'text',
			// 	'label' => 'Рэйтинг',
			// 	),
			// array(
			// 	'id' => 'your-work',
			// 	'type' => 'text',
			// 	'label' => 'Работа',
			// 	),
			// array(
			// 	'id' => 'your-custom',
			// 	'type' => 'text',
			// 	'label' => '',
			// 	),
			// array(
			// 	'id' => 'your-custom2',
			// 	'type' => 'text',
			// 	'label' => '',
			// 	)
			);
		return apply_filters( 'review_fields', $review_fields );
	}

	function _actions(){
		add_action('init', array($this, 'register_review_type') );

		add_action('wpcf7_mail_sent', array($this, 'dt_create_review') );
	}

	function _admin_actions(){
		add_action( 'contextual_help', array($this, 'add_review_help_text'), 10, 3 );

		if( class_exists('dt_CustomMetaBoxes') ){
			add_action( 'load-post.php', array($this, 'call_MetaBox') );
			add_action( 'load-post-new.php', array($this, 'call_MetaBox') );

			add_action('edit_form_after_title', array($this, 'sort_boxes') );
		}
	}

	// Передать данные для записи отзыва из формы
	function dt_create_review ($contact_form){
		$posted_data = $contact_form->posted_data;
		$submission = WPCF7_Submission::get_instance();
		$posted_data = $submission->get_posted_data();
			 
		if(isset($posted_data['dp_review'])){
			$meta = array();
			require_once(ABSPATH .'wp-blog-header.php');

			if ( is_user_logged_in() ){
				$current_user = wp_get_current_user();
				$meta['user_id'] = $current_user->ID;
				$meta['_your_name'] = $current_user->display_name;
			} else {
				$meta['_your_name'] = (!empty($posted_data['your-name'])) ? $posted_data['your-name'] : 'Не указано';
			}
			$meta['user_ip'] = $_SERVER['REMOTE_ADDR'];
			$meta['posted_date'] = date('d.m.Y');

			$post_data = $this->get_review_fields_id();
			unset($post_data[0]); // _your_name

			foreach ($post_data as $v) {
				if(!empty($posted_data[$v]))
					$key = str_replace('_', '-', $v);
					$key = str_replace('-your', 'your', $key);
					$meta[$v] = wp_strip_all_tags($posted_data[$key]);
			}

			$message = (!empty($posted_data['your-message'])) ? $posted_data['your-message'] : '';

			$new_review = array(
				'post_title' => wp_strip_all_tags('Отзыв от '.$meta['_your_name'].' ['.$meta['posted_date'].'г.]'),
				'post_content' => wp_strip_all_tags($message),
				'post_date' => date('Y-m-d H:i:s'),
				'post_excerpt' => 'Оставте здесь свой ответ посетителю или удалите это сообщение перед тем как опубликовать',
				'post_status' => 'pending',
				'post_type' => 'review',
				);
			if(!empty($meta)) $new_review['meta_input'] = $meta;

			wp_insert_post( $new_review, true );
		}
	}

	function register_review_type(){
		$labels = array(
		'name' => 'Отзывы', 
		'singular_name' => 'Отзыв',
		'add_new' => 'Добавить отзыв',
		'add_new_item' => 'Добавить новый отзыв',
		'edit_item' => 'Изменить отзыв',
		'new_item' => 'Новый отзыв',
		'view_item' => 'Прочитать отзыв',
		'search_items' => 'Найти отзыв',
		'not_found' =>  'Отзывов не найдено',
		'not_found_in_trash' => 'В корзине нет отзывов',
		'parent_item_colon' => '',
		'menu_name' => 'Отзывы'

		);
		$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'menu_icon'   => 'dashicons-format-status',
		'supports' => array('title','editor','thumbnail','excerpt', 'custom-fields')
		);
		register_post_type(self::REVIEW_TYPE, $args);
	}

	function add_review_help_text($contextual_help, $screen_id, $screen) {
		if ('edit-review' == $screen->id || 'review' == $screen->id ) {
			$contextual_help =
			'<h4>Используйте ContactForm7</h4><p>Если добавить в форму [text] с именем dp_review и дать ему любое значение (При этом его можно скрыть при помощи css) помимо отправленного сообщения, система создаст "Запись" типа "Отзыв".</p>

			<p>Не работает если опция выключена. При выключении опции данные скрываются (НЕ Удаляются из базы).</p>

			<label><strong>К примеру:</strong></label>
			<p>[text* your-name][textarea your-message][text dp_review class:hide-me "text с именем dp_review"]</p>';
		}
		return $contextual_help;
	}

	/**
	 * Metabox
	 */
	function sort_boxes(){
		global $post, $wp_meta_boxes;

		if( $post->post_type == self::REVIEW_TYPE ){
			do_meta_boxes(get_current_screen(), 'advanced', $post);
			unset($wp_meta_boxes[get_post_type($post)]['advanced']);
		}
	}

	function call_MetaBox(){
		$screen = get_current_screen();
		if( !isset($screen->post_type) || $screen->post_type != self::REVIEW_TYPE )
			return false;

		$m_boxes = new dt_CustomMetaBoxes();
  		// box_name, cb_function_name
		$m_boxes->add_box('Отзыв', array($this, 'review_metabox_callback'), false, 'high' );

		$m_boxes->add_fields( $this->get_review_fields_id() );
	}

	function review_metabox_callback($post, $data){
		wp_nonce_field( $data['args'][0], $data['args'][0].'_nonce' );
		
		$active = array();
		$ids = $this->get_review_fields_id();
		foreach ( $ids as $id ) {
			$active[$id] = get_post_meta( $post->ID, $id, true );
		}
		DTForm::render( self::_review_fields(), $active, true );
	}

	function get_review_fields_id(){
		$result = array();
		$fields = self::_review_fields();
		foreach ($fields as $field) {
			$result[] = $field['id'];
		}
		return $result;
	}

	static function get_review_options($post_id=null){
		if(! $post_id ){
			global $post;

			if( isset($post->ID) )
				$post_id = $post->ID;
			else
				return false;
		}

		$fields = self::_review_fields();

		$result =array();
		foreach ($fields as $field) {
			$key = str_replace('_your_', '', $field['id']);
			$result[$key] = get_post_meta( $post_id, $field['id'], true );
		}

		return $result;
	}

}
new DTReview();

// u may use DTReview::get_review_options( get_post_id() ); in content template