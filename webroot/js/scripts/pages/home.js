/**
 * Classe javascript para ações de uso comum
 */
$(document).ready(function() {
    $(".botao-confirmar").on("click", function(e) {
        var form = e.target.form;

        var isValid = form.checkValidity();

        if (isValid) {
            callLoaderAnimation("");
        }
    });

    $(".botao-pesquisar").on("click", function() {
        callLoaderAnimation();
    });

    $(".botao-cancelar").on("click", function() {
        callLoaderAnimation();
    });

    $(".botao-navegacao-tabela").on("click", function() {
        callLoaderAnimation();
    });

    /**
     * Adiciona comportamento de sub-menu de dropdown (bootstrap)
     */
    $(".dropdown-submenu a.test").on("click", function(e) {
        $(this).next("ul").toggle();
        e.stopPropagation();
        e.preventDefault();
    });

    /**
     * Imprime o conteúdo de uma tabela
     */
    $(".btn-print-html").on("click", function(e) {
        $(".table-export").printThis();
    });

    /**
     * Imprime o conteúdo de uma tabela
     */
    $(".btn-export-html").on("click", function(e) {
        // $(".table-export").html();
        window.open(
            "data:application/vnd.ms-excel," +
                encodeURIComponent($(".table-export").html())
        );
        e.preventDefault();
    });

    /**
     * Configura exibição de modal para actions do tipo postlink
     *
     * @param {string} parameter
     */
    var addModalBootstrapPopup = function(parameter) {
        $("#" + parameter).on("show.bs.modal", function(e) {
            $(this)
                .find("form")
                .attr("action", $(e.relatedTarget).data("action"));
        });

        $("#" + parameter + " #submit_button").on("click", function(e) {
            $("#" + parameter)
                .find("form")
                .submit();
        });
    };

    var parameters = [
        "modal-invalidate",
        "modal-validate",
        "modal-delete",
        "modal-confirm",
        "modal-confirm-entire-network",
        "modal-manage-unit",
        "modal-quit-manage-unit"
    ];

    parameters.forEach(function(element) {
        addModalBootstrapPopup(element);
    }, this);

    /**
     * Adiciona informações no corpo do modal
     *
     * @param {*} parameter
     */
    var addModalBootstrapPopupWithMessage = function(parameter) {
        $("#" + parameter).on("show.bs.modal", function(e) {
            $(this)
                .find("form")
                .attr("action", $(e.relatedTarget).data("action"));

            $("#" + parameter)
                .find("p.modal-body-content")
                .text($(e.relatedTarget).attr("data-message"));
        });

        $("#" + parameter + " #submit_button").on("click", function(e) {
            $("#" + parameter)
                .find("form")
                .submit();
            callLoaderAnimation();
        });
    };

    var parametersWithMessage = [
        "modal-confirm-with-message",
        "modal-delete-with-message"
    ];

    parametersWithMessage.forEach(function(element) {
        addModalBootstrapPopupWithMessage(element);
    });

    /**
     * Adiciona informações no corpo do modal
     *
     * @param {*} parameter
     */
    var addModalBootstrapPopupWithMessageConfirmation = function(parameter) {
        $("#" + parameter).on("show.bs.modal", function(e) {
            $(this)
                .find("#modal-body-content-append")
                .empty();

            var action = $(e.relatedTarget).data("action");

            action = action.substr(action.indexOf("?") + 1);

            var actionArray = action.split("&");
            var arrayElements = [];
            actionArray.forEach(element => {
                console.log(element);

                var posicaoIgual = element.indexOf("=");
                var id = element.substr(0, posicaoIgual);
                var valor = element.substr(posicaoIgual + 1);

                arrayElements.push({ id: id, value: valor });

                $(this)
                    .find("#modal-body-content-append")
                    .append(
                        "<input type='text' class='hidden' name='" +
                            id +
                            "' id='" +
                            id +
                            "' value='" +
                            valor +
                            "' />"
                    );
            });
            console.log(action);

            $(this)
                .find("form")
                .attr("action", $(e.relatedTarget).data("action"));

            $("#" + parameter)
                .find("p.modal-body-content")
                .text($(e.relatedTarget).attr("data-message"));
        });

        $("#" + parameter + " #submit_button").on("click", function(e) {
            if (
                $(this.form)
                    .find("#senha_usuario")
                    .val().length > 0
            ) {
                callLoaderAnimation();
                $("#" + parameter)
                    .find("form")
                    .submit();
            }
        });
    };

    var parametersWithMessageConfirmation = [
        "modal-delete-with-message-confirmation"
    ];

    parametersWithMessageConfirmation.forEach(function(element) {
        addModalBootstrapPopupWithMessageConfirmation(element);
    });

    /**
     * Adiciona espaço na tela caso o gerenciamento de unidades esteja visível
     */
    if ($(".branch-management").is(":visible")) {
        var height = $("body").css("height");

        height = parseFloat(height) + 150;

        $("body").css("height", height);
    }

    $(".cep").on("blur", function() {
        getCEP(this);
    });
});

