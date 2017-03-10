<?php
add_action('before_delete_post', 'delete_attachments_with_post');
function delete_attachments_with_post( $postid ){
    $options = get_option( DT_PLUGIN_NAME );

    if( get_post($postid)->post_type == $options['remove-images'] || $options['remove-images'] == 'all' ){
      $attachments = get_posts( array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => '',
        'post_parent' => $postid
        ) );

      if( !$attachments )
          return;

      foreach( $attachments as $attachment )
          wp_delete_attachment( $attachment->ID, true );  
    }
}
