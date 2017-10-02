$(document).ready(function(){
    $( "#form_generatePassword" ).click(function() {
        password = generatePassword(10);
        $("#form_clearPassword").val(password);
        $("#form_generatedPassword").val(password);
    });

});
