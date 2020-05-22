/**
 * Arquivo de plugin para funcionalidades básicas
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-21
 */
(function ($) {
    $.fn.MaskFloat = function (options) {
        var settings = $.extend({
            max: 99999999999999999999,
        }, options);

        $(this).on("keyup", function (event) {
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
