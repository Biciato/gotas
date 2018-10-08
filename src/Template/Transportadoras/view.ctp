<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Transportadora $transportadora
  */
?>

<?= $this->element('../Transportadoras/left_menu', ['controller' => 'transportadoras', 'action' => 'index', 'mode' => 'edit']) ?>
<div class="transportadoras view col-lg-9 col-md-8 columns content">
    <h3><?= h($transportadora["nome_fantasia"]) ?></h3>
    <table class="table table-striped table-hover">
        <tr>
            <th scope="row"><?= __('Nome Fantasia') ?></th>
            <td><?= h($transportadora->nome_fantasia) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Razao Social') ?></th>
            <td><?= h($transportadora->razao_social) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Cnpj') ?></th>
            <td><?= h($transportadora->cnpj) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Endereco') ?></th>
            <td><?= h($transportadora->endereco) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Endereco Complemento') ?></th>
            <td><?= h($transportadora->endereco_complemento) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Bairro') ?></th>
            <td><?= h($transportadora->bairro) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Municipio') ?></th>
            <td><?= h($transportadora->municipio) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Estado') ?></th>
            <td><?= h($transportadora->estado) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tel Fixo') ?></th>
            <td><?= h($transportadora->tel_fixo) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tel Celular') ?></th>
            <td><?= h($transportadora->tel_celular) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($transportadora->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Endereco Numero') ?></th>
            <td><?= $this->Number->format($transportadora->endereco_numero) ?></td>
        </tr>

        <tr>
            <th scope="row"><?= __("Audit Insert") ?></th>
            <td><?= h($transportadora->audit_insert->format('d/m/Y H:i:s')) ?></td>
        </tr>

        <tr>
            <th scope="row"><?= __("Audit Update") ?></th>
            <td><?= h(isset($transportadora->audit_update) ? $transportadora->audit_update->format('d/m/Y H:i:s') : null) ?></td>
        </tr>

    </table>
</div>
