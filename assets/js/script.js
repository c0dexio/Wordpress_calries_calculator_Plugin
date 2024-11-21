jQuery(document).ready(function($) {
    // Logique JavaScript pour gérer l'interface utilisateur
    $('#random-checkbox').change(function() {
        if ($(this).is(':checked')) {
            $('#calories-input').val(1600).prop('disabled', true);
        } else {
            $('#calories-input').prop('disabled', false);
        }
    });
});