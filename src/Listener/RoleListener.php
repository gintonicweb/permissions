<?php

namespace Permissions\Listener;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Crud\Listener\BaseListener;
use Permissions\Model\Entity\Role;

class RoleListener extends BaseListener
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
            'Crud.afterRegister' => 'afterRegister',
        ];
    }

    /**
     * Automaticaly adds an aro for each subscribed user
     *
     * @param Event $event After identify event
     * @param array $user user that has identified
     * @return array
     */
    public function afterIdentify(Event $event, $user)
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
    public function afterRegister(Event $event)
    {
        $user = $this->_controller()->Auth->user();
        $user['role'] = Role::USER;
        $this->_controller()->Auth->setUser($user);
    }
}
