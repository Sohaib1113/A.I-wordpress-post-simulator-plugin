<?php
// Menu and form setup
function html_php_to_wp_post_menu()
{
    add_menu_page('HTML/PHP to WP Post Converter', 'HTML/PHP Converter', 'manage_options', 'html_php_converter', 'html_php_converter_form');
}
add_action('admin_menu', 'html_php_to_wp_post_menu');

function html_php_converter_form()
{
    global $wpdb;
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    $action_type = $post_id > 0 ? 'edit' : 'create';
    $post_type = 'post';
    $post_title = '';
    $post_content = '';

    // Check if editing an existing post/page
    if ($action_type == 'edit') {
        $post = get_post($post_id);
        if ($post) {
            $post_type = $post->post_type;
            $post_title = $post->post_title;
            $post_content = $post->post_content;
        }
    }

    ?>
    <div class="wrap">
        <h2><?php echo ucfirst($action_type); ?> Post/Page</h2>

        <!-- Option to Create New or Edit Existing -->
        <?php if ($action_type == 'create'): ?>
            <label for="select_type">Select Type:</label>
            <select name="select_type" id="select_type"
                onchange="location.href='?page=html_php_converter&create_type='+this.value">
                <option value="">Select</option>
                <option value="post" <?php selected($post_type, 'post'); ?>>Post</option>
                <option value="page" <?php selected($post_type, 'page'); ?>>Page</option>
            </select>
            <br /><br />
        <?php endif; ?>

        <!-- Form to Create/Edit Post/Page -->
        <form method="post" action="">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <label for="post_title">Title:</label>
            <input type="text" name="post_title" id="post_title" value="<?php echo esc_attr($post_title); ?>" required />
            <br />
            <textarea name="html_php_input" style="width:100%; height:200px;"
                placeholder="Enter HTML/PHP code here..."><?php echo esc_textarea($post_content); ?></textarea>
            <br />
            <label for="post_type">Convert to:</label>
            <select name="post_type" id="post_type">
                <option value="post" <?php selected($post_type, 'post'); ?>>Post</option>
                <option value="page" <?php selected($post_type, 'page'); ?>>Page</option>
            </select>
            <br />
            <label for="insert_position">Insert code at:</label>
            <select name="insert_position" id="insert_position">
                <option value="before">Before Existing Content</option>
                <option value="after">After Existing Content</option>
            </select>
            <br />
            <input type="submit" value="Save Post/Page" class="button button-primary">
        </form>

        <hr />

        <h3>Manage Posts/Pages</h3>
        <form method="post" id="bulk-actions-form">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all posts/pages including existing ones
                    $args = array(
                        'post_type' => array('post', 'page'),
                        'posts_per_page' => -1,
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();
                            ?>
                            <tr>
                                <td><input type="checkbox" name="post_ids[]" value="<?php echo get_the_ID(); ?>"></td>
                                <td><?php the_title(); ?></td>
                                <td><?php echo ucfirst(get_post_type()); ?></td>
                                <td><?php echo get_the_date(); ?></td>
                                <td>
                                    <a href="?page=html_php_converter&post_id=<?php echo get_the_ID(); ?>">Edit</a> |
                                    <a href="<?php echo get_delete_post_link(get_the_ID()); ?>">Delete</a> |
                                    <a href="<?php the_permalink(); ?>">View</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5">No posts/pages found.</td>
                        </tr>
                        <?php
                    }
                    wp_reset_postdata();
                    ?>
                </tbody>
            </table>
            <br />
            <select name="bulk_action">
                <option value="edit">Edit Selected</option>
                <option value="delete">Delete Selected</option>
            </select>
            <input type="submit" value="Apply" class="button button-secondary">
        </form>
    </div>
    <?php

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['html_php_input'])) {
        $post_id = intval($_POST['post_id']);
        $post_title = sanitize_text_field($_POST['post_title']);
        $post_type = $_POST['post_type'] ?? 'post';  // Default to 'post' if not set
        $content = wp_kses_post($_POST['html_php_input']);  // Sanitize content with kses
        $tags = generate_tags_from_content($content);  // Automatically generate tags based on content
        $insert_position = $_POST['insert_position'];

        // Determine the final content
        if ($insert_position == 'before') {
            $content = $content . $post_content;
        } else {
            $content = $post_content . $content;
        }

        // If post_id is set, update the existing post
        if ($post_id > 0) {
            $post_data = array(
                'ID' => $post_id,
                'post_title' => $post_title,
                'post_content' => $content,
                'post_type' => $post_type,
            );
            $updated_post_id = wp_update_post($post_data);
            if (!is_wp_error($updated_post_id)) {
                echo '<div>Post/Page updated successfully! <a href="' . get_permalink($updated_post_id) . '">View ' . ucfirst($post_type) . '</a></div>';
            }
        } else {
            // If no post_id, create a new post
            $created_post_id = create_wp_post_from_input($content, $post_type, $tags);
            if ($created_post_id) {
                echo '<div>Post/Page created successfully! <a href="' . get_permalink($created_post_id) . '">View ' . ucfirst($post_type) . '</a></div>';
            }
        }
    }

    // Handle Bulk Actions
    if (!empty($_POST['bulk_action']) && !empty($_POST['post_ids'])) {
        $bulk_action = $_POST['bulk_action'];
        $post_ids = $_POST['post_ids'];

        foreach ($post_ids as $id) {
            if ($bulk_action == 'delete') {
                wp_delete_post($id, true);
            } elseif ($bulk_action == 'edit') {
                wp_redirect("?page=html_php_converter&post_id={$id}");
                exit;
            }
        }
    }
}

