(function () {
    'use strict';

    angular
        .module('GotasApp')
        .directive('ui-select-max-length',
            function () {
                return {
                    restrict: 'A',
                    scope: {},
                    link: function (scope, element, attrs) {
                        var maxLength = Number(attrs.uiSelectMaxLength);
                        function fromInput(text) {
                            if (text.maxLength > maxLength) {
                                var transformedText = text.substring(0, maxLength);

                                return transformedText;
                            }
                            return text;
                        }
                    }
                };

            }


        );

})();
