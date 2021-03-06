$(document).ready(function() {

    var loginMaskApplied = false;

    setInterval(function(){
        var login = $("#login").val();

        if (login.length < 3)
        {
            $("#login").unmask("999.999.999-99");
        }
    }, 10);

    $("#login").on('keyup', function(data) {

        var loginValue = $("#login").val();
        loginValue = loginValue.replace(/"."/g, '');
        loginValue = loginValue.replace(/-/g, '');
        if (this.value.length < 3 && loginMaskApplied) {
            $("#login").unmask("999.999.999-99");
            $("#login").val(this.value);
            loginMaskApplied = false;
        } else if (this.value.length >= 3) {

            if ($.isNumeric(loginValue)) {
                if (!loginMaskApplied) {
                    $("#login").mask("999.999.999-99");
                    loginMaskApplied = true;
                }
            }
        }
    });
});
