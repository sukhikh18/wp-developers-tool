<?php
class dt_CustomMetaBoxes {
	const NONCE = 'abra_kadabra_security';

	private $render_function = '';
	private $box_name = 'Example title';
	private $side = false;

	private $meta_fields = array('');

	private static $count;

	function __construct() {
	}

	/**
	 * Добавляет в массив значения которые нужно сохранять.
	 * 
	 * @param string $field_name Название (name) значения.
	 */
	public function add_fields($field_name){
		if(is_array($field_name)){
			foreach ($field_name as $field) {
				array_push($this->meta_fields, esc_attr( $field ) );
			}
		}
		else {
			array_push($this->meta_fields, esc_attr( $field_name ) );
		}

		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Установка хука с предварительной установкой значений
	 * @param string 	$name   Название бокса
	 * @param string 	$render Название callback функции
	 * @param boolean 	$side   Показывать с боку / Нормально
	 */
	public function add_box($name = false, $render = false, $side = false){
		if($name)
			$this->box_name = sanitize_text_field($name);
		if($render)
			$this->render_function = sanitize_text_field($render);
		if($side)
			$this->side = true;

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Обертка WP функции add_meta_box, добавляет метабокс по параметрам класса
	 * 
	 * @param string $post_type Название используемого типа записи
	 */
	function add_meta_box( $post_type ){
		// get post types without WP default (for exclude menu, revision..)
		$post_types = get_post_types(array('_builtin' => false));
		$add = array('post', 'page');
		$post_types = array_merge($post_types, $add);
		
		if(!empty($this->render_function) && !empty($this->box_name)){
			$side = ($this->side) ? 'side' : 'advanced';
			
			self::$count++;
			add_meta_box(
				'CustomMetaBox-'.self::$count,
				$this->box_name,
				$this->render_function,
				$post_types,
				$side,
				'default',
				array(self::NONCE)
				);
		}
	}

	/**
	 * Сохраняем данные при сохранении поста.
	 *
	 * @param int $post_id ID поста, который сохраняется.
	 */
	function save( $post_id ) {
		if ( ! isset( $_POST[self::NONCE . '_nonce'] ) )
			return $post_id;

		$nonce = $_POST[self::NONCE . '_nonce'];
		if ( ! wp_verify_nonce( $nonce, self::NONCE ) )
			return $post_id;

		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;


		foreach ($this->meta_fields as $field) {
			if(isset($_POST[$field]))
				update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
			else
				delete_post_meta( $post_id, $field );
		}
	}
}