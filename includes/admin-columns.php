<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add views column to posts/pages admin
function pvc_add_admin_columns($columns) {
    $columns['page_views'] = 'Vues';
    $columns['reading_time'] = 'Temps de lecture';
    return $columns;
}

function pvc_admin_column_content($column_name, $post_id) {
    global $wpdb;
    
    switch ($column_name) {
        case 'page_views':
            $table_name = $wpdb->prefix . 'page_views';
            $views = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT views FROM $table_name WHERE post_id = %d",
                    $post_id
                )
            );
            echo $views ? number_format($views) . ' <span class="fire-icon">🔥</span>' : '0 <span class="fire-icon">🔥</span>';
            break;
            
        case 'reading_time':
            $post_content = get_post_field('post_content', $post_id);
            $reading_time = pvc_calculate_reading_time($post_content);
            echo pvc_format_reading_time($reading_time);
            break;
    }
}

// Add admin styles
function pvc_add_admin_styles() {
    ?>
    <style>
        .column-page_views {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .fire-icon {
            font-style: normal;
        }
    </style>
    <?php
}
add_action('admin_head', 'pvc_add_admin_styles');

// Add columns to posts and pages
add_filter('manage_posts_columns', 'pvc_add_admin_columns');
add_filter('manage_pages_columns', 'pvc_add_admin_columns');
add_action('manage_posts_custom_column', 'pvc_admin_column_content', 10, 2);
add_action('manage_pages_custom_column', 'pvc_admin_column_content', 10, 2);