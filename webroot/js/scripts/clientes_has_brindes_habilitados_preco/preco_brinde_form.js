$(document).ready(function () {
    $("#preco_atual").maskMoney();
    $("#preco_atual").attr('maxlength', 10);

    $("#preco").maskMoney();
    $("#preco").attr('maxlength', 10);
});
