[![Build Status](https://travis-ci.org/gintonicweb/permissions.svg?branch=master)](https://travis-ci.org/gintonicweb/permissions)
[![codecov.io](https://codecov.io/github/gintonicweb/permissions/coverage.svg?branch=master)](https://codecov.io/github/gintonicweb/permissions?branch=master)
# Permissions plugin for CakePHP

## Warning

Do not use, very early stage


## Installation

```
composer require gintonicweb/permissions
```

In your app's ```config/bootstrap.php``` add:

```
Plugin::load('Permissions');
```

## Usage

In your Auth setup, use ConfigAuthorize for authorization.

```
public $components = array(
    'Auth' => [
        'authorize' => ['Permissions.Config'],
    ]
);
```

In your controllers you can now use an array like this to grant permissions to
specific actions for specific roles

```
// Add Role at the top of your controller like this
// use Permissions\Model\Entity\Role;

public $_permissions = [
    Role::ADMIN => '*',
    Role::USER => ['index', 'view'],
];
```

You can also add the Roles Listener in bootstrap.php if you want to have access
to the users role from the Auth component.

```
// in config/bootstrap.php
use Permissions\Listener\RoleListener;
EventManager::instance()->attach(new RoleListener());
```

which makes it possible now to do things like

```
$role = $this->Auth->user('role');
$roleName = Role::types($role);
```
