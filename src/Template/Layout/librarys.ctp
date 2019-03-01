<?php

use Cake\Core\Configure;

$debug = Configure::read("debug");

if ($debug) {
    echo $this->Html->meta('icon');
    echo $this->Html->css('home-rti');
    echo $this->Html->css(['bootstrap/css/bootstrap', 'bootstrap/css/bootstrap-theme']);
    echo $this->Html->css(['font-awesome/css/font-awesome']);
    echo $this->Html->script('jquery/jquery');
    echo $this->Html->script('jquery-Mask/jquery.mask');
    echo $this->Html->script('jquery-barcode-2.0.3/jquery-barcode');
    echo $this->Html->script('printThis-master/printThis');

    echo $this->Html->script('plentz-jquery-maskmoney/dist/jquery.maskMoney');
    echo $this->Html->script('bootstrap/js/bootstrap');
    echo $this->Html->css(
        array(
            "bootstrap-datetimepicker/css/bootstrap-datetimepicker",
            "bootstrap-datetimepicker/css/bootstrap-datetimepicker.min"
        )
    );
    echo $this->Html->script(
        array(
            "bootstrap-datetimepicker/js/bootstrap-datetimepicker",
            "bootstrap-datetimepicker/js/bootstrap-datetimepicker.min"
        )
    );
    echo $this->Html->css(
        array(
            "bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker",
            "bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker3",
        )
    );
    echo $this->Html->script(
        array(
            "bootstrap-datepicker-1.6.4-dist/js/bootstrap-datepicker",
            "bootstrap-datepicker-1.6.4-dist/locales/bootstrap-datepicker.pt-BR.min"

        )
    );
    echo $this->Html->script('scripts/pages/home');
    echo $this->Html->script('jquery-qrcode/jquery-qrcode.0.14.0');
    echo $this->Html->script('pdf417-gh-pages/pdf417');
    echo $this->Html->script('scripts/util/pdf417_helper');
    echo $this->Html->css("cropper-master/dist/cropper");
    echo $this->Html->script("cropper-master/dist/cropper");
    echo $this->Html->script("jquery-qrcode/jquery-qrcode.0.14.0.js");

} else {
    echo $this->Html->meta('icon');
    echo $this->Html->css('home-rti');
    echo $this->Html->css(['bootstrap/css/bootstrap.min', 'bootstrap/css/bootstrap-theme.min']);
    echo $this->Html->css(['font-awesome/css/font-awesome.min']);

    echo $this->Html->script('jquery/jquery.min');
    echo $this->Html->script('jquery-Mask/jquery.mask.min');
    echo $this->Html->script('jquery-barcode-2.0.3/jquery-barcode.min');
    echo $this->Html->script('printThis-master/printThis');
    echo $this->Html->script('plentz-jquery-maskmoney/dist/jquery.maskMoney.min');
    echo $this->Html->script('bootstrap/js/bootstrap.min');
    echo $this->Html->css(
        array(
            "bootstrap-datetimepicker/css/bootstrap-datetimepicker",
            "bootstrap-datetimepicker/css/bootstrap-datetimepicker.min"
        )
    );
    echo $this->Html->script(
        array(
            "bootstrap-datetimepicker/js/bootstrap-datetimepicker",
            "bootstrap-datetimepicker/js/bootstrap-datetimepicker.min"
        )
    );
    echo $this->Html->css(
        array(
            "bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker.min",
            "bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker3.min",
        )
    );
    echo $this->Html->script(
        array(
            "bootstrap-datepicker-1.6.4-dist/js/bootstrap-datepicker.min",
            "bootstrap-datepicker-1.6.4-dist/locales/bootstrap-datepicker.pt-BR.min"

        )
    );

    echo $this->Html->script('scripts/pages/home');
    echo $this->Html->script('jquery-qrcode/jquery-qrcode.0.14.0.min');
    echo $this->Html->script('pdf417-gh-pages/pdf417');
    echo $this->Html->script('scripts/util/pdf417_helper');
    echo $this->Html->css("cropper-master/dist/cropper.min");
    echo $this->Html->script("cropper-master/dist/cropper.min");
    echo $this->Html->script("jquery-qrcode/jquery-qrcode.0.14.0.min.js");

}

