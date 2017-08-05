<?php
namespace DTools;

add_action('before_delete_post', 'DTools\delete_attachments_with_post');
function delete_attachments_with_post( $postid ){
  $_post = get_post($postid);

  if(is_wp_error($_post)) return;

  $ri = DevelopersTools::$settings['remove-images'];
  if( $ri != $_post->post_type && $ri != 'all' ) return;

  $attachments = get_posts( array(
    'post_type' => 'attachment',
    'posts_per_page' => -1,
    'post_status' => '',
    'post_parent' => $postid
    ) );

  if( is_wp_error($attachments) || !is_array($attachments) || sizeof($attachments) < 1 ) return;

  foreach( $attachments as $attachment )
    wp_delete_attachment( $attachment->ID, true );
}
