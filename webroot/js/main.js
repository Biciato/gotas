var sammy = Sammy("#content-html", function () {
    var self = this;
    self.use(Sammy.Template, 'tpl');

    // Caminho para página não encontrada
    self.notFound = function (verb, path) {
        self.runRoute('get', '#/404');
    }

    self.get("#/", ((context) => {
        context.partial("view/index.tpl", {}, (html) => {
            $(document).html(html);
        });
    }));
    self.get("#/404", function (context) {
        context.partial("view/404.tpl", {}, function (html) {
            $(document).html(html);
        });
    });

    self.get("#/redes/index", (context) => {
        context.partial("view/redes/index.tpl", {
            controller: "scripts/redes/redes.js"
        }, function (html) {
            $(document).html(html);
        })
    });
    self.get("#/redes/view/:id", async function (context) {
        let id = this.params.id;

        let rede = {
            id: id
        };

        localStorage.setItem("data", JSON.stringify(rede));

        context.partial("view/redes/view.tpl");
    });
    self.get("#/redes/add", (context) => {
        context.partial("view/redes/add.tpl", {
            controller: "scripts/redes/add.js"
        }, function (html) {
            $(document).html(html);
        })
    });
    self.get("#/redes/edit/:id", (context) => {
        context.partial("view/redes/edit.tpl", {
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
