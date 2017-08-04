<?php
/**
 * Дополнительный заголовок
 */

add_action('edit_form_after_title', 'render_second_title');
add_action('save_post', 'save_second_title');

function render_second_title(){
  global $post;

  $val = get_post_meta($post->ID, '_second_title', true);
  $empty = get_post_meta($post->ID, '_second_title_empty', true);
  wp_nonce_field( 'st', 'second-title-nonce' );
  ?>
  <style>
    #second-title {
      padding: 3px 8px;
      font-size: 1.7em;
      line-height: 100%;
      height: 1.7em;
      width: 100%;
      outline: 0;
      margin: 0 0 3px;
      background-color: #fff;
    }
    #empty-second-title {
      position: absolute;
      right: 8px;
      top: 10px;
    }
    #empty-second-title input {
      margin-left: 5px;
    }
  </style>

  <div id="second-title-wrap" style="position: relative;">
    <input type="text" id="second-title" name="second-title" value="<?php echo $val;?>" placeholder="Введите дополнительный заголовок" size="25"/>
    <label id="empty-second-title"> Пустой заголовок
      <input type="checkbox" name="empty-second-title" value="1" class="align-right"<?php checked($empty, '1');?>></label>
  </div>
  <?php
}

function save_second_title($post_id){
  if ( ! wp_verify_nonce( $_POST['second-title-nonce'], 'st' ) )
    return $post_id;

    // Убедимся что поле установлено.
  if ( ! isset( $_POST['second-title-nonce'] ) )
    return;

    // Фильтруем и записываем данные
  update_post_meta( $post_id, '_second_title', sanitize_text_field( $_POST['second-title'] ) );
  if( $clear = intval($_POST['empty-second-title']) )
    update_post_meta( $post_id, '_second_title_empty', $clear );
  else
    delete_post_meta( $post_id, '_second_title_empty' );
}

// фронт second_title функции (get_second_title() && the_second_title() )
function get_second_title($id=false, $before='', $after=''){
  global $post;

  if(!$id){
    $post = get_post( $post );
    $id = isset( $post->ID ) ? $post->ID : false;
  }

  if($id){
    if( get_post_meta( $id, '_second_title_empty', true ) )
      return $before . '' . $after;

    if( $s_title = get_post_meta($id, '_second_title', true) )
      return $before . $s_title . $after;
  }

  return false;
}
function the_second_title($id='', $before='<h1 class="entry-title">', $after='</h1>'){
  echo get_second_title($id, $before, $after);
}

add_filter( 'the_title', 'advanced_get_the_title', 10, 2 );
function advanced_get_the_title($title, $id){
  if( is_admin() || ! is_singular() || ! in_the_loop() )
    return $title;

  if( $new_title = get_second_title($id) == 'false' ) // string
  return '';

  return $new_title ? $new_title : $title;
}