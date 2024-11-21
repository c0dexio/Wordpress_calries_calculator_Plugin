<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_POST['ajouter_aliment'])) {
    supercaloriesfinder_ajouter_aliment($_POST['nom'], $_POST['calories'], $_POST['proteines']);
    echo '<div class="updated"><p>Aliment ajouté avec succès!</p></div>';
}
?>

<div class="wrap">
    <h1>Ajouter un Aliment</h1>
    <form method="post">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="nom">Nom</label></th>
                <td><input name="nom" type="text" id="nom" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="calories">Calories</label></th>
                <td><input name="calories" type="number" id="calories" value="7" readonly></td>
            </tr>
            <tr>
                <th scope="row"><label for="proteines">Protéines</label></th>
                <td><input name="proteines" type="number" step="0.1" id="proteines" required></td>
            </tr>
        </table>
        <?php submit_button('Ajouter Aliment', 'primary', 'ajouter_aliment'); ?>
    </form>
</div>