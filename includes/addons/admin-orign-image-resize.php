<?php

namespace CDevelopers\tool;

$DTools = DTools::get_instance();
if( $DTools->get( 'orign-image-resize' ) == 'default' ) {
    add_image_size( 'default', 1600, 1024, $resize = 1 );
}

function replace_uploaded_image($image_data){
    $DTools = DTools::get_instance();
    $size = $DTools->get( 'orign-image-resize' );

    // if there is no large image : return
    if ( !$size || !isset($image_data['sizes'][$size]) )
        return $image_data;

    // paths to the uploaded image and the large image
    $upload_dir              = wp_upload_dir();
    $uploaded_image_location = $upload_dir['basedir'] . '/' . $image_data['file'];
    // $large_image_location    = $upload_dir['path'] . '/' . $image_data['sizes'][$size]['file'];
    // fix some servers (beta)
    $large_image_location = $upload_dir['basedir'] . '/' . substr($image_data['file'], 0, 8) . $image_data['sizes'][$size]['file'];

    // delete the uploaded image
    unlink($uploaded_image_location);

    // rename the large image
    rename($large_image_location, $uploaded_image_location);

    // update image metadata and return them
    $image_data['width']  = $image_data['sizes'][$size]['width'];
    $image_data['height'] = $image_data['sizes'][$size]['height'];
    unset($image_data['sizes'][$size]);

    // for debug:
    // file_put_contents($upload_dir['path'].'/debug.log', print_r($image_data,true));

    return $image_data;
}
add_filter('wp_generate_attachment_metadata', __NAMESPACE__ . '\replace_uploaded_image');