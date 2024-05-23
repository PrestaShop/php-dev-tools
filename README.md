# PrestaShop Coding Standards

[![Latest Stable Version](https://img.shields.io/packagist/v/prestashop/php-dev-tools.svg?style=flat-square)](https://packagist.org/packages/prestashop/php-dev-tools) [![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2.5-8892BF.svg?style=flat-square)](https://php.net/) [![Quality Control Status](https://img.shields.io/github/workflow/status/prestashop/php-dev-tools/PHP%20tests?style=flat-square)](https://github.com/prestashop/php-dev-tools/actions/workflows/php.yml)


This repository includes tools to check that repositories are following the standards defined by the PrestaShop community and provides configuration files for some of them.

Related packages:

* [friendsofphp/php-cs-fixer](http://github.com/FriendsOfPHP/PHP-CS-Fixer)
* [phpstan/phpstan](https://github.com/phpstan/phpstan)
* [prestashop/autoindex](https://github.com/PrestaShopCorp/autoindex)
* [prestashop/header-stamp](https://github.com/PrestaShopCorp/header-stamp)


## Installation

```bash
composer require --dev prestashop/php-dev-tools

## Development dependencies runtimes
composer require --dev friendsofphp/php-cs-fixer
composer require --dev phpstan/phpstan
composer require --dev prestashop/header-stamp
composer require --dev prestashop/autoindex
composer require --dev squizlabs/php_codesniffer
```

When this project is successfully added to your dependencies, you can enable each review tool on your projet.

## Version Guidance

| Version | Status         | Packagist           -| Namespace    | Repo                | Docs                | PHP Version  |
|---------|----------------|----------------------|--------------|---------------------|---------------------|--------------|
| 1.x     | EOL         | `prestashop/php-dev-tools` | N/A  | [v1.x][lib-1-repo] | N/A                 | >=5.6,<7.2  |
| 2.x     | EOL         | `prestashop/php-dev-tools` | N/A  | [v2.x][lib-2-repo] | N/A                 | >=5.6,<7.2  |
| 3.x     | Security fixes         | `prestashop/php-dev-tools` | N/A  | [v3.x][lib-3-repo] | N/A                 | >=5.6,>=7.2.5  |
| 4.x     | EOL         | `prestashop/php-dev-tools` | N/A  | [v4.x][lib-4-repo] | N/A                 | >=7.2.5  |
| 5.x     | Latest         | `prestashop/php-dev-tools` | N/A  | [master][master-repo] | N/A                 | >=7.2.5  |

[lib-1-repo]: https://github.com/PrestaShop/php-dev-tools/tree/1.x
[lib-2-repo]: https://github.com/PrestaShop/php-dev-tools/tree/2.x
[lib-3-repo]: https://github.com/PrestaShop/php-dev-tools/tree/3.x
[lib-4-repo]: https://github.com/PrestaShop/php-dev-tools/tree/4.x
[master-repo]: https://github.com/PrestaShop/php-dev-tools/tree/master



## Usage

The configuration files added in your project can be freely modified in order to match your needs.

Running the tools can be done by calling their respective binary:

### PHP CS Fixer

Initialize the configuration with:
```bash 
$ php vendor/bin/prestashop-coding-standards cs-fixer:init [--dest /path/to/my/project]
```

It'll create a configuration file `.php-cs-fixer.dist.php` in the root of your project.

**Upgrade note :** When upgrading from 4.1.0 to newer version, you should re-run the init script or rename your ``.php_cs.dist`` file to ``.php-cs-fixer.dist.php`` in order to match the new requirements of cs-fixer.

```bash
$ vendor/bin/php-cs-fixer fix
```

### PHPStan

```bash
$ php vendor/bin/prestashop-coding-standards phpstan:init [--dest /path/to/my/project]
```

It'll create a default file `phpstan.neon` in `tests/phpstan`, that are required to run phpstan.
The default phpstan level is the lowest available, but we recommend you to update this value to get more recommandations.

```php
$ _PS_ROOT_DIR_=<Path_to_PrestaShop> php vendor/bin/phpstan --configuration=tests/phpstan/phpstan.neon analyse <path1 [path2 [...]]>
```

### Autoindex

Applying an index.php file to all your project subfolders will be useful to avoid directories to be listed by the webserver.

```php
$ vendor/bin/autoindex prestashop:add:index <path>
```

### Header Stamp

Your license headers can be updated by applying the header stamp.

Here is an example of call, applying the default license on a PrestaShop module:

```php
$ vendor/bin/header-stamp --license=assets/afl.txt --exclude=vendor,node_modules
```

Available options are provided with `--help`.
