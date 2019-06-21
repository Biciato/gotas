<?php

/**
 * Classe de Utilidades para objetos do tipo Gotas
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     13/10/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */

namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\ORM\Query;
use Cake\Core\Configure;

/**
 * Classe de manipulação de Gotas
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     11/08/2018
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class StringUtil
{
    /**
     * Construtor
     */
    function __construct()
    { }

    /**
     * StringUtil::validarCPF
     *
     * Função que gera nome do arquivo com diretorio e extensao
     *
     * @param string $cpf CPF à ser validado
     *
     * @author @rafael-neri
     * @link https://gist.github.com/rafael-neri/ab3e58803a08cb4def059fce4e3c0e40
     *
     * @return bool
     */
    public static function gerarNomeArquivoAleatorio(string $diretorio = null, string $extensao = null)
    {
        $nome = bin2hex(openssl_random_pseudo_bytes(16));

        if (is_null($diretorio)) {
            $diretorio = "./";
        }

        if (is_null($extensao)) {
            $extensao = ".jpg";
        }

        return array(
            "fullDir" => __("{0}{1}{2}", $diretorio, $nome, $extensao),
            "fileName" => __("{0}{1}", $nome, $extensao)
        );
    }

    /**
     * StringUtil::validarConteudoXML
     *
     * Valida se o conteúdo é XML
     *
     * @param string $conteudo Conteudo XML em string
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 02/11/2018
     *
     * @return bool resultado
     */
    public static function validarConteudoXML(string $conteudo)
    {
        if (empty($conteudo)) {
            return false;
        }

        //Elimina possibilidade de retornar HTML
        if (stripos($conteudo, '<!DOCTYPE html>') !== false) {
            return false;
        }

        libxml_use_internal_errors(true);
        simplexml_load_string($conteudo);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        return empty($errors);
    }

    /**
     * StringUtil::gerarStringAleatoria
     *
     * Gera uma string aleatória
     *
     * @param integer $tamanho Tamanho da string (Default 32 dígitos
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-05-16
     *
     * @return string
     */
    public static function gerarStringAleatoria(int $tamanho = 32)
    {
        return substr(md5(mt_rand()), 0, $tamanho);
    }
}
