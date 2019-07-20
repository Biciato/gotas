<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

// You can remove this if you are confident that your PHP version is sufficient.
if (version_compare(PHP_VERSION, '5.6.0') < 0) {
    trigger_error('Your PHP version must be equal or higher than 5.6.0 to use CakePHP.', E_USER_ERROR);
}

/*
 *  You can remove this if you are confident you have intl installed.
 */
if (!extension_loaded('intl')) {
    trigger_error('You must enable the intl extension to use CakePHP.', E_USER_ERROR);
}

/*
 * You can remove this if you are confident you have mbstring installed.
 */
if (!extension_loaded('mbstring')) {
    trigger_error('You must enable the mbstring extension to use CakePHP.', E_USER_ERROR);
}

/*
 * Configure paths required to find CakePHP + general filepath
 * constants
 */
require __DIR__ . '/paths.php';

// Carrega todas as configurações de variáveis globais
require __DIR__ . '/global_vars.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorHandler;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Network\Request;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Cake\I18n\Time;
use Cake\I18n\FrozenTime;
use Cake\Chronos\Date;
use Cake\I18n\FrozenDate;

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */

try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

/*
 * Load an environment local configuration file.
 * You can use a file like app_local.php to provide local overrides to your
 * shared configuration.
 */
//Configure::load('app_local', 'default');

/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
}

/*
 * Set server timezone to UTC. You can change it to another timezone of your
 * choice but using UTC makes time calculations / conversions easier.
 */
// date_default_timezone_set('UTC');
date_default_timezone_set('America/Sao_Paulo');

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
Email::setConfigTransport(Configure::consume('EmailTransport'));
Email::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::salt(Configure::consume('Security.salt'));

/*
 * The default crypto extension in 3.0 is OpenSSL.
 * If you are migrating from 2.x uncomment this code to
 * use a more compatible Mcrypt based implementation
 */
//Security::engine(new \Cake\Utility\Crypto\Mcrypt());

/*
 * Setup detectors for mobile and tablet.
 */
Request::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
Request::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});

/*
 * Enable immutable time objects in the ORM.
 *
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link http://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
Type::build('time')
    ->useImmutable();
Type::build('date')
    ->useImmutable();
Type::build('datetime')
    ->useImmutable();
Type::build('timestamp')
    ->useImmutable();

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);

/*
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on Plugin to use more
 * advanced ways of loading plugins
 *
 * Plugin::loadAll(); // Loads all plugins at once
 * Plugin::load('Migrations'); //Loads a single plugin named Migrations
 *
 */

Plugin::load('TwitterBootstrap');

/*
 * Only try to load DebugKit in development mode
 * Debug Kit should not be installed on a production system
 */
// if (Configure::read('debug')) {
//     Plugin::load('DebugKit', ['bootstrap' => true]);
// }

Plugin::load('ADmad/JwtAuth');

Plugin::load('ADmad/SocialAuth', ['bootstrap' => true, 'routes' => true]);

// Carrega arquivo de constantes específico por estação
require_once("definitions.php");

Cake\I18n\Date::setToStringFormat('yyyy-MM-dd');
Cake\I18n\FrozenDate::setToStringFormat('yyyy-MM-dd');

\Cake\Database\Type::build('date')
    ->useImmutable()
    ->useLocaleParser()
    ->setLocaleFormat('yyyy-MM-dd');

Time::setToStringFormat('HH:ii:ss'); // For any mutable DateTime
FrozenTime::setToStringFormat('yyyy-MM-dd HH:mm:ss'); // For any immutable DateTime
// Date::setToStringFormat(\IntlDateFormatter::SHORT); // For any mutable Date
// FrozenDate::setToStringFormat(\IntlDateFormatter::MEDIUM); // For any immutable Date

// Constantes

const DATA_TYPE_MESSAGE_JSON = "json";
const DATA_TYPE_MESSAGE_XML = "xml";

#region Relatórios

const REPORT_TYPE_ANALYTICAL = "Analítico";
const REPORT_TYPE_SYNTHETIC = "Sintético";

#endregion

// status de Job

const JOB_STATUS_INIT = "Inicializando...";
const JOB_STATUS_END = "Finalizando...";



#region Caminhos

const PATH_IMAGES_READ_BRINDES = "/img/brindes/";
const PATH_IMAGES_BRINDES = "img/brindes/";
const PATH_IMAGES_CLIENTES = "img/clientes/";
const PATH_IMAGES_READ_REDES = "/img/redes/";
const PATH_IMAGES_REDES = "img/redes/";
const PATH_WEBROOT = "webroot/";
const PATH_IMAGES_BRINDES_TEMP = "img/tmp/brindes";
const PATH_IMAGES_CLIENTES_TEMP = "img/tmp/clientes/";
const PATH_IMAGES_REDES_TEMP = "img/tmp/redes/";
const PATH_IMAGES_USUARIOS_TEMP = "img/tmp/usuarios";

