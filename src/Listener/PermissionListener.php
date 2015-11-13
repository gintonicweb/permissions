<?php

namespace Permissions\Listener;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class PermissionListener implements EventListenerInterface
{

    /**
     * Callbacks definition
     */
    public function implementedEvents()
    {
        return [
            'Users.afterSignup' => 'createAros',
        ];
    }

    /**
     * Automaticaly adds an aro for each subscribed user
     */
    public function createAros($event)
    {
        $user = $event->subject();
        
        $arosTable = TableRegistry::get('Acl.Aros');
        $role = $arosTable->findByAlias('user')->first();
        $aro = $arosTable->newEntity();

        $aro->parent_id = $role->id;
        $aro->model = 'Users';
        $aro->foreign_key = $user->id;

        $aro = $arosTable->save($aro);
    }
}
