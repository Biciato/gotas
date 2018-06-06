<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/novo_preco_form.ctp
 * @date     09/08/2017
 */
use Cake\Core\Configure;
use Cake\Routing\Router;

echo $this->Form->input(
    "preco",
    [
        "label" => "Preço Atual: ",
        "value" => is_null($ultimo_preco_autorizado) ? 0 : $ultimo_preco_autorizado->preco,
        "id" => "preco_atual",
        "readonly" => true
    ]
);
echo $this->Form->input(
    "preco",
    [
        "type" => "text",
        "label" => "Preço (em gotas):",
        "id" => "preco"
    ]
);
?>

<?php if (Configure::read("debug") == true) : ?>
    <?= $this->Html->script("scripts/clientes_has_brindes_habilitados_preco/preco_brinde_form") ?>
<?php else : ?>
    <?= $this->Html->script("scripts/clientes_has_brindes_habilitados_preco/preco_brinde_form.min") ?>
<?php endif; ?>

<?= $this->fetch("script") ?>
