# Exporters

[[toc]]

## Adding exporters

To add an exporter, use the data table builder's `addExporter()` method:

```php
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\CsvExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;

class UserDataTableType extends AbstractDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addExporter('csv', CsvExporterType::class)
            ->addExporter('xlsx', XlsxExporterType::class)
        ;
    }
}
```

This method accepts _three_ arguments:

- exporter name;
- exporter type — with a fully qualified class name;
- exporter options — defined by the exporter type, used to configure the exporter;

For reference, see [available exporter types](../../reference/types/exporter.md).

## Creating exporter types

Exporter types are classes that implement [`ExporterTypeInterface`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ExporterTypeInterface.php). However, it's better to extend from the [`AbstractExporterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/AbstractExporterType.php):

```php
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType;

class CustomExporterType extends AbstractExporterType
{
}
```

<div class="tip custom-block" style="padding-top: 8px;">

Recommended namespace for the exporter type classes is `App\DataTable\Exporter\Type\`.

</div>

## Exporter type inheritance

To make a type class use another type as a parent, provide its fully-qualified class name in the `getParent()` method:

```php
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\CallbackExporterType;

class CustomExporterType extends AbstractExporterType
{
    public function getParent(): ?string
    {
        return CallbackExporterType::class;
    }
}
```

::: tip
If you take a look at the [`AbstractExporterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/AbstractExporterType.php),
you'll see that `getParent()` method returns fully-qualified name of the [`ExporterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/ExporterType.php) type class.
This is the type that defines all the basic options, such as `label`, `use_headers`, etc.
:::
