<?php

namespace Permissions\Auth;

use Cake\Auth\BaseAuthorize;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Permissions\Model\Entity\Role;

class ConfigAuthorize extends BaseAuthorize
{
    /**
     * ComponentRegistry instance for getting more components.
     *
     * @var ComponentRegistry
     */
    protected $_defaultConfig = [
        'rolesModel' => 'Permissions.Roles',
        'defaultRole' => Role::USER,
        'adminRole' => Role::ADMIN,
    ];

    /**
     * Permissions array
     *
     * @var array
     */
    protected $_permission = [];

    /**
     * Roles table
     *
     * @var array
     */
    protected $_roles = [];

    /**
     * {@inheritDoc}
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->_permissions = $registry->getController()->permissions ?: [];
        $this->_controller = $registry->getController();
        $this->_roles = TableRegistry::get($this->config('rolesModel'));
    }

    /**
     * Get the role for user, fallback on default role if it's not set
     *
     * @param array $user Active user data
     * @return int
     */
    protected function _getRole($user)
    {
        $role = $this->_roles->find()
            ->where(['user_id' => $user['id']])
            ->first();
        return ($role) ? $role->role : $this->config('defaultRole');
    }

    /**
     * Permission check for given role on given action
     *
     * @param int $role Determined role for active user
     * @param string $action name of the requested action
     * @return bool
     */
    protected function _actionAllowed($role, $action)
    {
        if (!array_key_exists($role, $this->_permissions)) {
            return false;
        }

        if (!is_array($this->_permissions[$role])) {
            $this->_permissions[$role] = [$this->_permissions[$role]];
        }

        if (in_array('*', $this->_permissions[$role])) {
            return true;
        }

        return in_array($action, $this->_permissions[$role]);
    }

    /**
     * Verifies if at least one admin user exists
     *
     * @param int $role Determined role for active user
     * @param string $action name of the requested action
     * @return bool
     */
    protected function _adminExist($role, $action)
    {
        $role = $this->_roles->find()
            ->where(['role' => Role::ADMIN])
            ->first();
        return (bool)$role;
    }

    /**
     * Checks user authorization.
     *
     * @param array $user Active user data
     * @param \Cake\Network\Request $request Request instance.
     * @return bool
     */
    public function authorize($user, Request $request)
    {
        $role = $this->_getRole($user);
        if ($role === $this->config('adminRole')) {
            return true;
        }
        $action = $request->params['action'];
        if ($this->_actionAllowed($role, $action)) {
            return true;
        }
        if (!$this->_adminExist($role, $action)) {
            $this->_controller->Flash->warning('Warning : No admin role has been defined. The administration panel is world-accessible');
            return true;
        }
        return false;
    }
}
