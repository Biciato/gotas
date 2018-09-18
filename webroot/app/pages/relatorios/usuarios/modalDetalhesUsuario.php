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
        <h4 class="modal-title">Dados Cadastrais</h4>

        <div class="row ">
            <div class="col-lg-4 form-group">
                <label for="nome">Nome Completo:</label>
                <input ng-disabled="true" id="nome" ng-model="usuario.nome" class="form-control" />
            </div>
            <div class="col-lg-4 form-group">
                <label for="dataNasc">Data de Nascimento:</label>

                <input ng-disabled="true" id="dataNasc" ng-model="usuario.data_nasc | date: 'dd/MM/yyyy'" class="form-control" />
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 form-group">
                <label for="sexo">Sexo:</label>
                <input ng-disabled="true" id="sexo" ng-model="usuario.sexo | gender" class="form-control" />
            </div>
            <div class="col-lg-4 form-group">
                <label for="necessidades_especiais">Necessidades Especiais:</label>
                <input ng-disabled="true" id="necessidades_especiais" ng-model="usuario.necessidades_especiais | yesNo" class="form-control" />
            </div>

            <!-- <img src="{{inputData.usuario.foto_perfil_completo}}" alt=""> -->

        </div>

        <h4 class="modal-title">Veículos</h4>

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
