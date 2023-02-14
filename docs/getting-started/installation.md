# Installation

This bundle can be installed at any moment during a project’s lifecycle.

## Download the bundle

Use [Composer](https://getcomposer.org/) to install the bundle:

```bash
composer require kreyu/data-table-bundle
```

## Enable the Bundle

!!! Note

    If your application uses [Symfony Flex](https://symfony.com/components/Symfony%20Flex), you can skip this step. 

Enable the bundle by adding it to the `bundles.php`:

```php
// config/bundles.php
return [
    // ...
    Kreyu\Bundle\DataTableBundle\KreyuDataTableBundle::class => ['all' => true],
];
```

## Enable the Symfony UX Integration

This bundle provides front-end scripts created using the [Stimulus JavaScript framework](https://stimulus.hotwired.dev/).
To begin with, make sure your application uses the [Symfony Stimulus Bridge](https://github.com/symfony/stimulus-bridge).

!!! Note

    If your application uses [Symfony Flex](https://symfony.com/components/Symfony%20Flex), 
    you can ignore following steps and simply install dependencies and build the front-end.

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

## Enable the Translator Service

The translator service is required by the bundle to display all labels properly.
For more information, see [Symfony translation documentation](https://symfony.com/doc/current/translation.html#configuration).

```yaml
# config/packages/translation.yaml
framework:
  default_locale: 'en'
  translator:
    default_path: '%kernel.project_dir%/translations'
```

