<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

function pvc_calculate_reading_time($content) {
    // Strip HTML tags
    $content = strip_tags($content);
    
    // Count words
    $word_count = str_word_count($content);
    
    // Average reading speed (words per minute)
    $words_per_minute = 200;
    
    // Calculate reading time in minutes
    $reading_time = ceil($word_count / $words_per_minute);
    
    return $reading_time;
}

function pvc_format_reading_time($minutes) {
    if ($minutes < 1) {
        return 'Moins d\'une minute';
    } elseif ($minutes === 1) {
        return '1 minute';
    } else {
        return $minutes . ' minutes';
    }
}

// Shortcode function for reading time
function pvc_display_reading_time($atts) {
    global $post;
    
    // Get post ID (either current post or from shortcode attribute)
    $atts = shortcode_atts(array(
        'id' => $post->ID,
    ), $atts);
    
    $post_id = intval($atts['id']);
    $post_content = get_post_field('post_content', $post_id);
    
    $reading_time = pvc_calculate_reading_time($post_content);
    $formatted_time = pvc_format_reading_time($reading_time);
    
    return '<span class="reading-time">Temps de lecture : ' . $formatted_time . '</span>';
}

// Register shortcode for reading time
add_shortcode('reading_time', 'pvc_display_reading_time');