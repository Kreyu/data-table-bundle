# Configuration

This bundle can be configured using the:

- `config/packages/kreyu_data_table.yaml` (when using YAML configuration);
- `config/packages/kreyu_data_table.php` (when using PHP configuration);

## Data table builder defaults

You can specify default values applied to **all the data tables** using the `defaults` node.
Those are used as a default builder values, unless the user enters some option value manually,
either by passing it as a data table option, or by using the data table builder directly.

!!! Note
The default configuration is loaded by the [:icon-mark-github: DefaultConfigurationDataTableTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Core/DefaultConfigurationDataTableTypeExtension.php),
that extends every data table type class with [:icon-mark-github: DataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableType.php) specified as a parent.
!!!

The given values represent the default ones, unless specifically stated otherwise:

+++ YAML
```yaml # config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    themes:
      - '@KreyuDataTable/themes/base.html.twig'
    column_factory: kreyu_data_table.column.factory
    request_handler: kreyu_data_table.request_handler.http_foundation
    sorting:
      enabled: true
      persistence_enabled: false
      # if persistence is enabled and symfony/cache is installed, null otherwise 
      persistence_adapter: kreyu_data_table.sorting.persistence.adapter.cache
      # if persistence is enabled and symfony/security-bundle is installed, null otherwise 
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage
    pagination:
      enabled: true
      persistence_enabled: false
      # if persistence is enabled and symfony/cache is installed, null otherwise 
      persistence_adapter: kreyu_data_table.pagination.persistence.adapter.cache
      # if persistence is enabled and symfony/security-bundle is installed, null otherwise 
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage
    filtration:
      enabled: true
      persistence_enabled: false
      # if persistence is enabled and symfony/cache is installed, null otherwise 
      persistence_adapter: kreyu_data_table.filtration.persistence.adapter.cache
      # if persistence is enabled and symfony/security-bundle is installed, null otherwise 
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage
      form_factory: form.factory
      filter_factory: kreyu_data_table.filter.factory
    personalization:
      enabled: false
      persistence_enabled: false
      # if persistence is enabled and symfony/cache is installed, null otherwise 
      persistence_adapter: kreyu_data_table.personalization.persistence.adapter.cache
      # if persistence is enabled and symfony/security-bundle is installed, null otherwise 
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage
      form_factory: form.factory
    exporting:
      enabled: true
      form_factory: form.factory
      exporter_factory: kreyu_data_table.exporter.factory
```
+++ PHP 
```php # config/packages/kreyu_data_table.php
<?php

use Symfony\Config\KreyuDataTableConfig;

return static function (KreyuDataTableConfig $config) {
    $defaults = $config->defaults();

    $defaults
        ->themes([
            '@KreyuDataTable/themes/base.html.twig'
        ])
        ->columnFactory('kreyu_data_table.column.factory')
        ->requestHandler('kreyu_data_table.request_handler.http_foundation')
    ;

    $defaults->sorting()
        ->enabled(true)
        ->persistenceEnabled(true)
        // if persistence is enabled and symfony/cache is installed, null otherwise
        ->persistenceAdapter('kreyu_data_table.sorting.persistence.adapter.cache')
        // if persistence is enabled and symfony/security-bundle is installed, null otherwise
        ->persistenceSubjectProvider('kreyu_data_table.persistence.subject_provider.token_storage')
    ;

    $defaults->pagination()
        ->enabled(true)
        ->persistenceEnabled(true)
        // if persistence is enabled and symfony/cache is installed, null otherwise
        ->persistenceAdapter('kreyu_data_table.pagination.persistence.adapter.cache')
        // if persistence is enabled and symfony/security-bundle is installed, null otherwise
        ->persistenceSubjectProvider('kreyu_data_table.persistence.subject_provider.token_storage')
    ;

    $defaults->filtration()
        ->enabled(true)
        ->persistenceEnabled(true)
        // if persistence is enabled and symfony/cache is installed, null otherwise
        ->persistenceAdapter('kreyu_data_table.filtration.persistence.adapter.cache')
        // if persistence is enabled and symfony/security-bundle is installed, null otherwise
        ->persistenceSubjectProvider('kreyu_data_table.persistence.subject_provider.token_storage')
    ;

    $defaults->personalization()
        ->enabled(true)
        ->persistenceEnabled(true)
        // if persistence is enabled and symfony/cache is installed, null otherwise
        ->persistenceAdapter('kreyu_data_table.personalization.persistence.adapter.cache')
        // if persistence is enabled and symfony/security-bundle is installed, null otherwise
        ->persistenceSubjectProvider('kreyu_data_table.persistence.subject_provider.token_storage')
    ;

    $defaults->exporting()
        ->enabled(true)
        ->formFactory('form.factory')
        ->exporterFactory('kreyu_data_table.exporter.factory')
    ;
};
```
+++

!!! Note
The default cache persistence adapters are provided only if the [Symfony Cache](https://symfony.com/doc/current/components/cache.html) component is installed.
If the component is not installed, then the default value equals null, meaning you'll have to specify an adapter manually if you wish to use the persistence.
!!!

!!! Note
The persistence subject providers are provided only if the [Symfony Security](https://symfony.com/doc/current/security.html) component is installed.
If the component is not installed, then the default value equals null, meaning you'll have to specify a subject provider manually if you wish to use the persistence.
!!!
