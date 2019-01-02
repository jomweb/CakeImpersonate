# CakePHP Tools Plugin
[![Build Status](https://api.travis-ci.org/jomweb/cake-impersonate.svg?branch=master)](https://travis-ci.org/jomweb/cake-impersonate)
[![Coverage Status](https://codecov.io/gh/jomweb/cake-impersonate/branch/master/graph/badge.svg)](https://codecov.io/gh/jomweb/cake-impersonate)
[![Latest Stable Version](https://poser.pugx.org/jomweb/cake-impersonate/v/stable.svg)](https://packagist.org/packages/jomweb/cake-impersonate)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)](https://php.net/)
[![Total Downloads](https://poser.pugx.org/jomweb/cake-impersonate/d/total.svg)](https://packagist.org/packages/jomweb/cake-impersonate)

# CakeImpersonate
Cakephp3 impersonate plugin. A component that store current session and create new session for impersonating. User can revert back to original sessions without the needs to re-login.

# Requirement
1. Cakephp 3.6 and above.
2. Use of default Cakephp AuthComponent.

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
