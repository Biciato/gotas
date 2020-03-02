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
const STRING_YES = 'Sim';
const STRING_NO = 'Não';
const TYPE_BARCODE_QRCODE = "QRCode";
const TYPE_BARCODE_CODE128 = 'Code128';
const TYPE_BARCODE_PDF417 = 'PDF417';

#region Relatórios

const REPORT_TYPE_ANALYTICAL = "Analítico";
const REPORT_TYPE_SYNTHETIC = "Sintético";

#endregion

#region status de Job

const JOB_STATUS_INIT = "Inicializando...";
const JOB_STATUS_END = "Finalizando...";

#endregion

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

// Erros

const MESSAGE_ERROR_GPS_VALIDATION_CODE = 0x00000008;
const MESSAGE_ERROR_GPS_VALIDATION = "Informações de Localização não obtidas, favor confira se a LOCALIZAÇÃO (GPS) está ativa!";

const MSG_MAX_FILTER_TIME_MONTH = "Período máximo de filtro permitido é %s mês(es)!";
const MSG_MAX_FILTER_TIME_MONTH_CODE = 0x00000016;

const MSG_MAX_FILTER_TIME_ONE_YEAR = "Período máximo de filtro permitido é 1 (UM) ano!";
const MSG_MAX_FILTER_TIME_ONE_YEAR_CODE = 0x0000000D;

const MSG_DATE_BEGIN_GREATER_THAN_DATE_END = "DATA_INICIO não pode ser maior que DATA_FIM";
const MSG_DATE_BEGIN_GREATER_THAN_DATE_END_CODE = 0x0000000E;

const MESSAGE_RECORD_NOT_FOUND_CODE = 0x00000001;
const MESSAGE_RECORD_NOT_FOUND = "Registro não encontrado!";
const MESSAGE_RECORD_DOES_NOT_BELONG_NETWORK_CODE = 0x00000002;
const MESSAGE_RECORD_DOES_NOT_BELONG_NETWORK = 'Este registro não pertence à esta rede! Não é permitido a edição!';
const USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION_CODE = 0x00000003;
const USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION = "Usuário não possui permissão para acessar esta funcionalidade!";

const MSG_REPORT_TYPE_EMPTY = "Campo TIPO_RELATORIO deve ser informado!";
const MSG_REPORT_TYPE_EMPTY_CODE = 0x00000009;

// Sucesso / Aviso

const MESSAGE_QUERY_DOES_NOT_CONTAIN_DATA = "A consulta não retornou dados!";


#endregion

#region Exceções

// Titulos

const MSG_ERROR = "Erro!";
const MSG_WARNING = "Atenção!";

// Erros
const MSG_LOAD_EXCEPTION_CODE = 0x00000004;
const MSG_LOAD_EXCEPTION = "Exceção ao obter dados!";
const MESSAGE_SAVED_EXCEPTION_CODE = 0x00000005;
const MESSAGE_SAVED_EXCEPTION = "Exceção ao salvar dados!";
const MSG_DELETE_EXCEPTION_CODE = 0x00000006;
const MSG_DELETE_EXCEPTION = "Exceção ao remover dados!";
const MESSAGE_GENERIC_EXCEPTION_CODE = 0x00000007;
const MESSAGE_GENERIC_EXCEPTION = "Exceção ao processar!";

const MSG_NOT_POSSIBLE_TO_IMPORT_COUPON_CODE = 0x0000000F;
const MSG_NOT_POSSIBLE_TO_IMPORT_COUPON = "Não foi possível importar o cupom fiscal, SEFAZ inoperante!";
const MSG_NOT_POSSIBLE_TO_IMPORT_COUPON_AWAITING_PROCESSING_CODE = 0x00000010;
const MSG_NOT_POSSIBLE_TO_IMPORT_COUPON_AWAITING_PROCESSING = "A Importação do Cupom Fiscal não pode ser realizada agora, há uma falha de comunicação com a SEFAZ. Mas não se preocupe, assim que tudo estiver certo os pontos serão atribuídos em seu cadastro!";

const MSG_QR_CODE_SEFAZ_MISMATCH_PATTERN_CODE = 0x00000015;
const MSG_QR_CODE_SEFAZ_MISMATCH_PATTERN = "O QR Code informado não está gerado conforme os padrões pré-estabelecidos da SEFAZ, não sendo possível realizar sua importação!";

