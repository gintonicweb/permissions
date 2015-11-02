<?php
namespace Permissions\Model\Table;

use Acl\Model\Table\PermissionsTable as BaseTable;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * {@inherit}
 */
class PermissionsTable extends BaseTable
{

    public function initialize(array $config)
    {
        $this->table('aros_acos');
    }

    public function acoList($aro)
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

    public function mapStatus($aco, $permissions, $allowed)
    {
        $aco['inherited'] = true;
        $aco['allowed'] = $allowed;

        if (array_key_exists($aco['id'], $permissions)) {
            $aco['inherited'] = $permissions[$aco['id']]->inherited;
            if(!$aco['inherited']) {
                $aco['allowed'] = $permissions[$aco['id']]->allowed;
            }
        }

        foreach ($aco['children'] as $key => $children) {
            $aco['children'][$key] = $this->mapStatus($children, $permissions, $aco['allowed']);
        }
        return $aco;
    }
}