// ?>
<!-- Início Estilos -->
<!-- <link href="/webroot/app/css/site.css" rel="stylesheet" > -->
<link href="/webroot/css/fontawesome5/css/all.css" rel="stylesheet" />
<!-- <link href="/webroot/app/css/main.css" rel="stylesheet" />
<link href="/webroot/app/css/site.css" rel="stylesheet" /> -->

 <!-- Icones -->
 <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
<link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicon.png">

<link rel="stylesheet" href="/webroot/app/lib/ui.select/select.min.css" >
<!-- Fim Estilos -->

<!-- Bibliotecas -->
<script src="/webroot/app/lib/angularjs/angular.min.js" ></script>
<script src="/webroot/app/lib/angularjs/angular-locale_pt-br.js" ></script>
<script src="/webroot/app/lib/angularjs/angular-animate.min.js" ></script>
<script src="/webroot/app/lib/angularjs/angular-route.min.js" ></script>
<script src="/webroot/app/lib/angularjs/angular-sanitize.min.js"></script>
<script src="/webroot/app/lib/angularjs/angular-touch.min.js" ></script>
<script src="/webroot/app/lib/angularjs/extra/angular-file-saver.bundle.min.js"></script>
<!-- <script src="/webroot/app/lib/FileSaver.js"></script> -->
<script src="/webroot/app/lib/FileSaver.min.js"></script>
<script src="/webroot/app/lib/Blob.js"></script>
<script src="/webroot/app/lib/ui.mask/mask.min.js"></script>
<script src="/webroot/app/lib/ui.select/select.min.js"></script>
<script src="/webroot/app/lib/moment/moment.min.js" ></script>
<script src="/webroot/app/lib/moment/moment-with-locales.min.js" ></script>
<script src="/webroot/app/lib/moment/locales.min.js" ></script>
<script src="/webroot/app/lib/ui.bootstrap/ui-bootstrap-tpls-2.5.0.min.js" ></script>


<!-- angular-toastr -->

<script src="/webroot/app/lib/angularjs/extra/angular-toaster/angular-toastr.min.js"></script>
<script src="/webroot/app/lib/angularjs/extra/angular-toaster/angular-toastr.tpls.min.js"></script>
<link rel="stylesheet" href="/webroot/app/lib/angularjs/extra/angular-toaster/angular-toastr.min.css">

<!-- Css -->
<link rel="stylesheet" href="/webroot/app/css/pages/relatorios/usuarios/modalDetalhesUsuario.css">

<script src="/webroot/app/js/app.js"></script>
<script src="/webroot/app/js/app.module.js"></script>
<script src="/webroot/app/js/configuracoes.module.js"></script>
<script src="/webroot/app/js/config/routeConfig.js"></script>

<!-- Controllers -->
<script src="/webroot/app/js/controllers/usuarios/relUsuariosAssiduosController.js" ></script>
<script src="/webroot/app/js/controllers/usuarios/modalDetalhesUsuarioController.js"></script>
<script src="/webroot/app/js/controllers/usuarios/modalDetalhesAssiduidadeUsuarioController.js"></script>
<script src="/webroot/app/js/controllers/usuarios/relUsuariosFidelizadosController.js" ></script>
<script src="/webroot/app/js/controllers/usuarios/relUsuariosFrequenciaMediaController.js"></script>

<!-- Services -->
<script src="/webroot/app/js/services/utils/downloadService.js" ></script>

<!-- Serviços Básicos -->
<script src="/webroot/app/js/services/clientes/clientesService.js" ></script>
<script src="/webroot/app/js/services/usuarios/usuariosService.js" ></script>
<script src="/webroot/app/js/services/transportadoras/transportadorasService.js"></script>
<script src="/webroot/app/js/services/veiculos/veiculosService.js" ></script>

<script src="/webroot/app/js/services/usuarios/relUsuariosAssiduosService.js" ></script>

<script src="/webroot/app/js/services/usuarios/relUsuariosFidelizadosService.js" ></script>

<!-- Diretivas -->

<script src="/webroot/app/js/directives/loading-spinner.js"></script>
<!-- /webroot/app/js/services/usuarios/relUsuariosFidelizadosService.js -->
<!-- <script src="/webroot/app/js/controllers/usuarios/relUsuariosAtivos.js" ></script> -->
<?php
$this->fetch('meta');
$this->fetch('css');
$this->fetch('script');

?>
