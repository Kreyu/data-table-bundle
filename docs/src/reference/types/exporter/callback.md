<script setup>
    import ExporterTypeOptions from "./options/exporter.md";
</script>

# CallbackExporterType

The [`CallbackExporterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Exporter/Type/CallbackExporterType.php) represents a filter that uses a given callback as its handler.

## Options

### `callback`

- **type**: `\Closure`

Sets closure that works as an exporter handler.

```php
use Kreyu\Bundle\DataTableBundle\Exporter\Type\CallbackExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\DataTableView;

$builder
    ->addExporter('txt', CallbackExporterType::class, [
        'callback' => function (DataTableView $view, ExporterInterface $exporter, string $filename): ExportFile {
            // ...
        },
    ])
;
```

## Inherited options

<ExporterTypeOptions />
