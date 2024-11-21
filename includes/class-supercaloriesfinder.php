<?php

class SuperCaloriesFinder {
    public function __construct() {
        add_action('admin_menu', array($this, 'ajouter_menu'));
        add_action('wp_enqueue_scripts', array($this, 'charger_scripts'));
        add_shortcode('super_calories_finder', array($this, 'afficher_formulaire_shortcode'));
    }

    public function afficher_formulaire_shortcode() {
        ob_start(); // Commencer la mise en tampon de sortie
        include 'page-generer-programme.php'; // Inclure le fichier contenant le formulaire
        return ob_get_clean(); // Retourner le contenu mis en tampon
    }

    public function ajouter_menu() {
        add_menu_page('Super Calories Finder', 'Super Calories Finder', 'manage_options', 'supercaloriesfinder', array($this, 'afficher_page'));
        add_submenu_page('supercaloriesfinder', 'Ajouter Aliments', 'Ajouter Aliments', 'manage_options', 'ajouter-aliments', array($this, 'afficher_page_ajouter_aliments'));
    }

    public function afficher_page() {
        include 'page-generer-programme.php';
    }

    public function afficher_page_ajouter_aliments() {
        include 'page-ajouter-aliments.php';
    }

    public function charger_scripts() {
        wp_enqueue_style('supercaloriesfinder-style', plugins_url('/assets/css/style.css', __FILE__));
        wp_enqueue_script('supercaloriesfinder-script', plugins_url('/assets/js/script.js', __FILE__), array('jquery'), null, true);
    }
}