const MSG_QR_CODE_READING_ERROR_CODE = 0x00000014;
const MSG_QR_CODE_READING_ERROR = "Erro na Leitura do QR Code, tente novamente!";

const MSG_SEFAZ_NO_DATA_FOUND_TO_IMPORT_CODE = 0x00000012;
const MSG_SEFAZ_NO_DATA_FOUND_TO_IMPORT = "No Cupom Fiscal {0} da SEFAZ do estado {1} não há gotas à processar conforme configurações definidas!...";
const MSG_SEFAZ_NOT_RESPONDING_CODE = 0x00000013;
const MSG_SEFAZ_NOT_RESPONDING = "Sistema SEFAZ não está respondendo!";
const MSG_SEFAZ_CONTINGENCY_MODE_CODE = 0x00000011;
const MSG_SEFAZ_CONTINGENCY_MODE = "Sistema SEFAZ operando em modo contingência. Suas pontuações serão atribuídas assim que tudo estiver normalizado.";
const MSG_SEFAZ_CNPJ_NOT_FOUND = "Não há CNPJ cadastrado no sistema para este Cupom Fiscal apresentado!";
const MSG_SEFAZ_CNPJ_NOT_FOUND_CODE = 0x0000001C;
const MSG_SEFAZ_ALL_PRODUCTS_ALREADY_IMPORTED = "Todos os Produtos contidos no Cupom Fiscal já foram adicionados ao sistema!";
const MSG_SEFAZ_ALL_PRODUCTS_ALREADY_IMPORTED_CODE = 0x0000001D;

// Sucesso / Aviso
const MSG_LOAD_DATA_WITH_SUCCESS = "Dados carregados com sucesso!";
const MSG_LOAD_DATA_WITH_ERROR = "Erro durante carregamento dos dados!";
const MSG_LOAD_DATA_NOT_FOUND = "A consulta não retornou dados!";
const MSG_LOAD_DATA_NOT_FOUND_CODE = 0x0000001E;

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
const MSG_PROCESSING_COMPLETED = "Processamento realizado com sucesso!";
const MESSAGE_OPERATION_FAILURE_DURING_PROCESSING = "Erro! Não foi possível concluir a operação devido os seguintes erros:";

// Títulos de mensagem

const MESSAGE_GENERIC_ERROR = "Houve um erro!";
const MESSAGE_GENERIC_COMPLETED_ERROR = "Não foi possível realizar a operação!";
const MESSAGE_GENERIC_CHECK_FIELDS = "Verifique se todos os campos estão preenchidos!";
const MSG_ERROR_GENERIC_AT_LEAST_ONE_FIELD = "Erro! Ao menos um dos seguintes campos devem ser informados!";

#endregion


#region Entidades

#region Genérica

const PATH_IMG_NOT_AVAILABLE = "/webroot/img/icons/not-available.jpg";
const LOGIN_API = "API";
const LOGIN_WEB = "WEB";

const MSG_ID_EMPTY = "Campo ID deve ser informado!";
const MSG_ID_EMPTY_CODE = 0x0000000A;
const MSG_DATE_BEGIN_EMPTY = "Campo DATA_INICIO deve ser informado!";
const MSG_DATE_BEGIN_EMPTY_CODE = 0x0000000B;
const MSG_DATE_END_EMPTY = "Campo DATA_FIM deve ser informado!";
const MSG_DATE_END_EMPTY_CODE = 0x0000000C;
const MSG_USUARIOS_ID_EMPTY_CODE = 0x00000017;
const MSG_USUARIOS_ID_EMPTY = "O campo USUARIOS_ID deve ser informado!";
const MSG_REDES_ID_EMPTY_CODE = 0x00000018;
const MSG_REDES_ID_EMPTY = "O campo REDES_ID deve ser informado!";
const MSG_CLIENTES_ID_NOT_EMPTY_CODE = 0x00000019;
const MSG_CLIENTES_ID_NOT_EMPTY = "O campo CLIENTES_ID deve ser informado!";
const MSG_QRCODE_EMPTY_CODE = 0x0000001A;
const MSG_QRCODE_EMPTY = "Campo QRCODE (Cupom Fiscal ECF) deve ser informado!";
const MSG_QRCODE_MISMATCH_FORMAT_CODE = 0x0000001B;
const MSG_QRCODE_MISMATCH_FORMAT = "Campo QRCODE (Cupom Fiscal ECF) com formato inválido!";

