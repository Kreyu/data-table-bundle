# Understanding type classes

Multiple parts of the bundle, such as columns, filters etc. are described using the type classes. The type classes are similar to the [form types](https://symfony.com/doc/current/reference/forms/types.html), with small differences, making them tailored for the usage in the data tables.

Following parts of the bundle are defined using the type classes:

* data tables
* columns
* filters
* actions
* exporters

## Defining the type

The type classes work as a blueprint that defines a configuration how its feature should work. They implement their own, feature-specific interface. 
However, it is better to extend from the abstract classes, which already implement the interface and provide some utilities.

| Component   | Interface                                                                                                                 | Abstract class                                                                                                          |
|-------------|---------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------|
| Data tables | [DataTableTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableTypeInterface.php)        | [AbstractDataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractDataTableType.php)        |
| Columns     | [ColumnTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnTypeInterface.php)       | [AbstractColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnType.php)       |
| Filters     | [FilterTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/FilterTypeInterface.php)       | [AbstractFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterType.php)       |
| Actions     | [ActionTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ActionTypeInterface.php)       | [AbstractActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/AbstractActionType.php)       |
| Exporters   | [ExporterTypeInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ExporterTypeInterface.php) | [AbstractExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/AbstractExporterType.php) |

Every type in the bundle is registered as a [tagged service](https://symfony.com/doc/current/service_container/tags.html):

| Component   | Type tag                         |
|-------------|----------------------------------|
| Data tables | `kreyu_data_table.type`          |
| Columns     | `kreyu_data_table.column.type`   |
| Filters     | `kreyu_data_table.filter.type`   |
| Actions     | `kreyu_data_table.action.type`   |
| Exporters   | `kreyu_data_table.exporter.type` |


### Using the inheritance

For example, let's think of a column type that represents a phone number. In theory, it should extend the existing text column type, only adding a phone number oriented formatting. In practice, the type's class **should not** extend the text type class:

!!!danger
This is <span style="color:#e5413e;">invalid</span> - do **NOT** use PHP class inheritance!
```php #
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class PhoneColumnType extends TextColumnType
{
}
```
!!!

Instead, it should return the fully qualified class name of the parent type in the `getParent()` method:

!!!success
This is <span style="color:#36ad99;">valid</span> - extend abstract type and return parent's class name!

```php #
use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class PhoneColumnType implements AbstractColumnType
{
    public function getParent(): string
    {
        return TextColumnType::class;
    }
}
```
!!!

The difference is all about the extensions. Considering the example above, while using the PHP inheritance, 
a [type extensions](#defining-the-type-extensions) defined for the text column type won't be applied to the phone column type.

### Adding configuration options

Each type class contains its own set of options, that can be used to configure the type according to a specific need. 
The options can be defined in the `configureOptions()` method by using the [OptionsResolver component](https://symfony.com/doc/current/components/options_resolver.html).

## Defining the type extensions

The type extensions allow to easily extend existing types. Those classes contain methods similar as their corresponding feature type classes. They implement their own, feature-specific interface. For easier usage, there's also an abstract classes, which already implements the interface and provides some utilities.

| Component   | Interface                                                                                                                                    | Abstract class                                                                                                                            |
|-------------|----------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------|
| Data tables | [DataTableTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Type/DataTableTypeInterface.php)        | [AbstractDataTableTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/AbstractDataTableExtensionType.php)        |
| Columns     | [ColumnTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Column/Type/ColumnTypeInterface.php)       | [AbstractColumnTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/AbstractColumnExtensionType.php)       |
| Filters     | [FilterTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Filter/Type/FilterTypeInterface.php)       | [AbstractFilterTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterExtensionType.php)       |
| Actions     | [ActionTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Action/Type/ActionTypeInterface.php)       | [AbstractFilterTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/AbstractFilterExtensionType.php)       |
| Exporters   | [ExporterTypeExtensionInterface](https://github.com/Kreyu/data-table-bundle/blob/main/src/Extension/Exporter/Type/ExporterTypeInterface.php) | [AbstractExporterTypeExtension](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/AbstractExporterExtensionType.php) |

### Configuring the types to extend

Each type extension class **have to** define a list of types that it extends, using the `getExtendedTypes()` method. 
For example, if you wish to create an extension for a built-in text column type, consider following configuration:

```php #
use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractColumnTypeExtension;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;

class TextColumnTypeExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [TextColumnType::class];
    }
}
```

To apply extension to _every type_ in the system, use the base type of each part of the bundle. For example, in case of the column types:

```php #
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractColumnTypeExtension;

class ColumnTypeExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ColumnType::class];
    } 
}
```

For reference, a list of each feature base type class:

| Component   | Base type class                                                                                         |
|-------------|---------------------------------------------------------------------------------------------------------|
| Data tables | [DataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/DataTableType.php)        |
| Columns     | [ColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ColumnType.php)       |
| Filters     | [FilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/FilterType.php)       |
| Actions     | [ActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ActionType.php)       |
| Exporters   | [ExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ExporterType.php) |

### Configuring the order of extension loading

Every type extension in the bundle is registered as a [tagged service](https://symfony.com/doc/current/service_container/tags.html):

| Component   | Service tag                                |
|-------------|--------------------------------------------|
| Data tables | `kreyu_data_table.type_extension`          |
| Columns     | `kreyu_data_table.column.type_extension`   |
| Filters     | `kreyu_data_table.filter.type_extension`   |
| Actions     | `kreyu_data_table.action.type_extension`   |
| Exporters   | `kreyu_data_table.exporter.type_extension` |

Tagged services [can be prioritized using the `priority` attribute](https://symfony.com/doc/current/service\_container/tags.html#tagged-services-with-priority) to define the order the extensions will be loaded:

```yaml # config/services.yaml
services:
    App\DataTable\Extension\ExtensionA:
        tags:
            - { name: kreyu_data_table.type_extension, priority: 1 }

    App\DataTable\Extension\ExtensionB:
        tags:
            - { name: kreyu_data_table.type_extension, priority: 2 }
```

In the example above, the `ExtensionB` will be applied before the `Extension A`, because it has higher priority.
Without the priority specified, the extensions would be applied in the order they are registered.

## The type resolving process

Because type classes support inheritance and extensions, they have to be **resolved** before usage.
Each component that supports the type classes contains its resolved counterpart:

| Component   | Resolved type class                                                                                                     |
|-------------|-------------------------------------------------------------------------------------------------------------------------|
| Data tables | [ResolvedDataTableType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Type/ResolvedDataTableType.php)        |
| Columns     | [ResolvedColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/ResolvedColumnType.php)       |
| Filters     | [ResolvedFilterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/Type/ResolvedFilterType.php)       |
| Actions     | [ResolvedActionType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/Type/ResolvedActionType.php)       |
| Exporters   | [ResolvedExporterType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ResolvedExporterType.php) |

Resolved type classes contain similar methods as a non-resolved types, and handle both inheritance & extensions. 
For example, take a look at implementation of the resolved data table type's `buildDataTable()` method:

```php # vendor/kreyu/data-table-bundle/src/Type/ResolvedDataTableType.php
public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
{
    $this->parent?->buildDataTable($builder, $options);

    $this->innerType->buildDataTable($builder, $options);

    foreach ($this->typeExtensions as $extension) {
        $extension->buildDataTable($builder, $options);
    }
}
```

First, the type's parent method is called, followed by the type itself, then comes the extensions.
This is why defining an order of extensions may be very important in some cases. 
Same flow applies to every resolved type class and most of its methods in the bundle.

## Accessing the type registry

The registry stores all the types and extensions registered in the system.
Those classes can be used to retrieve a specific type or extension using their fully qualified class name.
Each component that supports the type classes contains its own registry:

| Component   | Resolved type class                                                                                        |
|-------------|------------------------------------------------------------------------------------------------------------|
| Data tables | [DataTableRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/DataTableRegistry.php)        |
| Columns     | [ColumnRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/ColumnRegistry.php)       |
| Filters     | [FilterRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Filter/FilterRegistry.php)       |
| Actions     | [ActionRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Action/ActionRegistry.php)       |
| Exporters   | [ExporterRegistry](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/ExporterRegistry.php) |
