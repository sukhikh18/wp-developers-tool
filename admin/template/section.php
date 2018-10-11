<?php

use NikolayS93\WPListTable as Table;

// for example
$table = new Table();
$table->set_columns();
// @todo repair it
// $table->set_sortable_columns();

foreach (get_posts() as $post) {
    $table->set_value( array(
        'title' => esc_html( $post->post_title ),
    ) );
}

$table->prepare_items();
$table->display();

printf( '<input type="hidden" name="page" value="%s" />', $_REQUEST['page'] );