# DataTable type

The [`DataTableType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnType.php) represents a base data table, and should be used as a base for every data table defined in the system.

## Options

### `title`

- **type**: `null`, `string` or `TranslatableInterface`
- **default**: `null`

### `title_translation_parameters`

- **type**: `array`
- **default**: `[]`

### `translation_domain`

- **type**: `null`, `bool` or `string`
- **default**: `null`

### `themes`

- **type**: `string[]`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `column_factory`

- **type**: `ColumnFactoryInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `filter_factory`

- **type**: `FilterFactoryInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `action_factory`

- **type**: `ActionFactoryInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `exporter_factory`

- **type**: `ExporterFactoryInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `request_handler`

- **type**: `RequestHandlerInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `sorting_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `sorting_persistence_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `sorting_persistence_adapter`

- **type**: `null` or `PersistenceAdapterInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `sorting_persistence_subject_provider`

- **type**: `null` or `PersistenceSubjectProviderInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `pagination_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `pagination_persistence_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `pagination_persistence_adapter`

- **type**: `null` or `PersistenceAdapterInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `pagination_persistence_subject_provider`

- **type**: `null` or `PersistenceSubjectProviderInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `filtration_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `filtration_persistence_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `filtration_persistence_adapter`

- **type**: `null` or `PersistenceAdapterInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `filtration_persistence_subject_provider`

- **type**: `null` or `PersistenceSubjectProviderInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `filtration_form_factory`

- **type**: `null` or `FormFactoryInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `personalization_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `personalization_persistence_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `personalization_persistence_adapter`

- **type**: `null` or `PersistenceAdapterInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `personalization_persistence_subject_provider`

- **type**: `null` or `PersistenceSubjectProviderInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `personalization_form_factory`

- **type**: `null` or `FormFactoryInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `exporting_enabled`

- **type**: `bool`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)

### `exporting_form_factory`

- **type**: `null` or `FormFactoryInterface`
- **default**: value defined in [`defaults` configuration](../configuration.md#data-table-builder-defaults)
