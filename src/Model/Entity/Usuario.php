<?php

namespace App\Model\Entity;

use App\Custom\RTI\NumberUtil;
use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\I18n\FrozenDate;

/**
 * Usuario Entity
 *
 * @property int $id
 * @property int $tipo_perfil
 * @property string $nome
 * @property string $cpf
 * @property int $sexo
 * @property \Cake\I18n\FrozenDate $data_nasc
 * @property string $email
 * @property string $senha
 * @property string $telefone
 * @property string $endereco
 * @property int $endereco_numero
 * @property string $endereco_complemento
 * @property string $bairro
 * @property string $municipio
 * @property string $estado
 * @property string $cep
 * @property int $tentativas_login
 * @property \Cake\I18n\FrozenDate $ultima_tentativa_login
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 */
class Usuario extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * ------------------------------------------------------------------------------------------
     * Propriedades Virtuais
     * ------------------------------------------------------------------------------------------
     */

    protected $_virtual = array(
        "cpf_formatado",
        "foto_documento_completo",
        "foto_perfil_completo",
        "sexo_formatado",
    );

    protected function _setSenha($password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher)->hash($password);
        }
    }

    /**
     * Usuario::_getCpfFormatado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-30
     *
     * Obtem o valor do sexo em String
     *
     * @return value propriedade virtual
     */
    protected function _getCpfFormatado()
    {
        $cpf = !empty($this->_properties["cpf"]) ? $this->_properties["cpf"] : null;

        if (is_null($cpf)) {
            return "";
        }

        return NumberUtil::formatarCPF($cpf);
    }

    /**
     * Usuario::_getFotoDocumentoCompleto
     *
     * @return value propriedade virtual
     */
    protected function _getFotoDocumentoCompleto()
    {
        $emptyImg = sprintf(
            "%s%s%s",
            Configure::read("webrootAddress"),
            Configure::read("documentUserPathRead"),
            "empty.jpg"
        );
        return
            empty($this->_properties["foto_documento"]) ? $emptyImg : __("{0}{1}{2}", Configure::read("webrootAddress"), Configure::read("documentUserPathRead"), $this->_properties["foto_documento"]);
    }

    /**
     * Usuario::_getFotoPerfilCompleto
     *
     * @return value propriedade virtual
     */
    protected function _getFotoPerfilCompleto()
    {
        $emptyImg = sprintf(
            "%s%s%s",
            Configure::read("webrootAddress"),
            Configure::read("imageUserProfilePathRead"),
            "empty.jpg"
        );

        return
            empty($this->_properties["foto_perfil"]) ? $emptyImg : __("{0}{1}{2}", Configure::read("webrootAddress"), Configure::read("imageUserProfilePathRead"), $this->_properties["foto_perfil"]);
    }

    /**
     * Usuario::_getSexoFormatado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-30
     *
     * Obtem o valor do sexo em String
     *
     * @return value propriedade virtual
     */
    protected function _getSexoFormatado()
    {
        $sexo = !empty($this->_properties["sexo"]) ? $this->_properties["sexo"] : null;

        if (is_null($sexo)) {
            return "";
        }

        $sexoList = [
            0 => "Feminino",
            1 => "Masculino",
            2 => "Nâo informado"
        ];

        return $sexoList[$sexo];
    }
}