/**
 * Abre janela de Modal que exibe conteúdo de mensagem, procura pelo atributo setado
 */
var callHowItWorks = function(data) {
    // abre modal
    $(".modal-how-it-works").modal();

    // seta o titulo
    var title = $(data).find(".modal-how-it-works-title")[0].innerHTML;
    $(".modal-how-it-works .modal-title-content").html(title);

    // seta o conteúdo

    var content = $(data).find(".modal-how-it-works-body")[0].innerHTML;
    $(".modal-how-it-works .modal-body-content").html(content);
};

/**
 * Chama a modal de confirmação ao gravar
 * @param {object} content
 */
var callModalSave = function(content) {
    closeLoaderAnimation();
    $(".modal-save").modal();

    $(".modal-save .modal-body-table-content").empty();

    if (content == undefined) {
        $(".modal-save .table-content").hide();
    } else {
        $(".modal-save .table-content").show();
        $(".modal-save .modal-body-table-content").append(content);
    }
};

var callModalError = function(error, arrayContent) {
    closeLoaderAnimation();
    $(".modal-error .modal-body-content").html(error);
    $(".modal-error .modal-body-content-description").empty();

    if (arrayContent != undefined && arrayContent.length > 0) {
        $(".modal-error .modal-body-content-description").empty();
        $.each(arrayContent, function(index, value) {
            $(".modal-error .modal-body-content-description").append(
                "(" + (parseInt(index) + 1) + ")  " + value + "<br />"
            );
        });
    }
    $(".modal-error").modal();
};

/**
 * Apresenta tela de loading
 *
 * @param string text-info Texto para informação
 *
 */
var callLoaderAnimation = function(text_info) {
    // $(".modal-loader").modal();
    $(".loading").show();
    // $(".modal-loader").modal();

    $(".loading-message").text("");
    if (text_info !== undefined && text_info.length > 0) {
        $(".loading-message").text(text_info);
    }
};

/**
 * Fecha tela de loading
 */
var closeLoaderAnimation = function() {
    $(".loading").hide();

    // $(".modal-loader").modal("hide");
};

/**
 * Procura todas as ocorrências de um termo em uma string
 * @param {string} haystack
 * @param {string} needle
 */
var getAllIndexes = function(haystack, needle) {
    var indexes = [];

    for (index = 0; index < haystack.length; index++) {
        if (haystack[index] == needle) {
            indexes.push(index);
        }
    }

    return indexes;
};

/**
 * Obtêm dados de CEP
 */
var getCEP = function(parameter) {
    //Nova variável "cep" somente com dígitos.
    var cep = $(parameter)
        .val()
        .replace(/\D/g, "");

    //Verifica se campo cep possui valor informado.
    if (cep != "") {
        //Expressão regular para validar o CEP.
        var validacep = /^[0-9]{8}$/;

        //Valida o formato do CEP.
        if (validacep.test(cep)) {
            callLoaderAnimation("Pesquisando CEP...");

            getGeolocalizationGoogle(cep);

            //Consulta o webservice viacep.com.br/
            $.ajax({
                type: "GET",
                url: "https://viacep.com.br/ws/" + cep + "/json/",
                complete: function(success) {
                    console.log(success);
                    var dados = success.responseJSON;
                    $(".endereco").val(dados.logradouro);
                    $(".bairro").val(dados.bairro);
                    $(".municipio").val(dados.localidade);
                    $(".estado").val(dados.uf);
                    $(".pais").val("Brasil");
                },
                error: function(err) {
                    //end if.
                    //CEP pesquisado não foi encontrado.
                    //limpa_formulário_cep();
                    callModalError("CEP não encontrado.");
                    console.log(err);
                }
            });

            closeLoaderAnimation();

            // $.getJSON(
            //     "https://viacep.com.br/ws/" + cep + "/json/?callback=?",
            //     function (dados) {
            //         if (!("erro" in dados)) {
            //             //Atualiza os campos com os valores da consulta.
            //             $(".endereco").val(dados.logradouro);
            //             $(".bairro").val(dados.bairro);
            //             $(".municipio").val(dados.localidade);
            //             $(".estado").val(dados.uf);
            //             $(".pais").val("Brasil");

            //             closeLoaderAnimation();
            //         } else {
            //             //end if.
            //             //CEP pesquisado não foi encontrado.
            //             //limpa_formulário_cep();
            //             closeLoaderAnimation();

            //             callModalError("CEP não encontrado.");
            //         }
            //     }
            // );
        } else {
            callModalError("Formato de CEP inválido.");
        }
    }
};

