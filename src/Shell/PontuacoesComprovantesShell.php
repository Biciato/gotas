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
    protected $datetime_util = null;
    protected $email_util = null;
    protected $sefaz_util = null;

    /**
     * Método de inicialização
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (is_null($this->datetime_util)) {
            $this->datetime_util = new DateTimeUtil();
        }

        if (is_null($this->email_util)) {
            $this->email_util = new EmailUtil();
        }

        if (is_null($this->sefaz_util)) {
            $this->sefaz_util = new SefazUtil();
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

            $yesterday = $this->datetime_util->substractDaysFromDateTime($today, 1, 'Y-m-d');

            // TODO: usar só para carater de teste
            // $yesterday = $today;

            $date_start = $yesterday . ' 00:00:00';
            $date_end = $yesterday . ' 23:59:59';

            $array_options = [];

            array_push($array_options, ['data between "'.$date_start.'" and "'.$date_end.'"']);

            // pegar lista de todos os clientes

            $clientes = $this->Clientes->getAllClientes();

            foreach ($clientes as $key => $cliente) {
                $content_array = [];

                // obtem os administradores (destinatários) de cada rede

                $destination_users_array = $this->ClientesHasUsuarios->getAllUsersByClienteId(
                    $cliente->id,
                    Configure::read('profileTypes')['AdminNetworkProfileType']
                );

                // obtêm a lista de funcionários de cada cliente
                // TODO: Ver se impactará a mudança do serviço com novas assinaturas
                $funcionarios_array = $this->Usuarios->findFuncionariosRede(
                    $cliente->id,
                    false,
                    false
                );

                // para cada funcionário, obtêm a lista de cupons processados
                // no dia que foram feitos de forma manual e
                // sorteia um para auditoria

                $comprovantes_array = [];
                $attachments_array = [];

                $comprovantes_id_array_selected = [];

                foreach ($funcionarios_array as $key => $funcionario) {
                    $comprovantes_id
                        = $this->PontuacoesComprovantes->getAllCouponsIdByWorkerId(
                            $funcionario->id,
                            $date_start,
                            $date_end,
                            true,
                            false
                        );


                    $comprovantes_id_array_not_selected = [];

                    foreach ($comprovantes_id as $key => $comprovante) {
                        array_push($comprovantes_id_array_not_selected, $comprovante['id']);
                    }

                    if (sizeof($comprovantes_id_array_not_selected) > 0) {
                        $comprovante_selected
                            = rand(0, sizeof($comprovantes_id_array_not_selected) -1);

                        array_push($comprovantes_id_array_selected, $comprovantes_id_array_not_selected[$comprovante_selected]);
                    }
                }

                foreach ($comprovantes_id_array_selected as $key => $comprovante_id_selected) {

                    // se teve registro para o funcionário, obtêm o que foi sorteado
                    $comprovante = $this->PontuacoesComprovantes->getCouponById(
                        $comprovante_id_selected
                    );

                    // comprovante encontrado, chama função que retorna
                    // as informações para enviar à função de e-mail

                    if ($comprovante) {
                        array_push(
                            $comprovantes_array,
                            $this->_prepareComprovanteData($comprovante)
                        );
                    }

                }

                foreach ($comprovantes_array as $key => $value) {
                    array_push($attachments_array, $value['attachment']);
                }

                // E-mail deve ser enviado somente se teve atendimento
                if (sizeof($comprovantes_array) > 0) {

                    $content_array['pontuacoes_comprovantes'] = $comprovantes_array;

                    $subject = __(
                        "Relatório dos Cupons Fiscais Inseridos Manualmente de {0}",
                        date('d/m/Y')
                    );

                    $content_array['link_sefaz'] = $this->sefaz_util->getUrlSefazByState($cliente->estado);

                    foreach ($destination_users_array as $key => $destination_user) {

                        $content_array['admin_name'] = $destination_user->usuario->nome . ' / '. $destination_user->usuario->email;

                        Log::write(
                            'info',
                            __(
                                'Enviando e-mail para administrador {0} / {1} ...',
                                $destination_user->usuario->nome,
                                $destination_user->usuario->email
                            )
                        );

                        // debug($content_array);

                        $this->email_util->sendMail(
                            'batch_email_report_day_coupons',
                            $destination_user->usuario,
                            $subject,
                            $content_array,
                            $attachments_array
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
    private function _prepareComprovanteData(PontuacoesComprovante $comprovante)
    {
        try {
            $file = __(
                "{0}{1}{2}",
                Configure::read('appAddress').'webroot/',
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
            $object['image_data'] =  $comprovante['nome_img'];
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
            $stringError= __("Erro ao criar objeto: {0} em: {1} ", $e->getMessage(), $trace[1]);

            debug($stringError);
            Log::write('error', $stringError);

            return false;
        }
    }
}
