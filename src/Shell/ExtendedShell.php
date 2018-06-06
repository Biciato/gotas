<?php
namespace App\Shell;

use Cake\Console\Shell;
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

class ExtendedShell extends Shell
{
    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadNecessaryModels(Configure::read('models'));

    }

    public function loadNecessaryModels($models = null)
    {
        foreach ($models as $key => $model) {
            $this->loadModel($model);
        }
    }
}

?>