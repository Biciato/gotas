var system = require('system');
var page = require('webpage').create();
page.settings.userAgent = 'SpecialAgent';

var args = system.args;
var url = args[1];

function onPageReady() {
    var htmlContent = page.evaluate(function () {
        // // Apparently framesCount doesn't include the main frame so add 1
        // var frameCount = page.framesCount + 1
        // var html = page.frameContent + '\n\n'
        // for (var i = 1; i < frameCount; ++i) {
        //     page.switchToFrame(i)
        //     html += page.frameContent + '\n\n'
        // }
        // return html;
        return document.documentElement.outerHTML;
        // return page.frameContent;
    });
    console.log(htmlContent);
    phantom.exit();
}

page.injectJs('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
page.open(url, function (status) {



    function checkReadyState() {
        console.log(status);
        setTimeout(function () {
            var readyState = page.evaluate(function () {
                return document.readyState;
            });

            if ("complete" === readyState) {
                onPageReady();
            } else {
                checkReadyState();
            }
        }, 1000);
    }

    checkReadyState();
});

// http://jonnnnyw.github.io/php-phantomjs/