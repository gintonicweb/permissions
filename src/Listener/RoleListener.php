<?php

namespace Permissions\Listener;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class RoleListener implements EventListenerInterface
{
    /**
     * Callbacks definition
     */
    public function implementedEvents()
    {
        return [
            'Auth.afterIdentify' => 'afterIdentify',
        ];
    }
    /**
     * Automaticaly adds an aro for each subscribed user
     */
    public function afterIdentify(Event $event, array $user)
    {
        $rolesTable = TableRegistry::get('Permissions.Roles');
        $role = $rolesTable->find()->where(['user_id' => $user['id']])->first();
        $user['role'] = $role['role'];
        return $user;
    }
}
