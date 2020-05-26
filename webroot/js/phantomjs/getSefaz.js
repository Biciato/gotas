var page = require('webpage').create(),
    system = require('system'),
    resources = [],
    statusPage = 0;
page.settings.userAgent = 'SpecialAgent';
page.customHeaders = {
    "Content-Type": "application/json",
    "Accept": "application/json"
};

var args = system.args;
var url = args[1];
var getLink = "";

if (args[2] !== undefined) {
    getLink = args[2] === "true" ? true : false;
}

function onPageReady() {
    var htmlContent = page.evaluate(function () {
        return document.documentElement.outerHTML;
    });
    console.log(htmlContent);
    phantom.exit();
}

/**
 * getSefaz::getPageContent
 * 
 * Obtem conteúdo da página
 * 
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-06-13
 * 
 * @returns JSON string
 */
function getPageContent() {
    var status = JSON.stringify(resources[0].status);
    var link = page.evaluate(function (status, page) {
        var result = {
            "response": document.documentElement.outerHTML,
            "statusCode": status,
            "url": page.url,
            "status": status < 400 ? "online" : "offline"
        };
        return JSON.stringify(result);

    }, status, page);

    console.log(link);
    phantom.exit();
};

/**
 * 
 */
function getRealLink() {
    var link = page.evaluate(function () {
        return document.getElementById("iframeConteudo").getAttribute("src");
    });
    console.log(link);
    phantom.exit();
}

page.open(url, function (status) {
    /**
     * Verifica se link está ativo, se conseguir success, pega o link que precisa para próximo passo
     */
    function checkReadyState() {

        setTimeout(function () {

            var state = page.evaluate(function () {
                return document.readyState;
            });

            if (state === "complete") {
                if (getLink) {

                    getRealLink();
                } else {
                    // @todo obter conteúdo
                    getPageContent();
                }
            } else {
                checkReadyState();
            }
        }, 1);
    }

    checkReadyState();
});
page.onResourceReceived = function (response) {

    // statusPage = response.status;

    // return resources;

    // terminou carregamento 
    if (response.stage !== "end") return;
    resources.push(response);

    // if (response.headers.filter(function (header) {
    //     if (header.name == "contentType" && header.value.indexOf("text/html") == 0) {
    //         return true;
    //     }
    //     return false;
    // }).length > 0) {
    // }
};

// getPageContent(url);
// phantom.exit();

// function onPageReady() {
//     var htmlContent = page.evaluate(function () {
//         return document.documentElement.outerHTML;
//     });
//     console.log(htmlContent);
//     phantom.exit();
// }

// // page.injectJs('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
// page.open(url, function (status) {
//     function checkReadyState() {
//         console.log(status);
//         setTimeout(function () {
//             var readyState = page.evaluate(function () {
//                 return document.readyState;
//             });

//             if ("complete" === readyState) {
//                 onPageReady();
//             } else {
//                 checkReadyState();
//             }
//         }, 1000);
//     }

//     checkReadyState();
// });

// http://jonnnnyw.github.io/php-phantomjs/