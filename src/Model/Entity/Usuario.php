<?php
namespace App\Model\Entity;

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
        "foto_documento_completo",
        "foto_perfil_completo",
    );

    protected function _setSenha($password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher)->hash($password);
        }
    }

    /**
     * Usuario::_getFotoDocumentoCompleto
     *
     * @return value propriedade virtual
     */
    protected function _getFotoDocumentoCompleto()
    {
        return
            empty($this->_properties["foto_documento"]) ? null :
            __("{0}{1}{2}", Configure::read("webrootAddress"), Configure::read("documentUserPathRead"), $this->_properties["foto_documento"]);
    }

    /**
     * Usuario::_getFotoPerfilCompleto
     *
     * @return value propriedade virtual
     */
    protected function _getFotoPerfilCompleto()
    {
        return
            empty($this->_properties["foto_perfil"]) ? null :
            __("{0}{1}{2}", Configure::read("webrootAddress"), Configure::read("documentUserPathRead"), $this->_properties["foto_perfil"]);
    }
}
