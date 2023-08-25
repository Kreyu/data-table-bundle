# 0.14

- **[Feature]** Data table events (see more)
- **[Feature] [Breaking change]** The data table type persistence subject options are removed in favor of subject provider options (see more) 
- **[Feature] [Breaking Change]** Optimized exporting process - introduces breaking changes (see more)
- **[Feature]** Doctrine ORM proxy query class now allows setting the hydration mode (see more)
- **[Feature]** Improved DX with data table search handler (see more)
- **[Feature]** CollectionColumnType default separator is now `', '` (with space after comma) instead of `','`
- **[Bugfix]** CollectionColumnType now renders without spaces around separator

Internally, the columns, filters and exporters are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!

For a list of all breaking changes, see the [upgrade guide](UPGRADE_GUIDE_0_14.md).

# 0.13

- **[Feature]** Batch actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/batch-actions/))
- **[Feature]** Improved DX with row actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/row-actions/))
- **[Feature]** Actions `visible` option ([see more](https://data-table-bundle.swroblewski.pl/reference/actions/types/action/#visible))
- **[Feature]** NumberColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/number/))
- **[Feature]** MoneyColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/money/))

Internally, the actions are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!
