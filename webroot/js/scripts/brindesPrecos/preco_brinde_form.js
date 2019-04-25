'use strict';
$(document).ready(function () {

    var defineFormatoPrecosVenda = function (value) {
        if (value){
            $("#preco_atual").maskMoney();
            $("#preco_atual").attr('maxlength', 10);
            $("#preco").maskMoney();
            $("#preco").attr('maxlength', 10);
            $("#valor_moeda_venda").maskMoney();
            $("#valor_moeda_venda").attr('maxlength', 10);
        } else {
            $("#preco").maskMoney('destroy');
            $("#preco").attr('maxlength', 10);
            $("#valor_moeda_venda").maskMoney('destroy');
            $("#valor_moeda_venda").attr('maxlength', 10);
        }
    };

    var defineObrigatoriedadeCampos = function(){
        var valor = $("#tipo_venda").val();
        if (valor == "Com Desconto"){
            $("label[for=preco]").val("Preço em Gotas*");
            $("#preco").attr("required", true);
            $("label[for=valor_moeda_venda]").val("Preço (R$ / venda avulsa)*");
            $("#valor_moeda_venda").attr("required", true);
            validarForm(false);
        } else if (valor == "Gotas ou Reais"){
            validarForm(true);
        }
    };

    /**
     * preco_brinde_form::verificaPreenchimentoCamposPreco
     *
     * Verifica se o preenchimento de campos está de acordo com a regra no submit
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-03-30
     *
     * @return void
     */
    var verificaPreenchimentoCamposPreco = function(){
        var valorGotas = $("#preco").val();

        if (parseFloat(valorGotas) == 0 || valorGotas.length == 0) {
            $("#preco").val(null);
            $("#valor_moeda_venda").attr("required", true);
        } else {
            $("#valor_moeda_venda").attr("required", false);
        }

        var valorMoeda = $("#valor_moeda_venda").val();

        if (parseFloat(valorMoeda) == 0 || valorMoeda.length == 0) {
            $("#valor_moeda_venda").val(null);
            $("#preco").attr("required", true);
        } else {
            $("#preco").attr("required", false);
        }

        defineFormatoPrecosVenda(true);
    };

    /**
     * preco_brinde_form::validarForm
     *
     * Remove validação padrão, e adiciona comportamento específico da tela.
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-03-31
     */
    var validarForm = function (value) {
        $(".botao-confirmar").unbind("click");

        if (value) {

            $(".botao-confirmar").on("click", function (e) {
                var form = e.target.form;

                // remove mascara dos campos de valor, e se valor for 0, limpa.
                defineFormatoPrecosVenda(false);
                verificaPreenchimentoCamposPreco();
                // form é válido?
                if (form.checkValidity()) {
                    callLoaderAnimation();

                } else {
                    // return false;
                }
            });
        } else {
            validacaoGenericaForm();
        }

    };

    defineObrigatoriedadeCampos();
    defineFormatoPrecosVenda(true);
});
