<!--
    Modal de Detalhes de Usuário
    @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
    @since  17/09/2018
    @path   /webroot/app/pages/relatorios/usuarios/modalDetalhesUsuario.php
 -->

<div class="modal-demo">

    <div class="modal-header">
        <h3 class="modal-title">
            Detalhes do Usuário {{inputData.usuario.nome}}
        </h3>
    </div>

    <div class="modal-body">
        <legend>Dados Cadastrais</legend>

        <legend>Veículos</legend>

        <table class="table table-condensed table-responsive table-striped table-hover">
            <thead>
                <th ng-repeat="cabecalho in cabecalhos">{{cabecalho}}</th>
            </thead>
            <tbody>
                <tr ng-repeat="veiculo in dadosVeiculosUsuario | orderBy: veiculo.placa | startFrom:(paginaAtual - 1 ) * tamanhoDaPagina | limitTo:tamanhoDaPagina">
                    <td>{{veiculo.placa}}</td>
                    <td>{{veiculo.modelo}}</td>
                    <td>{{veiculo.fabricante}}</td>
                    <td>{{veiculo.ano}}</td>
                    <td>{{veiculo.dataCadastro | date : "dd/MM/yyyy HH:MM:ss"}}</td>
                </tr>
            </tbody>
        </table>
        <!-- Paginação -->
        <div class="text-center">
            <ul uib-pagination boundary-links="true" total-items="dadosUsuarios.length" ng-model="paginaAtual" items-per-page="tamanhoDaPagina" class="pagination-sm" max-size="5" previous-text="<< Ant." next-text="Próx. >>" first-text="Primeira" last-text="Última"></ul>
        </div>
        <div ng-show="dadosVeiculosUsuario.length == 0">
            <span class="alert alert-warning">Não há registros à serem exibidos!</span>
        </div>
    </div>

    <div class="modal-footer">

        <div class="btn btn-primary" ng-click="fechar()">
            <i class="fa fa-check"></i> Fechar
        </div>
    </div>


</div>
