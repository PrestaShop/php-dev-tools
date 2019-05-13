# PrestaShop Coding Standards

This repository provides a configuration for [friendsofphp/php-cs-fixer](http://github.com/FriendsOfPHP/PHP-CS-Fixer), [phpstan/phpstan](https://github.com/phpstan/phpstan) and includes tools to check that repositories are following the standards defined by the PrestaShop community.


## Installation

```
composer require --dev prestashop/php-coding-standards
```

## Usage

### PHP Cs fixer

```bash 
$ ./bin/prestashop-coding-standards cs-fixer:init --dest /path/to/my/project
```

It'll create a configuration file `.php_cs.dist` in the root of your project.

### Phpstan

```bash
$ ./bin/prestashop-coding-standards phptan:init --dest /path/to/my/project
```

It'll create a `boostrap.php` and a `phpstan.neon` files, that are required to run phpstan.
The default phpstan level is 5 ;)


