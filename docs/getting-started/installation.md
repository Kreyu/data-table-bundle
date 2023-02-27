# Installation

This bundle can be installed at any moment during a project’s lifecycle.

## Download the bundle

Use [Composer](https://getcomposer.org/) to install the bundle:

```bash
composer require kreyu/data-table-bundle:dev-main
```

## Enable the bundle

Enable the bundle by adding it to the `bundles.php`:

```php
// config/bundles.php
return [
    // ...
    Kreyu\Bundle\DataTableBundle\KreyuDataTableBundle::class => ['all' => true],
];
```

## Enable the Symfony UX integration

This bundle provides front-end scripts created using the [Stimulus JavaScript framework](https://stimulus.hotwired.dev/).
To begin with, make sure your application uses the [Symfony Stimulus Bridge](https://github.com/symfony/stimulus-bridge).

### Add dependency to package.json

Check your `package.json`, that should contain a `@kreyu/data-table-bundle` dependency:

```json
{
    "devDependencies": {
        "@kreyu/data-table-bundle": "file:vendor/kreyu/data-table-bundle/assets"
    }
}
```

### Enable the Stimulus controller

Check your [assets/controllers.json](https://github.com/symfony/stimulus-bridge#the-controllersjson-file) file, 
which should contain the `@kreyu/data-table-bundle` configuration:

```json
{
    "controllers": {
        "@kreyu/data-table-bundle": {
            "personalization": {
                "enabled": true
            }
        }
    }
}
```

### Install dependencies and build the front-end

```bash
# if using npm
npm install
npm run build

# if using yarn
yarn
yarn build
```

## Enable the translator service

The translator service is required by the bundle to display all labels properly.
For more information, see [Symfony translation documentation](https://symfony.com/doc/current/translation.html#configuration).

```yaml
# config/packages/translation.yaml
framework:
  default_locale: 'en'
  translator:
    default_path: '%kernel.project_dir%/translations'
```

This bundle supports two locales out of the box: English (`en`) and Polish (`pl`).

## Select theme

By default, a base HTML theme is used. It's primary role is to work as a base theme for other themes.

The following themes are natively available in the bundle:

- [@KreyuDataTable/themes/bootstrap_5.html.twig](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/bootstrap_5.html.twig) - integrates [Bootstrap 5](https://getbootstrap.com/docs/5.0/);
- [@KreyuDataTable/themes/tabler.html.twig](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/tabler.html.twig) - integrates [Tabler UI Kit](https://tabler.io/);
- [@KreyuDataTable/themes/base.html.twig](https://github.com/Kreyu/data-table-bundle/blob/main/src/Resources/views/themes/base.html.twig) - base HTML template;

To select a theme, provide which one to use in the bundle configuration file:

```yaml
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
  themes:
    - '@KreyuDataTable/themes/bootstrap_5.html.twig'
```

For more information, see [theming documentation section](../reference/theming.md).
