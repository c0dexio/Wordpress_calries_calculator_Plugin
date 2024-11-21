<?php
/**
 * Plugin Name: SuperCaloriesFinder
 * Description: Un plugin pour générer un programme alimentaire basé sur les calories.
 * Version: 1.0
 * Author: Votre Nom
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}
// Enqueue des styles
function supercaloriesfinder_enqueue_styles() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_style('supercaloriesfinder-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'supercaloriesfinder_enqueue_styles');

// Inclure les fichiers nécessaires
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-supercaloriesfinder.php';

// Activation du plugin
register_activation_hook(__FILE__, 'supercaloriesfinder_activation');

function supercaloriesfinder_activation() {
    // Créer la table pour les aliments
    global $wpdb;
    $table_name = $wpdb->prefix . 'aliments';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nom varchar(255) NOT NULL,
        calories int NOT NULL,
        proteines float NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Ajouter des aliments par défaut
    supercaloriesfinder_insert_default_aliments();
}

// Initialiser le plugin
new SuperCaloriesFinder();  