#endregion

#region Mensagens

#region Comuns ao Sistema

const MESSAGE_QUERY_DOES_NOT_CONTAIN_DATA = "A consulta não retornou dados!";



#endregion

#region Entidades

// Genérica

const LOGIN_API = "API";
const LOGIN_WEB = "WEB";

const MESSAGE_LOAD_DATA_WITH_SUCCESS = "Dados carregados com sucesso!";
const MESSAGE_LOAD_DATA_WITH_ERROR = "Erro durante carregamento dos dados!";
const MESSAGE_LOAD_DATA_NOT_FOUND = "A consulta não retornou dados!";
const MESSAGE_LOAD_EXCEPTION = "Exceção ao obter dados!";
const MESSAGE_SAVED_EXCEPTION = "Exceção ao salvar dados!";
const MESSAGE_DELETE_EXCEPTION = "Exceção ao remover dados!";
const MESSAGE_GENERIC_EXCEPTION = "Exceção ao processar!";

const MESSAGE_RECORD_NOT_FOUND = "Registro não encontrado!";
const MESSAGE_ID_EMPTY = "Campo ID não informado!";
const TYPE_BARCODE_QRCODE = "QRCode";
const TYPE_BARCODE_CODE128 = 'Code128';
const TYPE_BARCODE_PDF417 = 'PDF417';

const FILTER_TYPE_DATE_TIME = "Data/Hora";
const FILTER_TYPE_SHIFT = "Turno";


const STRING_YES = 'Sim';
const STRING_NO = 'Não';


// Brindes

const MESSAGE_CLIENTE_DOES_NOT_HAVE_BRINDE = "O posto/loja selecionado(a) não possui o brinde desejado!";
const MESSAGE_BRINDES_CLIENTES_ID_EMPTY = "Campo CLIENTES_ID não informado!";
const MESSAGE_BRINDES_CLIENTES_ID_REQUIRED = "Necessário informar o Posto de Atendimento!";
const MESSAGE_BRINDES_TYPE_EQUIPMENT_INCORRECT = "Campo TIPO_EQUIPAMENTO incorreto!";
const MESSAGE_BRINDES_TYPE_EQUIPMENT_EMPTY = "Campo TIPO_EQUIPAMENTO não informado!";
const MESSAGE_BRINDES_TYPE_EQUIPMENT_RTI_PRIMARY_CODE_EMPTY = "Se Equipamento for RTI, é necessário informar o Código Primário";
const TYPE_EQUIPMENT_RTI = "Equipamento RTI";
const TYPE_EQUIPMENT_PRODUCT_SERVICES = "Produtos/Serviços";
const STATUS_AUTHORIZATION_PRICE_AWAITING = "Aguardando";
const STATUS_AUTHORIZATION_PRICE_AUTHORIZED = "Autorizado";
const STATUS_AUTHORIZATION_PRICE_DENIED = "Negado";

// Brindes Estoque
const MESSAGE_BRINDES_ESTOQUE_QUANTITY_EMPTY = "Campo QUANTIDADE não informado!";

const TYPE_OPERATION_INITIALIZE = "Criação";
const TYPE_OPERATION_ADD_STOCK = "Adicionado ao Estoque";
const TYPE_OPERATION_SELL_BRINDE = "Saída Brinde";
const TYPE_OPERATION_SELL_CURRENCY = "Saída Venda";
const TYPE_OPERATION_RETURN = "Retornado";


const TYPE_SELL_FREE = 0;
const TYPE_SELL_FREE_TEXT = "Isento";
const TYPE_SELL_DISCOUNT = 1;
const TYPE_SELL_DISCOUNT_TEXT = "Com Desconto";
const TYPE_SELL_CURRENCY_OR_POINTS = 2;
const TYPE_SELL_CURRENCY_OR_POINTS_TEXT = "Gotas ou Reais";
const TYPE_SELL_EMPTY = "Campo TIPO VENDA não informado!";

// Clientes

const RULE_CLIENTES_NEED_TO_INFORM = "É necessário especificar o Estabelecimento à gerenciar!";

const MESSAGE_CNPJ_EMPTY = "Campo CNPJ não informado!";
const MESSAGE_CNPJ_NOT_REGISTERED_ON_SYSTEM = "CNPJ não cadastrado no sistema Web!";
const MESSAGE_ESTABLISHMENT_WITHOUT_TIME_SHIFTS = "Estabelecimento não possui quadro de horários, não será possível realizar a impressão dos dados emitidos aos clientes!";