const TIME_EXPIRATION_TOKEN_SECONDS = 31536000;
// const TIME_EXPIRATION_TOKEN_SECONDS = 60;
const TIME_EXPIRATION_TOKEN_MINUTES = 10080;
const TYPE_EXPORTATION_DATA_OBJECT = "Object";
const TYPE_EXPORTATION_DATA_TABLE = "Table";
const TYPE_EXPORTATION_DATA_EXCEL = "Excel";
const TYPE_OPERATION_IN = 'Entrada';
const TYPE_OPERATION_OUT = 'Saída';
const FILTER_TYPE_DATE_TIME = "Data/Hora";
const FILTER_TYPE_SHIFT = "Turno";



#endregion

#region Brindes

const MSG_BRINDES_CLIENTE_DOESNT_OFFER_CODE = 0x00010001;
const MSG_BRINDES_CLIENTE_DOESNT_OFFER = "O posto/loja selecionado(a) não possui o brinde desejado!";
const MSG_BRINDES_TYPE_EQUIPMENT_RTI_PRIMARY_CODE_EMPTY_CODE = 0x00010002;
const MSG_BRINDES_TYPE_EQUIPMENT_RTI_PRIMARY_CODE_EMPTY = "Se Equipamento for RTI, é necessário informar o Código Primário!";
const MSG_BRINDES_CLIENTES_ID_EMPTY_CODE = 0x00010003;
const MSG_BRINDES_CLIENTES_ID_EMPTY = "Campo CLIENTES_ID deve ser informado!";
const MSG_BRINDES_CLIENTES_ID_REQUIRED_CODE = 0x00010004;
const MSG_BRINDES_CLIENTES_ID_REQUIRED = "Necessário informar o Posto de Atendimento!";
const MSG_BRINDES_TYPE_EQUIPMENT_INCORRECT_CODE = 0x00010005;
const MSG_BRINDES_TYPE_EQUIPMENT_INCORRECT = "Campo TIPO_EQUIPAMENTO incorreto!";
const MSG_BRINDES_TYPE_EQUIPMENT_EMPTY_CODE = 0x00010006;
const MSG_BRINDES_TYPE_EQUIPMENT_EMPTY = "Campo TIPO_EQUIPAMENTO deve ser informado!";
const MSG_BRINDES_CONFIRM_PURCHASE_CODE = 0x00010007;
const MSG_BRINDES_CONFIRM_PURCHASE = "Deseja confirmar o resgate dos brindes à seguir?";
const MSG_BRINDES_ID_EMPTY = "Campo BRINDES_ID deve ser informado!";
const MSG_BRINDES_ID_EMPTY_CODE = 0x00010008;

const STATUS_AUTHORIZATION_PRICE_AWAITING = "Aguardando";
const STATUS_AUTHORIZATION_PRICE_AUTHORIZED = "Autorizado";
const STATUS_AUTHORIZATION_PRICE_DENIED = "Negado";
const TYPE_EQUIPMENT_RTI = "Equipamento RTI";
const TYPE_EQUIPMENT_PRODUCT_SERVICES = "Produtos/Serviços";

#endregion

#region Brindes Estoque

const MSG_BRINDES_ESTOQUE_QUANTITY_EMPTY = "Campo QUANTIDADE deve ser informado!";
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
const TYPE_SELL_EMPTY = "Campo TIPO VENDA deve ser informado!";

const MSG_BRINDES_ESTOQUE_INSUFFICIENT_STOCK = "Estoque insuficiente para brinde solicitado no sistema!";
const MSG_BRINDES_ESTOQUE_INSUFFICIENT_STOCK_ERROR = 0x00020001;

#endregion

#region Categorias Brindes

