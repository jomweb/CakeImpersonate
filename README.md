# CakeImpersonate
Cakephp3 impersonate plugin. A component that store current session and create new session for impersonating. User can revert back to original sessions without the needs to re-login.

# Requirement
1. Cakephp 3.4 and above.
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