function initMap() {
    // Google maps are now initialized.
}

var getGeolocalizationGoogle = function(cep) {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: cep }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if ($("#latitude").length == 1) {
                var latitude = results[0].geometry.location.lat().toString();

                var virgulaLatPos = latitude.indexOf(".");

                if (virgulaLatPos > 0) {
                    latitude = latitude.substr(0, virgulaLatPos + 7);
                }

                $("#latitude").val(latitude);
            }
            if ($("#longitude").length == 1) {
                var longitude = results[0].geometry.location.lng().toString();

                var virgulaLongPos = longitude.indexOf(".");

                if (virgulaLongPos > 0) {
                    longitude = longitude.substr(0, virgulaLongPos + 7);
                }

                $("#longitude").val(longitude);
            }
        } else {
            callModalError(
                "Google não encontrou Latitude/Longitude pelo CEP informado! Informe Latitude/Longitude manualmente!"
            );
        }
    });
};

/**
 * Prepara conteúdo para exibir após gravar dados de Pontuações do usuário
 * @param {*} data Dados da gravação de pontuacoes
 */
var prepareContentPontuacoesDisplay = function(data) {
    var content = $("<div></div>");

    var usuario = data.pontuacoes_comprovantes.usuario;
    var pontuacoes = data.pontuacoes_comprovantes.pontuacoes;
    var somaPontuacoes = data.pontuacoes_comprovantes.soma_pontuacoes;

    var title = $(
        "<legend>" +
            "Dados gravados para o usuário " +
            usuario.nome +
            "</legend>"
    );

    var table = $(
        "<table class='table table-responsive table-hover table-condensed'></table>"
    );

    var header = $("<thead><th>Produto</th><th>Gota</th></thead>");

    table.append(header);

    var rows = [];
    $.each(pontuacoes, function(index, pontuacao) {
        var row =
            "<tr><td>" +
            pontuacao.gota.nome_parametro +
            "</td><td>" +
            pontuacao.quantidade_gotas +
            "</td></tr>";
        table.append(row);
    });

    var total = $(
        "<table class='table table-responsive'><th>Total:</th><td> " +
            somaPontuacoes +
            "</td></table>"
    );

    content.append(title);
    content.append(table);

    content.append(total);

    return content;
};

var formatDateTimeToDate = function(data) {
    var dataToReturn = data.substr(0, data.indexOf("+"));

    dataToReturn = new Date(dataToReturn);

    var month =
        dataToReturn.getMonth() < 10
            ? "0" + (dataToReturn.getMonth() + 1)
            : dataToReturn.getMonth() + 1;
    var day =
        dataToReturn.getDay() < 10
            ? "0" + (dataToReturn.getDay() + 1)
            : dataToReturn.getDay() + 1;
    var year = dataToReturn.getFullYear();

    return day + "/" + month + "/" + year;
};

var generateQRCode = function(element, value) {
    // https://larsjung.de/jquery-qrcode/
    console.log("teste");
    var options = {
        // render method: 'canvas', 'image' or 'div'
        render: "image",
        minVersion: 1,
        maxVersion: 40,
        // offset in pixel if drawn onto existing canvas
        left: 0,
        top: 0,
        // corner radius relative to module width: 0.0 .. 0.5
        radius: 0,

        // quiet zone in modules
        quiet: 0,

        mSize: 0.1,
        mPosX: 0.5,
        mPosY: 0.5,

        // label: "no label",
        fontname: "sans",
        fontcolor: "#000",

        image: null,
        // error correction level: 'L', 'M', 'Q' or 'H'
        ecLevel: "L",
        // background color or image element, null for transparent background
        background: null,
        // code color or image element
        fill: "#000",

        // modes
        // 0: normal
        // 1: label strip
        // 2: label box
        // 3: image strip
        // 4: image box
        mode: 0,
        text: value,
        size: 240
    };
    $(element).qrcode(options);
};

/**
 * Comportamento padrão em campo de Date Picker
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2018-12-26
 *
 * @param {*} ev
 * @param {*} value
 */
