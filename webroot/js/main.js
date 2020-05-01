var sammy = Sammy("#content-html", function () {
    var self = this;
    self.use(Sammy.Template);

    // Caminho para página não encontrada
    self.notFound = function (verb, path) {
        self.runRoute('get', '#/404');
    }

    self.get("#/", ((context) => {
        context.partial("view/index.html", {}, (html) => {
            $(document).html(html);
        });
    }));
    self.get("#/404", function (context) {
        context.partial("view/404.html", {}, function (html) {
            $(document).html(html);
        });
    });

    self.get("#/redes/index", (context) => {
        context.partial("view/redes/index.html", {
            controller: "scripts/redes/redes.js"
        }, function (html) {
            $(document).html(html);
        })
    });
    self.get("#/redes/view/:id", async function (context) {
        let id = this.params.id;
        let url = `/api/redes/${id}`;

        let get = async function (url) {
            return Promise.resolve($.ajax({
                type: "GET",
                url: url,
                dataType: "JSON"
            }));
        }

        let restRequest = await get(url);

        let rede = restRequest.data;
        context.render("view/redes/view.template", {
            rede: rede
        }).replace($("#content-html"));
        // context.partial("view/redes/view.html", {
        //     rede: rede
        // }, function (html) {
        //     // $(document).html(html);
        //     return html;
        // });

    });
    self.get("#/redes/add", (context) => {
        context.partial("view/redes/add.html", {
            controller: "scripts/redes/add.js"
        }, function (html) {
            $(document).html(html);
        })
    });
    self.get("#/redes/edit/:id", (context) => {
        context.partial("view/redes/edit.html", {
            controller: "scripts/redes/add.js"
        }, function (html) {
            $(document).html(html);
        })
    });

    return self;
});

$(function () {
    sammy.run("#/");
});
// app.run("#/");
// app.run();

// console.log('run');
