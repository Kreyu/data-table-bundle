# DataTableBundle

[![Latest Stable Version](http://poser.pugx.org/kreyu/data-table-bundle/v)](https://packagist.org/packages/kreyu/data-table-bundle)
[![PHP Version Require](http://poser.pugx.org/kreyu/data-table-bundle/require/php)](https://packagist.org/packages/kreyu/data-table-bundle)
[![License](http://poser.pugx.org/kreyu/data-table-bundle/license)](https://packagist.org/packages/kreyu/data-table-bundle) 

Streamlines creation process of the data tables in Symfony applications.

!!! Note

    This bundle structure was heavily inspired by the [:material-symfony: Symfony Form](https://github.com/symfony/form) component.

## Features

- class-based definition of data tables to reduce repeated codebase;
- source data pagination, filtration and sorting;
- filters supporting multiple operators (e.g. user can select if string filter contains or equals given value);
- per-user persistence with [:material-symfony: Symfony Cache component](https://symfony.com/doc/current/components/cache.html) by default;
- extension system used to modify data tables across the entire application;
- personalization, where user is able to show/hide or even change order of the columns;
- exporting, where user is able to export data tables to various file formats;
- logic decoupled from the source of the data;
- easy theming of every part of the bundle;
- out-of-the-box support for [:material-symfony: Symfony UX](https://symfony.com/blog/new-in-symfony-the-ux-initiative-a-new-javascript-ecosystem-for-symfony), including [:material-symfony: Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html);

## License

The MIT License (MIT). Please see [license file](../LICENSE) for more information.
