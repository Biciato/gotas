<?php

/**
 * @var \App\View\AppView $this
 */

/**
 * selecionar_cliente_tipo_brinde.ctp
 *
 *
 * View para tipos_brindes_clientes/tipos_brindes_cliente
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TiposBrindesCliente
 *
 * @category  View
 * @package   App\Template\TiposBrindesClientes
 * @author    Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since     2018-11-29
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   1.0
 * @link      http://pear.php.net/package/PackageName
 * @since     File available since Release 1.0.0
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = __("Selecionar Loja para Configurar Tipo de Brinde");

// Barra de navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

// Menu Esquerdo

echo $this->element("../TiposBrindesClientes/left_menu");


?>
<div class="tiposBrindesClientes form col-lg-9 col-md-8 columns content">
    
    <fieldset>
        <legend><?= __($title) ?></legend>
        <table class="table table-striped table-hover table-responsive">
            <thead>
                <tr>
                    <th scope="col">Loja/Posto de Atendimento</th>
                    <th scope="col" class="actions">
                        <?= __('Ações') ?>
                        <?= $this->Html->tag(
                            'button',
                            __(
                                "{0} Legendas",
                                $this->Html->tag('i', '', ['class' => 'fa fa-book'])
                            ),
                            [
                                'class' => 'btn btn-xs btn-default right-align modal-legend-icons-save',
                                'data-toggle' => 'modal',
                                'data-target' => '#modalLegendIconsSave'
                            ]
                        ) ?>

                        </th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($clientes as $cliente) : ?> 
                <tr>
                    <td><?php echo $cliente['razao_social'] ?></td>
                    <td>
                        <a href="/tipos-brindes-clientes/tipos-brindes-cliente/<?= $cliente["id"] ?>" 
                            class="btn btn-primary btn-xs"
                            title="Configurar">
                            <span class="fa fa-cogs"></span>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?> 
            </tbody>
        </table>
    </fieldset>
</div>
