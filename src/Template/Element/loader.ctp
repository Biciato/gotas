<?php

use Cake\Core\Configure;

?>
<div class="modal modal-loader" data-backdrop="static" data-keyboard="false">
<div class="loading">
        <!--
            TODO:
            Isto deverá ser portado para o template principal depois da mudança para AngularJS
        -->
        <img src="/img/icons/loading.gif" alt="">
</div>

    <div class="loader-container">
        <div class="loader-panel center-block">
            <div class="loader">
            </div>
            <p>Processando...</p>
            <p><span class="loader-message"></span></p>
        </div>
            <!--
                TODO:
                Isto deverá ser portado para o template principal depois da mudança para AngularJS
            -->
            <img src="/img/icons/loading.gif" alt="">
    </div>
</div>



<?php if (Configure::read('debug')) : ?>
    <?= $this->Html->css('styles/common/loader') ?>
<?php else : ?>
    <?= $this->Html->css('styles/common/loader.min') ?>
<?php endif;?>

<?= $this->fetch('css') ?>
