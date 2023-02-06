# Installation

To install the bundle, run following command:

```shell
composer require kreyu/data-table-bundle
```

## Integration with Symfony UX

This bundle provides front-end scripts created using the [Stimulus JavaScript framework](https://stimulus.hotwired.dev/).
To easily integrate in with your application, be sure your application uses the [Symfony Stimulus Bridge](https://github.com/symfony/stimulus-bridge).

Because the bundle is tagged as the `symfony-ux`, the [Symfony Flex](https://github.com/symfony/flex) should automatically configure the 
front-end controllers for you. 

To confirm that, first check your `package.json`, that should contain a `@kreyu/data-table-bundle` dependency:

```json5
{
    "devDependencies": {
        // ...
        "@kreyu/data-table-bundle": "file:vendor/kreyu/data-table-bundle/assets",
    }
}
```

Then, check your [assets/controllers.json](https://github.com/symfony/stimulus-bridge#the-controllersjson-file) file, which should contain following configuration:

```json5
{
    "controllers": {
        // ...
        "@kreyu/data-table-bundle": {
            "personalization": {
                "enabled": true,
                "fetch": "eager"
            }
        }
    },
    // ...
}
```

Finally, remember to run install & build front-end dependencies:

```shell
# if using npm
npm install
npm run watch

# if using yarn
yarn
yarn watch
```

## Integration with PhpSpreadsheet

This bundle provides exporters created using the [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/).
If you wish to use it, be sure to have it installed:

```shell
composer require phpoffice/phpspreadsheet
```