function generate_tags_from_content($content)
{
    $keywords = [
        'html' => ['<div>', '<span>', '<html>'],
        'css' => ['{', '}', 'color:', 'font-size:'],
        'javascript' => ['function', 'var ', 'let ', 'const ', 'document.'],
        'bootstrap' => ['class="container"', 'class="row"', 'class="col-'],
        'php' => ['<?php', 'echo', '$', '->']
    ];

    $tags = [];
    foreach ($keywords as $tag => $triggers) {
        foreach ($triggers as $trigger) {
            if (strpos($content, $trigger) !== false) {
                $tags[] = $tag;
                break;
            }
        }
    }
    return $tags;
}

// Add a Live Preview button and iframe
function add_live_preview_feature() {
    ?>
    <button type="button" id="preview-btn">Live Preview</button>
    <iframe id="live-preview-frame" style="width:100%; height:300px; display:none;"></iframe>
    <script>
    document.getElementById('preview-btn').addEventListener('click', function() {
        var content = editor.getValue();
        var iframe = document.getElementById('live-preview-frame');
        iframe.style.display = 'block';
        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write(content);
        iframe.contentWindow.document.close();
    });
    </script>
    <?php
}
add_action('admin_footer', 'add_live_preview_feature');

// Register a custom post type for templates
function register_template_post_type() {
    $args = array(
        'public' => true,
        'label'  => 'Code Templates',
        'supports' => array('title', 'editor'),
    );
    register_post_type('code_template', $args);
}
add_action('init', 'register_template_post_type');

// Save current code as a template
function save_as_template() {
    if (isset($_POST['save_as_template'])) {
        $title = sanitize_text_field($_POST['template_title']);
        $content = wp_kses_post($_POST['html_php_input']);

        $post_id = wp_insert_post(array(
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'code_template',
        ));

        if ($post_id) {
            echo 'Template saved successfully!';
        }
    }
}
add_action('admin_post_save_as_template', 'save_as_template');

// Register a shortcode for each template
function register_template_shortcodes() {
    $templates = get_posts(array('post_type' => 'code_template', 'posts_per_page' => -1));

    foreach ($templates as $template) {
        add_shortcode($template->post_name, function() use ($template) {
            return do_shortcode($template->post_content);
        });
    }
}
add_action('init', 'register_template_shortcodes');
