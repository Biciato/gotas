<?php
/**
 * Classe de Utilidades para objetos do tipo crypt
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     03/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */

namespace App\Custom\RTI;

use App\Controller\AppController;

use Cake\Core\Configure;

/**
 * Classe de manipulação de Data
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     16/11/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class CryptUtil
{
    // ------------------ Campos ------------------

    protected $password = '[gT,YB2R7~4W/=h;';
    protected $method = 'aes-256-cbc';

    // IV must be exact 16 chars (128 bit)
    protected $iv = null;

    /**
     * Construtor
     */
    function __construct()
    {
        $this->iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) .
        chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    }

    /**
     * Criptografa uma string
     *
     * @param string $word Palavra a ser criptografada
     *
     * @return string Valor criptografado
     */
    public function encrypt(string $word)
    {
        // Deve ser exatamente 32-bit de caracteres (128-bit)
        $password = substr(hash('sha256', $this->password, true), 0, 32);
        
        return base64_encode(openssl_encrypt($word, $this->method, $password, OPENSSL_RAW_DATA, $this->iv));
    }

    /**
     * Descriptografa uma string
     *
     * @param string $word Palavra a ser criptografada
     *
     * @return string Valor criptografado
     */
    public function decrypt(string $word)
    {
        // Deve ser exatamente 32-bit de caracteres (128-bit)
        $password = substr(hash('sha256', $this->password, true), 0, 32);

        return openssl_decrypt(base64_decode($word), $this->method, $password, OPENSSL_RAW_DATA, $this->iv);
    }
}
