// var GotasApp = angular.module("GotasApp", ["ngRoute", "ngSanitize", "ui.mask", "ui.select", "ui.bootstrap"]);

var GotasApp = angular.module("GotasApp");

//filtro para limitar tamanho da frase incluindo tres pontos no final
GotasApp.filter('ellipsis', function () {
	return function (input, size) {
		if (input != undefined && input != null && input.length > 0) {
			var offset = 0;
			var i = 0;
			for (; i < input.length && offset <= size; i++) {
				if (input.charAt(i) == input.charAt(i).toUpperCase()) { //conte caracteres uppercase como 2
					offset += 1.5;
				} else {
					offset += 1;
				}
			}
			var output = input;
			if (input.length > i) { //caso realmente necessite de elipsis
				output = input.substring(0, i - 1) + "...";
			}
			return output;
		}
	};
});


GotasApp.filter('startFrom', function () {
	return function (input, start) {
		start = +start; //parse to int
		if (input !== undefined)
			return input.slice(start);
	};
});

GotasApp.run(function ($rootScope) {


    // ----------------------------------------------------------------------------------
    // --------------------------- Configuração de DatePicker ---------------------------
    // ----------------------------------------------------------------------------------

    $rootScope.today = function () {
        $rootScope.dt = new Date();
    };
    $rootScope.today();

    $rootScope.clear = function () {
        $rootScope.dt = null;
    };

    $rootScope.inlineOptions = {
        customClass: getDayClass,
        // minDate: new Date(),
        showWeeks: true
    };

    $rootScope.dateOptions = {
        // dateDisabled: disabled,
        formatYear: 'yy',
        // maxDate: new Date(2020, 5, 22),
        // minDate: new Date(),
        startingDay: 0,
        locale: 'pt_BR'
    };

    // Disable weekend selection
    function disabled(data) {
        var date = data.date,
            mode = data.mode;
        return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
    }

    $rootScope.toggleMin = function () {
        $rootScope.inlineOptions.minDate = $rootScope.inlineOptions.minDate ? null : new Date();
        $rootScope.dateOptions.minDate = $rootScope.inlineOptions.minDate;
    };

    // $rootScope.toggleMin();

    function getDayClass(data) {
        var date = data.date,
            mode = data.mode;
        if (mode === 'day') {
            var dayToCheck = new Date(date).setHours(0, 0, 0, 0);

            for (var i = 0; i < $rootScope.events.length; i++) {
                var currentDay = new Date($rootScope.events[i].date).setHours(0, 0, 0, 0);

                if (dayToCheck === currentDay) {
                    return $rootScope.events[i].status;
                }
            }
        }

        return '';
    }

    $rootScope.open1 = function () {
        $rootScope.popup1.opened = true;
    };

    $rootScope.open2 = function () {
        $rootScope.popup2.opened = true;
    };

    $rootScope.setDate = function (year, month, day) {
        $rootScope.dt = new Date(year, month, day);
    };

    $rootScope.formats = ['dd/MM/yyyy', 'dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $rootScope.format = $rootScope.formats[0];
    $rootScope.altInputFormats = ['M!/d!/yyyy'];

    $rootScope.popup1 = {
        opened: false
    };

    $rootScope.popup2 = {
        opened: false
    };

    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);

    var afterTomorrow = new Date();
    afterTomorrow.setDate(tomorrow.getDate() + 1);

    $rootScope.events = [
        {
            date: tomorrow,
            status: 'full'
        },
        {
            date: afterTomorrow,
            status: 'partially'
        }
    ];

    // ----------------------------------------------------------------------------------
    // --------------------------- Configuração de DatePicker ---------------------------
    // ----------------------------------------------------------------------------------
}
);
