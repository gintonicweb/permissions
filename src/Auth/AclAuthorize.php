<?php

namespace Permissions\Auth;

use Acl\Auth\ActionsAuthorize;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;

/**
 * Base authorization adapter for other adapter of this plugin.
 */
class AclAuthorize extends ActionsAuthorize
{
    /**
     * Authorize a user using the AclComponent.
     *
     * @param array $user The user to authorize
     * @param \Cake\Network\Request $request The request needing authorization.
     * @return bool
     */
    public function authorize($user, Request $request)
    {
        if (parent::authorize($user, $request)) {
            //return true;
        }

        $actionAlias = rtrim($this->config()['actionPath'], '/');
        $permissionsTable = TableRegistry::get('Acl.Permissions');
        $result = $permissionsTable->find()
            ->where([
                '_create' => 1,
                '_read' => 1,
                '_update' => 1,
                '_delete' => 1,
            ])
            ->matching('Acos', function ($q) use ($actionAlias) {
                return $q->where(['alias' => $actionAlias]);
            })
            ->matching('Aros', function ($q) {
                return $q->where(['Aros.parent_id IS NULL']);
            })->count();

        if ($result === 0) {
            $this->_registry->Flash->warning('Warning : No admin role has been defined. The administration panel is world-accessible');
            return true;
        }
        return false;
    }
}
