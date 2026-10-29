# Unreleased

## Breaking changes

- **Symfony 6.4 or higher is required.** Symfony 6.0–6.3 are no longer supported. PHP 8.1 stays the minimum.
- **Twig 3.21 or higher is required.**
- **Row grouping compares values with `===`.** `DateTime` values with the same date and time still form one group. Other objects form one group only when they're the same instance, so value objects hydrated per row (UUIDs, money) need a `group_by` callable that returns a scalar.
- **`ExporterTypeTestCase::createDataTableView()` keys row children `column_0`, `column_1`, …** instead of `0`, `1`, …. Associative rows work too.
- **The `ActionsColumnType` action `type_options` accepts arrays only.** A callable was accepted before but always failed at render.
- **Doctrine ORM 3.7 or higher is required.** The Doctrine bridge uses `OffsetPaginator` instead of the deprecated `Paginator`. Custom implementations must follow the new signatures:
  - `PaginatorFactoryInterface::create(QueryBuilder $queryBuilder, Query $query, array $hints = []): OffsetPaginator`
  - `DoctrineOrmResultSetFactoryInterface::create(OffsetPaginator $paginator, Query $query, int $batchSize = 5000): ResultSet`
  - Exports run the count query once per batch (`OffsetPaginator` always counts), instead of once per export.
- **New interface methods.** Custom implementations of these interfaces must add them:
  - `DataTableInterface`: `setColumnVisibilityGroup()`, `getColumnVisibilityGroup()`, `isDeferred()`
  - `DataTableConfigInterface`: `getPaginationFactory()`, `getColumnVisibilityGroups()`, `getColumnVisibilityGroupParameterName()`, `getGroupBy()`
  - `DataTableConfigBuilderInterface`: `setPaginationFactory()`, `setColumnVisibilityGroups()`, `setGroupBy()`
  - `DataTableTypeInterface` and `ResolvedDataTableTypeInterface`: `createDefaultQuery()`
- **Dotted filter names use `:` in URLs.** `filter_products[author.name]` is now sent as `filter_products[author:name]` instead of `filter_products[author__name]`. Bookmarked URLs with the old key no longer apply that filter.
- **`base` renders the confirmation dialog for link, button, form and dropdown item actions that set `confirmation`.** Confirmation stays opt-in (`false` by default). Custom themes that render their own dialog for these actions must turn the built-in one off with `{% with { confirmation: false } %}{{ parent() }}{% endwith %}`.
- **`base` shows active filter chips in `action_bar`.** Custom themes that render chips elsewhere show them twice.
- **The `bootstrap-modal` Stimulus controller is deprecated.** Modals and confirmations use the native `<dialog>` element through the new `dialog` controller. Enable it in `assets/controllers.json`.
- **`MoneyColumnType` extends `NumberColumnType`** through `getParent()`, so `NumberColumnType` type extensions now apply to money columns too.

## Features

