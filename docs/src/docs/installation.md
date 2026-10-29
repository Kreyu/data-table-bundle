# Installation

This bundle can be installed at any moment during a project’s lifecycle.

[[toc]]

## Prerequisites

- PHP version 8.1 or higher
- Symfony version 6.0 or higher

## Download the bundle

Use [Composer](https://getcomposer.org/) to install the bundle:

```shell-vue
composer require kreyu/data-table-bundle
```

> [!DANGER] This bundle is not stable yet. Use with caution.

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
            },
            "dialog": {
                "enabled": true
            }
        }
    }
}
```

`dialog` opens modal actions in every theme, and action confirmations in the base theme.

::: warning
**`bootstrap-modal` is deprecated.** Replace it with `dialog`:

```diff
 "@kreyu/data-table-bundle": {
-    "bootstrap-modal": {
+    "dialog": {
         "enabled": true
     }
 }
```

Copied the old `bootstrap_5` `action_modal_control` block into your theme? Keep `bootstrap-modal` enabled until you update that block.
:::

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
