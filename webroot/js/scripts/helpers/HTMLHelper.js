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

        $(this)
            .off("keyup")
            .on("keyup", function (event) {
                let value = this.value;

                // Garante que terá somente números
                this.value = value.replace(/\D/gm, "");
            })
            .off("focus")
            .on("focus", function (event) {
                $(this).prop("maxlength", 11);
                this.value = this.value.replace(/\D/gm, "");
            })
            .off("blur")
            .on("blur", function (event) {
                let value = this.value;
                value = value.replace(/\D/g, "").substr(0, 11);

                // Define máscara ao perder o foco e define limite de caracteres na string de retorno
                this.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/gm, "$1.$2.$3-$4");
            });
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

        $(this)
            .off("keyup")
            .on("keyup", function (event) {
                let value = this.value;

                // Garante que terá somente números
                this.value = value.replace(/\D/gm, "");
            })
            .off("focus")
            .on("focus", function (event) {
                $(this).prop("maxlength", settings.maxlength);
                this.value = this.value.replace(/\D/gm, "");
            })
            .off("blur")
            .on("blur", function (event) {
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
            });
    };

    $.fn.MaskFloat = function (options) {
        var settings = $.extend({
            max: 99999999999999999999,
        }, options);

        $(this)
            .off("keyup")
            .on("keyup", function (event) {
                let value = this.value;

                if (value !== undefined && value !== null) {
                    value = value.replace(/\D/g, "");
                    value = parseFloat(value) / 1000;

                    value = Number.isNaN(value) ? 0.000 : value;

                    if (value > settings.max)
                        value = settings.max;

                    this.value = value.toFixed(3);
                }
            });
        return this;
    };

})(jQuery);
