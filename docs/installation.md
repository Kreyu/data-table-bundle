---
icon: rocket
order: 5
---

# Installation

This bundle can be installed at any moment during a project’s lifecycle.

## Download the bundle

Use [Composer](https://getcomposer.org/) to install the bundle:

```shell
$ composer require kreyu/data-table-bundle 0.15.*
```

!!! Warning
It is recommended to lock the minor version, as minor versions can provide breaking changes until the stable release!
!!!

If your application is using [Symfony Flex](https://github.com/symfony/flex), you can skip to [installing the front-end dependencies](#install-front-end-dependencies-and-rebuild).

## Enable the bundle

Enable the bundle by adding it to the `config/bundles.php`:

```php # config/bundles.php
return [
    // ...
    Kreyu\Bundle\DataTableBundle\KreyuDataTableBundle::class => ['all' => true],
];
```

## Enable the Symfony UX integration

This bundle provides front-end scripts created using the [Stimulus JavaScript framework](https://stimulus.hotwired.dev/).   
To begin with, make sure your application uses the [Symfony Stimulus Bridge](https://github.com/symfony/stimulus-bridge).

Add `@kreyu/data-table-bundle` dependency to your `package.json` file:

```json # package.json
{
    "devDependencies": {
        "@kreyu/data-table-bundle": "file:vendor/kreyu/data-table-bundle/assets"
    }
}
```

Now, add `@kreyu/data-table-bundle` controllers to your `assets/controllers.json` file: 

```json # assets/controllers.json
{
    "controllers": {
        "@kreyu/data-table-bundle": {
            "personalization": {
                "enabled": true
            },
            "batch": {
                "enabled": true
            }
        }
    }
}
```

## Install front-end dependencies and rebuild

If you're using WebpackEncore, install your assets and restart Encore (not needed if you're using AssetMapper):

+++ yarn
```shell
$ yarn install --force
$ yarn watch
```
+++ npm
```shell
$ npm install --force
$ npm run watch
```
+++