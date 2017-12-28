<?php
/**
 * Выводить сообщение о флуде, если комментарии запрещены
 */

namespace CDevelopers\tool;

add_filter( 'wp_is_comment_flood', '\__return_true', 777, 5 );
