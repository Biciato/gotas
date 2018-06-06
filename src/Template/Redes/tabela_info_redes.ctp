<?php

/**
 * @description Ver detalhes de Usuário
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Redes/tabela_info_redes.ctp
 * @date        22/11/2017
 *
 */

?>


<table class="table table-striped table-hover">
    <tr>
        <th scope="row">
            <?= __('Nome Rede') ?>
        </th>
        <td>
            <?= h($rede->nome_rede) ?>
        </td>
    </tr>
    
    <tr>
        <th scope="row">
            <?= __('Ativado') ?>
        </th>
        <td>
            <?= $this->Boolean->convertBooleanToString($rede->ativado) ?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?= __('Data de Criação') ?>
        </th>
        <td>
            <?= h($rede->audit_insert->format('d/m/Y H:i:s')) ?>
        </td>
    </tr>
    
    <?php if (isset($imagem) && strlen($imagem) > 0) : ?>
        <tr>
            <th scope="row">
                <?= __('Imagem atualmente alocada') ?>
            </th>
            <td>
                <img src="<?php echo $imagem ?>" height="120px" width="380px"/>
            </td>
        </tr>
    <?php endif; ?>
</table>
