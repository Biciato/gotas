var sefazService = {

    getDetailsQRCode: function (qrCode) {
        return Promise.resolve($.ajax({
            type: "GET",
            url: "/api/sefaz/get_nf_sefaz_qr_code",
            data: {
                qr_code: qrCode
            },
            dataType: "JSON"

        }));
    }
};
