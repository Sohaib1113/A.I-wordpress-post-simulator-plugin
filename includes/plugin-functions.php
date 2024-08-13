<?php
// Menu and form setup
function html_php_to_wp_post_menu()
{
    add_menu_page('HTML/PHP to WP Post Converter', 'HTML/PHP Converter', 'manage_options', 'html_php_converter', 'html_php_converter_form');
}
add_action('admin_menu', 'html_php_to_wp_post_menu');

function html_php_converter_form()
{
    echo '<h2>Convert HTML/PHP to WordPress Post</h2>
    <form method="post" action="">
        <textarea name="html_php_input" style="width:100%; height:200px;"></textarea>
        <input type="submit" value="Convert to Post" class="button button-primary">
    </form>';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['html_php_input'])) {
        $converted_post_id = process_html_php_input($_POST['html_php_input']);
        if ($converted_post_id) {
            echo '<div>Post created successfully! <a href="' . get_permalink($converted_post_id) . '">View Post</a></div>';
        }
    }
}

function process_html_php_input($input)
{
    return create_wp_post_from_input(sanitize_text_field($input));
}
