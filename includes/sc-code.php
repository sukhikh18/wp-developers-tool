<?php
global $post;
  /**
   * Добавить шорткод [CODE] который, <br>
   * отключает woordpress форматирование [/CODE]
   * 
   * Функция не стабильна с wooCommerce ! Поэтому добавлена проверка на использование
   */
function render_sc_code_content( $post, $data ) {
   wp_nonce_field( $data['args'][0], $data['args'][0].'_nonce' );

   $checked = get_post_meta( $post->ID, '_check_sc_code', true ) ? ' checked' : '';
   ?>
      <label><input type="checkbox" class="checkbox" name="_check_sc_code" value="on"<?=$checked;?>>
      Разрешить шорткод [CODE] для этой записи</label>
   <?php
}
function call_AddSCCodeMetaBox(){
  if(! is_advanced_type())
    return false;

  $m_boxes = new dt_CustomMetaBoxes();
  // box_name, cb_function_name
  $m_boxes->add_box('Помощь разработчику', 'render_sc_code_content', true);
  // use array() for multyple
  $m_boxes->add_fields('_check_sc_code');
}
if( class_exists('dt_CustomMetaBoxes') ){
  add_action( 'load-post.php', 'call_AddSCCodeMetaBox' );
  add_action( 'load-post-new.php', 'call_AddSCCodeMetaBox' );
}


remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');
add_filter('the_content', 'dt_raw_content', 99);
function dt_raw_content($content) {
  if( get_post_meta( get_the_id(), '_check_sc_code', true ) ){
    $new_content = '';
    $pattern_full = '{(\[CODE\].*?\[/CODE\])}is';
    $pattern_contents = '{\[CODE\](.*?)\[/CODE\]}is';
    $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($pieces as $piece) {
      if (preg_match($pattern_contents, $piece, $matches)) {
        $new_content .= $matches[1];
      } else {
        $new_content .= wptexturize(wpautop($piece));
      }
    }
    return $new_content;
  }
  else {
    // Если галка не стоит возвращаем форматирование WP обратно
      // $content = wptexturize(wpautop($content));
    add_filter('the_content', 'wpautop');
    add_filter('the_content', 'wptexturize');
    return $content;
  }
}