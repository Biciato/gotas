<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/PontuacoesComprovantes/historico_pontuacoes.ctp
 * @date     08/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['WorkerProfileType']) {
    $this->Breadcrumbs->add('Pontuações do Usuário', [], ['class' => 'active']);
} else {
    $this->Breadcrumbs->add('Meu Histórico de Pontuações', [], ['class' => 'active']);
}

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../PontuacoesComprovantes/left_menu') ?> 

<div class="redes form col-lg-9 col-md-8 columns content">

    <?php if ($usuarioLogado['tipo_perfil'] <= (int)Configure::read('profileTypes')['WorkerProfileType']) : ?>
        <legend>Meu Histórico de Pontuações</legend>

    <?php else : ?> 
        <legend>Meu Histórico de Pontuações</legend>

    <?php endif; ?>


    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_id', ['label' => 'Ponto de Atendimento']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('chave_nfe', ['label' => 'Chave da Nota Fiscal Eletrônica']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('soma_pontuacoes', ['label' => 'Soma de Pontuacoes']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('data', ['label' => 'Data']) ?></th>
                
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pontuacoes_comprovantes as $key => $pontuacao_comprovante) : ?> 
            <tr>
                <td>
                    <?= $this->Html->link(
                        __($pontuacao_comprovante->cliente->nome_fantasia),
                        [
                            'controller' => 'clientes', 'action' => 'dados_cliente_atendimento_usuario', $pontuacao_comprovante->id
                        ]
                    ) ?>
                </td>
                <td>
                    <?= $pontuacao_comprovante->chave_nfe ?>
                </td>
                <td>
                    <?php 

                    $valor = 0;

                    foreach ($pontuacao_comprovante->soma_pontuacoes as $key => $value) {
                        $valor = $valor + $value['quantidade_gotas'];
                    };

                    echo $this->Number->precision($valor, 2);

                    ?> 
                </td>
                <td>
                        <?= $pontuacao_comprovante->data->format('d/m/Y H:i:s') ?>
                </td>
                <td class="actions" style="white-space:nowrap">
                    <?=
                    $this->Html->link(
                        __(
                            '{0}',
                            $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                        ),
                        [
                            'controller' => 'pontuacoes_comprovantes',
                            'action' => 'ver_detalhes',
                            $pontuacao_comprovante->id
                        ],
                        [
                            'title' => 'Ver detalhes',
                            'class' => 'btn btn-default btn-xs',
                            'escape' => false
                        ]
                    )
                    ?>
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
<div/>