// Clientes Has Brindes Habilitados Estoque
const STOCK_OPERATION_TYPES_ADD_TYPE = 0;
const STOCK_OPERATION_TYPES_SELL_TYPE_GIFT = 1;
const STOCK_OPERATION_TYPES_SELL_TYPE_SALE = 2;
const STOCK_OPERATION_TYPES_RETURN_TYPE = 3;

// Cupom

// Máximo de intervalo de horas ao gerar relatório de caixa de funcionários
const MAX_TIME_COUPONS_REPORT_TIME = 16;
// Tempo padrão caso funcionário gerar o relatório completo de caixa de funcionários
const DEFAULT_TIME_COUPONS_REPORT_TIME = 16;
const MESSAGE_WARNING_GENERATE_REPORT = "Relatório Parcial de Caixa do Funcionário, não vale como Relatório Oficial!";

const MESSAGE_CUPOM_ALREADY_RETRIEVED = "Cupom já foi resgatado, não é possível novo resgate!";
const MESSAGE_CUPOM_ALREADY_USED = "Cupom já foi validado, não é possível novo uso!";
const MESSAGE_CUPOM_EMPTY = "Campo QRCODE (Cupom Fiscal ECF) deve ser informado!";
const MESSAGE_CUPOM_MISMATCH_FORMAT = "Campo QRCODE (Cupom Fiscal ECF) com formato inválido!";
const MESSAGE_CUPOM_PRINTED_EMPTY = "Necessário informar o CUPOM!";
const MESSAGE_CUPOM_PRINTED_DOES_NOT_EXIST = "Cupom não existe no sistema!";
const MESSAGE_CUPOM_PRINTED_CANNOT_BE_CANCELLED = "O cupom informado não pode ser cancelado!";
const MESSAGE_CUPOM_ANOTHER_NETWORK = "O cupom informado pertence a outra rede!";
const MESSAGE_CUPOM_PRINTED_ALREADY_CANCELLED = "O cupom informado já está cancelado no sistema!";
const MESSAGE_CUPOM_PRINTED_CANCELLED = "O cupom informado foi cancelado com sucesso!";
const MESSAGE_CUPOM_TYPE_PAYMENT_REQUIRED = "O campo TIPO DE PAGAMENTO deve ser informado!";
const MESSAGE_REDEEM_COUPON_REDEEMED = "Cupom resgatado!";
const MESSAGE_REDEEM_COUPON_USED = "Cupom usado!";

const TYPE_OPERATION_RETRIEVE = 'Resgate';
const TYPE_OPERATION_USE = 'Uso';
const TYPE_OPERATION_RETRIEVED = 'Resgatado';
const TYPE_OPERATION_USED = 'Usado';

const TYPE_PAYMENT_POINTS = "Gotas";
const TYPE_PAYMENT_MONEY = "Dinheiro";


// Pontuações

const PONTUACOES_TYPE_OPERATION_IN = 'Entrada';
const PONTUACOES_TYPE_OPERATION_OUT = 'Saída';
const MESSAGE_QR_CODE_EMPTY = "O Campo QR_CODE deve ser informado!";

#region Pontuacoes Comprovantes

const MESSAGE_PONTUACOES_COMPROVANTES_USUARIOS_ID_EMPTY = "O campo USUARIOS_ID deve ser informado!";

#endregion

// Redes

const MESSAGE_REDES_ID_EMPTY = "Campo ID de Rede não informado!";

const MESSAGE_NETWORK_DESACTIVATED = "Rede desativada!";

// Tipos de Brindes de Clientes

const MESSAGE_TYPE_GIFTS_POINT_OF_SERVICE_FOUND = "O estabelecimento selecionado não possui tipo de brinde definido. Defina antes de continuar!";

