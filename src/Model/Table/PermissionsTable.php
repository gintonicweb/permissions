<?php
namespace Permissions\Model\Table;

use Acl\Model\Table\PermissionsTable as BaseTable;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * {@inherit}
 */
class PermissionsTable extends BaseTable
{

    /**
     * {@inherit}
     */
    public function initialize(array $config)
    {
        $this->table('aros_acos');
    }

    /**
     * Returns a nested list of every aco with the permissions for given aro
     *
     * param \Cake\ORM\Entity $aro the target of the aco list permissions
     * return array
     */
    public function acoList(Entity $aro)
    {
        $permissions = $this->find()
            ->where(['aro_id' => $aro->id])
            ->toArray();
        $permissions = Hash::combine($permissions, '{n}.aco_id', '{n}');

        $acos = TableRegistry::get('Acl.Acos')->find('threaded')
            ->all()
            ->toArray();

        foreach ($acos as $key => $aco) {
            $acos[$key] = $this->mapStatus($aco, $permissions, false);
        }

        return $acos;
    }

    /**
     * Recursive method that maps if and aco is inherited or allowed based
     * on the list of permissions
     *
     * param array $aco the aco currently evaluated
     * param array $permissions complete list of permissions
     * return bool $allowed parent's status, use when inherited
     * return array
     */
    public function mapStatus(array $aco, array $permissions, $allowed)
    {
        $aco['inherited'] = true;
        $aco['allowed'] = $allowed;

        if (array_key_exists($aco['id'], $permissions)) {
            $aco['inherited'] = $permissions[$aco['id']]->inherited;
            if (!$aco['inherited']) {
                $aco['allowed'] = $permissions[$aco['id']]->allowed;
            }
        }

        foreach ($aco['children'] as $key => $children) {
            $aco['children'][$key] = $this->mapStatus($children, $permissions, $aco['allowed']);
        }
        return $aco;
    }
}
