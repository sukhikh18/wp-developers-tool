<?php

if( !defined('PRODUCT_CAT_FREQUENCY_META_KEY') ) define('PRODUCT_CAT_FREQUENCY_META_KEY', 'frequency-by-cat');

function the_cat_frequency($before = '', $after = '') {
    $frequency = '';
    $term_id = 0;
    $object = get_queried_object();

    if( $object instanceof WP_Term ) {
        $term_id = absint($object->term_id);
    } elseif (isset($_GET['tag_ID'])) {
        $term_id = absint($_GET['tag_ID']);
    }

    if($term_id = intval($term_id)) {
        $frequency = get_cat_frequency($term_id);
    }

    if($frequency) {
        echo $before . $frequency . $after;
    }
}

function get_cat_frequency( $term ) {
    $frequency = "";
    $term_id = ($term instanceof WP_Term) ? absint($term->term_id) :
        is_numeric($term) ? absint($term) : 0;

    if( $term_id ) {
        $frequency = get_term_meta( $term_id, PRODUCT_CAT_FREQUENCY_META_KEY, true );
    }

    return $frequency ? $frequency : "";
}

if( is_admin() ) {
    add_action( 'product_cat_add_form_fields', 'admin_cat_frequency_field', 10, 2 );
    add_action( 'product_cat_edit_form_fields', 'admin_cat_frequency_field_table', 20, 2 );
    // CRUD
    add_action("created_product_cat", 'admin_cat_frequency_new_field_save');
    add_action("edited_product_cat", 'admin_cat_frequency_field_save');
}

function admin_cat_frequency_field() {
    ?>
    <div class="form-field term-title-wrap">
        <label for="tag-frequency-by-cat">Кратность товаров в категории</label>
        <input name="<?= PRODUCT_CAT_FREQUENCY_META_KEY ?>" id="tag-frequency-by-cat" type="number" value="" size="40">
        <p>Присвоить кратность всех товаров в данной категории.</p>
    </div>
    <?php
}

function admin_cat_frequency_field_table() {
    ?>
    <tr class="form-field tag-frequency-by-cat-wrap">
        <th scope="row"><label for="tag-frequency-by-cat">Кратность в категории</label></th>
        <td>
            <p class="form-field">
                <input name="<?= PRODUCT_CAT_FREQUENCY_META_KEY ?>" id="tag-frequency-by-cat" type="number" value="<?php the_cat_frequency(); ?>">
            </p>
            <p class="description">Присвоить кратность всех товаров в данной категории.</p>
        </td>
    </tr>
    <?php
}

function admin_cat_frequency_new_field_save( $term_id ) {
    // Check field exists
    if ( !isset($_POST[PRODUCT_CAT_FREQUENCY_META_KEY]) ) return $term_id;
    // Check permisions
    if ( !current_user_can('edit_term', $term_id) ) return $term_id;

    $meta = wp_unslash($_POST[PRODUCT_CAT_FREQUENCY_META_KEY]);
    update_term_meta( $term_id, PRODUCT_CAT_FREQUENCY_META_KEY, $meta );
}

function admin_cat_frequency_field_save( $term_id ) {
    // Security nonce check
    if ( empty($_POST['_wpnonce']) || !wp_verify_nonce( $_POST['_wpnonce'], "update-tag_$term_id" ) ) {
        return $term_id;
    }

    admin_cat_frequency_new_field_save( $term_id );
}
