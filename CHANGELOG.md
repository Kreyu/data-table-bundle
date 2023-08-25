# 0.14

- **[Breaking Change]** The data tables `*_persistence_subject` options are removed in favor of `*_persistence_subject_provider` options
- **[Breaking Change]** Column types now contains `buildExportHeaderView` and `buildExportValueView` used exclusively for exporting. 
Any export-specific logic should be moved from existing `buildHeaderView` and `buildValueView` methods to the new ones.

- **[Feature]** Improved DX with data table search handler
- **[Feature]** CollectionColumnType default separator is now `', '` (with space after comma) instead of `','`
- **[Bugfix]** CollectionColumnType now renders without spaces around separator

Internally, the columns, filters and exporters are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!

# 0.13

- **[Feature]** Batch actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/batch-actions/))
- **[Feature]** Improved DX with row actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/row-actions/))
- **[Feature]** Actions `visible` option ([see more](https://data-table-bundle.swroblewski.pl/reference/actions/types/action/#visible))
- **[Feature]** NumberColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/number/))
- **[Feature]** MoneyColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/money/))

Internally, the actions are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!
