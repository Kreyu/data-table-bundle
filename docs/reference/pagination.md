# Pagination

This bundle provides pagination feature, what gives users the ability to display data in chunks, which saves memory on huge data sources.

## Configuring the pagination feature

By default, the pagination is enabled for every data table type.

Every part of the feature can be configured using the [data table options](#passing-options-to-data-tables):

- `pagination_enabled` - to enable/disable feature completely;
- `pagination_persistence_enabled` - to enable/disable feature persistence;
- `pagination_persistence_adapter` - to change the persistence adapter;
- `pagination_persistence_subject` - to change the persistence subject directly;

By default, if the feature is enabled, the [persistence adapter](#persistence-adapters) and [subject provider](#persistence-subject-providers) are autoconfigured.