const MSG_CATEGORIAS_BRINDES_ID_EMPTY_CODE = 0x00040001;
const MSG_CATEGORIAS_BRINDES_ID_EMPTY = "Campo ID deve ser informado!";
const MSG_CATEGORIAS_BRINDES_HABILITADO_EMPTY_CODE = 0x00040002;
const MSG_CATEGORIAS_BRINDES_HABILITADO_EMPTY = "Campo HABILITADO deve ser informado!";
const MSG_CATEGORIAS_BRINDES_NOME_EMPTY_CODE = 0x00040003;
const MSG_CATEGORIAS_BRINDES_NOME_EMPTY = "Campo NOME deve ser informado!";
const MSG_CATEGORIAS_BRINDES_REDES_ID_EMPTY_CODE = 0x00040004;
const MSG_CATEGORIAS_BRINDES_REDES_ID_EMPTY = "Campo REDES_ID deve ser informado!";

#endregion

#region Clientes

const RULE_CLIENTES_NEED_TO_INFORM = "É necessário especificar o Estabelecimento à gerenciar!";
const MSG_CLIENTES_FILTER_REQUIRED = "Necessário selecionar um estabelecimento para filtrar!";
const MSG_CLIENTES_FILTER_REQUIRED_CODE = 0x00050001;

const MESSAGE_CNPJ_EMPTY = "Campo CNPJ deve ser informado!";
const MESSAGE_CNPJ_NOT_REGISTERED_ON_SYSTEM = "CNPJ não cadastrado no sistema Web!";
const MESSAGE_ESTABLISHMENT_WITHOUT_TIME_SHIFTS = "Estabelecimento não possui quadro de horários, não será possível realizar a impressão dos dados emitidos aos clientes!";

const MSG_CLIENTES_MATRIZ_NOT_FOUND = "Matriz não encontrada para Rede!";
const MSG_CLIENTES_MATRIZ_NOT_FOUND_CODE = 0x00050002;

// Clientes Has Brindes Habilitados Estoque
const STOCK_OPERATION_TYPES_ADD_TYPE = 0;
const STOCK_OPERATION_TYPES_SELL_TYPE_GIFT = 1;
const STOCK_OPERATION_TYPES_SELL_TYPE_SALE = 2;
const STOCK_OPERATION_TYPES_RETURN_TYPE = 3;

#endregion

#region Cupons

// Erros
const MSG_CUPONS_ALREADY_RETRIEVED_CODE = 0x00080001;
const MSG_CUPONS_ALREADY_RETRIEVED = "Cupom já resgatado!";
const MSG_CUPONS_ALREADY_USED_CODE = 0x00080002;
const MSG_CUPONS_ALREADY_USED = "Cupom já utilizado!";
const MSG_CUPONS_ANOTHER_NETWORK_CODE = 0x00080003;
const MSG_CUPONS_ANOTHER_NETWORK = "Cupom pertence a outra rede!";
const MSG_CUPONS_ANOTHER_STATION_CODE = 0x0008000A;
const MSG_CUPONS_ANOTHER_STATION = "Brindes deste Cupom pertencem a outra unidade de atendimento, não será possível resgatar!";
const MSG_CUPONS_NOT_FOUND_CODE = 0x00080004;
const MSG_CUPONS_NOT_FOUND = "Cupom não encontrado!";
const MSG_CUPONS_PRINTED_EMPTY_CODE = 0x00080005;
const MSG_CUPONS_PRINTED_EMPTY = "Necessário informar o cupom!";
const MSG_CUPONS_PRINTED_CANNOT_BE_CANCELLED_CODE = 0x00080006;
const MSG_CUPONS_PRINTED_CANNOT_BE_CANCELLED = "O cupom informado não pode ser cancelado!";
const MSG_CUPONS_PRINTED_ALREADY_CANCELLED_CODE = 0x00080007;
const MSG_CUPONS_PRINTED_ALREADY_CANCELLED = "O cupom já está cancelado!";
const MSG_CUPONS_TYPE_PAYMENT_REQUIRED_CODE = 0x00080008;
const MSG_CUPONS_TYPE_PAYMENT_REQUIRED = "O campo TIPO DE PAGAMENTO deve ser informado!";


// Sucesso / Aviso

const MSG_CUPONS_PRINTED_CANCELLED = "O cupom informado foi cancelado com sucesso!";
const MSG_CUPONS_REDEEMED = "Cupom resgatado!";
const MSG_CUPONS_USED = "Cupom usado!";


// Entidade / Definições

