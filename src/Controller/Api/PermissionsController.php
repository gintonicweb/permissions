<?php
namespace Permissions\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;

/**
 * Threads Controller
 *
 * @property \Messages\Model\Table\ThreadsTable $Threads
 */
class PermissionsController extends AppController
{
    public $paginate = [
        'limit' => 25,
        'order' => [
            'Users.username' => 'asc'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Acl.Acl');
    }

    public function actions($role = null)
    {
        $arosTable = TableRegistry::get('Acl.Aros');
        if(isset($this->request->data['aro'])) {
            $role= $this->request->data['aro'];
        }
        if ($role != null) {
            $aro = $arosTable->findByAlias($role)->first();
        } else {
            $aro = $arosTable->find()->first();
        }

        // todo: throw error maybe?
        
        $aros = $arosTable->find()->where(['Aros.alias IS NOT' => null])->all();
        $acos = TableRegistry::get('Permissions.Permissions')->acoList($aro);

        $this->set(compact('aros', 'acos', 'aro'));
        $this->set('_serialize', ['aros', 'acos', 'aro']);
    }

    public function setPermissions()
    {
        $aro = $this->request->data['aro'];
        $aco = $this->request->data['aco'];
        $state = $this->request->data['state'];

        $this->Acl->{$state}($aro, $aco);
        $this->setAction('actions');
    }
}
