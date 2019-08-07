<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use DateTime;
use App\Model\Entity\TopBrindes;
use Cake\Log\Log;
use Exception;

/**
 * TopBrindes Model
 *
 * @property \App\Model\Table\RedesTable|\Cake\ORM\Association\BelongsTo $Rede
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Cliente
 * @property \App\Model\Table\BrindesTable|\Cake\ORM\Association\BelongsTo $Brinde
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\TopBrindes get($primaryKey, $options = [])
 * @method \App\Model\Entity\TopBrindes newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TopBrindes[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TopBrindes|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TopBrindes patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TopBrindes[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TopBrindes findOrCreate($search, callable $callback = null, $options = [])
 */
class TopBrindesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('top_brindes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Rede', [
            "className" => "Redes",
            'foreignKey' => 'redes_id',
            'joinType' => Query::JOIN_TYPE_INNER
        ]);
        $this->belongsTo('Cliente', [
            "className" => "Clientes",
            'foreignKey' => 'clientes_id',
            'joinType' => Query::JOIN_TYPE_INNER
        ]);
        $this->belongsTo('Brinde', [
            "className" => "Brindes",
            'foreignKey' => 'brindes_id',
            'joinType' => Query::JOIN_TYPE_INNER
        ]);
        $this->belongsTo('UsuarioCadastro', [
            "className" => "Usuarios",
            'foreignKey' => 'audit_user_insert_id',
            'joinType' => Query::JOIN_TYPE_INNER
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('posicao', 'create')
            ->notEmpty('posicao');

        $validator
            ->scalar('tipo')
            ->allowEmpty('tipo');

        $validator
            ->dateTime('data')
            ->requirePresence('data', 'create')
            ->notEmpty('data');

        $validator
            ->dateTime('audit_insert')
            ->allowEmpty('audit_insert');

        $validator
            ->dateTime('audit_update')
            ->allowEmpty('audit_update');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['redes_id'], 'Rede'));
        $rules->add($rules->existsIn(['clientes_id'], 'Cliente'));
        $rules->add($rules->existsIn(['brindes_id'], 'Brinde'));
        $rules->add($rules->existsIn(['audit_user_insert_id'], 'UsuarioCadastro'));

        return $rules;
    }

    #region Read

    /**
     * Obtem Top Brindes
     *
     * @param integer $redesId Redes Id
     * @param integer $clientesId Clientes Id
     * @param integer $brindesId Brindes Id
     * @param integer $posicao Posicao (Máx 4)
     * @param string $tipo Tipo ("Nacional", "Posto")
     * @param DateTime $dataMin Data Mínima de criação
     * @param DateTime $dataMax Data Maxima de criação
     * @param integer $usuarioAudiInsert Usuario Audi Insert
     * 
     * @return App\Model\Entity\TopBrindes $brinde
     */
    public function getTopBrindes(int $redesId, int $clientesId = null, int $brindesId = null, int $posicao = null, string $tipo = null, DateTime $dataMin = null, DateTime $dataMax = null, int $usuarioAudiInsert = null)
    {
        try {
            $where = [];

            $where[] = ["TopBrindes.redes_id" => $redesId];

            if (!empty($clientesId)) {
                $where[] = ["TopBrindes.clientes_id" => $clientesId];
            }

            if (!empty($brindesId)) {
                $where[] = ["TopBrindes.brindes_id" => $brindesId];
            }

            if (!empty($posicao)) {
                $where[] = ["TopBrindes.posicao" => $posicao];
            }

            if (!empty($tipo)) {
                $where[] = ["TopBrindes.tipo" => $tipo];
            }

            if (!empty($dataMin)) {
                $where[] = ["TopBrindes.data >= " => $dataMin];
            }

            if (!empty($dataMin)) {
                $where[] = ["TopBrindes.data <=" => $dataMax];
            }

            if (!empty($usuarioAudiInsert)) {
                $where[] = ["TopBrindes.audit_user_insert_id" => $usuarioAudiInsert];
            }

            return $this->find("all")->where($where)
                ->contain(["Rede", "Cliente", "Brinde", "UsuarioCadastro"])
                ->order(["TopBrindes.posicao" => "ASC"]);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }

    /**
     * Obtem Top Brindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-04
     * 
     * @param integer $redesId Redes Id
     * @param integer $clientesId Clientes Id
     * @param string $tipo Tipo ("Nacional", "Posto")
     * 
     * @return int $count
     */
    public function countTopBrindes(int $redesId, int $clientesId = null, string $tipo = null)
    {
        try {
            $where = [];

            $where[] = ["TopBrindes.redes_id" => $redesId];

            if (!empty($clientesId)) {
                $where[] = ["TopBrindes.clientes_id" => $clientesId];
            }

            if (!empty($tipo)) {
                $where[] = ["TopBrindes.tipo" => $tipo];
            }

            return $this->find()->where($where)->count();
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }
    #endregion

    #region Save

    /**
     * Salva um registro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-04
     *
     * @param TopBrindes $topBrinde Entidade
     *
     * @return TopBrindes $topBrinde Entidade salva com Id
     */
    public function saveUpdate(TopBrindes $topBrinde)
    {
        try {
            return $this->save($topBrinde);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }

    #endregion

    #region Delete
    #endregion
}
