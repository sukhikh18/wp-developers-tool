<?php
/**
 *	Регистрация типа review
 *	Фильтр сообщений
 *	Подсказки на страницу редактирования review записи
 *	WPCF7 хук - запись отзыва
 */

function add_revirews_type(){
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
	register_post_type('review',$args);
}
add_action('init', 'add_revirews_type');

// Добавляем фильтр, который изменит сообщение при публикации при изменении типа записи review
function review_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['review'] = array(
	0 => '', // Не используется. Сообщения используются с индекса 1.
	1 => sprintf( 'Отзыв обновлен. <a href="%s">Прочитать отзыв</a>', esc_url( get_permalink($post_ID) ) ),
	2 => 'Произвольное поле обновлено.',
	3 => 'Произвольное поле удалено.',
	4 => 'Отзыв обновлен.',
	/* %s: дата и время ревизии */
	5 => isset($_GET['revision']) ? sprintf( 'Отзыв востановлен из ревизии %s', wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	6 => sprintf( 'Отзыв опубликован. <a href="%s">Перейти к отзыву</a>', esc_url( get_permalink($post_ID) ) ),
	7 => 'Отзыв сохранен.',
	8 => sprintf( 'Отзыв сохранен. <a target="_blank" href="%s">Предпросмотр отзыва</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	9 => sprintf( 'Отзыв запланирован на: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Предпросмотр отзыва</a>',
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	10 => sprintf( 'Не утвержденный отзыв (Черновик) сохранен. <a target="_blank" href="%s">Предпросмотр отзыва</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}
add_filter('post_updated_messages', 'review_updated_messages');

function add_review_help_text($contextual_help, $screen_id, $screen) {
	if ('edit-review' == $screen->id || 'review' == $screen->id ) {
		$contextual_help =
		'<h4>Используйте ContactForm7</h4><p>Если добавить в форму [text] с именем dp_review и дать ему любое значение (При этом его можно скрыть при помощи css) помимо отправленного сообщения, система создаст "Запись" в категории "Отзывы".</p>

		<p>Не работает если опция выключена. При выключении опции данные скрываются (НЕ Удаляются из базы).</p>

		<label><strong>К примеру:</strong></label>
		<p>[text* your-name][textarea your-message][text dp_review class:hide-me "text с именем dp_review"]</p>
		';
	}
	return $contextual_help;
}
add_action( 'contextual_help', 'add_review_help_text', 10, 3 );

// Передать данные для записи отзыва из формы
function dp_add_review ($contact_form){
	$posted_data = $contact_form->posted_data;
	$submission = WPCF7_Submission::get_instance();
	$posted_data = $submission->get_posted_data();
		 
	if(isset($posted_data['dp_review'])){
		$meta = array();
		require_once(ABSPATH .'wp-blog-header.php');

		if ( is_user_logged_in() ){
			$current_user = wp_get_current_user();
			$meta['user_id'] = $current_user->ID;
			$meta['your-name'] = $current_user->display_name;
		} else {
			$meta['your-name'] = (!empty($posted_data['your-name'])) ? $posted_data['your-name'] : 'Не указано';
		}
		$meta['user_ip'] = $_SERVER['REMOTE_ADDR'];
		$meta['posted_date'] = date('d.m.Y');

		// Если указано записываем перечисленый параметр в $meta
		$post_data = array('your-number', 'your-email', 'your-city', 'review-rating', 'your-work', 'your-custom', 'your-custom2');
		foreach ($post_data as $v) {
			if(!empty($posted_data[$v]))   $meta[$v] = wp_strip_all_tags($posted_data[$v]);
		}

		$message = (!empty($posted_data['your-message'])) ? $posted_data['your-message'] : '';

		$new_review = array(
			'post_title' => wp_strip_all_tags('Отзыв от '.$meta['your-name'].' ['.$meta['posted_date'].'г.]'),
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
add_action('wpcf7_mail_sent', 'dp_add_review');