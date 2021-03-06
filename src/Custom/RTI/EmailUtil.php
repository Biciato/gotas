<?php

/**
 * Classe de Utilidades para objetos do tipo Email
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     05/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */

namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Core\Configure;
use Cake\Log\Log;
use App\Model\Entity\Usuario;

/**
 * Classe de manipulação de Data
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     03/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class EmailUtil
{
    /**
     * Undocumented function
     */
    function __construct()
    {
    }

    /**
     * Undocumented function
     *
     * @param string $template      Qual template utilizar
     * @param Usuario $destination   Destinátário
     * @param string $subject       Título
     * @param array  $contentArray Conteúdo
     * @param array  $attachments   Lista de anexos
     *
     * @return void
     */
    public function sendMail(string $template, Usuario $destination, string $subject, array $contentArray, array $attachments = [])
    {
        try {
            $from = Configure::read('emailAddressSender');
            $email = new Email();
            if (!empty($template)) {
                $email->template($template);
            }
            $email->subject($subject);
            $email->emailFormat('html');

            if (sizeof($attachments) > 0) {
                $email->attachments($attachments);
            }

            $email->setHeaders(
                [
                    'From' => $from,
                    'To' => $destination->email,
                    'Reply-To' => $from,
                    'Return-Path' => $from,
                    'Subject' => $subject,
                    // "X-Priority" => 2,
                    // 'X-MSmail-Priority' => 'high',
                    "Reply-To" => $from,
                    "Return-Path:" => $from,
                    // 'MIME-Version:' => '1.0',
                    // 'Content-type' => 'text/html; charset=iso 8859-1'
                ]
            );

            $email->from(Configure::read("emailAddressSender"));
            $email->to($destination->email, $destination->nome);
            $email->viewVars($contentArray);

            if (!$email->send()) {
                throw new Exception("E-mail não enviado!");
            }
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __(
                "Erro ao enviar e-mail: {0} em: {1} ",
                $e->getMessage(),
                $trace
            );

            Log::write('error', $stringError);
        }
    }

    /**
     * EmailUtil::validateEmail
     *
     * Realiza validação de e-mail
     *
     * @param string $email Email fornecido
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/13
     *
     * @return array ["status", "message"]
     */
    public static function validateEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => 0, "message" => MSG_USUARIOS_EMAIL_INVALID, "code" => MSG_USUARIOS_EMAIL_INVALID_CODE];
        }

        return ["status" => 1, "message" => null, "code" => null];
    }
}
