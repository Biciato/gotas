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

    <?php if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) : ?>

        <?php if ($selectTiposBrindesEnabled) : ?>
            <div class="<?= $divisor ?>">
                <label for="tipos_brindes_redes_id">Tipos de Brindes da Rede*</label>
                <?= $this->Form->input(
                    'tipos_brindes_redes_id',
                    [
                        "id" => "tipos_brindes_redes_id",
                        "class" => "tipos_brindes_redes_id",
                        "type" => "select",
                        "label" => false,
                        "required" => "required",
                        "empty" => true,
                        "options" => $tiposBrindesRedes,
                        // "disabled" => $selectTiposBrindesEnabled
                    ]
                ); ?>
            </div>
        <?php endif; ?>
        <div class="<?= $divisor ?>">
            <label for="tipo_principal_codigo_brinde">Tipo Principal Codigo Brinde*</label>
            <input type="text"
                name="tipo_principal_codigo_brinde"
                id="tipo_principal_codigo_brinde"
                required="required"
                maxLength="1"
                placeholder="Tipo Principal Codigo Brinde..."
                class="form-control"
                value="<?= $tiposBrindesCliente['tipo_principal_codigo_brinde']?>"
                />
        </div>
        <div class="<?= $divisor ?>">
            <label for="tipo_secundario_codigo_brinde">Tipo Secundário Codigo Brinde*</label>
            <input type="text"
                name="tipo_secundario_codigo_brinde"
                id="tipo_secundario_codigo_brinde"
                required="required"
                maxLength="2"
                placeholder="Tipo Secundario Codigo Brinde..."
                class="form-control"
                value="<?= $tiposBrindesCliente['tipo_secundario_codigo_brinde']?>"
                />
        </div>
    <?php else : ?>

        <label for="tipos_brindes_redes_id">Tipos de Brindes da Rede*</label>
        <?= $this->Form->input(
            'tipos_brindes_redes_id',
            [
                "id" => "tipos_brindes_redes_id",
                "class" => "tipos_brindes_redes_id",
                "type" => "select",
                "label" => false,
                "required" => "required",
                "empty" => true,
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

<div class="form-group row ">
    <div class="col-lg-12 text-right">
        <button type="submit"
            class="btn btn-primary botao-confirmar">
            <i class="fa fa-save "></i>
            Salvar
        </button>
        <a href="<?= sprintf("/tipos-brindes-clientes/tipos-brindes-cliente/%s", $cliente["id"] ); ?>"
            class="btn btn-danger  botao-cancelar" >
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
