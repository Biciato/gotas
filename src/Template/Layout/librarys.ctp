<?php

use Cake\Core\Configure;

$debug = Configure::read("debug");

echo $this->Html->meta('icon');

?>

<?php // echo  $this->Html->css(sprintf("site.css?version=%s", SYSTEM_VERSION));
?>
<?php echo $this->Html->css(sprintf("home-rti.css?version=%s", SYSTEM_VERSION)); ?>


<!-- <link rel="stylesheet" href="/webroot/css/home-rti.css?version=<?= SYSTEM_VERSION ?>"> -->

<!-- Entidades -->

<?= $this->Html->script(sprintf("entities/Gota.js?version=%s", SYSTEM_VERSION)); ?>
<!-- <script src="/webroot/js/entities/Gota.js?version=<?= SYSTEM_VERSION ?>"></script> -->

<!-- jQuery -->

<?= $this->Html->script(sprintf("jquery/jquery.js?version=%s",  SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("jquery/jquery.min.js?version=%s",  SYSTEM_VERSION)); ?>

<!-- Moment -->
<?= $this->Html->script(sprintf("moment/min/moment.min.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("moment/min/moment-with-locales.min.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("moment/min/locales.min.js?version=%s", SYSTEM_VERSION)); ?>

<!-- Select2 -->
<?php

echo $this->Html->css(sprintf("select2-4.0.13/css/select2.min.css?version=%s", SYSTEM_VERSION));
echo $this->Html->css(sprintf("select2-4.0.13/css/select2-bootstrap.min.css?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("select2-4.0.13/js/select2.min.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("select2-4.0.13/js/i18n/pt-BR.js?version=%s", SYSTEM_VERSION));
?>

<!-- <script src="/webroot/app/lib/moment/moment.min.js?version=<?= SYSTEM_VERSION ?>"></script> -->
<!-- <script src="/webroot/app/lib/moment/moment-with-locales.min.js?version=<?= SYSTEM_VERSION ?>"></script> -->
<!-- <script src="/webroot/app/lib/moment/locales.min.js?version=<?= SYSTEM_VERSION ?>"></script> -->

<!-- Bootstrap CSS 3 -->

<?php
// echo $this->Html->css(sprintf("bootstrap/css/bootstrap.css?version=%s",  SYSTEM_VERSION));
// echo $this->Html->css(sprintf("bootstrap/css/bootstrap.min.css?version=%s", SYSTEM_VERSION));
// echo $this->Html->css(sprintf("bootstrap/css/bootstrap.css?version=%s.map",  SYSTEM_VERSION));
// echo $this->Html->css(sprintf("bootstrap/css/bootstrap.min.css?version=%s.map", SYSTEM_VERSION));
// echo $this->Html->css(sprintf("bootstrap/css/bootstrap-theme.css?version=%s", SYSTEM_VERSION));
// echo $this->Html->css(sprintf("bootstrap/css/bootstrap-theme.css?version=%s.map", SYSTEM_VERSION));
// echo $this->Html->css(sprintf("bootstrap/css/bootstrap-theme.min.css?version=%s",  SYSTEM_VERSION));
// echo $this->Html->css(sprintf("bootstrap/css/bootstrap-theme.min.css?version=%s.map", SYSTEM_VERSION));
// echo $this->Html->script(sprintf("bootstrap/js/bootstrap.js?version=%s", SYSTEM_VERSION));
?>

<?php echo $this->Html->script("layout-update/popper.min.js"); ?>
<?php echo $this->Html->script("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"); ?>
<?php echo $this->Html->script("//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"); ?>

<?php echo $this->Html->script("bootbox/bootbox.min.js"); ?>
<?php echo $this->Html->script("bootbox/bootbox.locales.min.js"); ?>

<?php

echo $this->Html->css(sprintf("bootstrap3-dialog/css/bootstrap-dialog.min.css?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("bootstrap3-dialog/js/bootstrap-dialog.min.js?version=%s", SYSTEM_VERSION));


?>
<!-- Font Awesome -->
<?= $this->Html->css(sprintf("font-awesome/css/font-awesome.css?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->css(sprintf("font-awesome/css/font-awesome.css?version=%s.map", SYSTEM_VERSION)); ?>
<?= $this->Html->css(sprintf("font-awesome/css/font-awesome.min.css?version=%s",  SYSTEM_VERSION)); ?>

<!-- Font Awesome 5 -->
<?= $this->Html->css(sprintf("fontawesome5/css/all.css?version=%s", SYSTEM_VERSION)); ?>


<!-- Bibliotecas jQuery -->

<!-- jQueryUI -->
<?= $this->Html->script(sprintf("jqueryui/jquery-ui.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("jqueryui/jquery-ui.min.js?version=%s", SYSTEM_VERSION)); ?>

<!-- jQuery Mask -->

<?= $this->Html->script(sprintf("jquery-Mask/jquery.mask.js?version=%s",  SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("jquery-Mask/jquery.mask.min.js?version=%s",  SYSTEM_VERSION)); ?>

<!-- jQuery Barcode -->

<?= $this->Html->script(sprintf("jquery-barcode-2.2.0/jquery-barcode.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("jquery-barcode-2.2.0/jquery-barcode.min.js?version=%s", SYSTEM_VERSION)); ?>
<!-- jQuery QRCode -->

<?= $this->Html->script(sprintf("jquery-qrcode/jquery-qrcode.0.14.0.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("jquery-qrcode/jquery-qrcode.0.14.0.min.js?version=%s", SYSTEM_VERSION)); ?>

<!-- jQuery Validation Form -->
<?= $this->Html->script(sprintf("jquery-validation/dist/jquery.validate.min.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("jquery-validation/dist/additional-methods.min.js?version=%s", SYSTEM_VERSION)); ?>

<!-- jQuery Pagination -->
<?= $this->Html->script(sprintf("jquery-pagination/dist/pagination.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->css(sprintf("jquery-pagination/dist/pagination.css?version=%s", SYSTEM_VERSION)); ?>

<!-- Print This -->
<?= $this->Html->script(sprintf("printThis-master/printThis.js?version=%s", SYSTEM_VERSION)); ?>

<!-- jQuery Mask Money -->
<?= $this->Html->script(sprintf("plentz-jquery-maskmoney/dist/jquery.maskMoney.min.js?version=%s", SYSTEM_VERSION)); ?>

<!-- File Saver -->
<?= $this->Html->script(sprintf("FileSaver/FileSaver.js?version=%s", SYSTEM_VERSION)); ?>

<!-- PDF417 -->

<?= $this->Html->script(sprintf("pdf417-gh-pages/pdf417.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("scripts/util/pdf417_helper.js?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("scripts/util/pdf417_helper.min.js?version=%s", SYSTEM_VERSION)); ?>

<!-- Cropper Master -->

<?= $this->Html->css(sprintf("cropper-master/dist/cropper.css?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->css(sprintf("cropper-master/dist/cropper.min.css?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("cropper-master/dist/cropper.js?version=%s",  SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("cropper-master/dist/cropper.min.js?version=%s",  SYSTEM_VERSION)); ?>

<!-- Bootstrap Date Picker -->

<?= $this->Html->css(sprintf("bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker.css?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->css(sprintf("bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker.css?version=%s.map", SYSTEM_VERSION)); ?>
<?= $this->Html->css(sprintf("bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker.min.css?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->css(sprintf("bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker.min.css?version=%s.map", SYSTEM_VERSION)); ?>

<?= $this->Html->script(sprintf("bootstrap-datepicker-1.6.4-dist/js/bootstrap-datepicker.js?version=%s",  SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("bootstrap-datepicker-1.6.4-dist/js/bootstrap-datepicker.min.js?version=%s",  SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("bootstrap-datepicker-1.6.4-dist/locales/bootstrap-datepicker.pt-BR.min.js?version=%s",  SYSTEM_VERSION)); ?>

<!-- Bootstrap DateTimePicker -->
<?= $this->Html->css(sprintf("bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->css(sprintf("bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css?version=%s", SYSTEM_VERSION)); ?>
<?= $this->Html->script(sprintf("bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js?version=%s", SYSTEM_VERSION)); ?>

<!-- Início Estilos -->

<?= $this->Html->css(sprintf("styles/common/loader.css?version=%s",  SYSTEM_VERSION)); ?>

<!-- Icones -->
<?= "" // $this->Html->image("assets/img/apple-icon.png", ["sizes" => "76x76", "rel" => "apple-touch-icon"]);
?>
<?= "" // $this->Html->image("favicon.ico", ["sizes" => "96x96", "rel" => "icon"]);
?>


<!-- Desativado por ser Angular JS -->
<!-- <link rel="stylesheet" href="/webroot/app/lib/ui.select/select.min.css?version=<?= SYSTEM_VERSION ?>"> -->

<!-- DataTables -->

<?php
echo $this->Html->css(sprintf("DataTables/datatables.min.css?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("DataTables/datatables.min.js?version=%s", SYSTEM_VERSION));
?>


<!-- Helpers -->
<?php

echo $this->Html->script("scripts/helpers/HTMLHelper.js");
?>

<!-- DataTables -->

<?php
echo $this->Html->script(sprintf("scripts/helpers/Html/ImageHelper.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/helpers/Html/ButtonHelper.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/helpers/DataTables/DataTablesHelper.js?version=%s", SYSTEM_VERSION));

echo $this->Html->script(sprintf('layout-update/pipeline_wrapper.js?version=%s', SYSTEM_VERSION));
?>

<!-- Sammy.JS -->

<?php

echo $this->Html->script(sprintf("sammy-master/lib/min/sammy-latest.min.js?version=%s", SYSTEM_VERSION));
// echo $this->Html->script(sprintf("sammy-master/lib/plugins/sammy.ejs.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("sammy-master/lib/plugins/sammy.template.js?version=%s", SYSTEM_VERSION));
// echo $this->Html->script(sprintf("sammy-master/lib/sammy.js?version=%s", SYSTEM_VERSION));
?>

<!-- Services JS -->

<?php
echo $this->Html->script(sprintf("scripts/services/clientes-service.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/services/gotas-service.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/services/usuarios-service.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/services/veiculos-service.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/services/redes-service.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/services/sefaz-service.js?version=%s", SYSTEM_VERSION));
?>

<!-- Controllers JS -->

<?php
echo $this->Html->script(sprintf("main.js?version=%s", SYSTEM_VERSION));

// Administrativo

echo $this->Html->script(sprintf("scripts/admin/import-sefaz-products.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/admin/correction-user-points.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/admin/manage-user.js?version=%s", SYSTEM_VERSION));

// Clientes
echo $this->Html->script(sprintf("scripts/clientes/view.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/clientes/add.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/clientes/edit.js?version=%s", SYSTEM_VERSION));

// Redes
echo $this->Html->script(sprintf("scripts/redes/index.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/redes/view.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/redes/add.js?version=%s", SYSTEM_VERSION));
echo $this->Html->script(sprintf("scripts/redes/edit.js?version=%s", SYSTEM_VERSION));

?>

<?php echo $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'); ?>
<?php echo $this->Html->css('font-awesome/css/font-awesome.css'); ?>

<?php echo $this->Html->css('layout-update/animate.css'); ?>
<?php echo $this->Html->css('layout-update/style.css'); ?>
<?php echo $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css'); ?>
<?php //echo $this->fetch('css');
?>


<!-- Home -->
<?= $this->Html->script(sprintf("scripts/pages/home.js?version=%s", SYSTEM_VERSION)); ?>

<!-- Fim Estilos -->

<!-- Bibliotecas -->
<!-- <script src="/webroot/app/lib/angularjs/angular.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/angularjs/angular-locale_pt-br.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/angularjs/angular-animate.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/angularjs/angular-route.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/angularjs/angular-sanitize.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/angularjs/angular-touch.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/angularjs/extra/angular-file-saver.bundle.min.js?version=<?= SYSTEM_VERSION ?>"></script>

<script src="/webroot/app/lib/ui.bootstrap/ui-bootstrap-tpls-2.5.0.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/Blob.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/ui.mask/mask.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/ui.select/select.min.js?version=<?= SYSTEM_VERSION ?>"></script> -->


<!-- angular-toastr -->

<!-- <script src="/webroot/app/lib/angularjs/extra/angular-toaster/angular-toastr.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/lib/angularjs/extra/angular-toaster/angular-toastr.tpls.min.js?version=<?= SYSTEM_VERSION ?>"></script>
<link rel="stylesheet" href="/webroot/app/lib/angularjs/extra/angular-toaster/angular-toastr.min.css?version=<?= SYSTEM_VERSION ?>"> -->

<!-- Css -->
<!-- <link rel="stylesheet" href="/webroot/app/css/pages/relatorios/usuarios/modalDetalhesUsuario.css?version=<?= SYSTEM_VERSION ?>">

<script src="/webroot/app/js/app.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/app.module.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/configuracoes.module.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/config/routeConfig.js?version=<?= SYSTEM_VERSION ?>"></script> -->

<!-- Controllers -->
<!-- <script src="/webroot/app/js/controllers/usuarios/relUsuariosAssiduosController.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/controllers/usuarios/modalDetalhesUsuarioController.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/controllers/usuarios/modalDetalhesAssiduidadeUsuarioController.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/controllers/usuarios/relUsuariosFidelizadosController.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/controllers/usuarios/relUsuariosFrequenciaMediaController.js?version=<?= SYSTEM_VERSION ?>"></script> -->

<!-- Services -->
<!-- <script src="/webroot/app/js/services/utils/downloadService.js?version=<?= SYSTEM_VERSION ?>"></script> -->

<!-- Serviços Básicos -->
<!-- <script src="/webroot/app/js/services/clientes/clientesService.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/services/usuarios/usuariosService.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/services/transportadoras/transportadorasService.js?version=<?= SYSTEM_VERSION ?>"></script>
<script src="/webroot/app/js/services/veiculos/veiculosService.js?version=<?= SYSTEM_VERSION ?>"></script>

<script src="/webroot/app/js/services/usuarios/relUsuariosAssiduosService.js?version=<?= SYSTEM_VERSION ?>"></script> -->

<!-- <script src="/webroot/app/js/services/usuarios/relUsuariosFidelizadosService.js?version=<?= SYSTEM_VERSION ?>"></script> -->

<!-- Diretivas -->

<!-- <script src="/webroot/app/js/directives/loading-spinner.js?version=<?= SYSTEM_VERSION ?>"></script> -->
<!-- /webroot/app/js/services/usuarios/relUsuariosFidelizadosService.js -->
<!-- <script src="/webroot/app/js/controllers/usuarios/relUsuariosAtivos.js?version=<?= SYSTEM_VERSION ?>" ></script> -->
