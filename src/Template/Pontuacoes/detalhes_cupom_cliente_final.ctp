<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/detalhes_cupom_cliente_final.ctp
 * @date     20/02/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;
?>
    <?= $this->element('../Pages/left_menu', ['item_selected' => 'consulta_pontuacoes', 'mode_selected' => 'exibir_cliente_final_pontuacoes']); ?> 

        <div class="col-lg-9 col-md-10 columns">

            <legend>
                <?= __("Detalhes do Cupom Fiscal") ?>
            </legend>

            <h4>Dados do Cupom</h4>
            <table class="table table-striped table-hover">
                <tr>
                    <th>
                        <?= __('Cliente') ?>
                    </th>
                    <td>
                        <?= h($pontuacao->usuario->nome) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?= __('Funcionário do atendimento') ?>
                    </th>
                    <td>
                        <?= h($pontuacao->funcionario->nome) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?= __('Total de Gotas') ?>
                    </th>
                    <td>
                        <?= h($this->Number->precision($pontuacao->soma_pontuacoes, 2)) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?= __('Chave da NFE') ?>
                    </th>
                    <td>
                        <?= h($pontuacao->chave_nfe) ?>
                    </td>
                </tr>
              
                <tr>
                    <th>
                        <?= __('Data do Registro ') ?>
                    </th>
                    <td>
                        <?= h($pontuacao->data->format('d/m/Y H:i:s')) ?>
                    </td>

                </tr>
            </table>

            <h4>Descritivo de gotas:</h4>

                                
            <?php if (is_null($pontuacao->nome_img)) : ?>
                <?= $this->element('../Pontuacoes/tabela_descritivo_pontuacoes', ['pontos' => $pontuacao->pontuacoes]) ?>
                <?php else : ?>
                <div class="col-lg-4">
                    <legend>Imagem da Captura</legend>
                                <?= $this->Html->image($pontuacao->nome_img, ['alt' => 'Comprovante', 'class' => 'image-receipt']) ?>
                </div>
                <div class="col-lg-8">
                    <legend>Dados da Captura</legend>

                    <?= $this->element('../Pontuacoes/tabela_descritivo_pontuacoes', ['pontos' => $pontuacao->pontuacoes]) ?>
                </div>
            <?php endif; ?>


        </div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->css('styles/pontuacoes/detalhes_cupom'); ?>
<?php else : ?> 
    <?= $this->Html->css('styles/pontuacoes/detalhes_cupom.min'); ?>
<?php endif; ?>

<?= $this->fetch('css'); ?>
