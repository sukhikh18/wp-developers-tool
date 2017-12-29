<?php
/**
 * Дополнительный заголовок
 */

namespace CDevelopers\tool {
    add_action('edit_form_after_title', __NAMESPACE__ . '\render_second_title');
    function render_second_title() {
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
            <input
                type="text"
                id="second-title"
                name="second-title"
                value="<?php echo $val;?>"
                placeholder="<?php echo __('Insert second title', DOMAIN) ?>"
                size="25" />

            <label id="second-title-empty">
                <?php echo __('Empty title', DOMAIN); ?>
                <input
                    type="checkbox"
                    name="second-title-empty"
                    value="1"
                    class="align-right"
                    <?php checked($empty, '1');?> />
            </label>
        </div>
        <?php
        wp_nonce_field( 'save_second_title', 'second-title-nonce' );
    }

    add_action('save_post', __NAMESPACE__ . '\save_second_title');
    function save_second_title( $post_id ) {
        if ( ! isset($_POST['second-title-nonce']) )
            return $post_id;

        $nonce = sanitize_text_field( $_POST['second-title-nonce'] );
        if( ! wp_verify_nonce( $nonce, 'save_second_title' ) )
            return $post_id;

        $posted = wp_parse_args( $_POST, array(
            'second-title' => '',
            'second-title-empty' => '',
            ) );

        if( apply_filters( 'dt_sanitize_second_title_field', true ) ) {
            $posted = array_filter( $posted , 'sanitize_text_field' );
        }

        // Фильтруем и записываем данные
        if( $posted['second-title'] ) {
            update_post_meta( $post_id, '_second_title', $posted['second-title'] );
        }
        else {
            delete_post_meta( $post_id, '_second_title' );
        }

        if( 'on' == $posted['second-title-empty'] ) {
            update_post_meta( $post_id, '_second_title_empty', 1 );
        }
        else {
            delete_post_meta( $post_id, '_second_title_empty' );
        }
    }

    add_filter( 'the_title', __NAMESPACE__ . '\advanced_get_the_title', 10, 3 );
    function advanced_get_the_title( $title, $id = null, $enable_second_title = false ) {
        global $post;

        if( ! $enable_second_title ) {
            if( is_admin() ) return $title;
            if( ! in_the_loop() ) return $title;
        }

        if( ! $id ) $id = $post->ID;

        if( 'detail' == DTools::get('second-title') && ! is_singular() ) {
            return $title;
        }

        if( false !== $new_title = get_second_title($id) ) {
            $title = $new_title;
        }

        return $title;
    }
}

namespace {
    // фронт second_title функции (get_second_title() && the_second_title() )
    if( ! function_exists('get_second_title') ) {
        function get_second_title( $id=false, $before='', $after='' ) {
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
    }

    if( ! function_exists('the_second_title') ) {
        function the_second_title( $id='', $before='<h1 class="entry-title">', $after='</h1>' ) {

            echo get_second_title($id, $before, $after);
        }
    }
}
