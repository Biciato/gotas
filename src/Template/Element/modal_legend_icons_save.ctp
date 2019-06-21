<?php

/**
 * @about: view exclusiva para conteúdo do tipo 'how it works'
 * @author: Gustavo Souza Gonçalves
 * @file: \src\Template\Element\modal_howitworks.ctp
 * @date: 09/09/2017
 * 
 * --------------------------------------------------------------------------------
 * Version list:
 * @version: 0.1
 * @notes: Comportamento como diálogo 
 * 
 */

/**
 * Breve explicação de como usar:
 *
 * 1 - Definindo o Modal
 * 1.1 - crie um div pai chamado modal-how-it-works-parent
 * 1.2 - Defina o id com o nome desejado
 * 1.3 - Título: 
 * dentro de modal-how-it-works-parent, coloque um div modal-how-it-works-title.
 
 * 1.4 - Conteúdo:
 * dentro de modal-how-it-works-parent, coloque um div modal-how-it-works-body. 
 * O conteúdo deste div será inserido no corpo do modal
 * 
 * * Aviso:
 * Este modal não aceita comportamento para comandos. Talvez em futuras versões
 * 
 * 2 . Como executar:
 *
 * 2.1 - Crie um botão (div, button) contendo a classe modal-legend-icons-save, e coloque um atributo id em target-id anexe um evento conforme o seguinte:
 * em HTML:
 * <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
 * Em CAKEPHP3:
 * <?= $this->Html->tag('button', __("{0} Legendas", 
 *  $this->Html->tag('i', '', ['class' => 'fa fa-book'])),
 *  [
 *      'class' => 'btn btn-xs btn-default right-align modal-legend-icons-save',
 *      'data-toggle' => 'modal',
 *      'data-target' => '#modalLegendIconsSave'
 *      ]
 *  ) ?>
 * 
 */


?>

<div id="modalLegendIconsSave" class="modal fade modal-legend-icons-save" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Legenda para Botões de Ação</h3>
            </div>
        <div class="modal-body" >
        <!-- <div class="modal-body" style="background-color: white; border-radius: 2px; border: 1px solid black;"> -->
            <table class="table table-striped table-hover table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>
                            Ação
                        </th>
                        <th>
                            Nome da Ação
                        </th>
                        <th>
                            Descrição
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?= $this->Html->tag(
                                'button',
                                __('{0} ', $this->Html->tag('i', '', ['class' => 'fa fa-info-circle'])),
                                [
                                    'class' => 'btn btn-xs btn-default ',
                                    'escape' => false,
                                ]
                            ) ?>

                        </td>

                        <td>
                            Ver detalhes
                        </td>
                        <td>
                            Ver detalhes do registro selecionado
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?= $this->Html->tag(
                                'button',
                                __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-edit'])),
                                [
                                    'class' => 'btn btn-xs btn-primary ',
                                    'escape' => false
                                ]
                            ) ?>

                        </td>
                        <td>
                            Editar
                        </td>
                        <td>
                            Editar registro selecionado
                        </td>
                    </tr>

                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0} ', $this->Html->tag('i', '', ['class' => 'fa fa-trash'])),
                            [
                                'class' => 'btn btn-xs btn-danger ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Remover
                        </td>
                        <td>
                            Remove o registro selecionado
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-plus'])),
                            [
                                'class' => 'btn btn-xs btn-primary ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Adicionar
                        </td>
                        <td>
                            Adiciona o registro selecionado (Caso não exista)
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0} ', $this->Html->tag('i', '', ['class' => 'fa fa-minus'])),
                            [
                                'class' => 'btn btn-xs btn-danger ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Remover
                        </td>
                        <td>
                            Remove o registro selecionado (Caso tenha vínculo)
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])),
                            [
                                'class' => 'btn btn-xs btn-primary ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Ativar
                        </td>
                        <td>
                            Ativa o registro selecionado (Caso esteja desativado)
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0} ', $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])),
                            [
                                'class' => 'btn btn-xs btn-danger ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Desativar
                        </td>
                        <td>
                            Desativa o registro selecionado (Caso esteja ativado)
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-check-circle-o'])),
                            [
                                'class' => 'btn btn-xs btn-primary ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Autorizar
                        </td>
                        <td>
                            Autoriza o registro selecionado 
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-check-circle-o'])),
                            [
                                'class' => 'btn btn-xs btn-danger ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Negar
                        </td>
                        <td>
                            Nega autorização do registro selecionado 
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-print'])),
                            [
                                'class' => 'btn btn-xs btn-primary ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Imprimir
                        </td>
                        <td>
                            Abre impressão de registro
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?= $this->Html->tag(
                            'button',
                            __('{0}', $this->Html->tag('i', '', ['class' => 'fa fa-cogs'])),
                            [
                                'class' => 'btn btn-xs btn-primary ',
                                'escape' => false
                            ]
                        ) ?>
                        </td>
                        <td>
                            Configurar
                        </td>
                        <td>
                            Abre tela alvo de configuração
                        </td>
                    </tr>
                </tbody>
            </table>
        

        </div>
        <div class="modal-footer">
        <!-- <div class="modal-footer" style="background-color: white; border-radius: 2px; border: 1px solid black;"> -->
            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>

</div>

