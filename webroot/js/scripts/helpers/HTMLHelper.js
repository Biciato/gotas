/**
 * Arquivo de plugin para funcionalidades básicas
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-21
 */
(function ($) {

    /**
     * Define mascara de CPF
     *
     * @param {Object} options
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-25
     */
    $.fn.MaskCPF = function (options) {
        var settings = $.extend({}, options);

        let onBlur = function (event) {
            let value = this.value;
            value = value.replace(/\D/g, "").substr(0, 11);

            // Define máscara ao perder o foco e define limite de caracteres na string de retorno
            this.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/gm, "$1.$2.$3-$4");
        };

        let onFocus = function (event) {
            $(this).prop("maxlength", 11);
            this.value = this.value.replace(/\D/gm, "");
        };

        let onKeydown = function (event) {
            // Em caso de key enter, formata o campo
            if (event.keyCode === 13) {
                let value = this.value;
                value = value.replace(/\D/g, "").substr(0, 11);

                // Define máscara ao perder o foco e define limite de caracteres na string de retorno
                this.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/gm, "$1.$2.$3-$4");
            }
        };

        let onKeyup = function (event) {
            let value = this.value;

            // Garante que terá somente números
            if (event.keyCode !== 13) {
                this.value = value.replace(/\D/gm, "");
            }
        };

        $(this)
            .off("keydown", onKeydown)
            .off("keyup", onKeyup)
            .off("focus", onFocus)
            .off("blur", onBlur)
            .on("keydown", onKeydown)
            .on("keyup", onKeyup)
            .on("focus", onFocus)
            .on("blur", onBlur);
    };

    /**
     * Define mascara de telefone
     *
     * @param {Object} options
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-25
     */
    $.fn.MaskTelephone = function (options) {
        var settings = $.extend({
            maxlength: 11
        }, options);

        let onBlur = function (event) {
            let value = this.value;
            value = value.replace(/\D/g, "").substr(0, settings.maxlength);

            let mask = {
                areaCode: 2,
                prefix: settings.maxlength === 11 && value.length === 11 ? 5 : 4,
                suffix: 4
            };

            let replace = `(\\d{${mask.areaCode}})(\\d{${mask.prefix}})(\\d{${mask.suffix}})`;
            let regex = new RegExp(replace, "g");

            // Define máscara ao perder o foco e define limite de caracteres na string de retorno
            this.value = value.replace(regex, "($1)$2-$3").substr(0, mask.areaCode + mask.prefix + mask.suffix + 3);
        };

        let onFocus = function (event) {
            $(this).prop("maxlength", settings.maxlength);
            this.value = this.value.replace(/\D/gm, "");
        };

        let onKeydown = function (event) {
            // Em caso de key enter, formata o campo
            if (event.keyCode === 13) {
                let value = this.value;
                value = value.replace(/\D/g, "").substr(0, settings.maxlength);

                let mask = {
                    areaCode: 2,
                    prefix: settings.maxlength === 11 && value.length === 11 ? 5 : 4,
                    suffix: 4
                };

                let replace = `(\\d{${mask.areaCode}})(\\d{${mask.prefix}})(\\d{${mask.suffix}})`;
                let regex = new RegExp(replace, "g");

                // Define máscara ao perder o foco e define limite de caracteres na string de retorno
                this.value = value.replace(regex, "($1)$2-$3").substr(0, mask.areaCode + mask.prefix + mask.suffix + 3);
            }
        };

        let onKeyup = function (event) {
            let value = this.value;

            // Garante que terá somente números
            if (event.keyCode !== 13) {
                this.value = value.replace(/\D/gm, "");
            }
        }

        $(this)
            .off("keydown", onKeydown)
            .off("keyup", onKeyup)
            .off("focus", onFocus)
            .off("blur", onBlur)
            .on("keydown", onKeydown)
            .on("keyup", onKeyup)
            .on("focus", onFocus)
            .on("blur", onBlur);
    };

    $.fn.MaskFloat = function (options) {
        var settings = $.extend({
            allowNegative: false,
            decimals: 3,
            max: 99999999999999999999,
            separator: '.'
        }, options);

        var keyup = function (event) {
            let value = this.value;
            let negativeSymbol = value.indexOf("-") === 0 ? "-" : "";

            // Se já tiver o sinal - no valor, e for digitado novamente o -, apaga
            if ([109, 189].includes(event.keyCode)) {
                negativeSymbol = value.indexOf("-") === 0 ? "" : "-";
                negativeSymbol = settings.allowNegative ? negativeSymbol : "";
            }

            // Remove símbolo negativo se pressionado novamente
            // if (value.indexOf("-") === 0 && negativeSymbol === "-") negativeSymbol = "";

            if (value !== undefined && value !== null) {
                let divisor = "1";

                // Para cada decimal, aumenta um zero na divisão do float para formatação
                for (let i = 0; i < settings.decimals; i++) {
                    divisor += "0";
                }

                value = value.replace(/\D/g, "");
                value = parseFloat(value) / divisor;

                value = Number.isNaN(value) ? 0.000 : value;

                if (value > settings.max)
                    value = settings.max;

                if (value === 0) {
                    negativeSymbol = "";
                }

                this.value = negativeSymbol + value.toFixed(settings.decimals);
            }
        }

        $(this)
            .off("keyup", keyup)
            .on("keyup", keyup);

        return this;
    };

})(jQuery);
