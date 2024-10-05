# Installation

This bundle can be installed at any moment during a project’s lifecycle.

[[toc]]

## Prerequisites

- PHP version 8.1 or higher
- Symfony version 6.0 or higher

## Download the bundle

Use [Composer](https://getcomposer.org/) to install the bundle:

```shell
composer require kreyu/data-table-bundle 0.23.*
```

::: danger This bundle is not production ready!
It is recommended to lock the minor version, as minor versions can provide breaking changes until the stable release!
:::

## Enable the bundle

Enable the bundle by adding it to the `config/bundles.php` file of your project:

```php
return [
    // ...
    Kreyu\Bundle\DataTableBundle\KreyuDataTableBundle::class => ['all' => true],
];
```

## Enable the Stimulus controllers

This bundle provides front-end scripts created using the [Stimulus JavaScript framework](https://stimulus.hotwired.dev/).
To begin with, make sure your application uses the [Symfony Stimulus Bridge](https://github.com/symfony/stimulus-bridge).

Then, add `@kreyu/data-table-bundle` dependency to your `package.json` file:

```json
{
    "devDependencies": {
        "@kreyu/data-table-bundle": "file:vendor/kreyu/data-table-bundle/assets"
    }
}
```

Now, add `@kreyu/data-table-bundle` controllers to your `assets/controllers.json` file:

```json
{
    "controllers": {
        "@kreyu/data-table-bundle": {
            "personalization": {
                "enabled": true
            },
            "state": {
                "enabled": true
            },
            "batch": {
                "enabled": true
            }
        }
    }
}
```

## Rebuild assets

If you're using [AssetMapper](https://symfony.com/doc/current/frontend.html#assetmapper-recommended), you're good to go. Otherwise, run following command:

::: code-group

```shell [yarn]
yarn install --force && yarn watch
```

```shell [npm]
npm install --force && npm run watch
```

:::
