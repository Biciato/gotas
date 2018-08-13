<?php

/**
 * @var \App\View\AppView $this
 */

/**
 * form_genero_brindes_cliente.ctp
 *
 *
 * Formulário de input de dados
 *
 * Variáveis:
 * @var       \App\View\AppView $this
 * @var       \App\Model\Entity\GeneroBrindesCliente
 *
 * @category  View
 * @package   App\Template\GeneroBrindesClientes
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

$selectGeneroBrindesEnabled = isset($selectGeneroBrindesEnabled) ? $selectGeneroBrindesEnabled : false;

?>

<div class="form-group row">
    <div class="col-lg-4">
        <?= $this->Form->control(
            'genero_brindes_id',
            [
                "id" => "genero_brindes_id",
                "class" => "genero_brindes_id",
                "type" => "select",
                "empty" => "<Selecionar>",
                "options" => $generoBrindes,
                "disabled" => $selectGeneroBrindesEnabled
            ]
        ); ?>
    </div>
    <div class="col-lg-4">
        <?= $this->Form->control(
            'tipo_principal_codigo_brinde',
            [
                "id" => 'tipo_principal_codigo_brinde',
                "type" => "number",
                "min" => 0,
                "max" => 9,
                "step" => 1
            ]
        ); ?>
    </div>
    <div class="col-lg-4">
        <?= $this->Form->control(
            'tipo_secundario_codigo_brinde',
            [
                "id" => 'tipo_secundario_codigo_brinde',
                "type" => "number",
                "min" => 00,
                "max" => 99,
                "step" => 1
            ]
        ); ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->control('habilitado', ["Genero Habilitado para Cliente?"]); ?>
    </div>
</div>

<?= $this->element("../Element/Buttons/confirm", ["titleButton" => "Salvar"]); ?>


<?php if (Configure::read("debug")) {

    echo $this->Html->script("scripts/genero_brindes_clientes/form_genero_brindes_clientes");
} else {
    echo $this->Html->script("scripts/genero_brindes_clientes/form_genero_brindes_clientes.min");
}
echo $this->fetch("script");
?>
