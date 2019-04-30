# PrestaShop Coding Standards

This repository provides a configuration for [friendsofphp/php-cs-fixer](http://github.com/FriendsOfPHP/PHP-CS-Fixer), [phpstan/phpstan](https://github.com/phpstan/phpstan) and includes tools to check that repositories are following the standards defined by the PrestaShop community.


## Installation

```
composer require --dev prestashop/php-coding-standards
```

## Usage

```bash 
$ ./bin/prestashop-coding-standard cs-fixer:init --dest /path/to/my/project
```

It'll create a configuration file `.php_cs.dist` in the root of your project.


