<?php
namespace Permissions\Model\Entity;

use Cake\ORM\Entity;

/**
 * Role Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \Permissions\Model\Entity\User $user
 * @property int $role
 */
class Role extends Entity
{
    const ADMIN = 0;
    const USER = 1;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * The main method for any enumeration, should be called statically
     * Now also supports reordering/filtering
     *
     * @link http://www.dereuromark.de/2010/06/24/static-enums-or-semihardcoded-attributes/
     * @param string $value or array $keys or NULL for complete array result
     * @return mixed string/array
     */
    public static function names($value = null)
    {
        $options = [
            self::ADMIN => 'Admin',
            self::USER => 'User',
        ];
        return self::enum($value, $options);
    }

    /**
     * The main method for any enumeration, should be called statically
     * Now also supports reordering/filtering
     *
     * @link http://www.dereuromark.de/2010/06/24/static-enums-or-semihardcoded-attributes/
     * @param string $value or array $keys or NULL for complete array result
     * @param array $options (actual data)
     * @param mixed $default value if the type does not exists
     * @return mixed string/array
     */
    public static function enum($value, array $options, $default = null)
    {
        if ($value !== null && !is_array($value)) {
            if (array_key_exists($value, $options)) {
                return $options[$value];
            }
            return $default;
        }
        if ($value !== null) {
            $newOptions = [];
            foreach ($value as $v) {
                $newOptions[$v] = $options[$v];
            }
            return $newOptions;
        }
        return $options;
    }
}
