# 0.13 -> 0.14

This update contains a few breaking changes that need to be applied to your application.

## Removed deprecated `DataTableControllerTrait`

The previously deprecated `DataTableControllerTrait` is now removed. Use `DataTableFactoryAwareTrait` instead.

## Removed deprecated `themes` configuration node

The previously deprecated `themes` configuration node is now removed. Use `defaults.themes` instead.

## Removed `DefaultConfigurationDataTableTypeExtension`

The `DefaultConfigurationDataTableTypeExtension` is now removed. 
The default configuration is now applied by the base `DataTableType`.
If your application has this extension registered in the container (for example to change its priority), remove the definition.

## New column export views

The column type classes now contain two additional methods:

- `buildExportHeaderView`
- `buildExportValueView`

These methods are used exclusively for exporting. 
Move any export-specific logic from existing `buildHeaderView` and `buildValueView` methods to the new ones.

**Note**: these methods have to be especially lightweight, as they are called for every row in the data table,
and exporting large data sets may take a very long time.

## New sort direction enum cases

The `Kreyu\Bundle\DataTableBundle\Sorting\Direction` enum has changed its cases:

| Before | After  |
|--------|--------|
| `ASC`  | `Asc`  |
| `DESC` | `Desc` |

## New export strategy enum cases

The `Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy` enum has changed its cases:

| Before                 | After                |
|------------------------|----------------------|
| `INCLUDE_ALL`          | `IncludeAll`         |
| `INCLUDE_CURRENT_PAGE` | `IncludeCurrentPage` |

Replace all occurrences of the old cases with the new ones.

Additionally, the translation keys of operator cases have changed:

| Strategy             | Translation key before | Translation key after  |
|----------------------|------------------------|------------------------|
| `IncludeAll`         | `INCLUDE_ALL`          | `Include all`          |
| `IncludeCurrentPage` | `INCLUDE_CURRENT_PAGE` | `Include current page` |

## New operator enum cases

The `Kreyu\Bundle\DataTableBundle\Filter\Operator` enum has changed its cases:

| Before                | After              |
|-----------------------|--------------------|
| `EQUALS`              | `Equal`            |
| `CONTAINS`            | `Contain`          |
| `NOT_CONTAINS`        | `NotContain`       |
| `NOT_EQUALS`          | `NotEqual`         |
| `GREATER_THAN`        | `GreaterThan`      |
| `GREATER_THAN_EQUALS` | `GreaterThanEqual` |
| `LESS_THAN_EQUALS`    | `LessThanEqual`    |
| `LESS_THAN`           | `LessThan`         |
| `START_WITH`          | `StartWith`        |
| `END_WITH`            | `EndWith`          |

Replace all occurrences of the old cases with the new ones.

Additionally, the translation keys of operator cases have changed:

| Operator           | Translation key before | Translation key after   |
|--------------------|------------------------|-------------------------|
| `Equal`            | `EQUALS`               | `Equal`                 |
| `Contain`          | `CONTAINS`             | `Contain`               |
| `NotContain`       | `NOT_CONTAINS`         | `Not contain`           |
| `NotEqual`         | `NOT_EQUALS`           | `Not equal`             |
| `GreaterThan`      | `GREATER_THAN`         | `Greater than`          |
| `GreaterThanEqual` | `GREATER_THAN_EQUALS`  | `Greater than or equal` |
| `LessThanEqual`    | `LESS_THAN_EQUALS`     | `Less than or equal`    |
| `LessThan`         | `LESS_THAN`            | `Less than`             |
| `StartWith`        | `STARTS_WITH`          | `Start with`            |
| `EndWith`          | `ENDS_WITH`            | `End with`              |

## Removed data tables `*_persistence_subject` options

The following data table options are no longer available:

- `filtration_persistence_subject`
- `sorting_persistence_subject`
- `pagination_persistence_subject`
- `personalization_persistence_subject`

Remove every occurrence of these options from your data tables.
Instead, set the subject provider (that will provide a persistence subject) options:

- `filtration_persistence_subject_provider`
- `sorting_persistence_subject_provider`
- `pagination_persistence_subject_provider`
- `personalization_persistence_subject_provider`

## Column, filter and exporter builders

Internally, the columns, filters and exporters are now utilizing the builder pattern similar to data tables and actions.
If your application contains custom logic using internal bundle classes, you may need to update it.