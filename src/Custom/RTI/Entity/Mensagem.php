<?php

namespace App\Custom\RTI\Entity;

/**
 * Mensagem
 *
 * @property string $message Mensagem título
 * @property int|bool $status 1/true = Sucesso | 0/false= Falha
 * @property array $errors Lista de erros
 * @property array $error_codes Lista de errors em hex
 *
 * @return App\Custom\RTI\Entity\Mensagem Mensagem object
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-10-16
 */
class Mensagem
{
    public $message;
    public $status;
    public $errors;
    public $error_codes;

    public function __construct(array $props = null)
    {
        $this->errors = [];
        $this->error_codes  = [];

        if (!empty($props)) {
            foreach ($props as $key => $prop) {
                if (!empty($prop)) {
                    $this->{$key} = $prop;
                }
            }
        }
     }
}
