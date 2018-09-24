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
        <div class="row">
            <uib-tabset active="active">
                <uib-tab heading="Dados Cadastrais" index="0">
                    <div class="col-lg-8">
                        <div class="row ">
                            <div class="col-lg-6 form-group">
                                <label for="nome">Nome Completo:</label>
                                <input ng-disabled="true" id="nome" ng-model="usuario.nome" class="form-control" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label for="dataNasc">Data de Nascimento:</label>
                                <input ng-disabled="true" id="dataNasc" ng-model="usuario.data_nasc | date: 'dd/MM/yyyy'" class="form-control" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                <label for="sexo">Sexo:</label>
                                <input ng-disabled="true" id="sexo" ng-model="usuario.sexo | gender" class="form-control" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label for="necessidades_especiais">Necessidades Especiais:</label>
                                <input ng-disabled="true" id="necessidades_especiais" ng-model="usuario.necessidades_especiais | yesNo" class="form-control" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-7 form-group">
                                <label for="email">Email:</label>
                                <input type="text" id="email" class="form-control" ng-model="usuario.email" ng-disabled="true" />
                            </div>
                            <div class="col-lg-5 form-group">
                                <label for="cpf">CPF:</label>
                                <input type="text" id="cpf" class="form-control" ng-model="usuario.cpf | cpf" ng-disabled="true" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                <label for="municipio">Municipio:</label>
                                <input type="text" class="form-control" ng-model="usuario.municipio" ng-disabled="true" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label for="estado">Estado:</label>
                                <input type="text" class="form-control" ng-model="usuario.estado" ng-disabled="true" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 form-group">
                                <label for="telefone">Telefone:</label>
                                <input type="text" class="form-control" ng-model="usuario.telefone | phoneNumber" ng-disabled="true" />
                            </div>
                            <div class="col-lg-6 form-group">
                                <label for="estado">Conta Ativa:</label>
                                <input type="text" class="form-control" ng-model="usuario.conta_ativa | yesNo" ng-disabled="true" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row col-lg-12">
                            <label for="foto_perfil_completo">Foto Perfil:</label>
                            <img ng-src="{{usuario.foto_perfil_completo}}" id="foto_perfil_completo" class="fotoPerfil" />
                        </div>
                    </div>
                </uib-tab>
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
