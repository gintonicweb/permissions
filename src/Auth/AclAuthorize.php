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
     * Authorize a user. This method expects the role admin to exist in the
     * aros table. Admin users are allowed an authorization bypass
     *
     * @param array $user The user to authorize
     * @param \Cake\Network\Request $request The request needing authorization.
     * @return bool
     */
    public function authorize($user, Request $request)
    {
        // Regular permissions processing
        if (parent::authorize($user, $request)) {
            //return true;
        }

        // Non-logged users are not allowed in
        $arosTable = TableRegistry::get('Acl.Aros');
        $aro = $arosTable->find()
            ->where([
                'model' => 'Users',
                'foreign_key' => $user['id']
            ])
            ->first();
        if (is_null($aro)) {
            return false;
        }

        // Admins have a bypass and are allowed everything
        $role = $arosTable->get($aro->parent_id);
        if ($role->alias === 'admin') {
            return true;
        }

        // When there's no admin, everything is unlocked
        $count = $arosTable->find()
            ->where(['parent_id' => $role->id])
            ->count();

        if ($count == 0) {
            $this->_registry->Flash->warning('Warning : No admin role has been defined. The administration panel is world-accessible');
            return true;
        }

        // Default deny
        return false;
    }
}
