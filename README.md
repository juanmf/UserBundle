UserBundle
==========

FOS User Bundle Extension with Roles/RoleHierarchy Implemented in Database.

Follows my project's composer.json

```json
{
    "name": "symfony/framework-standard-edition",
    "license": "MIT",
    "type": "project",
    "description": "The \"Symfony Standard Edition\" distribution",
    "autoload": {
        "psr-0": { "": "src/" }
    },
    "require": {
        "php": ">=5.3.3",
        "symfony/symfony": "~2.4",
        "doctrine/orm": "~2.2,>=2.2.3",
        "doctrine/doctrine-bundle": "~1.3@dev",
        "twig/extensions": "~1.0",
        "symfony/assetic-bundle": "~2.3",
        "symfony/swiftmailer-bundle": "~2.3",
        "symfony/monolog-bundle": "~2.4",
        "sensio/distribution-bundle": "~2.3",
        "sensio/framework-extra-bundle": "~3.0",
        "sensio/generator-bundle": "~2.3",
        "incenteev/composer-parameter-handler": "~2.0",
        "symfony/dom-crawler": "*",
        "white-october/pagerfanta-bundle": "dev-master",
        "knplabs/knp-snappy-bundle": "dev-master",
        "stof/doctrine-extensions-bundle": "dev-master",
        
        "friendsofsymfony/user-bundle": "~2.0@dev",
        "lexik/form-filter-bundle": "~2.0",
        "nelmio/cors-bundle": "~1.0",
        "docdigital/filter-type-guesser": "dev-master",
        "docdigital/php-class-editor": "dev-master",
        "docdigital/cdn-bundle": "dev-master",
        "docdigital/pdf-manager-bundle": "~1.0"
    },
    "scripts": {
        "post-install-cmd": [
            "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile"
        ],
        "post-update-cmd": [
            "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile"
        ]
    },
    "config": {
        "bin-dir": "bin"
    },
    "extra": {
        "symfony-app-dir": "app",
        "symfony-web-dir": "web",
        "incenteev-parameters": {
            "file": "app/config/parameters.yml"
        },
        "branch-alias": {
            "dev-master": "2.4-dev"
        }
    }
}
```

Configs
=======

```yml
# app/config/config.yml
...
doctrine:
    dbal:
        ...

    orm:
        auto_generate_proxy_classes: %kernel.debug%
        auto_mapping: true

fos_user:
    db_driver: orm # other valid values are 'mongodb', 'couchdb' and 'propel'
    firewall_name: main
    user_class: DocDigital\Bundle\UserBundle\Entity\User
...
```