const MSG_CUPONS_CUPOM_EMITIDO_EMPTY_CODE = 0x00080009;
const MSG_CUPONS_CUPOM_EMITIDO_EMPTY = "O Campo CUPOM_EMITIDO deve ser informado!";

// Máximo de intervalo de horas ao gerar relatório de caixa de funcionários
const MAX_TIME_COUPONS_REPORT_TIME = 16;
// Tempo padrão caso funcionário gerar o relatório completo de caixa de funcionários
const DEFAULT_TIME_COUPONS_REPORT_TIME = 16;
const MESSAGE_WARNING_GENERATE_REPORT = "Relatório Parcial de Caixa do Funcionário, não vale como Relatório Oficial!";
const TYPE_OPERATION_RETRIEVE = 'Resgate';
const TYPE_OPERATION_USE = 'Uso';
const TYPE_OPERATION_RETRIEVED = 'Resgatado';
const TYPE_OPERATION_USED = 'Usado';
const TYPE_PAYMENT_POINTS = "Gotas";
const TYPE_PAYMENT_MONEY = "Dinheiro";

#endregion

#region CuponsTransacoes

const MSG_MAX_RETRIEVES_USER_GIFT = "Oops! Você já atingiu o máximo de resgates deste brinde por dia. Retorne em 24 horas para mais resgates.";
const MSG_MAX_RETRIEVES_USER_GIFT_BY_WORKER = "Usuário atingiu máximo de resgates para este brinde! Retorne em 24 horas para mais resgates.";
const MSG_MAX_RETRIEVES_USER_GIFT_CODE = 0x00090001;


#endregion

#region Gotas

// Nome Comum Gota Bonificacao

const MSG_GOTAS_DATA_EMPTY_CODE = 0x000A0001;
const MSG_GOTAS_DATA_EMPTY = "Lista de gotas recebido sem informação!";

const GOTAS_BONUS_SEFAZ = "BONIFICAÇÃO";
const GOTAS_BONUS_EXTRA_POINTS_SEFAZ = "OUTROS PRODUTOS";
const GOTAS_REGISTER_TYPE_AUTOMATIC = 1;
const GOTAS_REGISTER_TYPE_MANUAL = 0;
const GOTAS_ADJUSTMENT_POINTS = "GOTA VIRTUAL DE CORREÇÃO DE PONTOS";
const MSG_GOTAS_NOT_FOUND_IN_COUPON = "No Cupom Fiscal %s da SEFAZ do estado %s não há gotas à processar conforme configurações definidas!";
const MSG_GOTAS_NOT_FOUND_IN_COUPON_CODE = 0x000A0002;

#endregion

#region Pontuações

const TRANSMISSION_MODE_SEFAZ = "SEFAZ";
const TRANSMISSION_MODE_DIRECT = "DIRETO";
const MSG_QR_CODE_EMPTY = "O Campo QR_CODE deve ser informado!";
const MSG_QUANTIDADE_GOTAS_EMPTY = "Necessário informar a quantidade de gotas para ajuste!";
const MSG_QUANTIDADE_GOTAS_EMPTY_CODE = 0x00120001;

#endregion

#region Pontuacoes Comprovantes

// Erros
const MSG_PONTUACOES_COMPROVANTES_USUARIOS_GOTAS_MAX_REACHED = "Máximo de inserções de %s atingidas no dia!";
const MSG_PONTUACOES_COMPROVANTES_USUARIOS_GOTAS_MAX_REACHED_CODE = 0x00130001;
const MSG_PONTUACOES_COMPROVANTES_TICKET_NOT_AUTHORIZED = "Cupom não autorizado. Favor procurar a gerência!";
const MSG_PONTUACOES_COMPROVANTES_TICKET_NOT_AUTHORIZED_CODE = 0x00130002;
const MSG_PONTUACOES_COMPROVANTES_IMPORTED_SUCCESSFULLY = "Dados do cupom importados com sucesso!";
const MSG_PONTUACOES_COMPROVANTES_QR_CODE_ALREADY_IMPORTED = "Este registro já foi importado previamente!";
const MSG_PONTUACOES_COMPROVANTES_QR_CODE_ALREADY_IMPORTED_CODE = 0x00130003;
const MSG_PONTUACOES_COMPROVANTES_QR_CODE_CANCELLED_SUCESSFULLY = "Cupom Fiscal cancelado com sucesso!";

