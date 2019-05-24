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

    /**
     * CryptUtil::encryptCupom
     *
     * Utilizado para criptografar um cupom
     *
     * @param int $cc Código do Posto
     * @param int $dia Dia
     * @param int $mes Mês
     * @param int $ano Ano
     * @param int $tipo Tipo
     * @param int $auxiliar Auxiliar
     * @param int $senha Senha
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-05-14
     *
     * @return string encrypted
     */
    public static function encryptCupom(int $cc, int $dia, int $mes, int $ano, int $tipo, int $auxiliar, int $senha)
    {
        // cálculo senha

        $senha = str_pad($senha, 3, '0', STR_PAD_LEFT);
        $senhaArray = str_split($senha);

        $calculoSenha = $senhaArray[0] + $senhaArray[1] + $senhaArray[2];

        // Garante tamanho 2
        $calculoSenha = str_pad($calculoSenha, 2, '0', STR_PAD_LEFT);
        $calculoSenha = str_split($calculoSenha);
        $calculoSenha = $calculoSenha[0] + $calculoSenha[1];

        $restoSenha = (int)($calculoSenha % 7);
        $checkSum = $restoSenha + 1;

        // Cálculo AB

        $checkSumCC = sprintf("%s%s%s", $checkSum, '0', $cc);

        $a = self::calculateASCII($checkSumCC, 0);
        $b = self::calculateASCII($checkSumCC, 1);

        // Calculo CD
        $c = "";

        if (strlen($ano) > 2) {
            $c = substr($ano, 2);
        } else {
            $c = $ano;
        }
        $c = $c - 19;

        $c = ($c * 1024) + ($mes * 32) + $dia + $checkSum;
        $x = $c;

        $c = self::calculateASCII($c, 0);
        $d = self::calculateASCII($x, 1);

        // Calculo EF
        $auxiliarTmp = str_pad($auxiliar, 2, '0', STR_PAD_LEFT);
        $checkSumTipoPrimarioSecundario = sprintf("%s%s%s", $checkSum, $tipo, $auxiliarTmp);

        $e = self::calculateASCII($checkSumTipoPrimarioSecundario, 0);
        $f = self::calculateASCII($checkSumTipoPrimarioSecundario, 1);

        // Cálculo GH
        $checkSumGH = sprintf("%s%s", $checkSum, $senha);

        $g = self::calculateASCII($checkSumGH, 0);
        $h = self::calculateASCII($checkSumGH, 1);

        $cripto = sprintf("%s%s%s%s%s%s%s%s", $a, $b, $c, $d, $e, $f, $g, $h);

        // echo "Crypto...: ";
        // echo $cripto;
        // echo "<br />";

        return $cripto;
    }

    /**
     * CryptUtil::calculateASCII
     *
     * Função auxiliar para cálculo de ASCII
     *
     * @param int $input Valor de entrada
     * @param bool $mod Cálculo Via mod ou resto
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-05-14
     *
     * @return string Letra em ASCII
     */
    private static function calculateASCII($input, $mod)
    {
        $input = $mod ? $input % 93 : $input / 93;
        $input = $input + 33;
        $input = chr($input);

        return $input;
    }
}
