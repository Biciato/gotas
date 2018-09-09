<?php

use Cake\Core\Configure;

$debug = Configure::read("debug");

if ($debug) {

    echo $this->Html->meta('icon');
    echo $this->Html->css('home-rti');
    echo $this->Html->css(['bootstrap/css/bootstrap', 'bootstrap/css/bootstrap-theme']);
    echo $this->Html->css(['font-awesome/css/font-awesome']);
    echo $this->Html->css(['bootstrap-datetimepicker/css/bootstrap-datetimepicker']);
    echo $this->Html->script('jquery/jquery');
    echo $this->Html->script('jquery-Mask/jquery.mask');
    echo $this->Html->script('jquery-barcode-2.0.3/jquery-barcode');
    echo $this->Html->script('printThis-master/printThis');

    echo $this->Html->script('plentz-jquery-maskmoney/dist/jquery.maskMoney');
    echo $this->Html->script('bootstrap/js/bootstrap');
    // echo $this->Html->script(['bootstrap-datetimepicker/js/bootstrap-datetimepicker', 'bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.pt-br']);
    echo $this->Html->script('scripts/pages/home');
    echo $this->Html->script('jquery-qrcode/jquery-qrcode.0.14.0');
    echo $this->Html->script('pdf417-gh-pages/pdf417');
    echo $this->Html->script('scripts/util/pdf417_helper');
    echo $this->Html->css("cropper-master/dist/cropper");
    echo $this->Html->script("cropper-master/dist/cropper");


} else {
    echo $this->Html->meta('icon');
    echo $this->Html->css('home-rti');
    echo $this->Html->css(['bootstrap/css/bootstrap.min', 'bootstrap/css/bootstrap-theme.min']);
    echo $this->Html->css(['font-awesome/css/font-awesome.min']);
    echo $this->Html->css(['bootstrap-datetimepicker/css/bootstrap-datetimepicker.min']);
    echo $this->Html->script('jquery/jquery.min');
    echo $this->Html->script('jquery-Mask/jquery.mask.min');
    echo $this->Html->script('jquery-barcode-2.0.3/jquery-barcode.min');
    echo $this->Html->script('printThis-master/printThis');
    echo $this->Html->script('plentz-jquery-maskmoney/dist/jquery.maskMoney.min');
    echo $this->Html->script('bootstrap/js/bootstrap.min');
    echo $this->Html->script(['bootstrap-datetimepicker/js/bootstrap-datetimepicker.min', 'bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.pt-br']);
    echo $this->Html->script('scripts/pages/home');
    echo $this->Html->script('jquery-qrcode/jquery-qrcode.0.14.0.min');
    echo $this->Html->script('pdf417-gh-pages/pdf417');
    echo $this->Html->script('scripts/util/pdf417_helper');

    echo $this->Html->css("cropper-master/dist/cropper.min");
    echo $this->Html->script("cropper-master/dist/cropper.min");
}

?>
<!-- Início Estilos -->
<!-- <link href="/webroot/app/css/main.css" rel="stylesheet" />
<link href="/webroot/app/css/fontawesome/css/all.css" rel="stylesheet" />
<link href="/webroot/app/css/site.css" rel="stylesheet" /> -->

 <!-- Icones -->
 <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
<link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicon.png">

<!-- Fim Estilos -->

<!-- Bibliotecas -->
<script src="/webroot/app/lib/angularjs/angular.min.js" ></script>
<script src="/webroot/app/lib/angularjs/angular-locale_pt-br.js" ></script>
<script src="/webroot/app/lib/angularjs/angular-animate.min.js" ></script>
<script src="/webroot/app/lib/angularjs/angular-route.min.js" ></script>
<script src="/webroot/app/lib/angularjs/angular-sanitize.min.js"></script>
<script src="/webroot/app/lib/angularjs/angular-touch.min.js" ></script>
<script src="/webroot/app/lib/ui.mask/mask.min.js"></script>
<script src="/webroot/app/lib/ui.select/select.min.js"></script>
<script src="/webroot/app/lib/moment/moment.min.js" ></script>
<script src="/webroot/app/lib/moment/moment-with-locales.min.js" ></script>
<script src="/webroot/app/lib/moment/locales.min.js" ></script>
<script src="/webroot/app/lib/ui.bootstrap/ui-bootstrap-tpls-2.5.0.min.js" ></script>

<link rel="stylesheet" href="/webroot/app/lib/ui.select/select.min.css" >
<script src="/webroot/app/js/app.js"></script>
<script src="/webroot/app/js/app.module.js"></script>
<script src="/webroot/app/js/config/routeConfig.js"></script>
<script src="/webroot/app/js/controllers/usuarios/relUsuariosAtivos.js" ></script>
<script src="/webroot/app/js/controllers/usuarios/relUsuariosFidelizados.js" ></script>


<!-- <script src="/webroot/app/js/controllers/usuarios/relUsuariosAtivos.js" ></script> -->
<?php
$this->fetch('meta');
$this->fetch('css');
$this->fetch('script');

?>
