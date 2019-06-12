var system = require('system');
var page = require('webpage').create();
page.settings.userAgent = 'SpecialAgent';

var args = system.args;
var url = args[1];
var getLink = "";

if (args[2] !== undefined) {
    getLink = args[2] === "true" ? true : false;
}

// console.log('oi');
function onPageReady() {
    var htmlContent = page.evaluate(function () {
        return document.documentElement.outerHTML;
    });
    console.log(htmlContent);
    phantom.exit();
}

function getPageContent() {
    var link = page.evaluate(function () {
        // return page.plainText;
        return document.documentElement.outerHTML;
        // return page.content;

    });

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