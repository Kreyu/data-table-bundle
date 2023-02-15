# Configuration

This bundle can be configured using the `config/packages/kreyu_data_table.yaml` file. 

## Themes

You can define which Twig theme to use with the data tables using the `themes` node.
By default, the Bootstrap 5 theme is used. Because themes are built using [Twig blocks](https://twig.symfony.com/doc/3.x/tags/block.html),
the bundle iterates through given themes, until it find the desired block, using the first one it finds.

```yaml
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
  themes:
    - '@KreyuDataTable/themes/bootstrap_5.html.twig'
```

## Data table builder defaults

You can specify default values applied to **all the data tables** using the `defaults` node.
Those are used as a default builder values, unless the user enters some option value manually,
either by passing it as a data table option, or by using the data table builder directly. 

!!! Note

    The default configuration is loaded by the [DefaultConfigurationExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Core/DefaultConfigurationExtension.php),
    that extends every data table type class with [DataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableType.php) specified as a parent.

The given values represent the default ones, unless specifically stated otherwise:

```yaml
# config/packages/kreyu_data_table.yaml
kreyu_data_table:
  defaults:
    column_factory: kreyu_data_table.column.factory
    request_handler: kreyu_data_table.request_handler.http_foundation
    sorting:
      enabled: true
      persistence_enabled: false
      persistence_adapter: kreyu_data_table.sorting.persistence.adapter.cache # if symfony/cache is installed, null otherwise
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage # if symfony/security-bundle is installed, null otherwise
    pagination:
      enabled: true
      persistence_enabled: false
      persistence_adapter: kreyu_data_table.pagination.persistence.adapter.cache  # if symfony/cache is installed, null otherwise
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage # if symfony/security-bundle is installed, null otherwise
    filtration:
      enabled: true
      persistence_enabled: false
      persistence_adapter: kreyu_data_table.filtration.persistence.adapter.cache  # if symfony/cache is installed, null otherwise
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage # if symfony/security-bundle is installed, null otherwise
      form_factory: form.factory
      filter_factory: kreyu_data_table.filter.factory
    personalization:
      enabled: false
      persistence_enabled: false
      persistence_adapter: kreyu_data_table.personalization.persistence.adapter.cache  # if symfony/cache is installed, null otherwise
      persistence_subject_provider: kreyu_data_table.persistence.subject_provider.token_storage # if symfony/security-bundle is installed, null otherwise
      form_factory: form.factory
    exporting:
      enabled: true
      form_factory: form.factory
      exporter_factory: kreyu_data_table.exporter.factory
```

!!! Note

    The default cache persistence adapters are provided only, if the [symfony/cache](https://symfony.com/doc/current/components/cache.html) component is installed.
    If the component is not installed, then default value equals null, meaning you'll have to specify an adapter manually if you wish to use the persistence.

!!! Note

    The cache subject providers are provided only, if the [symfony/security-bundle](https://symfony.com/doc/current/security.html) component is installed.
    If the component is not installed, then default value equals null, meaning you'll have to specify a subject provider manually if you wish to use the persistence.