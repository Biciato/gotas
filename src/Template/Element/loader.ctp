<?php

use Cake\Core\Configure;

?>
<!--
    TODO:
    Isto deverá ser portado para o template principal depois da mudança para AngularJS
-->

<div class="loading">
        <img src="/img/icons/loading.gif" alt="">

        <p class="text-center">
            <span class="loading-message"></span>
        </p>
        <!-- <span class="loading-message text-center"></span> -->
</div>
<!-- <div class="modal modal-loader" data-backdrop="static" data-keyboard="false">
     <div class="loading">
        <img src="/img/icons/loading.gif" alt="">

        <p><span class="loading-message"></span></p>
</div>
 </div> -->

    <!--
        TODO:
        Isto deverá ser portado para o template principal depois da mudança para AngularJS
    -->
    <!-- <div class="loader-container">
        <div class="loader-panel center-block">
            <div class="loader">
            </div>
            <p>Processando...</p>
            <p><span class="loading-message"></span></p>
        </div>
            <img src="/img/icons/loading.gif" alt="">
    </div> -->
</div>


<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->css('styles/common/loader') ?>
<?php else : ?>
    <?= $this->Html->css('styles/common/loader.min') ?>
<?php endif;?>

<?= $this->fetch('css') ?>
