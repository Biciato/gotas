<?php
namespace App\Model\Table;

use ArrayObject;
use App\View\Helper;
use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\ResponseUtil;

/**
 * Usuarios Model
 *
 * @method \App\Model\Entity\Usuario get($primaryKey, $options = [])
 * @method \App\Model\Entity\Usuario newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Usuario[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Usuario|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Usuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Usuario[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Usuario findOrCreate($search, callable $callback = null, $options = [])
 */
class UsuariosTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $usuarioTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of usuario table property
     * @return Cake\ORM\Table Table object
     */
    private function _getUsuarioTable()
    {
        if (is_null($this->usuarioTable)) {
            $this->_setUsuarioTable();
        }
        return $this->usuarioTable;
    }

    /**
     * Method set of usuario table property
     *
     * @return void
     */
    private function _setUsuarioTable()
    {
        $this->usuarioTable = TableRegistry::get('Usuarios');
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        // parent::initialize($config);

        $this->setTable('usuarios');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->hasOne(
            'ClienteHasUsuario',
            [
                'className' => "ClientesHasUsuarios",
                'foreignKey' => 'usuarios_id',
                "joinType" => "INNER"

            ]
        );

        $this->hasOne(
            "PontuacaoComprovante",
            array(
                "className" => "PontuacoesComprovantes",
                "foreignKey" => "usuarios_id",
                "joinType" => "INNER"
            )

        );

        $this->hasMany('UsuariosHasVeiculos')
            ->setForeignKey('usuarios_id');

        $this->hasMany('TransportadorasHasUsuarios')
            ->setForeignKey('usuarios_id');

        $this->hasMany(
            'Pontuacoes',
            [
                'class' => 'Pontuacoes',
                'foreignKey' => 'usuarios_id',
                'joinType' => 'INNER'
            ]
        );
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
            ->allowEmpty('matriz_id');

        $validator
            ->integer('tipo_perfil')
            ->requirePresence('tipo_perfil', 'create')
            ->notEmpty('tipo_perfil', 'Tipo de perfil é necessário informar')
            ->add(
                'tipo_perfil',
                'inList',
                [
                    'rule' => ['inList', ['0', '1', '2', '3', '4', '5', '6', '998', '999']],
                    'message' => 'Por favor informe um tipo de perfil',
                    'allowEmpty' => false
                ]
            );

        $validator
            ->requirePresence('nome', 'create')
            ->notEmpty('nome', "É necessário informar o nome");

        $validator
            ->allowEmpty('cpf')
            ->add(
                'cpf',
                'unique',
                [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'Este CPF já está em uso'
                ]
            );

        $validator
            ->integer('sexo')
            ->requirePresence('sexo', 'create')
            ->notEmpty('sexo', 'Por favor informe o sexo')
            ->add(
                'sexo',
                'inList',
                [
                    'rule' => ['inList', ['0', '1']],
                    'message' => 'Por favor informe o sexo'
                ]
            );

        $validator
            ->requirePresence('data_nasc', 'create')
            ->notEmpty('data_nasc');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->add(
                'email',
                'unique',
                [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'Este e-mail já está em uso. Para logar com este e-mail, use o formulário de "Esqueci minha Senha"'
                ]
            )
            ->notEmpty('email', 'Você deve informar um e-mail', 'create');

        $validator
            ->requirePresence('senha', 'create')
            ->notEmpty('senha', 'Você deve inserir uma senha', 'create')
            ->add(
                'senha',
                [
                    'custom' =>
                        [
                        'provider' => 'table',
                        'rule' => [$this, 'checkPasswordUsuario'],
                        'message' => 'A senha deve conter 4 dígitos, somente números',

                    ],
                    [
                        'provider' => 'table',
                        'rule' => [$this, 'checkPasswordWorker'],
                        'message' => 'A senha deve conter 8 dígitos, letras ou números',
                    ]
                ]
            );

        $validator
            ->requirePresence('confirm_senha', 'create')
            ->notEmpty('confirm_senha', 'Você deve redigir a senha', 'create')
            ->allowEmpty('confirm_senha', 'update')
            ->add('confirm_senha', 'compareWith', [
                'rule' => ['compareWith', 'senha'],
                'message' => 'Senhas não conferem.'
            ]);

        $validator
            ->allowEmpty('telefone');

        $validator
            ->allowEmpty('endereco');

        $validator
            ->integer('endereco_numero')
            ->allowEmpty('endereco_numero');

        $validator
            ->allowEmpty('endereco_complemento');

        $validator
            ->allowEmpty('bairro');

        $validator
            ->allowEmpty('municipio');

        $validator
            ->allowEmpty('estado');

        $validator
            ->allowEmpty('cep');

        $validator
            ->dateTime('audit_insert')
            ->allowEmpty('audit_insert');

        $validator
            ->dateTime('audit_update')
            ->allowEmpty('audit_update');

        return $validator;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationCadastroEstrangeiro(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('matriz_id');

        $validator
            ->integer('tipo_perfil')
            ->requirePresence('tipo_perfil', 'create')
            ->notEmpty('tipo_perfil', 'Tipo de perfil é necessário informar')
            ->add(
                'tipo_perfil',
                'inList',
                [
                    'rule' => ['inList', ['0', '1', '2', '3', '4', '5', '6', '998', '999']],
                    'message' => 'Por favor informe um tipo de perfil',
                    'allowEmpty' => false
                ]
            );

        $validator
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->integer('sexo')
            ->requirePresence('sexo', 'create')
            ->notEmpty('sexo', 'Por favor informe o sexo')
            ->add(
                'sexo',
                'inList',
                [
                    'rule' => ['inList', ['0', '1']],
                    'message' => 'Por favor informe o sexo'
                ]
            );

        $validator
            ->requirePresence('data_nasc', 'create')
            ->notEmpty('data_nasc');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->add(
                'email',
                'unique',
                [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'Este e-mail já está em uso. Para logar com este e-mail, use o formulário de "Esqueci minha Senha"'
                ]
            )
            ->notEmpty('email', 'Você deve informar um e-mail', 'create');

        $validator
            ->requirePresence('senha', 'create')
            ->notEmpty('senha', 'Você deve inserir uma senha', 'create')
            ->add(
                'senha',
                [
                    'custom' =>
                        [
                        'provider' => 'table',
                        'rule' => [$this, 'checkPasswordUsuario'],
                        'message' => 'A senha deve conter 4 dígitos, somente números',
                    ],
                    [
                        'provider' => 'table',
                        'rule' => [$this, 'checkPasswordWorker'],
                        'message' => 'A senha deve conter 8 dígitos, letras ou números',
                    ]
                ]
            );

        $validator
            ->requirePresence('confirm_senha', 'create')
            ->notEmpty('confirm_senha', 'Você deve redigir a senha', 'create')
            ->allowEmpty('confirm_senha', 'update')
            ->add(
                'confirm_senha',
                'compareWith',
                [
                    'rule' => ['compareWith', 'senha'],
                    'message' => 'Senhas não conferem.'
                ]
            );

        $validator
            ->allowEmpty('telefone');

        $validator
            ->allowEmpty('endereco');

        $validator
            ->integer('endereco_numero')
            ->allowEmpty('endereco_numero');

        $validator
            ->allowEmpty('endereco_complemento');

        $validator
            ->allowEmpty('bairro');

        $validator
            ->allowEmpty('municipio');

        $validator
            ->allowEmpty('estado');

        $validator
            ->allowEmpty('cep');

        $validator
            ->dateTime('audit_insert')
            ->allowEmpty('audit_insert');

        $validator
            ->dateTime('audit_update')
            ->allowEmpty('audit_update');

        return $validator;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationEditUsuarioInfo(Validator $validator)
    {
        $validator
            ->integer('tipo_perfil')
            ->requirePresence('tipo_perfil', 'create')
            ->notEmpty('tipo_perfil', 'Tipo de perfil é necessário informar')
            ->add(
                'tipo_perfil',
                'inList',
                [
                    'rule' => ['inList', ['0', '1', '2', '3', '4', '5', '6', '998', '999']],
                    'message' => 'Por favor informe um tipo de perfil',
                    'allowEmpty' => false
                ]
            );

        $validator
            ->requirePresence('nome', 'create')
            ->notEmpty('nome', "É necessário informar o nome");

        $validator
            ->allowEmpty('cpf')
            ->add(
                'cpf',
                'unique',
                [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => 'Este CPF já está em uso'
                ]
            );

        $validator
            ->integer('sexo')
            ->requirePresence('sexo', 'create')
            ->notEmpty('sexo', 'Por favor informe o sexo')
            ->add('sexo', 'inList', [
                'rule' => ['inList', ['0', '1']],
                'message' => 'Por favor informe o sexo'
            ]);

        $validator
            ->allowEmpty('senha');

        $validator
            ->allowEmpty('confirm_senha');

        $validator
            ->requirePresence('data_nasc', 'create')
            ->notEmpty('data_nasc');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Este e-mail já está em uso. Para logar com este e-mail, use o formulário de "Esqueci minha Senha"'
            ])
            ->notEmpty('email', 'Você deve informar um e-mail', 'create');

        $validator
            ->allowEmpty("foto_documento");

        $validator
            ->allowEmpty("foto_perfil");

        $validator
            ->allowEmpty('telefone');

        $validator
            ->allowEmpty('endereco');

        $validator
            ->integer('endereco_numero')
            ->allowEmpty('endereco_numero');

        $validator
            ->allowEmpty('endereco_complemento');

        $validator
            ->allowEmpty('bairro');

        $validator
            ->allowEmpty('municipio');

        $validator
            ->allowEmpty('estado');

        $validator
            ->allowEmpty('cep');

        return $validator;
    }

    /**
     * Regras para resgatar um brinde
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationRedeemItem(Validator $validator)
    {
        $validator
            ->notEmpty('current_password')
            ->add(
                'current_password',
                'custom',
                [
                    'rule' =>
                        function ($value, $context) {
                        $query = $this->find()->where(
                            ['id' => $context['data']['id']]
                        )->first();

                        $data = $query->toArray();

                        return (new DefaultPasswordHasher)->check($value, $data['senha']);
                    },
                    'message' => 'Senha não confere com o cadastro!'
                ]
            );
        return $validator;
    }

    /**
     * Executes before validation when insert or edit happens
     *
     * @return entity $data object
     * @author Gustavo Souza Gonçalves
     */
    public function beforeMarshal(Event $event, ArrayObject $data)
    {
        if (isset($data['nome'])) {
            if (isset($data['matriz_id'])) {
                $data->matriz_id = $data['matriz_id'];
            }

            if (isset($data["tipo_perfil"])) {
                $data['tipo_perfil'] = $data['tipo_perfil'];
            }
            $data['nome'] = $data['nome'];

            if (isset($data['cpf'])) {
                $data['cpf'] = $this->cleanNumber($data['cpf']);
            }

            $data['sexo'] = $data['sexo'];
            $data['data_nasc'] = date_format(date_create_from_format('d/m/Y', $data['data_nasc']), 'Y-m-d');

            if (isset($data["email"])) {
                $data['email'] = $data['email'];
            }
            $data['telefone'] = isset($data['telefone']) ? $this->cleanNumber($data['telefone']) : null;
            $data['endereco'] = isset($data['endereco']) ? $data['endereco'] : null;
            $data['endereco_numero'] = isset($data['endereco_numero']) ? $data['endereco_numero'] : null;
            $data['endereco_complemento'] = isset($data['endereco_complemento']) ? $data['endereco_complemento'] : null;
            $data['bairro'] = isset($data['bairro']) ? $data['bairro'] : null;
            $data['municipio'] = isset($data['municipio']) ? $data['municipio'] : null;
            $data['estado'] = isset($data['estado']) ? $data['estado'] : null;
            $data['cep'] = isset($data['cep']) ? $this->cleanNumber($data['cep']) : null;
        }


        return $data;
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
        $rules->add($rules->isUnique(['email']));
        // cpf não pode estar na lista pois em caso de cadastro estrangeiro, ele deve ser nulo
        // $rules->add($rules->isUnique(['cpf' ]));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    /* ------------------------ Insert ------------------------ */

    /**
     * Adiciona um novo usuario ao sistema
     */
    public function addUsuario($usuario = null)
    {
        try {
            $usuarioAdd = $this->_getUsuarioTable()
                ->newEntity();
            $usuarioAdd = $this->formatUsuario(0, $usuario);

            return $this->_getUsuarioTable()->save($usuarioAdd);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao inserir registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Edita usuario
     */
    public function addUpdateUsuario($usuario = null)
    {
        try {
            $usuario = $this->_getUsuarioTable()->save($usuario);

            return $usuario;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Find ------------------------ */

    public function getUser(\Cake\Datasource\EntityInterface $profile)
    {
        try {


        // Make sure here that all the required fields are actually present
            if (empty($profile->email)) {
                throw new \RuntimeException('Could not find email in social profile.');
            }

        // Check if user with same email exists. This avoids creating multiple
        // user accounts for different social identities of same user. You should
        // probably skip this check if your system doesn't enforce unique email
        // per user.

            debug($profile);
            $user = $this->find()
                ->where(['email' => $profile->email])
                ->first();

            if ($user) {
                return $user;
            }

            // Create new user account

            die();

            $user = array(
                "email" => $profile["email"],
                'tipo_perfil' => Configure::read("profileTypes")["UserProfileType"],
                'nome' => $profile["email"],
                'sexo' => 1,
                'data_nasc' => "01/01/1970",
                'senha' => 9879,
                'confirm_senha' => 9879
            );
            $user = $this->newEntity($user);
            $user = $this->save($user);

            if (!$user) {
                throw new \RuntimeException('Unable to save new user');
            }

            return $user;
        } catch (\Exception $e) {
            Log::write("error", $e);
        }

    }

    /**
     * Verifica se usuario está travado e qual tipo
     *
     * @return object conteúdo informando se conta está bloqueada
     * @author
     */
    public function checkUsuarioIsLocked($usuario)
    {
        try {
            $usuario = $this->getUsuarioByEmail($usuario['email']);

            $message = '';

            /**
             * 0 = nothing
             * 1 = inactive
             * 2 = blocked
             * 3 = too much retries
             */
            $actionNeeded = 0;

            if (is_null($usuario)) {
                $message = __("usuario ou senha ínvalidos, tente novamente");
                $actionNeeded = 1;
            } else {
                // verifica se é uma conta sem ser usuário.
                // se não for, verifica se a rede a qual ele se encontra está desativada

                if ($usuario['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType']
                    && $usuario['tipo_perfil'] <= Configure::read('profileTypes')['UserProfileType']) {
                    // pega o vínculo do usuário com a rede

                    $cliente_has_usuario_table = TableRegistry::get('ClientesHasUsuarios');

                    $cliente_has_usuario = $cliente_has_usuario_table->findClienteHasUsuario(
                        [
                            'ClientesHasUsuarios.usuarios_id' => $usuario['id'],
                            'ClientesHasUsuarios.tipo_perfil' => $usuario['tipo_perfil']
                        ]
                    );

                    // ele pode retornar vários (Caso de Admin Regional, então, pegar o primeiro

                    $cliente = null;

                    if (sizeof($cliente_has_usuario->toArray()) > 0) {
                        $cliente = $cliente_has_usuario->toArray()[0]->cliente;

                        // verifica se a unidade está ativa. Se está, a rede também está

                        if (!$cliente->ativado) {
                            $message = __("A unidade/rede à qual esta conta está vinculada está desativada. O acesso não é permitido.");
                            $actionNeeded = 2;
                        }
                    }
                }

                if ($actionNeeded == 0) {

                    if ($usuario['conta_ativa'] == 0) {
                        if ($usuario['tipo_perfil'] <= Configure::read('profileTypes')['UserProfileType']) {
                            $message = __("A conta encontra-se desativada. Somente seu administrador poderá reativá-la.");
                            $actionNeeded = 2;
                        } else {
                            $message = __("A conta encontra-se desativada. Para reativar, será necessário confirmar alguns dados.");
                            $actionNeeded = 1;
                        }
                    } elseif ($usuario['conta_bloqueada'] == true) {
                        $message = __("Sua conta encontra-se bloqueada no momento. Ela pode ter sido bloqueada por um administrador. Entre em contato com sua rede de abastecimento.");
                        $actionNeeded = 2;
                    } else {
                        $tentativas_login = $usuario['tentativas_login'];
                        $ultima_tentativa_login = $usuario['ultima_tentativa_login'];

                        if (!is_null($tentativas_login) && !is_null($ultima_tentativa_login)) {
                            $format = 'Y-m-d H:i:s';


                            $fromTime = strtotime($ultima_tentativa_login->format($format));

                            $toTime = strtotime(date($format));

                            $diff = round(abs($fromTime - $toTime) / 60, 0);

                            if ($tentativas_login >= 5 && ($diff < 10)) {
                                $message = __('Você já tentou realizar 5 tentativas, é necessário aguardar mais {0} minutos antes da próxima tentativa', (10 - (int)$diff));

                                $actionNeeded = 3;
                            }
                        }
                    }
                }
            }

            $result = ['message' => $message, 'actionNeeded' => $actionNeeded];

            return $result;
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Encontra usuario por tipo
     *
     * @return void
     * @author
     */
    public function findUsuariosByType($type)
    {
        try {
            return $this->_getUsuarioTable()
                ->find('all')
                ->where(['tipo_perfil' => $type]);
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Usuarios::getFuncionarioFicticio
     *
     * Obtem funcionário fictício utilizado para Vendas em Mobile API
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/08
     *
     * @return Usuario $usuario
     */
    public function getFuncionarioFicticio()
    {
        try {
            return $this->_getUsuarioTable()->find('all')
                ->where(['tipo_perfil' => (int)Configure::read("profileTypes")["DummyWorkerProfileType"]])->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao consultar usuários: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Encontra usuario por Id
     *
     * @param int $id Id do usuário
     *
     * @return entity\usuario $usuario
     *
     * @author Gustavo Souza Gonçalves
     */
    public function getUsuarioById($id = null)
    {
        try {
            return $this->_getUsuarioTable()->get($id);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Encontra usuario por cpf
     *
     * @param string $cpf              CPF do usuário
     * @param array  $where_conditions Condições Extras
     *
     * @return entity\usuario $usuario
     */
    public function getUsuarioByCPF(
        $cpf = null,
        int $redesId = null,
        array $clientesIds = array(),
        bool $filtrarPorFuncionarios = false,
        array $where_conditions = array()
    ) {
        try {
            $conditions = [];

            $usuariosIds = array();

            // Faz pesquisa por Id da rede se for informado.

            if (!empty($redesId) && $redesId > 0) {
                $redeHasClienteTable = TableRegistry::get("RedesHasClientes");

                $clientesIds = $redeHasClienteTable->getClientesIdsFromRedesHasClientes($redesId);
            }

            if (sizeof($clientesIds) > 0) {
                $clientesHasUsuariosTable = TableRegistry::get("ClientesHasUsuarios");

                $clientesHasUsuariosWhere = array("clientes_id in " => $clientesIds);

                $usuariosIdsQuery = $clientesHasUsuariosTable->findClienteHasUsuario($clientesHasUsuariosWhere)->toArray();

                foreach ($usuariosIdsQuery as $clienteHasUsuario) {
                    $usuariosIds[] = $clienteHasUsuario["usuarios_id"];
                }
            }

            foreach ($where_conditions as $condition) {
                $conditions[] = $condition;
            }

            if ($filtrarPorFuncionarios) {
                $conditions[] = array(
                    "tipo_perfil >= " => Configure::read("profileTypes")["AdminNetworkProfileType"],
                    "tipo_perfil < " => Configure::read("profileTypes")["UserProfileType"]
                );
            } else {
                $conditions[] = array("tipo_perfil" => Configure::read("profileTypes")["UserProfileType"]);
            }

            // Filtra pelos usuários (pois usou filtro da rede)
            if (sizeof($usuariosIds) > 0) {
                $conditions[] = array("id in " => $usuariosIds);
            }

            $conditions[] = array('cpf like ' => '%' . $this->cleanNumber($cpf) . '%');

            $usuario = $this->_getUsuarioTable()
                ->find('all')
                ->where($conditions)
                ->first();

            return $usuario;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Encontra usuario por e-mail
     *
     * @param string $email Email do usuário
     *
     * @return entity\usuario $usuario
     * @author Gustavo Souza Gonçalves
     */
    public function getUsuarioByEmail($email = null)
    {
        try {
            return $this->_getUsuarioTable()
                ->find('all')
                ->where(['email like ' => $email])->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Encontra usuario por Documento Estrangeiro
     *
     * @param string $doc_estrangeiro  Documento Estrangeiro do usuário
     * @param array  $whereConditions  Condições extras
     *
     * @return entity\usuario $usuario
     * @author Gustavo Souza Gonçalves
     */
    public function getUsuariosByDocumentoEstrangeiro(string $doc_estrangeiro = null, int $redesId = null, array $clientesIds = array(), bool $filtrarPorFuncionarios = false, array $whereConditions = [])
    {
        try {
            $conditions = [];

            $usuariosIds = array();

            // Faz pesquisa por Id da rede se for informado.

            if (!empty($redesId) && $redesId > 0) {
                $redeHasClienteTable = TableRegistry::get("RedesHasClientes");

                $clientesIds = $redeHasClienteTable->getClientesIdsFromRedesHasClientes($redesId);
            }

            if (sizeof($clientesIds) > 0) {
                $clientesHasUsuariosTable = TableRegistry::get("ClientesHasUsuarios");

                $clientesHasUsuariosWhere = array("clientes_id in " => $clientesIds);

                $usuariosIdsQuery = $clientesHasUsuariosTable->findClienteHasUsuario($clientesHasUsuariosWhere)->toArray();

                foreach ($usuariosIdsQuery as $clienteHasUsuario) {
                    $usuariosIds[] = $clienteHasUsuario["usuarios_id"];
                }
            }

            foreach ($whereConditions as $key => $condition) {
                $conditions[] = $condition;
            }

            if (sizeof($usuariosIds) > 0) {
                $conditions[] = array("id in " => $usuariosIds);
            }

            if ($filtrarPorFuncionarios) {
                $conditions[] = array(
                    "tipo_perfil >= " => Configure::read("profileTypes")["AdminNetworkProfileType"],
                    "tipo_perfil < " => Configure::read("profileTypes")["UserProfileType"]
                );
            } else {
                $conditions[] = array("tipo_perfil" => Configure::read("profileTypes")["UserProfileType"]);
            }

            $conditions[] = array('doc_estrangeiro like ' => '%' . $doc_estrangeiro . '%');

            $usuarios = $this->_getUsuarioTable()
                ->find('all')
                ->where($conditions);

            // DebugUtil::printArray($usuarios->toArray(), false);
            return $usuarios;
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem todos os usuários por Name
     *
     * @param string $nome             Nome do usuário
     * @param int    $redesId          Id da rede à filtrar
     * @param array  $clientesIds      Array de Id de Clientes à filtrar
     * @param array  $where_conditions Condições extras
     *
     * @return entity\usuario $usuario
     **/
    public function getUsuariosByName(string $nome, int $redesId = null, array $clientesIds = array(), bool $filtrarPorFuncionarios = false, array $where_conditions = array())
    {
        try {
            $conditions = [];

            // Se informar a rede, a pesquisa de unidades da rede será desconsiderada
            $usuariosIds = array();

            if (!empty($redesId) && $redesId > 0) {
                $redeHasClienteTable = TableRegistry::get("RedesHasClientes");

                $redeHasClientesQuery = $redeHasClienteTable->getAllRedesHasClientesIdsByRedesId($redesId);

                $clientesIds = array();

                foreach ($redeHasClientesQuery->toArray() as $key => $value) {
                    $clientesIds[] = $value["clientes_id"];
                }
            }
            if (sizeof($clientesIds) > 0) {
                $clientesHasUsuariosTable = TableRegistry::get("ClientesHasUsuarios");

                $clienteHasUsuarioConditions = array(["clientes_id in " => $clientesIds]);
                $usuariosIdsResult = $clientesHasUsuariosTable->findClienteHasUsuario($clienteHasUsuarioConditions)->toArray();

                foreach ($usuariosIdsResult as $result) {
                    $usuariosIds[] = $result["usuarios_id"];
                }
            }

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            if ($filtrarPorFuncionarios) {
                $conditions[] = array(
                    "tipo_perfil >= " => Configure::read("profileTypes")["AdminNetworkProfileType"],
                    "tipo_perfil < " => Configure::read("profileTypes")["UserProfileType"]
                );
            } else {
                $conditions[] = array("tipo_perfil" => Configure::read("profileTypes")["UserProfileType"]);
            }

            if (sizeof($usuariosIds) > 0) {
                array_push($conditions, ["id in " => $usuariosIds]);
            }

            array_push($conditions, ['nome like ' => "%" . $nome . "%"]);

            $usuarios = $this->_getUsuarioTable()
                ->find('all')
                ->where($conditions);

            return $usuarios;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage());

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * UsuariosTable::getUsuariosByProfileType
     *
     * Busca usuários por tipo de perfil
     *
     * @param integer $tipoPerfil Tipo de Perfil
     * @return \App\Model\Entity\Usuarios[]
     */
    public function getUsuariosByProfileType(int $tipoPerfil, int $limit = 999)
    {
        try {
            $usuarios = $this->_getUsuarioTable()
                ->find('all')
                ->where(['tipo_perfil' => $tipoPerfil]);

            if (!isset($limit)) {
                $limit = 999;
            }

            if ($limit == 1) {
                return $usuarios->first();
            }

            return $usuarios->limit($limit);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao consultar usuários: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Busca usuários aguardando aprovação
     *
     * @param array $where_conditions Array de condições
     *
     * @return App\Model\Entity\Usuarios $usuarios
     */
    public function findUsuariosAwaitingApproval(array $where_conditions = [])
    {
        try {
            $conditions = [];

            foreach ($where_conditions as $key => $value) {
                array_push($conditions, $condition);
            }

            array_push($conditions, ['aguardando_aprovacao' => true]);

            return $this->_getUsuarioTable()->find('all')
                ->where($conditions);
                // ->contain('ClientesHasUsuarios.Clientes');
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Encontra usuario aguardando reset de senha
     *
     * @param string   $tokenSenha         token de senha
     * @param dateTime $dataExpiracaoToken data de expiração do token
     *
     * @return entity\usuario $usuario
     */
    public function findUsuarioAwaitingPasswordReset($tokenSenha = null, $dataExpiracaoToken = null)
    {
        try {
            return $this->_getUsuarioTable()
                ->find('all')
                ->where([
                    'token_senha' => $tokenSenha,
                    'data_expiracao_token >= ' => $dataExpiracaoToken
                ])
                ->first();
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Busca todos os usuários conforme parâmetros
     *
     * @param array $whereConditions Condições
     *
     * @return array $usuarios Lista de Usuários
     */
    public function findAllUsuarios(array $whereConditions = [])
    {
        try {
            return $this->_getUsuarioTable()
                ->find('all')
                ->where($whereConditions)
                ->contain(['ClientesHasUsuarios.Cliente.RedeHasCliente.Redes']);

        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Busca todos os usuários conforme parâmetros
     *
     * @param array $redesConditions    Condições de Redes
     * @param array $usuariosConditions Condições de Usuários
     *
     * @return array $usuarios Lista de Usuários
     */
    public function findAllUsuariosByRede(int $redesId, array $usuariosConditions = [])
    {
        try {
            $redes = $this->_getUsuarioTable()->ClientesHasUsuarios->Clientes->RedeHasCliente->Redes->getAllRedes('all', ['id' => $redesId]);

            $redes = $redes->toArray();

            $clientes_ids = [];

            foreach ($redes as $key => $rede) {
                foreach ($rede->redes_has_clientes as $key => $value) {
                    $clientes_ids[] = $value->clientes_id;
                }
            }

            $clientesHasUsuariosTable = TableRegistry::get('Clientes_Has_Usuarios');

            $usuariosIdArray = [];

            $clientesHasUsuarios = $clientesHasUsuariosTable->find('all')
                ->where(
                    [
                        'clientes_id in ' => $clientes_ids
                    ]
                )->toArray();

            $usuariosIdsArray = [];

            foreach ($clientesHasUsuarios as $key => $clienteHasUsuario) {
                $usuariosIdsArray[] = $clienteHasUsuario->usuarios_id;
            }

            $conditions = [];

            $conditions[] = ['id in' => $usuariosIdsArray];

            foreach ($usuariosConditions as $key => $value) {
                $conditions[] = $value;
            }

            $usuarios = null;

            if (sizeof($usuariosIdsArray) > 0) {
                $usuarios = $this->_getUsuarioTable()->find('all')
                    ->where($conditions)
                    ->contain('ClientesHasUsuarios');
            }

            return $usuarios;

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtêm todos os funcionários de uma rede
     *
     * @param int   $redes_id         Id da rede
     * @param array $clientes_ids     Array de Id de clientes (Se for toda a rede, passar todos os ids de clientes_ids)
     * @param array $where_conditions Condições extras de pesquisa
     *
     * @return entity\usuarios[] $usuarios
     */
    public function findFuncionariosRede(int $redes_id, array $clientes_ids, array $where_conditions = array())
    {
        try {

            // condições de pesquisa
            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            /**
             * Pega o usuário informado e vê qual é a permissão dele.
             * Admin:
             * RTI/de Rede -> lista tudo
             * Admin regional -> lista os quais se encontra alocado
             * Admin comum/Gerente -> lista somente da sua unidade
             */

            $redes_table = TableRegistry::get("Redes");

            $rede = $redes_table->find('all')
                ->where(['Redes.id' => $redes_id])
                ->contain(['RedesHasClientes.Clientes.ClientesHasUsuarios'])
                ->first();

            // se a pesquisa é pela rede inteira, pega o id
            // de usuários de todas as unidades às quais o
            // usuário tem acesso (informado na chamada)

            $usuarios_ids_array = $this->_getUsuarioTable()->ClientesHasUsuarios->find('all')
                ->where(['ClientesHasUsuarios.clientes_id IN ' => $clientes_ids])
                ->contain(['Usuarios'])
                ->select(['ClientesHasUsuarios.usuarios_id'])->toArray();

            $usuarios_ids = [];

            foreach ($usuarios_ids_array as $key => $value) {
                $usuarios_ids[] = $value['usuarios_id'];
            }

            if (sizeof($usuarios_ids) == 0) {
                $usuarios_ids[] = 0;
            }

            array_push($conditions, ['Usuarios.id IN ' => $usuarios_ids]);

            array_push($conditions, ['Usuarios.tipo_perfil <=' => Configure::read('profileTypes')['WorkerProfileType']]);

            $usuarios = $this->_getUsuarioTable()->find('all')
                ->where($conditions)
                ->contain(['ClientesHasUsuarios.Cliente']);

            return $usuarios;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: {0}, em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtêm todos os usuários de um cliente (loja)
     *
     * @param array $where_conditions Array de condições
     *
     * @return array lista de usuários
     */
    public function getUsuarios(array $where_conditions)
    {
        try {
            $conditions = [];

            $tipo_perfil = Configure::read('profileTypes')['UserProfileType'];
            array_push(
                $conditions,
                [
                    'ClientesHasUsuarios.tipo_perfil '
                        => $tipo_perfil
                ]
            );

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            $usuarios = $this->_getUsuarioTable()->find('all')
                ->where($conditions)
                ->group(['usuarios.id'])
                ->join(
                    [
                        'ClientesHasUsuarios'
                            =>
                            [
                            'table' => 'clientes_has_usuarios',
                            'type' => 'inner',
                            'conditions' => [
                                'ClientesHasUsuarios.usuarios_id = usuarios.id'
                            ]
                        ]
                    ]
                );


            return $usuarios;
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    public function getUsuariosAssiduosClientes(
        array $clientesIds = array(),
        int $usuariosId = null,
        string $nome = null,
        string $cpf = null,
        string $veiculo = null,
        string $documentoEstrangeiro = null,
        int $status = null,
        bool $assiduidade = null,
        bool $agrupamento = false,
        string $dataInicio = null,
        string $dataFim = null
    ) {
        if (sizeof($clientesIds) == 0) {
            throw new Exception("Não foi informado o posto de atendimento para pesquisa!");
        }

        $whereConditions = array();

        if (!empty($usuariosId) && $usuariosId > 0) {
            $whereConditions[] = array("Usuarios.id" => $usuariosId);
        } else if (!empty($nome)) {
            $whereConditions[] = array("Usuarios.nome like '%$nome%'");
        }

        if (!empty($cpf)) {
            $whereConditions[] = array("Usuarios.cpf like '%$cpf%'");
        }

        if (!empty($veiculo)) {
            $whereConditions[] = array("Veiculos.placa like '%$veiculo%'");
        }

        if (!empty($documentoEstrangeiro)) {
            $whereConditions[] = array("Usuarios.doc_estrangeiro like '%$documentoEstrangeiro%'");
        }

        if (strlen($status) > 0) {
            $whereConditions[] = array("Usuarios.conta_ativa" => $status);
        }

        if (!empty($dataInicial)) {
            $whereConditions[] = array("DATE_FORMAT(ClienteHasUsuario.audit_insert, '%Y-%m-%d') >= '$dataInicial'");
        }

        if (!empty($dataFinal)) {
            $whereConditions[] = array("DATE_FORMAT(ClienteHasUsuario.audit_insert, '%Y-%m-%d') <= '$dataFinal'");
        }

        $whereConditions[] = array("ClienteHasUsuario.clientes_id in " => $clientesIds);
        $whereConditions[] = array("Usuarios.conta_ativa" => 1);

        // select
        $selectArray = array(

            "usuariosId" => "PontuacaoComprovante.usuarios_id",
            "clientesId" => "PontuacaoComprovante.clientes_id",
            "statusConta" => "Usuarios.conta_ativa",
            "nome" => "Usuarios.nome",
            "cpf" => "Usuarios.cpf",
            "docEstrangeiro" => "Usuarios.doc_estrangeiro",
            // "dataNascimento" => "Usuarios.data_nasc",
            "quantidadeMes" => $this->find()->func()->count("PontuacaoComprovante.usuarios_id"),
        );

        $groupByConditions = array();
        $groupByConditions[] = "usuariosId";
        $groupByConditions[] = "clientesId";

        // if ($agrupamento) {
        $selectArray["mes"] = "MONTH(PontuacaoComprovante.data)";
        $selectArray["ano"] = "YEAR(PontuacaoComprovante.data)";

        $groupByConditions[] = "ano";
        $groupByConditions[] = "mes";
        // }

        // ResponseUtil::success($whereConditions);

        // Obtem os ids de usuarios
        $usuariosCliente = $this->find("all")
            ->select($selectArray)
            ->where($whereConditions)
            ->contain(array("ClienteHasUsuario", "UsuariosHasVeiculos.Veiculos", "PontuacaoComprovante"))
            ->group($groupByConditions)
            ->order(array("PontuacaoComprovante.usuarios_id" => "DESC"))
            ->toArray();

        if (strlen($assiduidade) > 0) {
            $whereConditions[] = array("Usuario.conta_ativa" => $status);
        }

        // ResponseUtil::success($usuariosCliente);

        /**
         * Se agrupamento estiver habilitado, ele vai buscar
         * todos os usuários normalmente... A diferença, é que
         * ele vai ter que ver qual é a somatória para cada usuário
         *
         * Se não for para fazer o agrupamento, eu não preciso mostrar
         * os detalhes do usuário
         * (pois estes detalhes estarão disponíveis em um modal)
         */
        if ($agrupamento) {
            $usuariosListTemp = array();

            $usuariosIdTemp = 0;
            $totalUsuarios = sizeof($usuariosCliente);

            // Variáveis para cálculo de assiduidade por mês
            $totalAssiduidade = 0;
            $contadorUsuarioMes = 0;
            $usuarioTemp = array();
            $podeAdicionar = false;

            for ($index = 0; $index < sizeof($usuariosCliente); $index++) {

                $usuario = $usuariosCliente[$index];

                if ($usuariosIdTemp != $usuario["usuariosId"]) {
                    $usuariosIdTemp = $usuario["usuariosId"];
                    $totalAssiduidade = 0;
                    $contadorUsuarioMes = 0;
                }

                // É o primeiro índice e tem mais de um registro?
                if ($index == 0 && $totalUsuarios > 1) {

                    $proximoUsuario = $usuariosCliente[$index + 1];

                    // O id do próximo usuário é igual o do atual?
                    // Se sim, então não adiciona
                    if ($usuariosIdTemp == $proximoUsuario["usuariosId"]) {
                        $podeAdicionar = false;
                    } else {
                        $podeAdicionar = true;
                    }
                }

                if ($index == $totalUsuarios - 1) {
                    $podeAdicionar = true;
                }

                // É o primeiro registro, então já adiciona.
                $totalAssiduidade += $usuario["quantidadeMes"];
                $contadorUsuarioMes += 1;

                if ($podeAdicionar) {
                    $usuarioTemp["nome"] = $usuario["nome"];
                    $usuarioTemp["totalAssiduidade"] = $totalAssiduidade;
                    $usuarioTemp["mediaAssiduidade"] = number_format((float)$totalAssiduidade / $contadorUsuarioMes, 2, '.', '');
                    $usuarioTemp["statusConta"] = $usuario["statusConta"];
                    $usuarioTemp["nome"] = $usuario["nome"];
                    $usuarioTemp["cpf"] = $usuario["cpf"];
                    $usuarioTemp["docEstrangeiro"] = $usuario["docEstrangeiro"];

                    $usuariosListTemp[] = $usuarioTemp;
                }

                $podeAdicionar = false;
            }

            $usuariosCliente = $usuariosListTemp;
        }

        return $usuariosCliente;
    }


    /**
     * Undocumented function
     *
     * @param int $clientes_id
     * @param array $where_conditions
     *
     * @return void
     */
    public function getPendingWorkersNetwork(int $clientes_id, array $where_conditions = array())
    {
        try {
            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            array_push($conditions, ['chu.id is ' => null]);

            $usuarios = $this->_getUsuarioTable()
                ->find('all')
                ->where($conditions)
                ->join(
                    [
                        'ClientesHasUsuarios' =>
                            [
                            'table' => 'clientes_has_usuarios',
                            'alias' => 'chu',
                            'type' => 'left',
                            'conditions' =>
                                [
                                'chu.usuarios_id = Usuarios.id'
                            ],
                            'where' =>
                                [
                                'chu.id' => null
                            ]

                        ]
                    ]
                );

            return $usuarios;
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }



    /**
     * Obtem usuários que estão associados à um 'cliente'
     *
     * @param Entity\Cliente $client           Objeto cliente
     * @param int            $minProfileType   Mínimo tipo de perfil
     * @param int            $maxProfileType   Máximo tipo de perfil
     * @param array          $where_conditions Condições extras
     *
     * @return List<\Entity\Usuarios> $usuarios
     */
    public function getUsuariosAssociatedWithClient($client = null, $minProfileType = null, $maxProfileType = null, array $where_conditions = [])
    {
        try {
            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            $id = isset($client->matriz_id) ? $client->matriz_id : $client->id;

            $usuarios = null;

            if (is_null($client->matriz_id)) {
                array_push($conditions, ['Usuarios.matriz_id' => $client->id]);

                $usuarios = $this->_getUsuarioTable()->find('all')
                    ->where($conditions)
                    ->join(['ClientesHasUsuarios' =>
                        [
                        'table' => 'clientes_has_usuarios',
                        'alias' => 'chu',
                        'type' => 'left',
                        'conditions' =>
                            [
                            'usuarios.id = chu.usuarios_id',
                        ]

                    ]])
                    ->select([
                        'usuarios.id',
                        'usuarios.matriz_id',
                        'usuarios.tipo_perfil',
                        'usuarios.nome',
                        'usuarios.cpf',
                        'usuarios.sexo',
                        'usuarios.data_nasc',
                        'usuarios.email',
                        'usuarios.senha',
                        'usuarios.telefone',
                        'usuarios.endereco',
                        'usuarios.endereco_numero',
                        'usuarios.endereco_complemento',
                        'usuarios.bairro',
                        'usuarios.municipio',
                        'usuarios.estado',
                        'usuarios.cep',
                        'usuarios.audit_insert',
                        'usuarios.audit_update',
                        'chu.id',
                        'chu.matriz_id',
                        'chu.clientes_id',
                        'chu.usuarios_id'
                    ]);
            } else {
                array_push($conditions, ['usuarios.matriz_id' => $id]);

                $usuarios = $this->_getUsuarioTable()->find('all')
                    ->where($conditions)
                    ->join(['ClientesHasUsuarios' =>
                        [
                        'table' => 'clientes_has_usuarios',
                        'alias' => 'chu',
                        'type' => 'left',
                        'conditions' =>
                            [
                            'usuarios.id = chu.usuarios_id',
                        ]
                    ]])
                    ->select([
                        'usuarios.id',
                        'usuarios.matriz_id',
                        'usuarios.tipo_perfil',
                        'usuarios.nome',
                        'usuarios.cpf',
                        'usuarios.sexo',
                        'usuarios.data_nasc',
                        'usuarios.email',
                        'usuarios.senha',
                        'usuarios.telefone',
                        'usuarios.endereco',
                        'usuarios.endereco_numero',
                        'usuarios.endereco_complemento',
                        'usuarios.bairro',
                        'usuarios.municipio',
                        'usuarios.estado',
                        'usuarios.cep',
                        'usuarios.audit_insert',
                        'usuarios.audit_update',
                        'chu.id',
                        'chu.matriz_id',
                        'chu.clientes_id',
                        'chu.usuarios_id'
                    ]);
            }

            if (isset($minProfileType)) {
                $usuarios->where(['tipo_perfil >= ' => $minProfileType]);
            }

            if (isset($maxProfileType)) {
                $usuarios->where(['tipo_perfil <= ' => $maxProfileType]);
            }

            return $usuarios;
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Altera estado de conta ativa do usuário informado
     *
     * @param int  $usuarios_id Id de usuário
     * @param bool $conta_ativa Conta Ativa (True/False)
     *
     * @return bool $usuario Registro atualizado
     */
    public function changeAccountEnabledByUsuarioId(int $usuarios_id, bool $conta_ativa)
    {
        try {
            $usuario = $this->getUsuarioById($usuarios_id);

            $usuario['conta_ativa'] = $conta_ativa;

            return $this->_getUsuarioTable()->save($usuario);
        } catch (\Exception $e) {
            $stringError = __("Erro ao reativar conta: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Reativa conta para usuário
     *
     * @param int $id Id da conta
     *
     * @return void
     */
    public function reativarConta(int $id)
    {
        try {
            $usuario = $this->getUsuarioById($id);

            $usuario['conta_ativa'] = true;

            $this->addUpdateUsuario($usuario);
        } catch (\Exception $e) {
            $stringError = __("Erro ao reativar conta: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Atualiza login sem sucesso
     *
     * @param entity\usuario $usuario
     * @param boolean $type
     * @return string $message
     * @author Gustavo Souza Gonçalves
     */
    public function updateLoginRetry($usuario = null, $type)
    {
        try {
            $message = '';

            $usuario = $this->getUsuarioByEmail($usuario['email']);

            if ($type) {
            } else {
                $tentativas_login = $usuario['tentativas_login'];
                $ultima_tentativa_login = $usuario['ultima_tentativa_login'];

                $format = 'Y-m-d H:i:s';

                if (is_null($ultima_tentativa_login)) {
                    $ultima_tentativa_login = new \DateTime('now');
                }


                $fromTime = strtotime($ultima_tentativa_login->format($format));

                $toTime = strtotime(date($format));

                $diff = round(abs($fromTime - $toTime) / 60, 0);

                if ($tentativas_login >= 5 && ($diff < 10)) {
                    $message = __('Você já tentou realizar 5 tentativas, é necessário aguardar mais {0} minutos antes da próxima tentativa', (10 - (int)$diff));
                } else {
                    if ($tentativas_login >= 5) {
                        $tentativas_login = 0;
                    } else {
                        $ultima_tentativa_login = date("Y-m-d H:i:s");
                        $usuario['ultima_tentativa_login'] = $ultima_tentativa_login;
                    }

                    $tentativas_login = $tentativas_login + 1;

                    $usuario['tentativas_login'] = $tentativas_login;

                    $message = __("usuario ou senha ínvalidos, tente novamente");
                    $usuario = $this->addUpdateUsuario($usuario);
                }
            }

            return $message;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Seta token e data de expiração para usuário requisição reset de senha
     *
     * @param string $usuarioId
     * @param string $tokenSenha
     * @param datetime $dataExpiracaoToken
     * @return void
     * @author Gustavo Souza Gonçalves
     */
    public function setUsuarioTokenPasswordRequest($usuarioId = null, $tokenSenha = null, $dataExpiracaoToken = null)
    {
        try {
            $usuario = $this->getUsuarioById($usuarioId);

            $usuario->token_senha = $tokenSenha;
            $usuario->data_expiracao_token = $dataExpiracaoToken;

            $this->_getUsuarioTable()->addUpdateUsuario($usuario);

            return true;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Desabilita os usuários de um cliente
     *
     * @param int   $clientes_id      Id de Cliente
     * @param array $where_conditions Condições extras
     *
     * @return boolean
     */
    public function disableUsuariosOfCliente(int $clientes_id, array $where_conditions = [])
    {
        try {

            $clientes_has_usuarios = $this->_getUsuarioTable()->ClientesHasUsuarios->find('all')
                ->where(['clientes_id' => $clientes_id])->select(['id']);

            $clientes_has_usuarios_ids = [];

            foreach ($clientes_has_usuarios as $key => $value) {
                array_push($clientes_has_usuarios_ids, $value['id']);
            }

            $conditions = [];

            foreach ($where_conditions as $key => $value) {
                array_push($conditions, $value);
            }

            array_push($conditions, ['id in ' => $clientes_has_usuarios_ids]);

            if (sizeof($clientes_has_usuarios_ids) > 0) {

                return $this->updateAll(
                    [
                        'conta_bloqueada' => true
                    ],
                    $conditions
                );
            } else {
                return true;
            }

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    /* ------------------------ Delete ------------------------ */

    /**
     * Undocumented function
     *
     * @param array $clientes_ids     Ids de Clientes
     * @param array $where_conditions Condições extras
     *
     * @return void
     */
    public function deleteAllUsuariosByClienteIds(array $clientes_ids, array $where_conditions = [])
    {
        try {

            // pegar id de brindes que estão vinculados em um cliente

            $usuarios_clientes_id = $this->_getUsuarioTable()->ClientesHasUsuarios->find('all')
                ->where(['clientes_id in' => $clientes_ids])
                ->select(['usuarios_id']);

            $usuarios_clientes_ids = [];

            foreach ($usuarios_clientes_id as $key => $value) {
                array_push($usuarios_clientes_ids, $value['usuarios_id']);
            }

            if (sizeof($usuarios_clientes_ids) > 0) {

                $conditions = [];

                foreach ($where_conditions as $key => $value) {
                    $conditions[] = $value;
                }

                $conditions[] = ['id in' => $usuarios_clientes_ids];

                return $this->_getUsuarioTable()
                    ->deleteAll($conditions);
            } else {
                return true;
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * ------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------
     */

    /**
     * Verifica regras de senha para usuário cliente
     *
     * @param type $password
     * @param array $context
     * @return boolean
     */
    public function checkPasswordUsuario($password, array $context)
    {
        if (($context['data']['tipo_perfil'] == 6) && (preg_match("#[0-9]#", $password) && strlen($password) != 4)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Verifica regras de senha para funcionário
     *
     * @param type $password
     * @param array $context
     * @return boolean
     */
    public function checkPasswordWorker($password, array $context)
    {
        if (($context['data']['tipo_perfil'] < 6) && (strlen($password) != 8)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks password for a single instance of each:
     * number, uppercase, lowercase, and special character
     *
     * @param type $password
     * @param array $context
     * @return boolean
     */
    public function checkCharacters($password, array $context)
    {
                    // number
        if (!preg_match("#[0-9]#", $password)) {
            return false;
        }
                    // Uppercase
        if (!preg_match("#[A-Z]#", $password)) {
            return false;
        }
                    // lowercase
        if (!preg_match("#[a-z]#", $password)) {
            return false;
        }
                    // special characters
        if (!preg_match("#\W+#", $password)) {
            return false;
        }
        return true;
    }

    /**
     * Format usuario to insert/update into database
     *
     * @return Entity\Usuarios $usuario
     * @author Gustavo Souza Gonçalves
     */
    public function formatUsuario($id = null, $usuario = null)
    {
        if ($id > 0) {
            $usuario->id = $id;
        }

        if (isset($usuario['matriz_id'])) {
            $usuario->matriz_id = $usuario['matriz_id'];
        }

        $usuario->tipo_perfil = $usuario['tipo_perfil'];
        $usuario->nome = $usuario['nome'];

        if (strlen($usuario['cpf']) > 0) {
            $usuario->cpf = $this->cleanNumber($usuario['cpf']);
        }

        $usuario->sexo = $usuario['sexo'];

        $usuario->data_nasc = date_format(date_create_from_format('d/m/Y', $usuario['data_nasc']->format('d/m/Y')), 'Y-m-d');
        $usuario->email = $usuario['email'];

        $usuario->telefone = isset($usuario['telefone']) ? $this->cleanNumber($usuario['telefone']) : null;
        $usuario->endereco = isset($usuario['endereco']) ? $usuario['endereco'] : null;
        $usuario->endereco_numero = isset($usuario['endereco_numero']) ? $usuario['endereco_numero'] : null;
        $usuario->endereco_complemento = isset($usuario['endereco_complemento']) ? $usuario['endereco_complemento'] : null;
        $usuario->bairro = isset($usuario['bairro']) ? $usuario['bairro'] : null;
        $usuario->municipio = isset($usuario['municipio']) ? $usuario['municipio'] : null;
        $usuario->estado = isset($usuario['estado']) ? $usuario['estado'] : null;
        $usuario->cep = isset($usuario['cep']) ? $usuario['cep'] : null;

        return $usuario;
    }
}
