<?php
/*
Plugin Name: Compteur de vues et temps de lecture
Description: Compte les vues des pages et estime le temps de lecture avec les shortcodes [page_views] et [reading_time]
Version: 1.1
Author: Zak Chapman
Author URI: https://trtweb.fr
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/database.php';
require_once plugin_dir_path(__FILE__) . 'includes/view-counter.php';
require_once plugin_dir_path(__FILE__) . 'includes/reading-time.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-columns.php';