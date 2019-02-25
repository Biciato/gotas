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

// Constantes

const TYPE_PAYMENT_POINTS = "Gotas";
const TYPE_PAYMENT_MONEY = "Dinheiro";

const DATA_TYPE_MESSAGE_JSON = "json";
const DATA_TYPE_MESSAGE_XML = "xml";

// status de Job

const JOB_STATUS_INIT = "Inicializando...";
const JOB_STATUS_END = "Finalizando...";



#region Mensagens

#region Comuns ao Sistema

const MESSAGE_QUERY_DOES_NOT_CONTAIN_DATA = "A consulta não retornou dados!";

#endregion

#region Entidades

// Clientes

const MESSAGE_CNPJ_EMPTY = "Campo CNPJ não informado!";
const MESSAGE_CNPJ_NOT_REGISTERED_ON_SYSTEM = "CNPJ não cadastrado no sistema Web!";
const MESSAGE_ESTABLISHMENT_WITHOUT_TIME_SHIFTS = "Estabelecimento não possui quadro de horários, não será possível realizar a impressão dos dados emitidos aos clientes!";

// Clientes Has Brindes Habilitados Estoque
const STOCK_OPERATION_TYPES_ADD_TYPE = 0;
const STOCK_OPERATION_TYPES_SELL_TYPE_GIFT = 1;
const STOCK_OPERATION_TYPES_SELL_TYPE_SALE = 2;
const STOCK_OPERATION_TYPES_RETURN_TYPE = 3;

// Cupom

const MESSAGE_COUPON_EMPTY = "Campo QRCODE (Cupom Fiscal ECF) deve ser informado!";
const MESSAGE_COUPON_MISMATCH_FORMAT = "Campo QRCODE (Cupom Fiscal ECF) com formato inválido!";
const MESSAGE_COUPON_PRINTED_EMPTY = "Necessário informar o CUPOM!";
const MESSAGE_COUPON_PRINTED_DOES_NOT_EXIST = "Cupom não existe no sistema!";
const MESSAGE_COUPON_PRINTED_CANNOT_BE_CANCELLED = "O cupom informado não pode ser cancelado!";
const MESSAGE_COUPON_ANOTHER_NETWORK = "O cupom informado pertence a outra rede!";
const MESSAGE_COUPON_PRINTED_ALREADY_CANCELLED = "O cupom informado já está cancelado no sistema!";
const MESSAGE_COUPON_PRINTED_CANCELLED = "O cupom informado foi cancelado com sucesso!";


// Redes

const MESSAGE_REDES_ID_EMPTY = "Campo ID de Rede não informado!";

// Usuários
const MESSAGE_USUARIOS_CPF_EMPTY = "Campo CPF não informado!";
const MESSAGE_USUARIO_LOGGED_IN_SUCCESSFULLY = "Usuário logado com sucesso!";
const MESSAGE_USUARIO_LOGIN_PASSWORD_INCORRECT = "Usuário ou senha incorreto!";

// Mensagens de Suporte
const MESSAGE_CONTACT_SUPPORT = "Entre em contato com o suporte.";

// Mensagens de processamento
const MESSAGE_PROCESSING_COMPLETED = "Processamento realizado com sucesso!";
const MESSAGE_OPERATION_FAILURE_DURING_PROCESSING = "Erro! Não foi possível concluir a operação devido os seguintes erros:";

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
