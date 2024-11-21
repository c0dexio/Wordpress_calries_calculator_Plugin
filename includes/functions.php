<?php

// Fonction d'activation pour créer la table et insérer des aliments par défaut
register_activation_hook(__FILE__, 'supercaloriesfinder_install');

function supercaloriesfinder_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aliments'; // Nom de la table

    $charset_collate = $wpdb->get_charset_collate();

    // SQL pour créer la table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nom varchar(255) NOT NULL,
        calories mediumint(9) NOT NULL,
        proteines float NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Insérer des aliments par défaut
    supercaloriesfinder_insert_default_aliments();
}

// Fonction pour insérer des aliments par défaut
function supercaloriesfinder_insert_default_aliments() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aliments';

    // Exemple d'aliments par défaut
    $aliments = array(
        array('nom' => 'Pomme', 'calories' => 95, 'proteines' => 0.5),
        array('nom' => 'Banane', 'calories' => 105, 'proteines' => 1.3),
        // Ajoutez plus d'aliments ici (minimum 100)
    );

    foreach ($aliments as $aliment) {
        $wpdb->insert($table_name, $aliment);
    }
}

// Fonction pour ajouter un aliment
function supercaloriesfinder_ajouter_aliment($nom, $calories, $proteines) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aliments';
    $wpdb->insert($table_name, array(
        'nom' => $nom,
        'calories' => $calories,
        'proteines' => $proteines
    ));
}

// Fonction pour récupérer les aliments
function supercaloriesfinder_recuperer_aliments() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aliments';
    return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
}

function supercaloriesfinder_generer_programme($calories, $jours = 7) {
    $aliments = supercaloriesfinder_recuperer_aliments();
    $programme = [];

    // Vérifiez si des aliments sont disponibles
    if (empty($aliments)) {
        return []; // Retourne un tableau vide si aucun aliment n'est disponible
    }

    for ($i = 0; $i < $jours; $i++) {
        $jour = [];
        $calories_restantes = $calories;

        for ($j = 0; $j < 3; $j++) { // 3 repas par jour
            $repas = [];
            // Continuez à ajouter des aliments tant que des calories restent
            while ($calories_restantes > 0) {
                // Sélectionnez un aliment aléatoirement
                $aliment = $aliments[array_rand($aliments)];

                // Vérifiez que l'aliment a des calories et qu'il peut être ajouté au repas
                if ($aliment['calories'] <= $calories_restantes) {
                    $repas[] = $aliment;
                    $calories_restantes -= $aliment['calories'];
                }

                // Évitez une boucle infinie si aucun aliment ne peut être ajouté
                // Si les calories restantes sont inférieures au minimum des aliments disponibles, sortez de la boucle
                $min_calories = min(array_column($aliments, 'calories'));
                if ($calories_restantes < $min_calories) {
                    break;
                }
            }

            // Ajoutez le repas au jour s'il contient des aliments
            if (!empty($repas)) {
                $jour[] = $repas;
            } else {
                // Si aucun aliment n'a été ajouté, sortez de la boucle des repas
                break;
            }
        }

        // Ajoutez le jour au programme seulement s'il contient des repas
        if (!empty($jour)) {
            $programme[] = $jour;
        }
    }
    return $programme;
}

// Fonction pour télécharger le programme alimentaire au format CSV
function supercaloriesfinder_telecharger_csv($data) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="programme_alimentaire.csv"');

    $output = fopen('php://output', 'w');
    foreach ($data as $jour) {
        foreach ($jour as $repas) {
            foreach ($repas as $aliment) {
                fputcsv($output, $aliment);
            }
        }
    }
    fclose($output);
    exit();
}

