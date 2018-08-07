<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Core\Configure;

/**
 * Rede Entity
 *
 * @property int $id
 * @property string $nome_rede
 * @property bool $ativado
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 */
class Rede extends Entity
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

    protected $_virtual = array("nome_img_completo", "propaganda_img_completo");

    protected function _getNomeImgCompleto()
    {
        if (strlen($this->_properties["nome_img"]) > 0) {
            return __("{0}{1}{2}", Configure::read("appAddress"), Configure::read("imageNetworkPath"), $this->_properties["nome_img"]);
        }

        return $this->_properties["nome_img"];
    }

    protected function _getPropagandaImgCompleto()
    {
        if (strlen($this->_properties["propaganda_img"]) > 0) {
            return __("{0}{1}{2}", Configure::read("appAddress"), Configure::read("imageNetworkPath"), $this->_properties["propaganda_img"]);
        }

        return $this->_properties["propaganda_img"];
    }
}
