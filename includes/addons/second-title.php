<?php
namespace CDevelopers\tool;

/**
 * Дополнительный заголовок
 */
add_action('edit_form_after_title', __NAMESPACE__ . '\render_second_title');
add_action('save_post', __NAMESPACE__ . '\save_second_title');

function render_second_title(){
    global $post;

    $val = get_post_meta($post->ID, '_second_title', true);
    $empty = get_post_meta($post->ID, '_second_title_empty', true);
    ?>
    <style>
        #second-title-wrap {
            position: relative;
        }
        #second-title {
            padding: 3px 150px 3px 8px;
            font-size: 1.7em;
            line-height: 100%;
            height: 1.7em;
            width: 100%;
            outline: 0;
            margin: 0 0 3px;
            background-color: #fff;
        }
        #second-title-empty {
            position: absolute;
            right: 8px;
            top: 10px;
        }
        #second-title-empty input {
            margin-left: 5px;
        }
    </style>

    <div id="second-title-wrap">
        <input type="text" id="second-title" name="second-title" value="<?php echo $val;?>" placeholder="Введите дополнительный заголовок" size="25"/>
        <label id="second-title-empty"> Пустой заголовок
            <input type="checkbox" name="second-title-empty" value="1" class="align-right"<?php checked($empty, '1');?>>
        </label>
    </div>
    <?php
    wp_nonce_field( 'save_second_title', 'second-title-nonce' );
}

function save_second_title($post_id){
    if ( !isset($_POST['second-title-nonce']) || ! wp_verify_nonce( $_POST['second-title-nonce'], 'save_second_title' ) )
        return $post_id;

    $posted = array_filter( $_POST , 'sanitize_text_field' );
    $posted = wp_parse_args( $posted, array(
        'second-title' => '',
        'second-title-empty' => '',
        ) );

    // Фильтруем и записываем данные
    if( $posted['second-title'] ) {
        update_post_meta( $post_id, '_second_title', $posted['second-title'] );
    }
    else {
        delete_post_meta( $post_id, '_second_title' );
    }

    if( $posted['second-title-empty'] ) {
        update_post_meta( $post_id, '_second_title_empty', 1 );
    }
    else {
        delete_post_meta( $post_id, '_second_title_empty' );
    }
}

// фронт second_title функции (get_second_title() && the_second_title() )
function get_second_title($id=false, $before='', $after='') {
    global $post;

    if( ! $id ) {
        $id = isset( $post->ID ) ? $post->ID : false;
    }

    if( $id ) {
        if( get_post_meta( $id, '_second_title_empty', true ) ) {
            return $before . '' . $after;
        }

        if( $s_title = get_post_meta($id, '_second_title', true) ) {
            return $before . $s_title . $after;
        }
    }

    return false;
}

function the_second_title($id='', $before='<h1 class="entry-title">', $after='</h1>') {

    echo get_second_title($id, $before, $after);
}

add_filter( 'the_title', __NAMESPACE__ . '\advanced_get_the_title', 10, 3 );
function advanced_get_the_title($title, $id = null, $enable_second_title = false){
    global $post;
    $DTools = DTools::get_instance();

    if( ! $enable_second_title ) {
        if( is_admin() ) return $title;
        if( ! in_the_loop() ) return $title;
    }

    if( ! $id ) $id = $post->ID;

    if( 'detail' == $DTools->get('second-title') && ! is_singular() ) {
        return $title;
    }

    if( false !== $new_title = get_second_title($id) ) {
        $title = $new_title;
    }

    return $title;
}
