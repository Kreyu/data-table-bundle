### `header_row_style`

- **type**: `null`, `callable` or `OpenSpout\Common\Entity\Style\Style`
- **default**: `null`

Style object used to style the header row.
A callable can be used to dynamically apply styles based on the row:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use OpenSpout\Common\Entity\Style\Style;

$builder
    ->addExporter('xlsx', XlsxExporterType::class, [
        'header_row_style' => function (HeaderRowView $view, array $options): Style {
            return (new Style())->setFontBold();
        },
    ])
;
```

### `value_row_style`

- **type**: `null`, `callable` or `OpenSpout\Common\Entity\Style\Style`
- **default**: `null`

Style object used to style the value row.
A callable can be used to dynamically apply styles based on the row:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

$builder
    ->addExporter('xlsx', XlsxExporterType::class, [
        'value_row_style' => function (ValueRowView $view, array $options): Style {
            $style = new Style();
            
            if ($view->data->getQuantity() === 0) {
                $style->setFontColor(Color::RED);
            }
            
            return $style;
        },
    ])
;
```

### `header_cell_style`

- **type**: `null`, `callable` or `OpenSpout\Common\Entity\Style\Style`
- **default**: `null`

Style object used to style the header cells.
A callable can be used to dynamically apply styles based on the column:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use OpenSpout\Common\Entity\Style\Style;

$builder
    ->addExporter('xlsx', XlsxExporterType::class, [
        'header_cell_style' => function (ColumnHeaderView $view, array $options): Style {
            return (new Style())->setFontBold();
        },
    ])
;
```

### `value_cell_style`

- **type**: `null`, `callable` or `OpenSpout\Common\Entity\Style\Style`
- **default**: `null`

Style object used to style the value cells.
A callable can be used to dynamically apply styles based on the column:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

$builder
    ->addExporter('xlsx', XlsxExporterType::class, [
        'value_cell_style' => function (ColumnValueView $view, array $options): Style {
            $style = new Style();
            
            if ($view->data->getQuantity() === 0) {
                $style->setFontColor(Color::RED);
            }
            
            return $style;
        },
    ])
;
```
