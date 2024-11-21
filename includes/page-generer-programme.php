<?php
if (!defined('ABSPATH')) {
    exit; // Empêche l'accès direct au fichier
}
?>
<div class="container">
    <div class="container calfind">
        <h4>Enjoy a Free 7-Day Nutrition Plan</h4>
        <br>
        <p>With our SuperCaloriesFinder tool, you can easily generate a personalized nutrition program that helps you meet your caloric goals effortlessly. Take advantage of this free feature to save time and plan your meals effectively.</p>
        <p>Our application guides you in selecting foods, allowing you to create a balanced plan that meets your nutritional needs.</p>
        <p>Start by entering your desired daily caloric intake and the number of days you want the plan for. Click the button below to generate your personalized meal plan!</p>
        <form method="post">
            <label for="calories">Calories:</label>
            <input type="number" name="calories" id="calories" value="1600" required>
            <label for="jours">Jours:</label>
            <input type="number" name="jours" id="jours" value="3" readonly>
            <input type="submit" name="generer_programme" value="Générer le programme">
        </form>
    </div>
</div>
<?php
// Fonction pour récupérer les aliments de la base de données
function fetch_food_items() {
    global $wpdb; // Utilise l'objet global $wpdb de WordPress
    $table_name = $wpdb->prefix . 'aliments'; // Préfixe de la table

    // Requête pour récupérer les aliments
    $results = $wpdb->get_results("SELECT nom, calories FROM $table_name", ARRAY_A);
    return $results;
}

// Fonction pour générer le programme alimentaire
function generate_meal_plan($food_items, $target_calories, $days) {
    $meal_plan = [];
    $calorie_tolerance = 100; // Tolérance de calories
    $min_calories_per_meal = $target_calories * 0.3; // 30% de l'apport calorique total
    $max_calories_per_meal = $target_calories * 0.35; // 35% de l'apport calorique total

    for ($day = 0; $day < $days; $day++) {
        $meals = [[], [], []]; // Initialiser les repas pour la journée
        $total_calories = 0;

        // Générer des repas jusqu'à ce que nous atteignions le quota de calories
        while (true) {
            shuffle($food_items); // Mélanger les aliments pour la randomisation
            $meals = [[], [], []]; // Réinitialiser les repas
            $total_calories = 0; // Réinitialiser le total des calories

            // Essayer de remplir les repas
            for ($meal_index = 0; $meal_index < 3; $meal_index++) {
                $meal_calories = 0; // Calories pour le repas actuel
                while ($meal_calories < $min_calories_per_meal || $meal_calories > $max_calories_per_meal) {
                    shuffle($food_items); // Mélanger les aliments pour la randomisation
                    foreach ($food_items as $item) {
                        if ($meal_calories + $item['calories'] <= $max_calories_per_meal) {
                            $meals[$meal_index][] = $item;
                            $meal_calories += $item['calories'];
                        }
                    }
                    // Si on ne peut pas remplir le repas avec les aliments disponibles, sortir de la boucle
                    if ($meal_calories >= $min_calories_per_meal && $meal_calories <= $max_calories_per_meal) {
                        break;
                    }
                }
                $total_calories += $meal_calories; // Ajouter les calories du repas au total
            }

            // Vérifier si le total des calories est valide (dans la tolérance)
            if ($total_calories >= $target_calories - $calorie_tolerance && $total_calories <= $target_calories + $calorie_tolerance) {
                break; // Sortir de la boucle si le total est valide
            }
        }

        $meal_plan[] = $meals; // Ajouter les repas de la journée au plan
    }

    return $meal_plan;
}

// Vérification si le formulaire a été soumis
if (isset($_POST['generer_programme'])) {
    $calories = isset($_POST['calories']) ? intval($_POST['calories']) : 1600;
    $jours = isset($_POST['jours']) ? intval($_POST['jours']) : 7;

    // Récupérer les aliments de la base de données
    $food_items = fetch_food_items();

    // Générer le programme alimentaire
    $programme = generate_meal_plan($food_items, $calories, $jours);

    // Affichage du programme généré
    echo '<div class="container mt-4">';
    echo '<table class="table table-bordered table-striped text-center table-light">';
    echo '<thead class="thead-dark"><tr><th>Day</th><th>Meal 1</th><th>Meal 2</th><th>Meal 3</th></tr></thead>';
    echo '<tbody>';
    
    foreach ($programme as $index => $jour) {
        echo '<tr><td>' . ($index + 1) . '</td>';
        
        for ($mealIndex = 0; $mealIndex < 3; $mealIndex++) {
            echo '<td>';
            
            if (!empty($jour[$mealIndex])) {
                foreach ($jour[$mealIndex] as $aliment) {
                    echo esc_html($aliment['nom']) . ' (' . esc_html($aliment['calories']) . ' cal)<br>';
                }
            } else {
                echo 'Aucun repas';
            }
            
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}
?>