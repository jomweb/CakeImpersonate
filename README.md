# CakePHP Tools Plugin
[![Build Status](https://travis-ci.org/jomweb/CakeImpersonate.svg?branch=master)](https://travis-ci.org/jomweb/CakeImpersonate)
[![Coverage Status](https://codecov.io/gh/jomweb/CakeImpersonate/branch/master/graph/badge.svg)](https://codecov.io/gh/jomweb/CakeImpersonate)
[![Latest Stable Version](https://poser.pugx.org/jomweb/cake-impersonate/v/stable.svg)](https://packagist.org/packages/jomweb/cake-impersonate)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)](https://php.net/)
[![Total Downloads](https://poser.pugx.org/jomweb/cake-impersonate/d/total.svg)](https://packagist.org/packages/jomweb/cake-impersonate)

# CakeImpersonate
CakePHP Impersonate Plugin. A component that stores the current authentication session and creates new session for impersonating Users. User can revert back to original authentication sessions without the need to re-login.

# Requirement
1. CakePHP 3.6 and above.
2. Use of default CakePHP AuthComponent.

# Installation
`
composer require jomweb/cake-impersonate
`

# Plugin Load
Open \config\bootstrap.php add
```php
Plugin::load('CakeImpersonate');
```

Load the component from controller
```php
$this->loadComponent('CakeImpersonate.Impersonate'); 
```

# Usage
#### Impersonate user
```php
$this->Impersonate->login($targeted_user_id);
```

#### Check current user is impersonating
```php
$this->Impersonate->isImpersonate();
```

#### Logout from impersonating
```php
$this->Impersonate->logout();
```
