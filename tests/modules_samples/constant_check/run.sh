#!/bin/bash

function runPHPStan {
    echo "Running PHPStan with PS $1"
    docker run -tid --rm --name container-$1 prestashop/prestashop:$1
    docker cp container-$1:/var/www/html ./psContents
    
    _PS_ROOT_DIR_=$PWD/psContents vendor/bin/phpstan analyse --configuration=./phpstan.neon
    result=$?

    rm -rf ./psContents

    if [ $result -ne $2 ]; then
        echo "Expected result $2 does not match $result";
        exit 1;
    fi
}

composer install

# For copy of phpstan folder, in case we work on another branch locally
cp -R ../../../phpstan vendor/prestashop/php-dev-tools/

runPHPStan 8 0
runPHPStan 1.7 0
runPHPStan 1.6.0.1 1

