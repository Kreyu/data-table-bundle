# HtmlType

The [HtmlType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/PhpSpreadsheet/Exporter/Type/HtmlType.php) represents an exporter that uses a [PhpSpreadsheet HTML writer](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/src/PhpSpreadsheet/Writer/Html.php).

!!! Note

    Please note that HTML file format has some limits regarding styling cells, number formatting, etc.  
    For more details, see [PhpSpreadsheet HTML Writer documentation](https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#phpofficephpspreadsheetwriterhtml).

## Options

### `sheet_index`

**type**: `null` or `int` **default**: `0`

HTML files can only contain one or more worksheets. 
Therefore, you can specify which sheet to write to HTML.
If you want to write all sheets into a single HTML file, set this option to `null`.

### `images_root`

**type**: `string` **default**: `''`

There might be situations where you want to explicitly set the included images root. For example, instead of:

```html
<img src="./images/logo.jpg">
```

You might want to see:

```html
<img src="https://www.domain.com/images/logo.jpg">
```

Use this option to achieve this result:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\HtmlExporterType;

$builder
    ->addExporter('html', HtmlExporterType::class, [
        'images_root' => 'https://www.domain.com',
    ])
;
```

### `embed_images`

**type**: `bool` **default**: `false`

Determines whether the images should be embedded or not.

### `use_inline_css`

**type**: `bool` **default**: `false`

Determines whether the inline css should be used or not.

### `generate_sheet_navigation_block`

**type**: `bool` **default**: `true`

Determines whether the sheet navigation block should be generated or not.

### `edit_html_callback`

**type**: `null` or `callable` **default**: `null`

Accepts a callback function to edit the generated html before saving. 
For example, you could change the gridlines from a thin solid black line:

```php
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\HtmlExporterType;

$builder
    ->addExporter('html', HtmlExporterType::class, [
        'edit_html_callback' => function (string $html): string {
            return str_replace(
                '{border: 1px solid black;}',
                '{border: 2px dashed red;}',
                $html,
            );
        } 
    ])
;
```

### `decimal_separator`

**type**: `string` **default**: depends on the server's locale setting

If the worksheet you are exporting contains numbers with decimal separators,
then you should think about what characters you want to use for those before doing the export.

By default, PhpSpreadsheet looks up in the server's locale settings to decide what character to use. 
But to avoid problems it is recommended to set the character explicitly.

### `thousands_separator`

**type**: `string` **default**: depends on the server's locale setting

If the worksheet you are exporting contains numbers with thousands separators,
then you should think about what characters you want to use for those before doing the export.

By default, PhpSpreadsheet looks up in the server's locale settings to decide what character to use.
But to avoid problems it is recommended to set the character explicitly.

## Inherited options

See [base PhpSpreadsheet exporter type documentation](/reference/exporting/#phpspreadsheettype).
