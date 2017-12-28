<?php
/**
 * Выводить вместо the_content если "Контент" пуст.
 */

namespace CDevelopers\tool;

add_filter( 'the_content', __NAMESPACE__ . '\empty_content', 10, 1 );
function empty_content( $content ) {
    if( $content || ! is_singular() ) {
        return $content;
    }

    /**
     * @todo: Только для записей и страниц?
     */
    if( ! in_array(get_post_type(), array('post', 'page')) ) {
        return $content;
    }

    return DTools::get('empty-content');
}