// Sucesso / Avisos

// Entidades / Definições

#endregion

#region Redes

const MSG_REDES_FILTER_REQUIRED = "Necessário selecionar uma rede para filtrar!";
const MSG_REDES_FILTER_REQUIRED_CODE = 0x00F0002;

const MESSAGE_NETWORK_CUSTOM_APP_NOT_CONFIGURED = "Funcionalidade não permitida para rede sem configuração de APP_PERSONALIZADO!";
const MESSAGE_NETWORK_DESACTIVATED = "Rede desativada!";

#endregion

#region Top Brindes

const MESSAGE_TOP_BRINDES_BRINDE_ID_NOT_EMPTY = "O campo BRINDES_ID deve ser informado!";
const MESSAGE_TOP_BRINDES_REDES_ID_NOT_EMPTY = "O campo REDES_ID deve ser informado!";

const MESSAGE_TOP_BRINDES_ITEMS_REQUIRED = "Necessário informar brindes que deseja reposicionar!";
const MESSAGE_TOP_BRINDES_MAX = 4;
const MESSAGE_TOP_BRINDES_MAX_DEFINED = "O total de Top Brindes está definido, não é possível adicionar!";
const TOP_BRINDES_TYPE_NATIONAL = 'Nacional';
const TOP_BRINDES_TYPE_LOCAL = 'Posto';


#endregion

#region Usuários

const WORKER_EMAIL = "mobileapiworker@dummy.com";
const WORKER_PASSWORD = "73495277";

