<?php
function create_wp_post_from_input($content)
{
    $post_data = array(
        'post_title' => 'Generated Post',
        'post_content' => $content,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'post',
    );
    $post_id = wp_insert_post($post_data);
    return $post_id;
}
