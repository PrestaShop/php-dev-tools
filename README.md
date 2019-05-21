# PrestaShop Coding Standards

This repository provides a configuration for [friendsofphp/php-cs-fixer](http://github.com/FriendsOfPHP/PHP-CS-Fixer), [phpstan/phpstan](https://github.com/phpstan/phpstan) and includes tools to check that repositories are following the standards defined by the PrestaShop community.


## Installation

```
composer require --dev prestashop/php-coding-standards:dev-master
```

When this project is successfully added to your dependencies, you can enable each review tool on your projet.

### PHP Cs fixer

```bash 
$ php vendor/bin/prestashop-coding-standards cs-fixer:init [--dest /path/to/my/project]
```

It'll create a configuration file `.php_cs.dist` in the root of your project.

### Phpstan

```bash
$ php vendor/bin/prestashop-coding-standards phptan:init [--dest /path/to/my/project]
```

It'll create a `boostrap.php` and a `phpstan.neon` files, by default in `tests/phpstan`, that are required to run phpstan.
The default phpstan level is the lowest available, but we recommend you to update this value to get more recommandations.

## Usage

The configuration files added in your project can be freely modified in order to match your needs.

Running the tools can be done by calling its binary:

### PHP CS Fixer

```php
php vendor/bin/php-cs-fixer fix
```

### PHPStan

```php
$ _PS_ROOT_DIR_=<Path_to_PrestaShop> php vendor/bin/phpstan.phar --configuration=tests/phpstan/phpstan.neon analyse <path1 [path2 [...]]>
```
