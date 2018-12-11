<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/administrar_unidades.ctp
 * @date     27/08/2017
 * @deprecated 1.0
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>

<nav class="col-lg-3 col-md-2 columns" id="actions-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a href=""><?= __('Menu') ?></a></li>

        	<li><?= $this->Html->link(__('Voltar'), ['controller' => 'pages', 'action' => 'display']) ?></li>

        
    </ul>
</nav>

<div class="clientes form col-lg-9 col-md-10 columns content">

    <legend>Unidades à Administrar</legend>
    
    <?= $this->element('../Clientes/filtro_clientes', ['controller' => 'Clientes', 'action' => 'administrar_unidades']) ?>

    <table class="table table-striped table-hover">
    <thead>
    <tr>
        <th><?= $this->Paginator->sort('razao_social')?></th>
        <th><?= $this->Paginator->sort('nome_fantasia')?></th>
        <th><?= $this->Paginator->sort('cnpj')?></th>
        <th class="actions">
            <?= __('Ações') ?>
            <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
        </th>
    </tr>
    </thead>

    <tbody>
        <tbody>
            
            <?php foreach ($clientes as $key => $value) : ?>
                <tr>
                    <td><?= h($value->razao_social)?></td>
                    <td><?= h($value->nome_fantasia)?></td>
                    <td><?= h($this->NumberFormat->formatNumberToCNPJ($value->cnpj))?></td>
                    <td class="actions" style="white-space:nowrap">
                    <?= $this->Html->link(__("{0} Gerenciar",
                        $this->Html->tag('i', '', ['class' => 'fa fa-gear'])),
                        '#',
                        array(
                            'class'=>'btn btn-danger btn-gerenciar-unidade',
                            'data-toggle'=> 'modal',
                            'data-target' => '#modal-manage-unit',
                            'data-action'=> Router::url(
                                ['action'=>'administrar_unidades',$value->id]
                            ),
                            'escape' => false),
                    false); ?>
  
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </tbody>

    </table>
    <div class="paginator">
        <center>
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape'=>false]) ?>
                <?= $this->Paginator->numbers(['escape'=>false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape'=>false]) ?>
                <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>

</div>
