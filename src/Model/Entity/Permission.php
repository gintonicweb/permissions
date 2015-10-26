<?php
namespace Permissions\Model\Entity;

use Acl\Model\Entity\Permission as BasePermission;

class Permission extends BasePermission
{
    protected function _getAllowed()
    {
        $properties = $this->_properties;
        unset($properties['id']);
        unset($properties['aro_id']);
        unset($properties['aco_id']);

        foreach($properties as $property){
            if($property == -1){
                return false;
            }
        }

        return true;
    }

    protected function _getInherited()
    {
        $properties = $this->_properties;
        unset($properties['id']);
        unset($properties['aro_id']);
        unset($properties['aco_id']);

        foreach($properties as $property){
            if($property != 0){
                return false;
            }
        }

        return true;
    }
}
