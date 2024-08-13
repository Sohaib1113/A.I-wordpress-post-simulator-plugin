<?php
function html_php_to_wp_post_admin_settings_init()
{
    add_submenu_page(
        'html_php_converter',
        'Manage Converted Posts/Pages',
        'Manage Posts/Pages',
        'manage_options',
        'manage_converted_posts',
        'html_php_to_wp_post_management_page'
    );
}

add_action('admin_menu', 'html_php_to_wp_post_admin_settings_init');

function html_php_to_wp_post_management_page()
{
    ?>
    <div class="wrap">
        <h2>Manage Converted Posts/Pages</h2>

        <!-- Converted Posts/Pages -->
        <h3>Converted Posts/Pages by Plugin</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch posts/pages created by the plugin
                $args = array(
                    'post_type' => array('post', 'page'),
                    'meta_key' => '_created_by_html_php_converter',
                    'meta_value' => '1',
                    'posts_per_page' => -1,
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        ?>
                        <tr>
                            <td><?php the_title(); ?></td>
                            <td><?php echo ucfirst(get_post_type()); ?></td>
                            <td><?php echo get_the_date(); ?></td>
                            <td>
                                <a href="<?php echo get_edit_post_link(); ?>">Edit</a> |
                                <a href="<?php echo get_delete_post_link(get_the_ID()); ?>">Delete</a> |
                                <a href="<?php the_permalink(); ?>">View</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4">No posts/pages found.</td>
                    </tr>
                    <?php
                }
                wp_reset_postdata();
                ?>
            </tbody>
        </table>

        <!-- Existing Posts/Pages -->
        <h3>Existing Posts/Pages</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch existing posts/pages not created by the plugin
                $args = array(
                    'post_type' => array('post', 'page'),
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => '_created_by_html_php_converter',
                            'compare' => 'NOT EXISTS'
                        ),
                        array(
                            'key' => '_created_by_html_php_converter',
                            'value' => '',
                            'compare' => '='
                        ),
                    ),
                    'posts_per_page' => -1,
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        ?>
                        <tr>
                            <td><?php the_title(); ?></td>
                            <td><?php echo ucfirst(get_post_type()); ?></td>
                            <td><?php echo get_the_date(); ?></td>
                            <td>
                                <a href="<?php echo get_edit_post_link(); ?>">Edit</a> |
                                <a href="<?php echo get_delete_post_link(get_the_ID()); ?>">Delete</a> |
                                <a href="<?php the_permalink(); ?>">View</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4">No posts/pages found.</td>
                    </tr>
                    <?php
                }
                wp_reset_postdata();
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>