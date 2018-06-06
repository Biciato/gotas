<?php 

/**
 * @description Exibe tela de pesquisa de cliente, para atualizar os dados (view de Funcionário)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/PontuacoesComprovantes/exibir_cliente_final_pontuacoes.ctp
 * @date        20/02/2018
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'consulta_pontuacoes']); ?> 

<div class="col-lg-9"> 
<legend><?= __("Pontuações do Usuário {0}", $usuario->nome) ?> </legend>

<div class="col-lg-4">
<?= $this->Form->input(
    'soma_pontuacao_acumulada',
    [
        'readonly' => true,
        'value' => $soma_pontuacao_acumulada,
        'label' => 'Soma de Pontuação Acumulada'
    ]
) ?> 
</div>
<div class="col-lg-4">
<?= $this->Form->input(
    'soma_pontuacao_utilizada',
    [
        'readonly' => true,
        'value' => $soma_pontuacao_utilizada,
        'label' => 'Soma de Pontuação Utilizada'
    ]
) ?> 
</div>
<div class="col-lg-4">
<?= $this->Form->input(
    'soma_pontuacao_final',
    [
        'readonly' => true,
        'value' => $soma_pontuacao_final,
        'label' => 'Pontuação Restante'
    ]
) ?> 
</div>
 <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_id', ['label' => 'Ponto Atend.']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('funcionarios_id', ['label' => 'Func. Atend.']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('chave_nfe', ['label' => 'Chave NFE']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('soma_pontuacoes', ['label' => 'Total Pontos da NFE']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('data', ['label' => 'Data']) ?></th>
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pontuacoes_comprovantes as $pontuacoes_comprovante) : ?>
            <tr>
                <td><?= h($pontuacoes_comprovante->cliente->nome_fantasia) ?></td>
                <td><?= h($pontuacoes_comprovante->funcionario->nome) ?></td>
                <td><?= h($pontuacoes_comprovante->chave_nfe) ?></td>
                <td class="pull-right"><?= h($this->Number->precision($pontuacoes_comprovante->soma_pontuacoes[0]->quantidade_gotas, 2)) ?></td>
                <td><?= h($pontuacoes_comprovante->data->format('d/m/Y H:i:s')) ?></td>
                <td class="actions">
                    <?=
                    $this->Html->link(
                        __(
                            '{0} ',
                            $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])
                        ),
                        [
                            'controller' => 'pontuacoes',
                            'action' => 'detalhes_cupom_cliente_final', $pontuacoes_comprovante->id
                        ],
                        [
                            'class' => 'btn btn-default btn-xs',
                            'title' => 'Ver detalhes',
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


 

