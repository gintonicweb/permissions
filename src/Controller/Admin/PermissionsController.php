<?php
namespace Permissions\Controller\Admin;

use App\Controller\Admin\AppController;
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
    }

    /**
     * get the list of acos with the permissions for a given aro.
     */
    public function actions($role = null)
    {
        $arosTable = TableRegistry::get('Acl.Aros');
        if ($role != null) {
            $aro = $arosTable->findByAlias($role)->first();
        } else {
            $aro = $arosTable->find()->first();
        }
        $aros = $arosTable->find()
            ->where([
                'Aros.alias IS NOT' => null,
                'Aros.alias IS NOT' => 'admin',
            ])
            ->all();
        $acos = TableRegistry::get('Permissions.Permissions')->acoList($aro);
        $this->set(compact('aros', 'acos', 'aro', 'users'));
    }

    /**
     * like 'index', lists only roles from aros 
     */
    public function roles()
    {
        $arosTable = TableRegistry::get('Aros');
        $roles = $arosTable->find()
            ->where([
                'Aros.alias IS NOT' => null,
                'Aros.alias IS NOT' => 'admin',
            ])
            ->all();
        foreach ($roles as $role) {
            $role->count = $arosTable->find()
                ->where(['parent_id' => $role->id])
                ->count();
        }
        $this->set(compact('roles'));
    }
}
