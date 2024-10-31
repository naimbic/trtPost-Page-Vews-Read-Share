<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Count views
function pvc_count_view() {
    if (!is_singular()) return;
    
    global $wpdb, $post;
    $table_name = $wpdb->prefix . 'page_views';
    
    // Get current post ID
    $post_id = $post->ID;
    
    // Check if post exists in our table
    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE post_id = %d",
            $post_id
        )
    );
    
    if ($result) {
        // Update existing record
        $wpdb->update(
            $table_name,
            array('views' => $result->views + 1),
            array('post_id' => $post_id),
            array('%d'),
            array('%d')
        );
    } else {
        // Insert new record
        $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'views' => 1
            ),
            array('%d', '%d')
        );
    }
}

// Add view counting to wp_head
add_action('wp_head', 'pvc_count_view');

// Add required styles and scripts to head
function pvc_add_styles() {
    ?>
    <style>
        .page-views {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            position: relative;
        }
        .page-views .fire-icon {
            font-style: normal;
        }
        .social-share-popup {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 1000;
            margin-bottom: 12px;
            min-width: 140px;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        .social-share-popup.active {
            display: flex;
            opacity: 1;
            gap: 12px;
            justify-content: center;
        }
        .social-share-popup:after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 8px solid transparent;
            border-top-color: white;
        }
        .social-share-popup a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: transform 0.2s, background-color 0.2s;
        }
        .social-share-popup a:hover {
            transform: translateY(-2px);
        }
        .social-share-popup a.twitter {
            background-color: #1DA1F2;
        }
        .social-share-popup a.twitter:hover {
            background-color: #0d95e8;
        }
        .social-share-popup a.facebook {
            background-color: #4267B2;
        }
        .social-share-popup a.facebook:hover {
            background-color: #365899;
        }
        .social-share-popup a.linkedin {
            background-color: #0077b5;
        }
        .social-share-popup a.linkedin:hover {
            background-color: #006396;
        }
        .social-share-popup svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewsWidgets = document.querySelectorAll('.page-views');
        
        viewsWidgets.forEach(widget => {
            widget.addEventListener('click', function(e) {
                e.stopPropagation();
                const popup = this.querySelector('.social-share-popup');
                popup.classList.toggle('active');
            });
        });

        // Close popup when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.social-share-popup').forEach(popup => {
                popup.classList.remove('active');
            });
        });
    });
    </script>
    <?php
}
add_action('wp_head', 'pvc_add_styles');

// Shortcode function for views
function pvc_display_views($atts) {
    global $wpdb, $post;
    $table_name = $wpdb->prefix . 'page_views';
    
    // Get post ID (either current post or from shortcode attribute)
    $atts = shortcode_atts(array(
        'id' => $post->ID,
    ), $atts);
    
    $post_id = intval($atts['id']);
    
    // Get views
    $views = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT views FROM $table_name WHERE post_id = %d",
            $post_id
        )
    );
    
    $views = $views ? $views : 0;
    
    // Get sharing URLs
    $post_url = urlencode(get_permalink($post_id));
    $post_title = urlencode(get_the_title($post_id));
    
    // SVG icons for social media
    $twitter_icon = '<svg viewBox="0 0 24 24"><path d="M23.643 4.937c-.835.37-1.732.62-2.675.733.962-.576 1.7-1.49 2.048-2.578-.9.534-1.897.922-2.958 1.13-.85-.904-2.06-1.47-3.4-1.47-2.572 0-4.658 2.086-4.658 4.66 0 .364.042.718.12 1.06-3.873-.195-7.304-2.05-9.602-4.868-.4.69-.63 1.49-.63 2.342 0 1.616.823 3.043 2.072 3.878-.764-.025-1.482-.234-2.11-.583v.06c0 2.257 1.605 4.14 3.737 4.568-.392.106-.803.162-1.227.162-.3 0-.593-.028-.877-.082.593 1.85 2.313 3.198 4.352 3.234-1.595 1.25-3.604 1.995-5.786 1.995-.376 0-.747-.022-1.112-.065 2.062 1.323 4.51 2.093 7.14 2.093 8.57 0 13.255-7.098 13.255-13.254 0-.2-.005-.402-.014-.602.91-.658 1.7-1.477 2.323-2.41z"/></svg>';
    $facebook_icon = '<svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
    $linkedin_icon = '<svg viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>';
    
    $share_html = '<div class="social-share-popup">';
    $share_html .= '<a href="https://twitter.com/intent/tweet?url=' . $post_url . '&text=' . $post_title . '" target="_blank" class="twitter" title="Partager sur Twitter">' . $twitter_icon . '</a>';
    $share_html .= '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $post_url . '" target="_blank" class="facebook" title="Partager sur Facebook">' . $facebook_icon . '</a>';
    $share_html .= '<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . $post_url . '&title=' . $post_title . '" target="_blank" class="linkedin" title="Partager sur LinkedIn">' . $linkedin_icon . '</a>';
    $share_html .= '</div>';
    
    return '<span class="page-views">' . number_format($views) . ' <span class="fire-icon">🔥</span>' . $share_html . '</span>';
}

// Register shortcode for views
add_shortcode('page_views', 'pvc_display_views');