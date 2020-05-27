var sammy = Sammy("#content-html", function () {
    var self = this;

    self.credentials = {};
    self.use(Sammy.Template, 'tpl');

    // Caminho para página não encontrada
    self.notFound = function (verb, path) {
        document.title = 'GOTAS - Página não encontrada';
        self.runRoute('get', '#/404');
    };

    /**
     * Método que é executado antes de qualquer outra navegação
     */
    self.before({
        except: {
            path: "#/usuarios/login"
        }
    }, function () {
        // Se as credentials na session não estiverem definidas, redireciona ao login
        if (localStorage.getItem('credentials') === null) {
            window.location.href = "/usuarios/login";
        } else {
            self.credentials = JSON.parse(localStorage.getItem('credentials'));
        }

        document.title = 'GOTAS - ' + self.credentials.usuario.nome;

    });

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

    //#region ADMIN

    //#region ADMIN SYSTEM
    self.get("#/admin/import-sefaz-products", function (context) {
        context.partial("view/admin/import-sefaz-products.tpl");
    });
    self.get("#/admin/correction-user-points", function (context) {
        context.partial("view/admin/correction-user-points.tpl");
    });
    self.get("#/admin/manage-user", function (context) {
        context.partial("view/admin/manage-user.tpl");
    });

    //#endregion

    //#region ADMIN NETWORK

    self.get("#/admin/network-settings", function (context) {
        context.partial("view/admin/network-settings.tpl");
    });
    //#endregion

    //#endregion

    //#region CLIENTES

    self.get("#/redes/view/:redesId/clientes/view/:id", (context) => {
        let redesId = context.params.redesId;
        let id = context.params.id;
        let cliente = {
            id: id
        };

        context.redesId = redesId;

        localStorage.setItem("data", JSON.stringify(cliente));
        context.partial("view/clientes/view.tpl");
    });
    self.get("#/redes/view/:redesId/clientes/add/", (context) => {
        let redesId = context.params.redesId;
        let id = context.params.id;
        let cliente = {
            id: id,
            redesId: redesId
        };

        context.redesId = redesId;

        localStorage.setItem("data", JSON.stringify(cliente));
        context.partial("view/clientes/add.tpl");
    });
    self.get("#/redes/view/:redesId/clientes/edit/:id", (context) => {
        let redesId = context.params.redesId;
        let id = context.params.id;
        let cliente = {
            id: id,
            redesId: redesId
        };

        context.redesId = redesId;

        localStorage.setItem("data", JSON.stringify(cliente));
        context.partial("view/clientes/edit.tpl");
    });
    //#endregion

    // #region REDES
    self.get("#/redes/index", (context) => {
        context.partial("view/redes/index.tpl");
    });
    self.get("#/redes/view/:id", async function (context) {
        let id = context.params.id;
        let rede = {
            id: id
        };

        context.redesId = id;

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
    self.get("#/redes/edit/:id", async function (context) {
        let id = parseInt(context.params.id);
        let rede = {
            id: id
        };

        localStorage.setItem("data", JSON.stringify(rede));
        context.partial("view/redes/edit.tpl");
    });

    //#endregion

    return self;
});

$(function () {
    sammy.run("#/");
});
// app.run("#/");
// app.run();

// console.log('run');
