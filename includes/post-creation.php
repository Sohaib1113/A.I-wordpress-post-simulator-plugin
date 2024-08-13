<?php
function create_wp_post_from_input($content, $post_type = 'post', $tags = [])
{
    // Extract the <style> and <script> sections and apply them properly
    preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $content, $styles);
    preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $content, $scripts);

    // Remove <style> and <script> sections from content
    $content_without_styles = preg_replace('/<style[^>]*>(.*?)<\/style>/is', '', $content);
    $content_without_scripts = preg_replace('/<script[^>]*>(.*?)<\/script>/is', '', $content_without_styles);

    // Wrap the extracted styles and scripts properly and add them to the content
    $styles_section = !empty($styles[0]) ? implode("\n", $styles[0]) : '';
    $scripts_section = !empty($scripts[0]) ? implode("\n", $scripts[0]) : '';

    // Combine everything together
    $final_content = $styles_section . "\n" . $content_without_scripts . "\n" . $scripts_section;

    $post_data = array(
        'post_title' => 'Generated ' . ucfirst($post_type),
        'post_content' => $final_content,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => $post_type,
    );
    $post_id = wp_insert_post($post_data);

    if (!is_wp_error($post_id)) {
        wp_set_post_terms($post_id, $tags, 'post_tag', false);
        // Add a meta key to track the posts/pages created by this plugin
        update_post_meta($post_id, '_created_by_html_php_converter', '1');
    }

    return $post_id;
}
?>