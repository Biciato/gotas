<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use DateTime;

/**
 * ClientesHasUsuario Entity
 *
 * @property int $id
 * @property int $clientes_id
 * @property int $usuarios_id
 * @property int $audit_user_insert_id
 * @property bool $conta_ativa
 *
 * @property \App\Model\Entity\Cliente $cliente
 * @property \App\Model\Entity\Usuario $usuario
 */
class ClientesHasUsuario extends Entity
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

    protected $_virtual = [
        "audit_insert_localtime",
    ];

    protected function _getAuditInsertLocaltime()
    {
        if (empty($this->_properties["audit_insert"])) {
            return "";
        }

        $date = new DateTime($this->_properties["audit_insert"]);

        return $date->modify("-3 hour")->format("Y-m-d H:i:s");
    }
}
