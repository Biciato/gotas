$(document).ready(function () {

    if ($("#show_form").val() === undefined) {
        $(".transportadora").hide();
    }


    if (window.location.href.indexOf("edit") > 0){
        $(".transportadora .cnpj").prop("readonly", true);
        $(".transportadora .cnpj").attr("required", false);
        $(".nome_fantasia").focus();

    } else {
        $(".cnpj").focus();
    }

    var populateData = function (data) {
        if (data != undefined) {
            // $(".transportadora #cnpj").val(data.cnpj);

            $(".transportadora .nome_fantasia").val(data.nome_fantasia);
            $(".transportadora .razao_social").val(data.razao_social);
            $(".transportadora .cep_transportadoras").val(data.cep);
            $(".transportadora .endereco_transportadoras").val(data.endereco);
            $(".transportadora .endereco_numero_transportadoras").val(data.endereco_numero);
            $(".transportadora .endereco_complemento_transportadoras").val(data.endereco_complemento);
            $(".transportadora .bairro_transportadoras").val(data.bairro);
            $(".transportadora .municipio_transportadoras").val(data.municipio);
            $(".transportadora .estado_transportadoras").val(data.estado);
            $(".transportadora .pais_transportadoras").val(data.pais);
            $(".transportadora .tel_fixo").val(data.tel_fixo);
            $(".transportadora .tel_celular").val(data.tel_celular);

            // $(".transportadora #cnpj_validation").text('Registro localizado.');
        } else {
            $(".transportadora .nome_fantasia").val(undefined);
            $(".transportadora .razao_social").val(undefined);
            $(".transportadora .cep_transportadoras").val(undefined);
            $(".transportadora .endereco_transportadoras").val(undefined);
            $(".transportadora .endereco_numero_transportadoras").val(undefined);
            $(".transportadora .endereco_complemento_transportadoras").val(undefined);
            $(".transportadora .bairro_transportadoras").val(undefined);
            $(".transportadora .municipio_transportadoras").val(undefined);
            $(".transportadora .estado_transportadoras").val(undefined);
            $(".transportadora .pais_transportadoras").val(undefined);
            $(".transportadora .tel_fixo").val(undefined);
            $(".transportadora .tel_celular").val(undefined);
            callModalError('Registro não localizado, será adicionado novo registro.');
        }

    }

    $(".cep_transportadoras").on('blur', function () {

        getCEPTransportadora(this);
    });

    $(".transportadora .cnpj").mask('99.999.999/9999-99');
    $(".transportadora #tel_fixo").mask('(99)9999-9999');
    $(".transportadora #tel_celular").mask('(99)99999-9999');
    $(".transportadora #cep").mask('99.999-999');

    $(".transportadora .cnpj").on('blur', function () {
        console.log("oi");
        if (this.value.length == 18) {

            callLoaderAnimation();

            $.ajax({
                url: '/api/transportadoras/get_transportadora_by_cnpj',
                type: 'post',
                data: JSON.stringify({
                    cnpj: this.value.replace(/\D/g, '')
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json");
                    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                    xhr.setRequestHeader("IsMobile", true);
                },
                success: function (e) {
                    console.log(e);
                },
                error: function (e) {
                    console.log(e);

                    closeLoaderAnimation();
                }
            }).done(function (result) {
                closeLoaderAnimation();

                populateData(result.transportadora);
            })
        }
    });
});


var getCEPTransportadora = function (parameter) {

    //Nova variável "cep" somente com dígitos.
    var cep = $(parameter).val().replace(/\D/g, '');

    //Verifica se campo cep possui valor informado.
    if (cep != "") {

        //Expressão regular para validar o CEP.
        var validacep = /^[0-9]{8}$/;

        //Valida o formato do CEP.
        if (validacep.test(cep)) {
            callLoaderAnimation("Pesquisando CEP...");

            //Consulta o webservice viacep.com.br/
            $.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

                if (!("erro" in dados)) {
                    //Atualiza os campos com os valores da consulta.
                    $(".endereco_transportadoras").val(dados.logradouro);
                    $(".bairro_transportadoras").val(dados.bairro);
                    $(".municipio_transportadoras").val(dados.localidade);
                    $(".estado_transportadoras").val(dados.uf);
                    $(".pais_transportadoras").val("Brasil");

                    closeLoaderAnimation();
                } //end if.
                else {
                    //CEP pesquisado não foi encontrado.
                    //limpa_formulário_cep();
                    closeLoaderAnimation();

                    callModalError("CEP não encontrado.");

                }
            });
        }
        else {
            //cep é inválido.

            callModalError("Formato de CEP inválido.");
        }
    }
}
