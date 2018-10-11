<?php
/**
 * Удалять прикрепленные картинки с записью
 */

namespace NikolayS93\Tool;

add_action('before_delete_post', __NAMESPACE__ . '\delete_attachments_with_post');
function delete_attachments_with_post( $post_id ){
    $_post = get_post($post_id);

    if(is_wp_error($_post))
        return;

    if( ! in_array(DTools::get( 'remove-images' ), array($_post->post_type, 'all') ) )
        return;

    $attachments = get_posts( array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => '',
        'post_parent' => $post_id
    ) );

    if( is_wp_error($attachments) || !is_array($attachments) || sizeof($attachments) < 1 )
        return;

    foreach( $attachments as $attachment )
        wp_delete_attachment( $attachment->ID, true );
}