var defaultKeyUpDatePickerAction = function(campo, ev, value) {
    var value = value.replace(/(\d{2})(\d{2})(\d{4})/g, "$1/$2/$3");
    if (
        value.length == 10 &&
        ((ev.keyCode >= 48 && ev.keyCode <= 57) ||
            (ev.keyCode >= 96 && ev.keyCode <= 105))
    ) {
        updateDatePicker(campo, value);
    }
};

/**
 * Comportamento padrão em campo de Date Time Picker
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2018-12-26
 *
 * @param {*} ev
 * @param {*} value
 */
var defaultKeyUpDateTimePickerAction = function(campo, ev, value) {
    var value = value.replace(/(\d{2})(\d{2})(\d{4})(\d{2})(\d{2)/g,"$1/$2/$3 $4:$5");
    if (value.length == 10 && ((ev.keyCode >= 48 && ev.keyCode <= 57) || (ev.keyCode >= 96 && ev.keyCode <= 105))) {
        updateDateTimePicker(campo, value);
    }
};

/**
 * Prevê Enter de ser informado em campo de date picker
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2018-12-26
 *
 * @param {event} ev Evento
 */
var preventEnterActionInput = function(ev) {
    if (ev.keyCode === 13) {
        ev.stopPropagation();
        ev.preventDefault();

        return false;
    }
};

/**
 * home::initializeDatePicker
 *
 * Inicializa um campo como date picker
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2018-12-26
 *
 * @param {string} campo Campo a ser inicializado
 *
 * @return void
 */
var initializeDatePicker = function(campo) {
    $("#" + campo).datepicker({
        minView: 2,
        maxView: 2,
        clearBtn: true,
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        forceParse: false,
        language: "pt-BR",
        format: "dd/mm/yyyy",
        initialDate: new Date()
    });

    $("#" + campo)
        .on("keyup", function(ev) {
            preventEnterActionInput(ev);
            defaultKeyUpDatePickerAction(campo, ev, this.value);
        })
        .on("keydown", function(ev) {
            preventEnterActionInput(ev);
        });
};

/**
 * home::initializeDatePicker
 *
 * Inicializa um campo como date picker
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-03-04
 *
 * @param {string} campo Campo a ser inicializado
 * @param {string} campoOculto Campo oculto que será enviado ao server
 *
 * @return void
 */
var initializeDateTimePicker = function(
    campo,
    campoOculto,
    inicializarCampo = false,
    dataMaxima
) {
    console.log(dataMaxima);
    // Seta todos os campos DateTimePicker para Português Brasil
    moment.locale("pt-BR", {});
    var options = {
        locale: "pt-BR",
        format: "DD/MM/YYYY HH:mm",
        useCurrent: false
        // format: "d/m/Y H:i",
        // mask: "31/12/9999 23:59",
        // value: moment().format("DD/MM/YYYY HH:mm"),
        // step: 15,
        // allowBlank: false
    };

    if (dataMaxima != undefined){
        var format = "DD/MM/YYYY HH:mm";
        var maxDate = moment(dataMaxima, format).format(format);
        options.maxDate = maxDate;
    }

    $("#" + campo).datetimepicker(options);

    if (inicializarCampo != undefined && inicializarCampo) {
        var valor = moment().format("DD/MM/YYYY HH:mm");

        $("#" + campo).data("DateTimePicker").date(valor);
        if (campoOculto) {
            valor = moment().format("YYYY-MM-DD HH:mm");
            $("#" + campoOculto).val(valor);
        }
    }

    $("#" + campo)
        .on("keyup", function(ev) {
            preventEnterActionInput(ev);
            // defaultKeyUpDateTimePickerAction(campo, ev, this.value);
        })
        .on("keydown", function(ev) {
            preventEnterActionInput(ev);
        })
        .on("change", function(ev) {
            var value = ev.target.value;

            if (value != undefined && value.length > 0) {
                var valorEnviar = moment(value, "DD/MM/YYYY HH:mm").format(
                    "YYYY-MM-DD HH:mm"
                );
                $("#" + campoOculto).val(valorEnviar);
            }
        })
        .on("blur", function(ev) {
            var value = ev.target.value;

            if (value == "") {
                var data = moment(value, "DD/MM/YYYY HH:mm", true);

                if (!data.isValid()) {
                    var dataOculta = $("#" + campoOculto).val();

                    dataOculta = moment(dataOculta, "YYYY-MM-DD HH:mm", true);

                    if (dataOculta.isValid()) {
                        dataOculta = moment(dataOculta).format(
                            "DD/MM/YYYY HH:mm"
                        );
                        $("#" + campo).val(dataOculta);
                    }
                }
            }
        });
};

/**
 * home::updateDatePicker
 *
 * Atualiza o valor do campo de data do tipo date picker
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2018-12-26
 *
 * @param {string} campo Campo
 * @param {string} date Valor
 *
 * @return void
 */
var updateDatePicker = function(campo, date) {
    $("#" + campo).datepicker("update", date);
};

/**
 * home::updateDateTimePicker
 *
 * Atualiza o valor do campo de data do tipo date picker
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2018-12-26
 *
 * @param {string} campo Campo
 * @param {string} date Valor
 *
 * @return void
 */
var updateDateTimePicker = function(campo, date) {
    console.log(date);
    $("#" + campo).val(date);
};

/**
 * Popula dados de cupom para resgate
 */
var popularDadosCupomResgate = function(data) {
    if (data !== undefined && data !== null) {
        var usuario = null;
        var brinde_habilitado = {};
        var unidade_funcionario_id = 0;
        var cupom_emitido = null;
        var data_hora = null;
        var rows = [];

        $.each(data, function(index, value) {
            var valorPago = value.valor_pago;
            var tipoMoeda = value.tipo_venda == 0 ? "Gotas:" : "R$";

            data_hora = value.data;
            unidade_funcionario_id = value.unidade_funcionario_id;
            cupom_emitido = value.cupom_emitido;
            usuario = value.usuario;
            brinde_habilitado = value.clientes_has_brindes_habilitado;

            valorPago =
                valorPago.toString().indexOf(",") < 0
                    ? tipoMoeda + valorPago + ",00"
                    : tipoMoeda + valorPago;

            var row =
                "<tr><td>" +
                value.quantidade +
                "</td><td>" +
                brinde_habilitado.brinde.nome +
                "</td><td>" +
                valorPago +
                "</td></tr>";
            rows.push(row);
        });

        $(".tabela-produtos tbody").empty();
        $(".tabela-produtos tbody").append(rows);

        $(".impressao-cupom #print_data_emissao").text(data_hora);

        $(".impressao-cupom .cupom_emitido").val(cupom_emitido);
        $(".unidade-funcionario-id").val(unidade_funcionario_id);
        $(".nome-cliente-brinde-resgate").val(usuario.nome);
        $(".cpf-cliente-brinde-resgate").val(usuario.cpf);
        $(".data-nasc-cliente-brinde-resgate").val(
            formatDateTimeToDate(usuario.data_nasc)
        );

        $(
            ".impressao-resgate-cupom-canhoto-impressao #print_data_emissao"
        ).text(data_hora);
        $(".impressao-resgate-cupom-canhoto-impressao .usuario-final").text(
            usuario.nome
        );
    } else {
        $(".tabela-produtos tbody").empty();
        $(".cupom-resgatar").val(null);
        $(".unidade-funcionario-id").val(null);
        $(".nome-cliente-brinde-resgate").val(null);
        $(".cpf-cliente-brinde-resgate").val(null);
        $(".data-nasc-cliente-brinde-resgate").val(null);
    }
};

var imprimirCanhotoResgate = function() {
    setTimeout(
        $(".impressao-resgate-cupom-canhoto-impressao .print_area").printThis({
            importCss: false
        }),
        100
    );
};

/**
 * Reseta a aba de usuário
 */
var resetUserTab = function() {
    // exibe região de busca do usuário
    $(".user-query-region").show();

    // limpa os campos de busca do usuário
    $(".opcoes").val("nome");
    $(".opcoes").change();
    $("#parametro").val(null);

    $("#new-user-search").click();
    // limpa os campos armazenados de busca

    $("#usuarios_id").val(null);
    $("#usuariosNome").val(null);
    $("#usuariosDataNasc").val(null);
    $("#usuariosPontuacoes").val(null);

    // reseta o layout de usuário
    $(".video-capture-gotas-user-select-container").show();

    // reseta o layout de todos os formulários de inserção via QR Code
    $(".group-video-capture-gotas").show();
    $(".gotas-instascan-manual-insert").hide();
    $(".video-gotas-scanning-container").hide();

    $(".video-receipt-capture-container").hide();
    $(".video-receipt-captured-region").hide();

    $(".video-gotas-captured-region").hide();

    // reseta o layout de todos os formulários de inserção manual
    $(".gotas-camera-manual-insert").hide();
    $(".video-gotas-capture-container").hide();
};

/**
 * Reseta a aba de resgate de brindes
 */
var resetRedeemTab = function() {
    $(".resgate-cupom-main").show();
    $(".resgate-cupom-result").hide();

    popularDadosCupomResgate(null);

    $(".pdf-417-code").val(null);
};
