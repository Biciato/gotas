<?php

/**
 * @description Arquivo para escolha de unidades para cadastro de propaganda
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/RedesHasClientes/propaganda_escolha_unidades.ctp
 * @since       05/08/2018
 *
 */
use Cake\Core\Configure;
use Cake\Routing\Router;


$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Escolher Unidade para Configurar Propagandas', array(), array("class" => "active"));

echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../RedesHasClientes/left_menu') ?>

<div class="usuarios view col-lg-9 col-md-10">

    <legend>Escolha uma opção</legend>

    <?php if ($usuarioLogado["tipo_perfil"] <= Configure::read("profileTypes")["AdminNetworkProfileType"]) : ?>

    <div class="form-group row">
            <h4>Cadastrar para toda a Rede de atendimento:</h4>

            <div class="col-lg-12 pull-left">
                <?= $this->Html->link(
                    __(
                        '{0} Configurar',
                        $this->Html->tag('i', '', ['class' => "fa fa-cogs"])
                    ),
                    array(
                        "controller" => "redes",
                        'action' => 'configurar_propaganda',
                    ),
                    array(
                        'title' => 'Configurar',
                        'class' => 'btn btn-primary btn-confirm botao-navegacao-tabela',
                        'escape' => false
                    )
                );
                ?>
            </div>

    </div>

    <?php endif; ?>

    <div class="form-group row">
    <h4>Cadastrar para um Ponto de atendimento específico:</h4>
        <table class="table table-striped table-hover table-responsive">
            <thead>
                <th>Nome Fantasia</th>
                <th>Razão Social</th>
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
                </thead>
            <tbody>

                <?php foreach ($clientes as $key => $cliente) : ?>
                    <tr>
                        <td><?php echo $cliente["nome_fantasia"]; ?> </td>
                        <td><?php echo $cliente["razao_social"]; ?> </td>
                        <td><?php echo $this->Html->link(
                                __(
                                    '{0} Configurar',
                                    $this->Html->tag('i', '', ['class' => "fa fa-cogs"])
                                ),
                                array(
                                    "controller" => "clientes",
                                    'action' => 'configurar_propaganda', $cliente["id"]
                                ),
                                array(
                                    'title' => 'Configurar',
                                    'class' => 'btn btn-xs btn-primary btn-confirm botao-navegacao-tabela',
                                    'escape' => false
                                )
                            );
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

