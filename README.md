# CakeImpersonate Plugin
[![Build Status](https://travis-ci.org/jomweb/CakeImpersonate.svg?branch=master)](https://travis-ci.org/jomweb/CakeImpersonate)
[![Coverage Status](https://codecov.io/gh/jomweb/CakeImpersonate/branch/master/graph/badge.svg)](https://codecov.io/gh/jomweb/CakeImpersonate)
[![Latest Stable Version](https://poser.pugx.org/jomweb/cake-impersonate/v/stable.svg)](https://packagist.org/packages/jomweb/cake-impersonate)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)](https://php.net/)
[![Total Downloads](https://poser.pugx.org/jomweb/cake-impersonate/d/total.svg)](https://packagist.org/packages/jomweb/cake-impersonate)

# Impersonate Component
A component that stores the current authentication session and creates new session for impersonating Users. User can revert back to original authentication sessions without the need to re-login.

### Warning
Always double check that you cannot "spoof" other users in the controller actions. To prevent hijacking of users accounts that the current authenticated User shouldn't/wouldn't have normal access to. You should enable [CsfrComponent](https://book.cakephp.org/3.0/en/controllers/components/csrf.html) and [SecurityComponent](https://book.cakephp.org/3.0/en/controllers/components/security.html) in your Controller when loading this component. This Plugin does circumvent default authentication mechanisms.

# Requirement
1. CakePHP 3.7 and above.

# Installation
`
composer require jomweb/cake-impersonate:"^3.0"
`

# Plugin Load
Open \src\Application.php add
```php
$this->addPlugin('CakeImpersonate');
```
to your bootstrap() method or call `bin/cake plugin load CakeImpersonate`

Load the component from controller
```php
$this->loadComponent('CakeImpersonate.Impersonate'); 
```

# Configure Session Key
Open `configure\app.php` and add
```php
'Impersonate' => [
    'sessionKey' => 'OriginalAuth'
]

```
to the `return [];` or use `Configure::write('Impersonate.sessionKey', 'OriginalAuth');` when loading the component.

# Usage
#### Impersonate user
This requires the request to be a `POST`, `PUT`, `DELETE` so it can be protected by `SecurityComponent` and `CsrfComponent`
```php
$this->Impersonate->login($userIdToImpersonate);
```

#### Check current user is impersonated
```php
$this->Impersonate->isImpersonated();
```

#### Logout from impersonating
```php
$this->Impersonate->logout();
```
