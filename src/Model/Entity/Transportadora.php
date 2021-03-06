<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transportadora Entity
 *
 * @property int $id
 * @property string $nome_fantasia
 * @property string $razao_social
 * @property string $cnpj
 * @property string $endereco
 * @property int $endereco_numero
 * @property string $endereco_complemento
 * @property string $bairro
 * @property string $municipio
 * @property string $estado
 * @property string $tel_fixo
 * @property string $tel_celular
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 */
class Transportadora extends Entity
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
}
