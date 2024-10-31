<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Activation hook
register_activation_hook(__FILE__, 'pvc_create_table');

function pvc_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'page_views';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        views bigint(20) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id),
        UNIQUE KEY post_id (post_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}