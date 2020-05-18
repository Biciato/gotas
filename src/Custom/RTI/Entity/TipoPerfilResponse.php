<?php

namespace App\Custom\RTI\Entity;

/**
 * TipoPerfilResponse
 *
 * @property array $no_rule Lista de perfis quando a tela não tem regra de negócio para aquele select.
 * Deve ser utilizado quando se deseja filtrar em uma tela por todo mundo, sem distinção.
 * @property array $filter Lista de perfis de Funcionários de uma rede.
 * O perfil logado só terá como acessar determinados tipos de perfil conforme a regra especificada.
 * @property array $insert Lista para criação de usuários tipo de perfis permitidos para aquele perfil logado.
 *
 * @return App\Custom\RTI\Entity\TipoPerfilResponse Objeto de Resposta
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 1.2.3
 * @date 2020-05-18
 */
class TipoPerfilResponse
{
}
