<!--
    Modal de Detalhes de Usuário
    @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
    @since  17/09/2018
    @path   /webroot/app/pages/relatorios/usuarios/modalDetalhesUsuario.php
 -->

<div class="modal-demo">

    <div class="modal-header">
        <h4 class="modal-title" ng-maxlength=60>
            Detalhes de Assiduidade do Usuário  {{usuario.nome}}
        </h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <uib-tabset active="active">
                <uib-tab heading="Detalhes Assiduidade" index="1">
                    <div>
                        <table class="table table-condensed table-responsive table-striped table-hover">
                            <thead>
                                <th>Ano</th>
                                <th>Mês</th>
                                <th>Assiduidade</th>
                            </thead>
                            <tbody>
                                <tr ng-repeat="assiduidade in dadosAssiduidade | orderBy: assiduidade.ano | startFrom:(paginaAtual - 1 ) * tamanhoDaPagina | limitTo:tamanhoDaPagina">
                                    <td>{{assiduidade.ano}}</td>
                                    <td>{{assiduidade.mes}}</td>
                                    <td>{{assiduidade.quantidadeMes}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Paginação -->
                        <div class="text-center">
                            <ul uib-pagination boundary-links="true" total-items="dadosVeiculosUsuario.length" ng-model="paginaAtualVeiculos" items-per-page="tamanhoDaPaginaVeiculos" class="pagination-sm" max-size="5" previous-text="<< Ant." next-text="Próx. >>" first-text="Primeira" last-text="Última"></ul>
                        </div>
                        <div ng-if="dadosVeiculosUsuario.length == 0">
                            <span class="alert alert-warning">Não há registros à serem exibidos!</span>
                        </div>
                    </div>
                </uib-tab>

            </uib-tabset>
        </div>
    </div>

    <div class="modal-footer">

        <div class="btn btn-primary" ng-click="fechar()">
            <i class="fa fa-check"></i> Fechar
        </div>
    </div>


</div>
