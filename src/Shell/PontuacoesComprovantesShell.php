<?php

/**
 * Arquivo para Classe para execução em terminal (shell)
 *
 * @category ClasseDeExecucaoBackground
 * @package  Shell
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     05/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Shell/ClasseDeExecucaoBackground
 */

namespace App\Shell;

use Cake\Console\Shell;
use ArrayObject;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\EmailUtil;
use App\Custom\RTI\SefazUtil;
use App\View\Helper;
use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Entity\PontuacoesComprovante;
use \DateTime;

/**
 * Classe para execução em terminal (shell)
 *
 * @category ClasseDeExecucaoBackground
 * @package  Shell
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     05/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Shell/ClasseDeExecucaoBackground
 */
class PontuacoesComprovantesShell extends ExtendedShell
{
    // Fields
    protected $dateTimeUtil = null;
    protected $emailUtil = null;
    protected $sefazUtil = null;

    /**
     * Método de inicialização
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (is_null($this->dateTimeUtil)) {
            $this->dateTimeUtil = new DateTimeUtil();
        }

        if (is_null($this->emailUtil)) {
            $this->emailUtil = new EmailUtil();
        }

        if (is_null($this->sefazUtil)) {
            $this->sefazUtil = new SefazUtil();
        }
    }

    /**
     * Gera relatório diário e envia para cada administrador de cada loja/matriz
     *
     * @return void
     */
    public function generateDailyReport()
    {
        try {
            Log::write('info', 'Iniciando processamento de envio de Comprovantes dos Cupoms Fiscais processados...');

            // pega a data que será feito a análise
            $today = date('Y-m-d');

            $yesterday = $this->dateTimeUtil->substractDaysFromDateTime($today, 1, 'Y-m-d');

            // TODO: usar só para carater de teste
            // $yesterday = $today;

            $dateStart = $yesterday . ' 00:00:00';
            $dateEnd = $yesterday . ' 23:59:59';

            $arrayOptions = [];

            $arrayOptions[] = array('data between "' . $dateStart . '" and "' . $dateEnd . '"');

            // pegar lista de todos os clientes

            $clientes = $this->Clientes->getAllClientes();

            foreach ($clientes as $key => $cliente) {
                $contentArray = [];

                // obtem os administradores (destinatários) de cada rede

                $destinationUsersArray = $this->ClientesHasUsuarios->getAllUsersByClienteId(
                    $cliente->id,
                    Configure::read('profileTypes')['AdminNetworkProfileType']
                );

                // obtêm a lista de funcionários de cada cliente
                $funcionariosArray = $this->Usuarios->findFuncionariosRede(
                    $cliente->id,
                    array()
                );

                // para cada funcionário, obtêm a lista de cupons processados
                // no dia que foram feitos de forma manual e
                // sorteia um para auditoria

                $comprovantesArray = [];
                $attachmentsArray = [];

                $comprovantesIdArraySelected = [];

                foreach ($funcionariosArray as $key => $funcionario) {
                    $comprovantesId =
                        $this->PontuacoesComprovantes->getAllCouponsIdByWorkerId(
                        $funcionario->id,
                        $dateStart,
                        $dateEnd,
                        true,
                        false
                    );


                    $comprovantesIdNotSelected = [];

                    foreach ($comprovantesId as $key => $comprovante) {
                        $comprovantesIdNotSelected[] = $comprovante['id'];
                    }

                    if (sizeof($comprovantesIdNotSelected) > 0) {
                        $comprovanteSelected
                            = rand(0, sizeof($comprovantesIdNotSelected) - 1);

                        $comprovantesIdArraySelected[] = $comprovantesIdNotSelected[$comprovanteSelected];
                    }
                }

                foreach ($comprovantesIdArraySelected as $key => $comprovanteIdSelected) {

                    // se teve registro para o funcionário, obtêm o que foi sorteado
                    $comprovante = $this->PontuacoesComprovantes->getCouponById(
                        $comprovanteIdSelected
                    );

                    // comprovante encontrado, chama função que retorna
                    // as informações para enviar à função de e-mail

                    if ($comprovante) {
                        $comprovantesArray[] = $this->prepareComprovanteData($comprovante);
                    }
                }

                foreach ($comprovantesArray as $key => $value) {
                    array_push($attachmentsArray, $value['attachment']);
                }

                // E-mail deve ser enviado somente se teve atendimento
                if (sizeof($comprovantesArray) > 0) {

                    $contentArray['pontuacoes_comprovantes'] = $comprovantesArray;

                    $subject = __("Relatório dos Cupons Fiscais Inseridos Manualmente de {0}", date('d/m/Y'));

                    $contentArray['link_sefaz'] = $this->sefazUtil->getUrlSefazByState($cliente->estado);

                    foreach ($destinationUsersArray as $key => $destinationUser) {

                        $contentArray['admin_name'] = $destinationUser->usuario->nome . ' / ' . $destinationUser["usuario"]["email"];

                        Log::write(
                            'info',
                            __(
                                'Enviando e-mail para administrador {0} / {1} ...',
                                $destinationUser["usuario"]["nome"],
                                $destinationUser["usuario"]["email"]
                            )
                        );

                        // debug($contentArray);

                        $this->emailUtil->sendMail(
                            'batch_email_report_day_coupons',
                            $destinationUser["usuario"],
                            $subject,
                            $contentArray,
                            $attachmentsArray
                        );
                    }
                }
            }

            Log::write('info', 'Concluído o envio de Comprovantes dos Cupoms Fiscais inseridos manualmente...');
        } catch (\Exception $e) {
        }
    }

    /**
     * Prepara objeto de dados para corpo de mensagem de e-mail
     *
     * @param PontuacoesComprovante $comprovante Objeto de comprovante
     *
     * @return PontuacoesComprovante $data Dados preparados para envio
     */
    private function prepareComprovanteData(PontuacoesComprovante $comprovante)
    {
        try {
            $file = __(
                "{0}{1}{2}",
                Configure::read('appAddress') . 'webroot/',
                Configure::read('documentReceiptPathShellRead'),
                $comprovante['nome_img']
            );
            $file = __(
                "{0}{1}{2}",
                WWW_ROOT,
                Configure::read('documentReceiptPathShellRead'),
                $comprovante['nome_img']
            );
            $object['appAddress'] = Configure::read('appAddress');
            $object['id'] = $comprovante->id;
            // $object['image_data'] =  $file;
            $object['image_data'] = $comprovante['nome_img'];
            $object['chave_nfe'] = $comprovante->chave_nfe;
            $object['estado_nfe'] = $comprovante->estado_nfe;
            $object['data'] = $comprovante->data;
            $object['pontuacoes'] = $comprovante->pontuacoes;
            $object['soma_pontuacoes'] = $comprovante->soma_pontuacoes;
            $object['funcionario'] = $comprovante->funcionario;

            $object['usuario'] = $comprovante->usuario;
            $object['cliente'] = $comprovante->cliente;

            $object['conteudo'] = $comprovante->conteudo;

            $object['attachment'] = [
                'file' => $file,
                'mimetype' => 'image/jpg',
                'contentId' => $comprovante['nome_img']
            ];

            // debug($object);

            return $object;
        } catch (\Exception $e) {
            $stringError = __("Erro ao criar objeto: {0} em: {1} ", $e->getMessage(), $trace[1]);

            debug($stringError);
            Log::write('error', $stringError);

            return false;
        }
    }
}
