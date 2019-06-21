<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RedesUsuariosExcecaoAbastecimento Entity
 *
 * @property int $id
 * @property int $redes_id
 * @property int $adm_rede_id
 * @property int $usuarios_id
 * @property int $quantidade_dia
 * @property \Cake\I18n\FrozenTime $validade
 * @property bool $habilitado
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Rede $rede
 * @property \App\Model\Entity\Usuario $usuario
 */
class RedesUsuariosExcecaoAbastecimento extends Entity
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
        'redes_id' => true,
        'adm_rede_id' => true,
        'usuarios_id' => true,
        'quantidade_dia' => true,
        'validade' => true,
        'habilitado' => true,
        'audit_insert' => true,
        'audit_update' => true,
        'rede' => true,
        'usuario' => true
    ];
}
