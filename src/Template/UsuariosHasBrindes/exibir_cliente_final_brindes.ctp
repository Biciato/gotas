<?php 

/**
 * @description Exibe tela de pesquisa de cliente, para atualizar os dados (view de Funcionário)
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/UsuariosHasBrindes/exibir_cliente_final_brindes.ctp
 * @date        21/02/2018
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'historico_brindes']); ?> 

<div class="col-lg-9"> 
<legend><?= __("Brindes do Usuário {0}", $usuario->nome) ?> </legend>

 <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('clientes_has_brindes_habilitados_id', ['label' => 'Brinde']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantidade', ['label' => 'Qte.']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('preco', ['label' => 'Preço Pago (Em Gotas)']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('data', ['label' => 'Data']) ?></th>
                <th scope="col" class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios_has_brindes as $usuario_has_brinde) : ?>
            <tr>
                <td><?= h($usuario_has_brinde->clientes_has_brindes_habilitado->brinde->nome) ?></td>
                <td ><?= h($this->Number->precision($usuario_has_brinde->quantidade, 2)) ?></td>
                <td ><?= h($this->Number->precision($usuario_has_brinde->preco, 2)) ?></td>
                <td ><?= h($usuario_has_brinde->data->format('d/m/Y H:i:s')) ?></td>
                <td class="actions">
                    <span></span>
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


 

