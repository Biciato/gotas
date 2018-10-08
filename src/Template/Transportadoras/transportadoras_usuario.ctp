<?php

/**
 * @description Lista as transportadoras de um usuário (Interface Funcionário)
 * @author 	    Gustavo Souza Gonçalves
 * @file 	    Template\Transportadoras\transportadorasUsuario.ctp
 * @date 	    18/02/2018
 *
 */

use Cake\Routing\Router;
use Cake\Core\Configure;


?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atualizar_cadastro_cliente', 'mode_selected' => 'atualizar_cadastro_cliente_transportadoras']) ?>
<div class="transportadoras index col-lg-9 col-md-8 columns content">

<div class="form-group">

</div>
    <legend><?= __('Transportadoras do Usuário {0} ', $usuario->nome) ?> </legend>

    <?= $this->Form->create('Post', ['url' => ['controller' => 'transportadoras', 'action' => 'transportadorasUsuario', $usuarios_id]]) ?>

    <div class="form-group row">
        <?= $this->element("../Transportadoras/filtro_transportadoras", array("controller" => "transportadoras", "action" => "transportadorasUsuario", $usuario["id"])); ?>

    </div>

    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= __('Nome Fantasia') ?></th>
                <th scope="col"><?= __('Razao Social') ?></th>
                <th scope="col"><?= __('CNPJ') ?></th>
                <th scope="col"><?= __('Municipio') ?></th>
                <th scope="col"><?= __('Estado') ?></th>
                <th scope="col"><?= __('País') ?></th>
                <th scope="col"><?= __('Fixo') ?></th>
                <th scope="col"><?= __('Celular') ?></th>

                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transportadora_has_usuario as $transportadora_has_usuario) : ?>

            <tr>
                  <td><?= h($transportadora_has_usuario->transportadora->nome_fantasia) ?></td>
                    <td><?= h($transportadora_has_usuario->transportadora->razao_social) ?></td>
                    <td><?= h($this->NumberFormat->formatNumberToCNPJ($transportadora_has_usuario->transportadora->cnpj)) ?></td>
                    <td><?= h($transportadora_has_usuario->transportadora->municipio) ?></td>
                    <td><?= h($this->Address->getStatesBrazil($transportadora_has_usuario->transportadora->estado)) ?></td>
                    <td><?= h($transportadora_has_usuario->transportadora->pais) ?></td>
                    <td><?= h($this->Phone->formatPhone($transportadora_has_usuario->transportadora->tel_fixo)) ?></td>
                    <td><?= h($this->Phone->formatPhone($transportadora_has_usuario->transportadora->tel_celular)) ?></td>
                <td class="actions">
                    <?= $this->Html->link(
                        __(
                            '{0} ',
                            $this->Html->tag('i', '', ['class' => 'fa fa-trash'])
                        ),
                        '#',
                        [
                            'title' => 'Deletar',
                            'class' => 'btn btn-primary btn-danger btn-xs',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-delete',
                            'data-action' => Router::url(
                                [
                                    'controller' => 'transportadoras_has_usuarios',
                                    'action' => 'delete_transportadora_usuario_final', $transportadora_has_usuario->id
                                ]
                            ), 'escape' => false
                        ],
                        false
                    ) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <center>
            <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['escape' => false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
            <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>
