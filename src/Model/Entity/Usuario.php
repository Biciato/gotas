<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

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

    protected function _setSenha($password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher)->hash($password);
        }
    }
}