// Usuários
const MESSAGE_USUARIO_NEW_PASSWORD_DOESNT_MATCH = "Nova senha não confere!";
const MESSAGE_USUARIOS_NOT_AUTHENTICATED = "Usuário não autenticado!";
const MESSAGE_USUARIOS_CPF_EMPTY = "Campo CPF não informado!";
const MESSAGE_USUARIOS_DOC_ESTRANGEIRO_EMPTY = "Campo DOCUMENTO ESTRANGEIRO não informado!";
const MESSAGE_USUARIOS_DOC_ESTRANGEIRO_SEARCH_EMPTY = "Por favor informe corretamente o Documento de Identificação Estrangeira!";
const MESSAGE_USUARIOS_DOC_ESTRANGEIRO_ALREADY_EXISTS = "Já existe um cadastro com este documento estrangeiro, informe um outro documento!";
const MESSAGE_USUARIOS_EMAIL_EMPTY = "Campo EMAIL não informado!";
const MESSAGE_USUARIO_LOGGED_IN_SUCCESSFULLY = "Usuário logado com sucesso!";
const MESSAGE_USUARIO_LOGIN_PASSWORD_INCORRECT = "Usuário ou senha incorreto!";
const MESSAGE_USUARIO_PASSWORD_LENGTH = "Tamanho da senha deve ser de %s dígitos!";
const MESSAGE_USUARIO_PASSWORD_UPDATE_ERROR = "Alteração de senha não foi realizada, confira se a senha e nova senha conferem!";
const MESSAGE_USUARIO_PASSWORD_UPDATED = "Alteração de senha realizada com sucesso!";
const MESSAGE_USUARIO_PASSWORD_INCORRECT = "Senha do usuário incorreta! Tente novamente!";
const MESSAGE_USUARIO_PROFILE_ON_DATE = "Perfil está atualizado!";
const MESSAGE_USUARIO_PROFILE_OUT_DATE = "Perfil está desatualizado! Verifique seu cadastro!";
const MESSAGE_USUARIO_CANT_SEARCH = "Este serviço só está disponível para funcionários de Posto!";
const MESSAGE_USUARIO_WORKER_NOT_ASSOCIATED_CLIENTE = "Este funcionário não está associado à nenhum Posto do Sistema!";

const MESSAGE_CPF_LENGTH_INVALID = "Tamanho do CPF inválido!";
const CPF_LENGTH = 11;


// Mensagens de Suporte
const MESSAGE_CONTACT_SUPPORT = "Entre em contato com o suporte.";

// Mensagens de Questionamento e Avisos
const MESSAGE_ENABLE_QUESTION = 'Deseja realmente habilitar o registro %s ?';
const MESSAGE_DISABLE_QUESTION = 'Deseja realmente desabilitar o registro %s ?';
const MESSAGE_DELETE_ERROR = 'Não foi possível apagar o registro!';
const MESSAGE_DELETE_QUESTION = 'Deseja realmente apagar o registro %s ?';
const MESSAGE_DELETE_SUCCESS = 'O registro foi removido com sucesso!';
const MESSAGE_SAVED_ERROR = "Exceção ao Salvar!";
const MESSAGE_SAVED_SUCCESS = "O registro foi gravado com sucesso!";

// Mensagens de processamento
const MESSAGE_PROCESSING_COMPLETED = "Processamento realizado com sucesso!";
const MESSAGE_OPERATION_FAILURE_DURING_PROCESSING = "Erro! Não foi possível concluir a operação devido os seguintes erros:";

// Títulos de mensagem

const MESSAGE_GENERIC_ERROR = "Houve um erro!";
const MESSAGE_GENERIC_COMPLETED_ERROR = "Não foi possível realizar a operação!";
const MESSAGE_GENERIC_CHECK_FIELDS = "Verifique se todos os campos estão preenchidos!";
#endregion

#endregion

// Tipos de Perfil de usuários

const PROFILE_TYPE_ADMIN_DEVELOPER = 0;
const PROFILE_TYPE_ADMIN_NETWORK = 1;
const PROFILE_TYPE_ADMIN_REGIONAL = 2;
const PROFILE_TYPE_ADMIN_LOCAL = 3;
const PROFILE_TYPE_MANAGER = 4;
const PROFILE_TYPE_WORKER = 5;
const PROFILE_TYPE_USER = 6;
const PROFILE_TYPE_DUMMY_WORKER = 998;
const PROFILE_TYPE_DUMMY_USER = 999;

// Tipos de Perfil de Usuários (Tradução)
const PROFILE_TYPE_ADMIN_DEVELOPER_TRANSLATE = "Administrador RTI / Desenvolvedor";
const PROFILE_TYPE_ADMIN_NETWORK_TRANSLATE = "Administrador da Rede";
const PROFILE_TYPE_ADMIN_REGIONAL_TRANSLATE = "Administrador Regional";
const PROFILE_TYPE_ADMIN_LOCAL_TRANSLATE = "Administrador";
const PROFILE_TYPE_MANAGER_TRANSLATE = "Gerente";
const PROFILE_TYPE_WORKER_TRANSLATE = "Funcionário";
const PROFILE_TYPE_USER_TRANSLATE = "Usuário";
const PROFILE_TYPE_DUMMY_WORKER_TRANSLATE = "Funcionário Fictício";
const PROFILE_TYPE_DUMMY_USER_TRANSLATE = "Usuário Fictítio";
