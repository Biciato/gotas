<?php

/**
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// const SITE_ADDRESS = 'https://40.71.26.138/',
// const SITE_ADDRESS = 'https://www.rtibrindes.com.br/',
// const SITE_ADDRESS = "https://www.rtibrindes.local/";
// const WEBROOT_ADDRESS = self::SITE_ADDRESS + "webroot";

$debug = Configure::read("debug");

$develHost = 1;
$serverSuffix = $develHost ? ".local" : ".com.br";

// $serverAddress = $debug? "sistema-devel.gotas.com.br" : "sistema.gotas.com.br";
// $serverAddress = $debug ? "sistema.gotas.com.br" : "sistema.gotas.com.br";
$serverAddress = $debug? "sistema-devel.gotas" : "sistema.gotas";

$serverAddress = $serverAddress . $serverSuffix;



// $server = empty($_SERVER) ? "sistema.gotas.com.br" : $_SERVER["HTTP_HOST"];
Configure::write(
    [
        // configuração de banco de dados:

        // "environmentMode" => "development",
        // // "environmentMode" => "production",
        // // 'appAddress' => 'https://40.71.26.138/',
        'appAddress' => sprintf("%s%s%s", 'https://', $serverAddress, '/'),
        'webrootAddress' => sprintf("%s%s%s", 'https://', $serverAddress, '/webroot'),

        // Código antigo
        // "environmentMode" => "development",
        "environmentMode" => "production",
         // 'appAddress' => 'https://40.71.26.138/',
        'appAddress' => sprintf("%s%s%s", 'https://', $_SERVER["HTTP_HOST"], '/'),
        // 'appAddress' => "https://sistema-devel.gotas.com.br/",
        'webrootAddress' => sprintf("%s%s%s", 'https://', $_SERVER["HTTP_HOST"], '/webroot'),
        // 'webrootAddress' => "https://sistema-devel.gotas.com.br/webroot",


        // Gotas

        'dropletsUsageStatus' => array(
            'NotUsed' => 0,
            'ParcialUsed' => 1,
            'FullyUsed' => 2
        ),
        'emailAddressSender' => 'noreply@rtisolutions.com.br',
        'enabledDisabledArray' => [
            true => 'Habilitado',
            false => 'Desabilitado'
        ],
        'giftApprovalStatus' =>
            [
            'AwaitingAuthorization' => 0,
            'Allowed' => 1,
            'Denied' => 2
        ],
        'giftApprovalStatusTranslated' =>
            [
            0 => 'Aguardando Autorização',
            1 => 'Autorizado',
            2 => 'Negado'
        ],
        'models' =>
            [
            "Brindes",
            "Clientes",
            "ClientesHasBrindesEstoque",
            "ClientesHasBrindesHabilitados",
            "ClientesHasBrindesHabilitadosPreco",
            "ClientesHasUsuarios",
            "ClientesHasQuadroHorario",
            "Cupons",
            "Gotas",
            "Pontuacoes",
            "PontuacoesComprovantes",
            "PontuacoesPendentes",
            "Redes",
            "RedesHasClientes",
            "RedesHasClientesAdministradores",
            "TiposBrindesRedes",
            "TiposBrindesClientes",
            "Transportadoras",
            "TransportadorasHasUsuarios",
            "UsuariosEncrypted",
            "Usuarios",
            "UsuariosHasBrindes",
            "UsuariosHasVeiculos",
            "Veiculos"
        ],
        'profileTypes' =>
            [
            'AdminDeveloperProfileType' => 0,
            'AdminNetworkProfileType' => 1,
            'AdminRegionalProfileType' => 2,
            'AdminLocalProfileType' => 3,
            'ManagerProfileType' => 4,
            'WorkerProfileType' => 5,
            'UserProfileType' => 6,
            'DummyWorkerProfileType' => 998,
            'DummyUserProfileType' => 999,
        ],
        'profileTypesTranslated' =>
            [
            0 => 'Admin. RTI / Desenvolvedor',
            1 => 'Administrador de Rede',
            2 => 'Administrador Regional',
            3 => 'Administrador',
            4 => 'Gerente',
            5 => 'Funcionário',
            6 => 'Usuário',
            998 => 'Mobile API',
            999 => 'Usuário de Venda Avulsa',
        ],
        'profileTypesTranslatedDevel' =>
            [
            0 => 'Admin. RTI / Desenvolvedor',
            1 => 'Administrador de Rede',
            2 => 'Administrador Regional',
            3 => 'Administrador',
            4 => 'Gerente',
            5 => 'Funcionário',
            6 => 'Usuário'
        ],
        "profileTypesTranslatedAdminNetwork" =>
            array(
            1 => 'Administrador de Rede',
            2 => 'Administrador Regional',
            3 => 'Administrador',
            4 => 'Gerente',
            5 => 'Funcionário',
            6
                => 'Usuário'
        ),
        "profileTypesTranslatedAdminToWorker" =>
            array(
            1 => 'Administrador de Rede',
            2 => 'Administrador Regional',
            3 => 'Administrador',
            4 => 'Gerente',
            5 => 'Funcionário'
        ),
        "profileTypesWorkersTranslated" =>
            array(
            1 => 'Administrador de Rede',
            2 => 'Administrador Regional',
            3 => 'Administrador',
            4 => 'Gerente',
            5 => 'Funcionário'
        ),
        "serviceTypes" => array(
            "productServiceNetwork" => 0,
            "rti" => 1
        ),
        'stockOperationTypes' =>
            [
            'addType' => 0,
            'sellTypeGift' => 1,
            'sellTypeSale' => 2,
            'returnType' => 3
        ],
        'stockOperationTypesTranslated' =>
            [
            0 => 'Adicionado ao Estoque',
            1 => 'Vendido como Brinde',
            2 => 'Venda normal',
            3 => 'Produto retornado'
        ],
        'showerType' =>
            [
            1 => 'Masculino',
            2 => 'Masculino PNE',
            3 => 'Feminino',
            4 => 'Feminino PNE'
        ],
        'imageUserProfilePath' => 'img/usuarios/fotosPerfil/',
        'imageUserProfilePathTemp' => 'img/tmp/usuarios/fotosPerfil/',
        'imageUserProfilePathRead' => '/img/usuarios/fotosPerfil/',
        'temporaryDocumentUserPath' => 'img/tmp/usuarios/documentos/',
        'documentUserPath' => 'img/usuarios/documentos/',
        'documentUserPathRead' => '/img/usuarios/documentos/',
        'documentReceiptPath' => 'img/recibos/',
        'documentReceiptPathRead' => '/img/recibos/',
        'documentReceiptPathShellRead' => 'img\\recibos\\',
        'imageReceiptPathTemporary' => 'img/tmp/recibos/',
        'imageGiftPath' => 'img/brindes/',
        'imageGiftPathRead' => '/img/brindes/',
        'imageGiftPathTemp' => 'img/tmp/brindes/',
        'imageGiftPathReadTemp' => '/img/tmp/brindes/',
        'imageNetworkPath' => 'img/redes/',
        'imageNetworkPathRead' => '/img/redes/',
        'imageNetworkPathTemp' => 'img/tmp/redes/',
        'imageNetworkPathReadTemp' => '/img/tmp/redes/',
        'imageClientPath' => 'img/clientes/',
        'imageClientPathRead' => '/img/clientes/',
        'imageClientPathTemp' => 'img/tmp/clientes/',
        'imageClientPathReadTemp' => '/img/tmp/clientes/',

        // MESSAGES
        'callSupport' => 'Entre em contato com o suporte.',
        'callNetworkAdministrator' => 'Entre em contato com o seu Administrador de sua rede.',
        'messageApprovedSuccess' => 'O registro foi autorizado com sucesso.',
        'messageApprovedFailure' => 'Houve um erro ao autorizar o registro.',
        "messageCNPJInvalid" => "CNPJ Inválido!",
        'messageQuestionAllowGiftPrice' => 'Autorizar o preço para o brinde {0} ?',
        'messageQuestionDenyGiftPrice' => 'Negar o preço para o brinde {0} ?',
        'messageAllowGiftPrice' => 'O preço foi autorizado.',
        "messageNoGiftFoundNetwork" => "Não foi encontrado Brindes para a sua Rede!",
        "messageQueryNoDataToReturn" => "A consulta não retornou dados!",
        "messageQueryPaginationEnd" => "Fim de paginação!",
        "messagePointOfServiceCNPJNotEqual" => "CNPJ apresentado na nota não confere com o CNPJ do estabelecimento!",
        "messageCouponImportSuccess" => "Dados do cupom importados com sucesso!",
        "messageNotPossibleToImportCoupon" => "Não foi possível importar o cupom!",
        "messageNotPossibleToImportCouponAwaitingProcessing" => "A Importação de dados não pode ser concluída no momento, pois há uma falha de comunicação. Mas não se preocupe, assim que tudo estiver certo os dados irão aparecer em seu cadastro!",

        'messageDateRangeInvalid' => 'A data de início deve ser menor que a Data de fim!',
        'messageDateTodayHigherInvalid' => "A {0} não pode ser maior que a data de Hoje!",
        'messageDenyErrorPrivileges'
            => `Este registro só pode ser modificado por um Administrador. Você não possui este nível de acesso.`,
        'messageDenyGiftPrice' => 'O preço foi negado.',
        'messageDisableError' => 'Não foi possível desabilitar o registro!',
        'messageDisableQuestion' => 'Deseja realmente desabilitar o registro {0} ?',
        'messageDisableSuccess' => 'O registro foi desabilitado com sucesso!',
        'messageDisableAccessUserError' => 'Não foi possível desabilitar o acesso do usuário.',
        'messageDisableAccessUserQuestion' => 'Deseja realmente desabilitar o acesso do usuário {0} ?',
        'messageDeleteError' => 'Não foi possível apagar o registro!',
        'messageDeleteQuestion' => 'Deseja realmente apagar o registro {0} ?',
        'messageDeleteSuccess' => 'O registro foi removido com sucesso!',
        'messageDeleteMainCompanyDeny' => 'Não é possível remover esta unidade, ela é a matriz da rede.',

        'messageEmailInvalid' => "Email inválido! {0}",
        'messageEmailNotFound' => "Cadastro com endereço de e-mail não encontrado. Confira se o e-mail está correto.",

        'messageEnableError' => 'Não foi possível habilitar o registro!',
        'messageEnableQuestion' => 'Deseja realmente habilitar o registro {0} ?',
        'messageEnableSuccess' => 'O registro foi habilitado com sucesso!',
        'messageEnableAccessUserError' => 'Não foi possível habilitar o acesso do usuário!',
        'messageEnableAccessUserQuestion' => 'Deseja realmente habilitar o acesso do usuário {0} ?',

        'messageGenericCompletedSuccess' => "A operação foi concluída com sucesso!",
        'messageGenericCompletedError' => "Não foi possível realizar a operação!",
        'messageGenericError' => "Houve um erro!",
        'messageGenericCheckFields' => "Verifique se todos os campos estão preenchidos!",

        'messageLoadDataWithSuccess' => "Dados carregados com sucesso!",
        'messageLoadDataWithError' => "Erro durante carregamento dos dados!",
        'messageLoadDataNotFound' => "A consulta não retornou dados!",

        'messageInvalidateSuccess' => 'O registro foi invalidado com sucesso',
        'messageInvalidateError' => 'Não foi possível invalidar o registro!',
        'messageValidateSuccess' => 'O registro foi validado com sucesso',
        'messageValidateError' => 'Não foi possível validar o registro!',
        'messageNotAuthorized' => 'Você não possui autorização para acessar tal operação!',
        "messageRedeemCouponCNPJNotFound" => "Não foi localizado o CNPJ da unidade na Nota Fiscal Eletrônica, logo, não é possível importar os dados...",
        'messageRedeemCouponError' => 'Não foi possível resgatar o cupom.',
        'messageRedeemCouponNotFound' => 'Cupom não encontrado!',
        'messageRedeemCouponRedeemed' => 'Cupom resgatado!',
        'messageRecordAlreadyLinked' => 'Registro já vinculado, não é possível adicionar novo registro!',
        'messageRecordExists' => 'Registro já existente!',
        'messageRecordExistsSameCharacteristics' => 'Registro já existente com as mesmas características! Não é permitido gravar com estas condições!',
        'messageRecordNotFound' => 'Registro não encontrado!',
        'messageSavedError' => 'Não foi possível gravar o registro!',
        'messageSavedSuccess' => 'O registro foi gravado com sucesso!',
        'messageTransporterAlreadyLinked' => 'Transportadora já vinculada, não é possível adicionar novo registro ao usuário!',
        'messageUnlinkQuestion' => 'Deseja realmente desvincular o registro {0} ?',
        'messageUnlinkSuccess' => 'O registro foi desvincunlado com sucesso.',

        "messageUsuarioDoesNotAcquiredPoints" => "Você ainda não adquiriu pontos em nenhum Posto Credenciado ao Sistema!",

        "messageProcessingCompleted" => "Processamento realizado com sucesso!",
        "messageOperationFailureDuringProcessing" => "Erro! Não foi possível concluir a operação devido os seguintes erros:",

        "messageErrorDefault" => "Erro!",
        "messageWarningDefault" => "Atenção!",

        "messageFieldEmptyDefault" => "O Campo {0} deve ser informado!",
        "messageFieldDigitsMinimum" => "O Campo {0} deve ter {1} dígitos!",

        /**
         * ------------------------------------------ ENTIDADES ------------------------------------------
         */

        // ClientesHasBrindesHabilitados

        "messageBrindeBarcodeNotConfigured" => "Para que o Brinde seja utilizado em seu posto, é necessário definir o Código de Barras!",

        "messageClienteDoesNotHaveBrinde" => "O posto/loja selecionado(a) não possui o brinde desejado!",

        // Clientes

        'messageRecordClienteNotFound' => 'Cliente não encontrado!',

        'messageClienteNotFoundByCupomFiscal' => "Esta Nota Fiscal é de uma unidade que ainda não está cadastrada no sistema, sendo assim, não será possível realizar a importação de dados. Procure o gerente do Posto para maiores detalhes. Cupom Fiscal Informado: {0}",



        // Gotas

        "messageGotasCouponDoesNotContainPointOfService" => "O cupom informado não possui as Gotas que o cliente oferece como pontos de milhagem.",
        "messageGotasPointOfServiceNotConfigured" => "O estabelecimento ainda não configurou a(s) Gota(s). As Gotas de seu Cupom serão creditadas quando o estabelecimento efetuar a configuração!",

        // Mensagens de Pontuações
        "messageUsuarioNoPointsInNetwork" => "Usuário não possui pontuações na Rede informada!",

        // Mensagens de Redes"

        "messageRedesIdNotFound" => "O Campo Redes Id deve ser informado",

        // Mensagens de Tipos de Brindes de Redes


        // Mensagens de Usuários
        'messageUsuarioRegistrationClienteNotNull' => 'Se o usuário não for Administrador de Rede, ele deverá ser alocado em uma Unidade da Rede!',

        "messageUsuarioCPFNotValidInvalidSize" => "Tamanho do CPF errado, esperado 11 dígitos! {0}",

        "messageUsuarioCPFNotValidInvalidNumber" => "CPF Informado não é válido! {0}",
        "messageUsuarioProfileDocumentNotFoundError" => "Atenção! Para usar o sistema, é necessário ter um CPF ou Documento Estrangeiro cadastrado! Complete seu perfil.",
        "messageUsuarioSenhaDoesntMatch" => "Senha não confere!",
        "messageUsuarioSenhaInvalid" => "Senha inválida!",

        "messageUsuarioLoginPasswordIncorrect" => "Usuário ou senha incorreto!",
        'messageUsuarioLoggedInSuccessfully' => "Usuário logado com sucesso!",
        'messageUsuarioLoggedOutSuccessfully' => "Usuário encerrou sessão com sucesso!",

        "userNotAllowedToExecuteFunction" => "Usuário logado não possui permissão para acessar esta funcionalidade!",

        // Mensagens de Veículos
        'messageVeiculoAlreadyLinked' => 'Veículo já vinculado, não é possível adicionar novo registro ao usuário!',
        "messageVeiculoIdEmpty" => "O Campo ID do Veículo deve ser informado!",
        "messageVeiculoPlateLength" => "O Campo Placa deve ter 7 dígitos para realizar a pesquisa!",


        // Mensagens de Usuários Has Veículos
        "messageVeiculoDoesntBelongToUserOnUpdate" => "Não é possível atualizar o cadastro, usuário não possui este veículo em seu cadastro!",

        // Mensagem de Transportadoras

        "messageTransportadoraNotFound" => "Transportadora não encontrada conforme parâmetros informados!",

        // Mensagem de TransportadorasHasUsuarios
        "messageUsuarioAlreadyHaveTransportadora" => "Você já possui esta Transportadora em seu cadastro!",


        'yesNoArray' => [
            1 => "Sim",
            0 => "Não"
        ]
    ]
);
