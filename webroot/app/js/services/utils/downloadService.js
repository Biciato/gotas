'use strict';

angular
    .module('GotasApp')
    .service('downloadService', downloadService);

downloadService.$inject = ['FileSaver', 'Blob'];
function downloadService(FileSaver, Blob) {
    $self = {
        downloadExcel: downloadExcel
    }

    ////////////////


    function downloadExcel(excelData, reportName) {
        var excel = JSON.parse(excelData);
        var blob = new Blob([excel], {
            type: 'application/xml;charset=utf-8',
            encoding: "utf-8"
        });
        FileSaver.saveAs(blob, reportName + ".xls");
    }

    return $self;
};
