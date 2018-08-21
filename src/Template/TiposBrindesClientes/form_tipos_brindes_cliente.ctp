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

?>

<div class="form-group row">
    <div class="col-lg-4">
        <?= $this->Form->control(
            'tipos_brindes_redes_id',
            [
                "id" => "tipos_brindes_redes_id",
                "class" => "tipos_brindes_redes_id",
                "type" => "select",
                "empty" => "<Selecionar>",
                "options" => $tiposBrindesRedes,
                "disabled" => $selectTiposBrindesEnabled
            ]
        ); ?>
    </div>
    <div class="col-lg-4">
        <?= $this->Form->control(
            'tipo_principal_codigo_brinde',
            [
                "id" => 'tipo_principal_codigo_brinde',
                "type" => "text",
                "maxLength" => 1
            ]
        ); ?>
    </div>
    <div class="col-lg-4">
        <?= $this->Form->control(
            'tipo_secundario_codigo_brinde',
            [
                "id" => 'tipo_secundario_codigo_brinde',
                "type" => "text",
                "maxLength" => 2
            ]
        ); ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-12">
        <?= $this->Form->control('habilitado', ["Tipo de Brinde Habilitado para Cliente?"]); ?>
    </div>
</div>

<?= $this->element("../Element/Buttons/confirm", ["titleButton" => "Salvar"]); ?>


<?php if (Configure::read("debug")) {
    echo $this->Html->script("scripts/tipos_brindes_clientes/form_tipos_brindes_clientes");
} else {
    echo $this->Html->script("scripts/tipos_brindes_clientes/form_tipos_brindes_clientes.min");
}
echo $this->fetch("script");
?>
