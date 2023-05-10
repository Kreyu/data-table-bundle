---
order: g
---

# Exporting the data

Ability to export the data is crucial in some use cases. The exporting process is handled by the exporters, which, similar to data tables and its columns and filters, are using the [Types API](../philosophy/understanding-the-types-api.md).

<figure><img src="../.gitbook/assets/image_2023-04-25_21-55-22.png" alt=""><figcaption><p>Export modal with the built-in Tabler theme</p></figcaption></figure>

## Prerequisites

The bundle comes with exporter types using the PhpSpreadsheet.\
This library is not included as a bundle dependency, therefore you have to make sure it is installed:

```bash
$ composer require phpoffice/phpspreadsheet
```

## Adding exporters to the data table

To add exporter, use the builder's `addExporter()` method:

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns and filters added before...
        
        $builder
            ->addExporter('csv', CsvExporterType::class)
            ->addExporter('xlsx', XlsxExporterType::class)
        ;
    }
}
```
{% endcode %}

First argument represents an exporter name. The second argument represents a fully qualified class name of an exporter type, which similarly to data table, column and filter type classes, works as a blueprint for an exporter - and describes how to handle it.

For reference, see [built-in exporter types](../reference/exporters/types.md).

## Adding multiple exporters of the same type

Let's think of a scenario, where the user wants to export the data table to CSV format, \
but there's a catch - is must be possible to export as either comma or semicolon separated file.

{% code title="src/DataTable/Type/ProductDataTableType.php" lineNumbers="true" %}
```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class ProductDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        // Columns and filters added before...
        
        $builder
            ->addExporter('csv_comma', CsvExporterType::class, [
                'label' => 'CSV (separated by comma)',
                'delimiter' => ',',
            ])
            ->addExporter('csv_semicolon', CsvExporterType::class, [
                'label' => 'CSV (separated by semicolon)',
                'delimiter' => ';',
            ])
            ->addExporter('xlsx', XlsxExporterType::class)
        ;
    }
}
```
{% endcode %}

{% hint style="info" %}
**Note**&#x20;

The given exporter names **must** be unique!\
Adding a second exporter with the same name as first will overwrite the first.
{% endhint %}

Similar to built-in column and filter types, each exporter type defines its own set of options.\
To see what options are available for which type, see [built-in exporter types reference](../reference/exporters/types.md).&#x20;
