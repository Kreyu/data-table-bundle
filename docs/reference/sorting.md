# Sorting

This bundle provides sorting feature, what gives users the ability to sort the data table by its columns.

## Configuring the sorting feature

By default, the sorting is enabled for every data table type.

Every part of the feature can be configured using the [data table options](#passing-options-to-data-tables):

- `sorting_enabled` - to enable/disable feature completely;
- `sorting_persistence_enabled` - to enable/disable feature persistence;
- `sorting_persistence_adapter` - to change the persistence adapter;
- `sorting_persistence_subject` - to change the persistence subject directly;

By default, if the feature is enabled, the [persistence adapter](#persistence-adapters) and [subject provider](#persistence-subject-providers) are autoconfigured.