const MSG_USUARIOS_CPF_EMPTY_CODE = 0x00170001;
const MSG_USUARIOS_CPF_EMPTY = "O campo CPF deve ser informado!";
const MSG_USUARIOS_CPF_LENGTH_INVALID_CODE = 0x00170002;
const MSG_USUARIOS_CPF_LENGTH_INVALID = "Tamanho do CPF inválido!";
const MSG_USUARIOS_CPF_ALREADY_EXISTS = "Usuário já existe com este CPF!";
const MSG_USUARIOS_CPF_ALREADY_EXISTS_CODE = 0x00170017;
const MSG_USUARIOS_CANT_SEARCH_CODE = 0x00170003;
const MSG_USUARIOS_CANT_SEARCH = "Este serviço só está disponível para funcionários de Posto!";
const MSG_USUARIOS_DOC_ESTRANGEIRO_ALREADY_EXISTS_CODE = 0x00170004;
const MSG_USUARIOS_DOC_ESTRANGEIRO_ALREADY_EXISTS = "Já existe um cadastro com este documento estrangeiro, informe um outro documento!";
const MSG_USUARIOS_DOC_ESTRANGEIRO_EMPTY_CODE = 0x00170005;
const MSG_USUARIOS_DOC_ESTRANGEIRO_EMPTY = "Campo DOCUMENTO ESTRANGEIRO deve ser informado!";
const MSG_USUARIOS_DOC_ESTRANGEIRO_SEARCH_EMPTY_CODE = 0x00170006;
const MSG_USUARIOS_DOC_ESTRANGEIRO_SEARCH_EMPTY = "Por favor informe corretamente o Documento de Identificação Estrangeira!";
const MSG_USUARIOS_EMAIL_EMPTY_CODE = 0x00170007;
const MSG_USUARIOS_EMAIL_EMPTY = "Campo EMAIL deve ser informado!";
const MSG_USUARIOS_EMAIL_INVALID_CODE = 0x00170016;
const MSG_USUARIOS_EMAIL_INVALID = "EMAIL inválido!";
const MSG_USUARIOS_LOGGED_IN_SUCCESSFULLY_CODE = 0x00170008;
const MSG_USUARIOS_LOGGED_IN_SUCCESSFULLY = "Usuário logado com sucesso!";
const MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT_CODE = 0x00170009;
const MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT = "Usuário ou senha incorreto!";
const MSG_USUARIOS_NOME_EMPTY = "O campo NOME deve ser informado!";
const MSG_USUARIOS_NOME_EMPTY_CODE = 0x00170018;
const MSG_USUARIOS_NOT_AUTHENTICATED_CODE = 0x0017000A;
const MSG_USUARIOS_NOT_AUTHENTICATED = "Usuário não autenticado!";
const MSG_USUARIOS_OLD_PASSWORD_DOESNT_MATCH_CODE = 0x0017000B;
const MSG_USUARIOS_OLD_PASSWORD_DOESNT_MATCH = "Senha antiga não confere!";
const MSG_USUARIOS_PASSWORD_INCORRECT_CODE = 0x0017000C;
const MSG_USUARIOS_PASSWORD_INCORRECT = "Senha do usuário incorreta! Tente novamente!";
const MSG_USUARIOS_PASSWORD_LENGTH_CODE = 0x0017000D;
const MSG_USUARIOS_PASSWORD_LENGTH = "Tamanho da senha deve ser de %s dígitos!";
const MSG_USUARIOS_PASSWORD_UPDATE_ERROR_CODE = 0x0017000E;
const MSG_USUARIOS_PASSWORD_UPDATE_ERROR = "Senha não foi alterada, por favor confira se a senha e nova senha conferem!";
const MSG_USUARIOS_PASSWORD_UPDATED_CODE = 0x0017000F;
const MSG_USUARIOS_PASSWORD_UPDATED = "Alteração de senha realizada com sucesso!";
const MSG_USUARIOS_PROFILE_ON_DATE_CODE = 0x00170010;
const MSG_USUARIOS_PROFILE_ON_DATE = "Perfil está atualizado!";
const MSG_USUARIOS_PROFILE_OUT_DATE_CODE = 0x00170011;
const MSG_USUARIOS_PROFILE_OUT_DATE = "Perfil está desatualizado! Verifique seu cadastro!";
const MSG_USUARIOS_TELEFONE_EMPTY = "O campo TELEFONE deve ser informado!";
const MSG_USUARIOS_TELEFONE_EMPTY_CODE = 0x00170019;
const MSG_USUARIOS_WORKER_BELONGS_ANOTHER_APP_CODE = 0x00170012;
const MSG_USUARIOS_WORKER_BELONGS_ANOTHER_APP = "Não é possível efetuar login neste aplicativo, Funcionário pertence à outro aplicativo específico!";
const MSG_USUARIOS_WORKER_BELONGS_CUSTOM_APP_CODE = 0x00170013;
const MSG_USUARIOS_WORKER_BELONGS_CUSTOM_APP = "Funcionário pertence à uma rede com aplicativo personalizado, não é possível fazer login no Gotas!";
const MSG_USUARIOS_WORKER_BELONGS_GENERIC_APP_CODE = 0x00170014;
const MSG_USUARIOS_WORKER_BELONGS_GENERIC_APP = "Funcionário pertence à uma rede que não possui aplicativo personalizado, ele deve fazer o login no aplicativo Gotas!";
const MSG_USUARIOS_WORKER_NOT_ASSOCIATED_CLIENTE_CODE = 0x00170015;
const MSG_USUARIOS_WORKER_NOT_ASSOCIATED_CLIENTE = "Este funcionário não está associado à nenhum Posto do Sistema!";

const CPF_LENGTH = 11;

#endregion

#region Usuarios Has Brindes

const MSG_USUARIOS_BRINDES_LIMIT_FREE_TEXT_CODE = 0x0019000B;
const MSG_USUARIOS_BRINDES_LIMIT_FREE_TEXT = "Este brinde é limitado a 1 (uma) unidade por usuário! Não é possível novo resgate!";

#endregion

#region Veiculos

const MSG_VEICULOS_PLACA_EMPTY = 'O campo PLACA deve ser informado!';
const MSG_VEICULOS_PLACA_EMPTY_CODE = 0x001C0001;
#endregion

#endregion

#endregion


#endregion

#region Variáveis de sistema

// Tipos de Perfil de usuários

// Mínimo de porcentagem para comparação de texto de gotas com os produtos da NF Sefaz
const MIN_PERCENTAGE_SIMILAR_TEXT_GOTAS = 70.0;

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

/**
 * Regra de versão
 * x.y.z
 *
 * x = alteração grande do sistema, conjunto de novos recursos.
 * y = alteração de banco de dados (tabelas novas, campos novos)
 * z = alterações pequenas do sistema (ajuste de campos, pequenas novas funcionalidades)
 */
const SYSTEM_VERSION = "1.1.5";

#endregion
