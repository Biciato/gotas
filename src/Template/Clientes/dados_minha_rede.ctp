<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

?>
        

<?= $this->element('../Clientes/left_menu', ['controller' => 'pages', 'action' => 'index', 'dados_minha_rede' => true]) ?>
<div class="clientes index col-lg-9 col-md-10 columns content">
    <h3><?= h($cliente->nome_fantasia) ?></h3>
    <table class="table table-striped table-hover">
    <?php if (isset($matriz) && $matriz->id > 0) : ?>

    <tr>
    <th> Matriz</th>
    <td> <?= h($matriz->nome_fantasia)?></td>
    </tr>
<?php endif ?>

        <tr>
            <th>Nome Fantasia</th>
            <td><?= h($cliente->nome_fantasia) ?></td>
        </tr>
        <tr>
            <th>Razao Social</th>
            <td><?= h($cliente->razao_social) ?></td>
        </tr>
        <tr>
            <th>Cnpj</th>
            <td><?= h($this->NumberFormat->formatNumberToCNPJ($cliente->cnpj)) ?></td>
        </tr>
        <tr>
            <th>Endereco</th>
            <td><?= h($cliente->endereco )?></td>
        </tr>
        <tr>
            <th>Número</th>
            <td><?= $this->Number->format($cliente->endereco_numero) ?></td>
        </tr>
        <tr>
            <th>Complemento</th>
            <td><?= h($cliente->endereco_complemento) ?></td>
        </tr>
        <tr>
            <th>Bairro</th>
            <td><?= h($cliente->bairro) ?></td>
        </tr>
        <tr>
            <th>Municipio</th>
            <td><?= h($cliente->municipio) ?></td>
        </tr>
        <tr>
            <th>Estado</th>
            <td><?= h($cliente->estado) ?></td>
        </tr>
        <tr>
            <th>País</th>
            <td><?= h($cliente->pais) ?></td>
        </tr>
        <tr>
            <th>CEP</th>
            <td><?= h($this->Address->formatCEP($cliente->cep)) ?></td>
        </tr>
        <tr>
            <th>Tel Fixo</th>
            <td><?= h($this->Phone->formatPhone($cliente->tel_fixo)) ?></td>
        </tr>
        <tr>
            <th>Tel Fax</th>
            <td><?= h($this->Phone->formatPhone($cliente->tel_fax)) ?></td>
        </tr>
        <tr>
            <th>Tel Celular</th>
            <td><?= h($this->Phone->formatPhone($cliente->tel_celular)) ?></td>
        </tr>
        <tr>
            <th>Tipo de Unidade</th>
            <td><?= $this->ClienteUtil->getTypeUnity($cliente->tipo_unidade) ?></td>
        </tr>
      
      
        <tr>
            <th>Data de Inclusão</th>
            <td><?= h(($cliente->audit_insert->format('d/m/Y H:i:s'))) ?></tr>
        </tr>
        
    </table>

        <h4>
            <?= __("Dados de filiais") ?>
        </h4>

        <?= $this->element('../Clientes/filtro_clientes', ['controller' => 'clientes', 'action' => 'dados_minha_rede']) ?>

    <?php if (sizeof($filiais->toArray()) == 0) : ?> 

    <h5 class="text-center"><?= __("Não foram encontrados filiais.") ?></h5>
    <?php else: ?>
        
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('tipo_unidade') ?></th>
                    <th><?= $this->Paginator->sort('nome_fantasia') ?></th>
                    <th><?= $this->Paginator->sort('razao_social') ?></th>
                    <th><?= $this->Paginator->sort('cnpj') ?></th>
                    <th class="actions">
                        <?= __('Ações') ?>
                        <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filiais as $filial) : ?>
                <tr>
                    <td><?= $this->ClienteUtil->getTypeUnity($filial->tipo_unidade) ?></td>
                    <td><?= h($filial->nome_fantasia) ?></td>
                    <td><?= h($filial->razao_social) ?></td>
                    <td><?= h($this->NumberFormat->formatNumberToCNPJ($filial->cnpj)) ?></td>
                    
                    <td class="actions" style="white-space:nowrap">
                        <?= $this->Html->link(
                            __('{0} Ver detalhes',
                                $this->Html->tag('i', '', 
                                [
                                    'class' => 'fa fa-info-circle'
                                ]
                                )
                            ),
                            [
                                'action' => 'view', $filial->id
                            ],
                            [
                                'class'=>'btn btn-primary btn-xs', 'escape' => false
                            ]
                        ) ?>
                        <!-- <?= $this->Html->link(__('Editar'), ['action' => 'editar_rede', $filial->id], ['class'=>'btn btn-primary btn-xs']) ?> -->
                        
                    </td>
                </tr>
                <?php endforeach; ?>
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
    <?php endif; ?>
</div>
