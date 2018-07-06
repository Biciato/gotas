<?php

/**
 *
 */

use Cake\Core\Configure;

// const SITE_ADDRESS = 'https://40.71.26.138/',
// const SITE_ADDRESS = 'https://www.rtibrindes.com.br/',
// const SITE_ADDRESS = "https://www.rtibrindes.local/";
// const WEBROOT_ADDRESS = self::SITE_ADDRESS + "webroot";

Configure::write(
    [
        // 'appAddress' => 'https://40.71.26.138/',
        // 'appAddress' => 'https://www.rtibrindes.com.br/',
        'appAddress' => "https://www.rtibrindes.local/",
        "webrootAddress" => "https://www.rtibrindes.local/webroot",

                // Gotas

        'dropletsUsageStatus' =>
            [
            'NotUsed' => 0,
            'ParcialUsed' => 1,
            'FullyUsed' => 2
        ],
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
            "Cupons",
            "GeneroBrindes",
            "GeneroBrindesClientes",
            "Gotas",
            "Pontuacoes",
            "PontuacoesComprovantes",
            "PontuacoesPendentes",
            "Redes",
            "RedesHasClientes",
            "RedesHasClientesAdministradores",
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
        'temporaryDocumentUserPath' => 'img/tmp/users/documents/',
        'documentUserPath' => 'img/users/documents/',
        'documentUserPathRead' => '/img/users/documents/',
        'temporaryReceiptPath' => 'img/tmp/receipts/',
        'imageGiftPath' => 'img/gifts/images/',
        'imageGiftPathRead' => '/img/gifts/images/',
        'imageGiftPathTemp' => 'img/tmp/gifts/images/',
        'imageGiftPathReadTemp' => '/img/tmp/gifts/images/',
        'imageNetworkPath' => 'img/networks/images/',
        'imageNetworkPathRead' => '/img/networks/images/',
        'imageNetworkPathTemp' => 'img/tmp/networks/images/',
        'imageNetworkPathReadTemp' => '/img/tmp/networks/images/',
        'documentReceiptPath' => 'img/receipts/documents/',
        'documentReceiptPathRead' => '/img/receipts/documents/',
        'documentReceiptPathShellRead' => 'img\\receipts\\documents\\',

        // MESSAGES
        'callSupport' => 'Entre em contato com o suporte.',
        'messageApprovedSuccess' => 'O registro foi autorizado com sucesso.',
        'messageApprovedFailure' => 'Houve um erro ao autorizar o registro.',
        'messageQuestionAllowGiftPrice' => 'Autorizar o preço para o brinde {0} ?',
        'messageQuestionDenyGiftPrice' => 'Negar o preço para o brinde {0} ?',
        'messageAllowGiftPrice' => 'O preço foi autorizado.',
        "messageQueryNoDataToReturn" => "A pesquisa não retornou dados!",
        "messageQueryPaginationEnd" => "Fim de paginação!",

        'messageDateRangeInvalid' => 'A data de início deve ser menor que a Data de fim!',
        'messageDateTodayHigherInvalid' => "A {0} não pode ser maior que a data de Hoje!",
        'messageDenyErrorPrivileges'
            => `Este registro só pode ser modificado por um Administrador. Você não possui este nível de acesso.`,
        'messageDenyGiftPrice' => 'O preço foi negado.',
        'messageDisableError' => 'Não foi possível desabilitar o registro.',
        'messageDisableQuestion' => 'Deseja realmente desabilitar o registro {0} ?',
        'messageDisableSuccess' => 'O registro foi desabilitado com sucesso.',
        'messageDisableAccessUserError' => 'Não foi possível desabilitar o acesso do usuário.',
        'messageDisableAccessUserQuestion' => 'Deseja realmente desabilitar o acesso do usuário {0} ?',
        'messageDeleteError' => 'Não foi possível apagar o registro.',
        'messageDeleteQuestion' => 'Deseja realmente apagar o registro {0} ?',
        'messageDeleteSuccess' => 'O registro foi removido com sucesso.',
        'messageDeleteMainCompanyDeny' => 'Não é possível remover esta unidade, ela é a matriz da rede.',

        'messageEmailInvalid' => "Email inválido! {0}",
        'messageEmailNotFound' => "Cadastro com endereço de e-mail não encontrado. Confira se o e-mail está correto.",

        'messageEnableError' => 'Não foi possível habilitar o registro.',
        'messageEnableQuestion' => 'Deseja realmente habilitar o registro {0} ?',
        'messageEnableSuccess' => 'O registro foi habilitado com sucesso.',
        'messageEnableAccessUserError' => 'Não foi possível habilitar o acesso do usuário.',
        'messageEnableAccessUserQuestion' => 'Deseja realmente habilitar o acesso do usuário {0} ?',

        'messageGenericCompletedSuccess' => "A operação foi concluída com sucesso!",
        'messageGenericCompletedError' => "Não foi possível realizar a operação!",
        'messageGenericCheckFields' => "Verifique se todos os campos estão preenchidos!",

        'messageLoadDataWithSuccess' => "Dados carregados com sucesso!",
        'messageLoadDataWithError' => "Erro durante carregamento dos dados!",

        'messageInvalidateSuccess' => 'O registro foi invalidado com sucesso',
        'messageInvalidateError' => 'Não foi possível invalidar o registro!',
        'messageValidateSuccess' => 'O registro foi validado com sucesso',
        'messageValidateError' => 'Não foi possível validar o registro!',
        'messageNoDataToDisplay' => 'Não há dados à serem exibidos!',
        'messageNotAuthorized' => 'Você não possui autorização para acessar tal operação',
        'messageRedeemCouponError' => 'Não foi possível regatar o cupom.',
        'messageRecordAlreadyLinked' => 'Registro já vinculado, não é possível adicionar novo registro!',
        'messageRecordExists' => 'Registro já existente!',
        'messageRecordExistsSameCharacteristics' => 'Registro já existente com as mesmas características! Não é permitido gravar com estas condições!',
        'messageRecordNotFound' => 'Registro não encontrado!',
        'messageSavedError' => 'Não foi possível gravar o registro.',
        'messageSavedSuccess' => 'O registro foi gravado com sucesso.',
        'messageTransporterAlreadyLinked' => 'Transportadora já vinculada, não é possível adicionar novo registro ao usuário!',
        'messageUnlinkQuestion' => 'Deseja realmente desvincular o registro {0} ?',
        'messageUnlinkSuccess' => 'O registro foi desvincunlado com sucesso.',


        "messageProcessingCompleted" => "Processamento realizado com sucesso!",
        "messageOperationFailureDuringProcessing" => "Erro durante o processamento! Não foi possível concluir a operação devido os seguintes erros:",

        /**
         * ------------------------------------------ ENTIDADES ------------------------------------------
         */

        // Clientes

        'messageRecordClienteNotFound' => 'Cliente não encontrado!',

        'messageClienteNotFoundByCNPJ' => "Esta Nota Fiscal é de uma unidade que ainda não está cadastrada no sistema, sendo assim, não será possível realizar a importação de dados. Procure o gerente do Posto para maiores detalhes. CNPJ Informado: {0}",

        // Mensagens de Usuários
        'messageUserRegistrationClientNotNull' => 'Se o usuário não for Administrador de Rede, ele deverá ser alocado em uma Unidade da Rede!',

        "messageUserCPFNotValidInvalidSize" => "Tamanho do CPF errado, esperado 11 dígitos! {0}",

        "messageUserCPFNotValidInvalidNumber" => "CPF Informado não é válido! {0}",

        'messageUserLoggedInSuccessfully' => "Usuário logado com sucesso!",

        // Mensagens de Veículos
        'messageVehicleAlreadyLinked' => 'Veículo já vinculado, não é possível adicionar novo registro ao usuário!',


        'yesNoArray' => [
            1 => "Sim",
            0 => "Não"
        ]
    ]
);
