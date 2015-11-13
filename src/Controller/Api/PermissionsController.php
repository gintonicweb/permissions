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
    /**
     * {@inherit}
     */
    public $paginate = [
        'limit' => 25,
        'order' => [
            'Users.username' => 'asc'
        ]
    ];

    /**
     * {@inherit}
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Acl.Acl');
    }

    /**
     * Retrieve permissions for a given role
     *
     * param string $role the aro alias
     */
    public function actions($role = null)
    {
        $arosTable = TableRegistry::get('Acl.Aros');
        if (isset($this->request->data['aro'])) {
            $role = $this->request->data['aro'];
        }
        if ($role != null) {
            $aro = $arosTable->findByAlias($role)->first();
        } else {
            $aro = $arosTable->find()->first();
        }

        $aros = $arosTable->find()->where(['Aros.alias IS NOT' => null])->all();
        $acos = TableRegistry::get('Permissions.Permissions')->acoList($aro);

        $this->set(compact('aros', 'acos', 'aro'));
        $this->set('_serialize', ['aros', 'acos', 'aro']);
    }

    /**
     * Retrieve permissions for a given role
     */
    public function setPermissions()
    {
        $aro = $this->request->data['aro'];
        $aco = $this->request->data['aco'];
        $state = $this->request->data['state'];

        $this->Acl->{$state}($aro, $aco);
        $this->setAction('actions');
    }
}
