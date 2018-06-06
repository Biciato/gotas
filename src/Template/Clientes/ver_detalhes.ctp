<?php

use Cake\Core\Configure;
use Cake\Routing\Router;


$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);

$this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'Redes', 'action' => 'ver_detalhes', $cliente->rede_has_cliente->redes_id]);

$this->Breadcrumbs->add('Detalhes da Unidade', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);


$controller_voltar = null;
$action_voltar = null;

if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $controller_voltar = 'redes';
    $action_voltar = 'ver_detalhes';
    $id_voltar = $cliente->rede_has_cliente->redes_id;
} else {
    $controller_voltar = 'pages';
    $action_voltar = 'display';
}

?>

<?= $this->element(
    '../Clientes/left_menu',
    [
        'controller' => $controller_voltar,
        'action' => $action_voltar,
        'id' => $id_voltar,
        'view' => true,
        'configurations' => true
    ]
) ?>
<div class="clientes view col-lg-9 col-md-10">
    <legend>
        <?= h(__("{0} - {1}", $cliente->rede_has_cliente->rede->nome_rede, $cliente->nome_fantasia)) ?>
    </legend>


    <table class="table table-striped table-hover">
        <tr class="form-group">
            <th>Nome da Rede</th>
            <td>
                <?= h($cliente->rede_has_cliente->rede->nome_rede) ?>
            </td>
            <th>Nome Fantasia</th>
            <td>
                <?= h($cliente->nome_fantasia) ?>
            </td>
        </tr>
        <tr class="form-group">
            <th>Razao Social</th>
            <td>
                <?= h($cliente->razao_social) ?>
            </td>
            <th>Código Equipamento Smart Shower</th>
            <td>
                <?= h($cliente->codigo_rti_shower) ?>
            </td>
        </tr>

        <tr class="form-group">

            <th>CNPJ</th>
            <td>
                <?= h($this->NumberFormat->formatNumberToCNPJ($cliente->cnpj)) ?>
            </td>
            <th>Tipo de Unidade</th>
            <td>
                <?= $this->ClienteUtil->getTypeUnity($cliente->tipo_unidade) ?>
            </td>
        </tr>
        <tr>
            <th>Endereco</th>
            <td>
                <?= h($cliente->endereco) ?>
            </td>
            <th>Número</th>
            <td>
                <?php
                $numero = $cliente->endereco_numero;
                $complemento = strlen($cliente->endereco_complemento) > 0 ? " / A" . $cliente->endereco_complemento : "";

                echo $numero . $complemento;
                ?>
            </td>
        </tr>
        <tr>
            <th>Bairro</th>
            <td>
                <?= h($cliente->bairro) ?>
            </td>
            <th>Municipio</th>
            <td>
                <?= h($cliente->municipio) ?>
            </td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>
                <?= h($cliente->estado) ?>
            </td>
            <th>País</th>
            <td>
                <?= h($cliente->pais) ?>
            </td>
        </tr>
        <tr>
            <th>CEP</th>
            <td>
                <?= h($this->Address->formatCEP($cliente->cep)) ?>
            </td>

        </tr>
        <tr>
            <th>Telefone Fixo</th>
            <td>
                <?= h($this->Phone->formatPhone($cliente->tel_fixo)) ?>
            </td>
            <th>Telefone Fax</th>
            <td>
                <?= h($this->Phone->formatPhone($cliente->tel_fax)) ?>
            </td>
        </tr>
        <tr>
            <th>Telefone Celular</th>
            <td>
                <?= h($this->Phone->formatPhone($cliente->tel_celular)) ?>
            </td>
            <th>Data do Registro</th>
            <td>
                <?= h(($cliente->audit_insert->format('d/m/Y H:i:s'))) ?>
            </td>
        </tr>
    </table>
    <div class="related">
        <?php if (!empty($cliente->cliente_lojas)) : ?>
        <h4>
            <?= __('Lojas da Rede') ?>
        </h4>
        <table class="table table-striped table-hover">
            <tr>
                <th>Id</th>

                <th>Tipo Unidade</th>
                <th>Nome Fantasia</th>
                <th>Razao Social</th>
                <th>Bairro</th>
                <th>Municipio</th>
                <th>Telefone</th>
                <th>Fax</th>
                <th>Celular</th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
            <?php foreach ($cliente->cliente_lojas as $clienteLojas) : ?>
            <tr>
                <td>
                    <?= h($clienteLojas->id) ?>
                </td>
                <td>
                    <?= h($this->ClienteUtil->getTypeUnity($clienteLojas->tipo_unidade)) ?>
                </td>
                <td>
                    <?= h($clienteLojas->nome_fantasia) ?>
                </td>
                <td>
                    <?= h($clienteLojas->razao_social) ?>
                </td>
                <td>
                    <?= h($clienteLojas->municipio) ?>
                </td>
                <td>
                    <?= h($clienteLojas->estado) ?>
                </td>

                <td>
                    <?= h($this->Phone->formatPhone($clienteLojas->tel_fixo)) ?>
                </td>
                <td>
                    <?= h($this->Phone->formatPhone($clienteLojas->tel_fax)) ?>
                </td>
                <td>
                    <?= h($this->Phone->formatPhone($clienteLojas->tel_celular)) ?>
                </td>


                <td class="actions">
                    <?= $this->Html->link(__('Ver'), ['controller' => 'Clientes', 'action' => 'view', $clienteLojas->id], ['class' => 'btn btn-default btn-xs']) ?>

                        <?= $this->Html->link(__('Editar'), ['controller' => 'Clientes', 'action' => 'edit', $clienteLojas->id], ['class' => 'btn btn-primary btn-xs']) ?>

                            <?= $this->Form->postLink(
                                __('Remover'),
                                [
                                    'controller' => 'Clientes', 'action' => 'delete',
                                    $clienteLojas->id,
                                    '?' =>
                                        [
                                        'cliente_id' => $clienteLojas->id,
                                        'return_url' => 'index'
                                    ]
                                ],
                                [
                                    'confirm' => __(
                                        'Are you sure you want to delete # {0}?',
                                        $clienteLojas->id
                                    ), 'class' => 'btn btn-danger btn-xs'
                                ]
                            ) ?>


                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
