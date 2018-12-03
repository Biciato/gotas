<?php

/**
 * @var \App\View\AppView $this
 */

/**
 * form_tipos_brindes_cliente.ctp
 *
 *
 * Formulário de input de dados
 *
 * Variáveis:
 * @var       \App\View\AppView $this
 * @var       \App\Model\Entity\TiposBrindesCliente
 *
 * @category  View
 * @package   App\Template\TiposBrindesClientes
 * @author    Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date      06/06/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   1.0
 * @link      http://pear.php.net/package/PackageName
 * @since     File available since Release 1.0.0
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$selectTiposBrindesEnabled = isset($selectTiposBrindesEnabled) ? $selectTiposBrindesEnabled : false;
$divisor = $selectTiposBrindesEnabled ? "col-lg-4" : "col-lg-6";
?>

<div class="form-group row">

    <?php if ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["AdminDeveloperProfileType"]) : ?> 

        <?php if ($selectTiposBrindesEnabled) : ?>
            <div class="<?= $divisor ?>">
                <?= $this->Form->input(
                    'tipos_brindes_redes_id',
                    [
                        "id" => "tipos_brindes_redes_id",
                        "class" => "tipos_brindes_redes_id",
                        "type" => "select",
                        "label" => "Tipos de Brindes da Rede",
                        "empty" => "<Selecionar>",
                        "options" => $tiposBrindesRedes,
                        // "disabled" => $selectTiposBrindesEnabled
                    ]
                ); ?>
            </div>
        <?php endif; ?>
        <div class="<?= $divisor ?>">
            <?= $this->Form->control(
                'tipo_principal_codigo_brinde',
                [
                    "id" => 'tipo_principal_codigo_brinde',
                    "type" => "text",
                    "maxLength" => 1
                ]
            ); ?>
        </div>
        <div class="<?= $divisor ?>">
            <?= $this->Form->control(
                'tipo_secundario_codigo_brinde',
                [
                    "id" => 'tipo_secundario_codigo_brinde',
                    "type" => "text",
                    "maxLength" => 2
                ]
            ); ?>
        </div>
    <?php else : ?> 
        
        <?= $this->Form->input(
            'tipos_brindes_redes_id',
            [
                "id" => "tipos_brindes_redes_id",
                "class" => "tipos_brindes_redes_id",
                "type" => "select",
                "label" => "Tipos de Brindes da Rede",
                "empty" => "<Selecionar>",
                "options" => $tiposBrindesRedes,
                // "disabled" => $selectTiposBrindesEnabled
            ]
        ); ?>
        

    <?php endif; ?> 
</div>

<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->control('habilitado', ["Tipo de Brinde Habilitado para Cliente?"]); ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-2">
        <?= $this->Form->button(
            __('{0} {1}', $this->Html->tag('i', '', ['class' => 'fa fa-save']), "Salvar"),
            [
                'class' => 'btn btn-primary btn-block',
                'escape' => false
                ]
        ) ?>
    </div>
    <div class="col-lg-2">
        <a href="<?= sprintf("/tipos-brindes-clientes/tipos-brindes-cliente/%s", $cliente["id"] ); ?>"
            class="btn btn-danger btn-block botao-cancelar" > 
            <span class="fa fa-window-close"></span> Cancelar
        </a>
    </div>

</div>




<?php if (Configure::read("debug")) {
    echo $this->Html->script("scripts/tipos_brindes_clientes/form_tipos_brindes_clientes");
} else {
    echo $this->Html->script("scripts/tipos_brindes_clientes/form_tipos_brindes_clientes.min");
}
echo $this->fetch("script");
?>
