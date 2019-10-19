$(document).ready(function(){
    $( "#form_generatePassword" ).click(function() {
        password = generatePassword(10);
        $("#form_clearPassword").val(password);
        $("#form_generatedPassword").val(password);
    });


    $('#form_sendInvitationMail').change(function() {
        if($(this).is(":checked")) {
            $("#form_sendInvitationMailAddress").removeAttr("disabled");
            $("#form_sendInvitationMailAddress").attr("required", "");
        }
        else {
            $("#form_sendInvitationMailAddress").attr("disabled", "");
            $("#form_sendInvitationMailAddress").removeAttr("required");
        }
    });

});
