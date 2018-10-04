$(document).ready(function () {

    if ($("#show_form").val() === undefined) {
        $(".transportadora").hide();
    }

    var populateData = function (data) {
        if (data != undefined) {
            // $(".transportadora #cnpj").val(data.cnpj);
            $(".transportadora #nome_fantasia").val(data.nome_fantasia);
            $(".transportadora #razao_social").val(data.razao_social);
            $(".transportadora #cep").val(data.cep);
            $(".transportadora #endereco").val(data.endereco);
            $(".transportadora #numero").val(data.endereco_numero);
            $(".transportadora #endereco_complemento").val(data.endereco_complemento);
            $(".transportadora #bairro").val(data.bairro);
            $(".transportadora #municipio").val(data.municipio);
            $(".transportadora #estado").val(data.estado);
            $(".transportadora #pais").val(data.pais);
            $(".transportadora #tel_fixo").val(data.tel_fixo);
            $(".transportadora #tel_celular").val(data.tel_celular);

            $(".transportadora #cnpj_validation").text('Registro localizado.');
        } else {
            $(".transportadora #nome_fantasia").val(null);
            $(".transportadora #razao_social").val(null);
            $(".transportadora #endereco").val(null);
            $(".transportadora #endereco_complemento").val(null);
            $(".transportadora #bairro").val(null);
            $(".transportadora #municipio").val(null);
            $(".transportadora #estado").val(null);
            $(".transportadora #pais").val(null);
            $(".transportadora #tel_fixo").val(null);
            $(".transportadora #tel_celular").val(null);
            $(".transportadora #cnpj_validation").text('Registro não localizado, será adicionado novo registro.');
        }

    }

    $(".cep_transportadoras").on('blur', function () {

        getCEPTransportadora(this);
    });

    $(".transportadora #cnpj").mask('99.999.999/9999-99');
    $(".transportadora #tel_fixo").mask('(99)9999-9999');
    $(".transportadora #tel_celular").mask('(99)99999-9999');
    $(".transportadora #cep").mask('99.999-999');

    $("#cnpj").on('keyup', function () {
        if (this.value.length == 18) {

            callLoaderAnimation();

            $.ajax({
                url: '/transportadoras/findTransportadoraByCNPJ',
                type: 'post',
                data: JSON.stringify({
                    cnpj: this.value
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json");
                    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
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
