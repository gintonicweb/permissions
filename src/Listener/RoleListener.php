<?php

namespace Permissions\Listener;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Permissions\Model\Entity\Role;

class RoleListener implements EventListenerInterface
{
    /**
     * Callbacks definition
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Auth.afterIdentify' => 'afterIdentify',
            'Users.afterSignup' => 'afterSignup',
        ];
    }

    /**
     * Automaticaly adds an aro for each subscribed user
     *
     * @param Event $event After identify event
     * @param array $user user that has identified
     * @return array
     */
    public function afterIdentify(Event $event, array $user)
    {
        if ($event->result !== null) {
            $user = $event->result;
        }
        $rolesTable = TableRegistry::get('Permissions.Roles');
        $role = $rolesTable->find()->where(['user_id' => $user['id']])->first();
        $user['role'] = $role['role'] ?: Role::USER;
        return $user;
    }

    /**
     * Automaticaly adds an aro for each subscribed user
     *
     * @param Event $event After identify event
     * @param array $user user that has identified
     * @return void
     */
    public function afterSignup(Event $event, array $user)
    {
        $user = $event->subject->Auth->user();
        $user['role'] = Role::USER;
        $event->subject->Auth->setUser($user);
    }
}