- **[Feature]** Symfony 8, PHP 8.5 and Twig 4 support
- **[Feature]** Column header filters: the column `filter` and `filter_options` options render a filter in the column header (https://github.com/Kreyu/data-table-bundle/pull/239)
- **[Feature]** Column visibility groups: the data table `column_visibility_groups` option and the column `column_visibility_groups` option (https://github.com/Kreyu/data-table-bundle/pull/241)
- **[Feature]** Row grouping with the data table `group_by` option (https://github.com/Kreyu/data-table-bundle/pull/257)
- **[Feature]** Data table types can provide their own query with `createDefaultQuery()` (https://github.com/Kreyu/data-table-bundle/pull/252)
- **[Feature]** Deferred data table loading through lazy Turbo frames, with the `async` and `async_loading` options
- **[Feature]** Replaceable pagination factory (`PaginationFactoryInterface`) and the `page_visible_range` option (https://github.com/Kreyu/data-table-bundle/issues/219)
- **[Feature]** daisyUI 5 theme: `@KreyuDataTable/themes/daisyui.html.twig`
- **[Feature]** Native `<dialog>` modals and confirmations, with attribute hooks for the modal, its box, title, description, close and cancel buttons
- **[Feature]** Theme attribute hooks: `root_attr`, `cell_attr`, `value_cell_attr`, `active_filters_attr`, `disabled_attr`
- **[Feature]** `FormActionType` `csrf_token_id` option
- **[Feature]** The action `identifier` view variable
- **[Feature]** `has_active_non_search_filters` view variable and `FiltrationData::hasActiveFiltersExcept()`
- **[Feature]** Date filter chips are formatted in the current locale
- **[Feature]** Personalization supports dotted column names (`author.name`)

## Bugfixes

- **[Bugfix]** Sort and pagination links keep the search term and dotted filter names
- **[Bugfix]** Clear filter links work for dotted filter names
- **[Bugfix]** Dotted filter names no longer throw in the filtration view
- **[Bugfix]** Array data keeps its pagination when sorted after paginating
- **[Bugfix]** Personalization and intl number formatter options work on Symfony 8
- **[Bugfix]** Confirmations on dropdown item actions are no longer ignored
- **[Bugfix]** Dialogs no longer stay open after navigating back with Turbo
- **[Bugfix]** Resolved type factory service aliases point to existing services
- **[Bugfix]** A `null` confirmation type no longer breaks the Bootstrap 5 theme
- **[Bugfix]** The OpenSpout `value_row_style` callable receives the value row instead of the header row
- **[Bugfix]** Doctrine queries selecting a function expression no longer throw when resolving aliases

# 0.17

## Breaking changes

### Type classes are now final

Built-in data table, column, filter, action and exporter types are now marked as "final".
This promotes the usage of `getParent()` method instead of PHP inheritance.

### Removed extension mechanisms

The extensions (not *type* extensions!) were a mechanisms copied from Symfony Form component.
Their main purpose was to provide a way to load type and type extension classes manually.
In most cases, that would be used only outside the framework, which is out of scope of this bundle.

Everything related with extensions is now completely removed.
Every type and type extension class defined in the container is now passed directly to the registry classes.

# 0.16

- **[Feature]** French translation (https://github.com/Kreyu/data-table-bundle/pull/53)
- **[Feature]** Doctrine ORM expression transformers with built-in `lower`, `upper` and `trim` options (https://github.com/Kreyu/data-table-bundle/issues/50)
- **[Feature]** Filter handler events
- **[Docs]** New documentation, written using [VitePress](https://vitepress.dev/)

# 0.15

- **[Feature]** Integration with AssetMapper (https://github.com/Kreyu/data-table-bundle/issues/42)

# 0.14

- **[Feature]** Data table events
- **[Feature]** Column `priority` option to allow setting order of columns
- **[Feature]** Column `visible` option to allow setting visibility of columns
- **[Feature]** Column `personalizable` option to allow excluding the column from personalization
- **[Feature]** More verbose filter type form-related options such as `form_type`, `operator_form_type`
- **[Feature]** Ability to set hydration mode of the Doctrine ORM proxy query
- **[Feature]** Data table builder's `setSearchHandler` method for easier search definition
- **[Feature]** The `CollectionColumnType` default separator changed `', '` (with space after comma) instead of `','`
- **[Feature]** Ability to create `ExportData` with exporter name string
- **[Feature]** Ability to provide property path in the `SortingColumnData`. The data table ensures valid property path is given (backwards compatible)
- **[Feature]** The Doctrine ORM `EntityFilterType` no longer requires `form_options.choice_value` option as the identifier field name will be retrieved from Doctrine class metadata by default
- **[Feature]** The `DateColumnType` that works exactly like `DateTimeColumnType`, but with date-only format by default
- **[Breaking change]** The data table type persistence subject options are removed in favor of subject provider options (see more)
- **[Breaking change]** Optimized exporting process - introduces breaking changes (see more)
- **[Breaking change]** The `DataTableBuilder` methods to add columns, filters, actions and exporters has changed definition - the `type` argument is now nullable to prepare for future implementation of type guessers
- **[Bugfix]** Fixed a bug in personalization form where changing the column visibility resulted in an exception
- **[Bugfix]** The `CollectionColumnType` now renders without spaces around separator
- **[Bugfix]** Default export data is now properly used within the export form 

Internally, the columns, filters and exporters are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!

For a list of all breaking changes and deprecations, see the [upgrade guide](../../../docs/upgrade-guide/0.14.md).

# 0.13

- **[Feature]** Batch actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/batch-actions/))
- **[Feature]** Improved DX with row actions ([see more](https://data-table-bundle.swroblewski.pl/features/actions/row-actions/))
- **[Feature]** Actions `visible` option ([see more](https://data-table-bundle.swroblewski.pl/reference/actions/types/action/#visible))
- **[Feature]** NumberColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/number/))
- **[Feature]** MoneyColumnType with optional Intl integration ([see more](https://data-table-bundle.swroblewski.pl/reference/columns/types/money/))

Internally, the actions are now utilizing the builder pattern similar to data tables.
Please note that this is a **breaking change** for applications using internal bundle classes!
