# Personalization

This bundle provides personalization feature, what gives users the ability to freely show/hide specific columns and even set their order per data-table basis.

## Configuring the personalization feature

By default, the personalization is enabled for every data table type.

Every part of the feature can be configured using the [data table options](#passing-options-to-data-tables):

- `personalization_enabled` - to enable/disable feature completely;
- `personalization_persistence_enabled` - to enable/disable feature persistence;
- `personalization_persistence_adapter` - to change the persistence adapter;
- `personalization_persistence_subject` - to change the persistence subject directly;

By default, if the feature is enabled, the [persistence adapter](#persistence-adapters) and [subject provider](#persistence-subject-providers) are autoconfigured.
