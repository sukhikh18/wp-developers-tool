<?php
class dt_CustomMetaBoxes {
	const NONCE = 'abra_kadabra_security';

	private $output_function = '';
	private $box_name = 'Example title';
	private $side = false;
	private $priority;

	private $meta_fields = array('');

	private static $count;

	function __construct() {}

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
	 * @param string 	$output_function Название callback функции
	 * @param boolean 	$side   Показывать с боку / Нормально
	 */
	public function add_box($name = false, $output_function = false, $side = false, $priority = 'normal'){
		if($name)
			$this->box_name = sanitize_text_field($name);
		
		if($output_function)
			$this->output_function = $output_function;

		$this->side = $side;
		$this->priority = $priority;

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
		
		if(!empty($this->output_function) && !empty($this->box_name)){
			$side = ($this->side) ? 'side' : 'advanced';
			
			self::$count++;
			add_meta_box(
				'CustomMetaBox-'.self::$count,
				$this->box_name,
				$this->output_function,
				$post_types,
				$side,
				$this->priority,
				array( self::NONCE )
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

		$test = array();
		$test['post'] = $_POST;
		$test['metas'] = $this->meta_fields;
		file_put_contents(__DIR__ . '/meta_debug.log', print_r($test, 1));

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