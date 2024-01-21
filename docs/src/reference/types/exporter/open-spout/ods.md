<script setup>
    import ExporterTypeOptions from "../options/exporter.md";
</script>

# OpenSpout OdsExporterType

The [`OdsExporterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/OpenSpout/Exporter/Type/OdsExporterType.php) represents an exporter that uses an [OpenSpout](https://github.com/openspout/openspout) ODS writer.

## Options

### `default_row_style`

- **type**: `OpenSpout\Common\Entity\Style\Style`
- **default**: an unmodified instance of `Style` class

An instance of style class that will be applied to all rows.

### `should_create_new_sheets_automatically`

- **type**: `bool`
- **default**: `true`

Determines whether new sheets should be created automatically
when the maximum number of rows (1,048,576) per sheet is reached.

### `default_column_width`

- **type**: `null` or `float`
- **default**: `null`

Represents a width that will be applied to all columns by default.

### `default_row_height`

- **type**: `null` or `float`
- **default**: `null`

Represents a height that will be applied to all rows by default.

## Inherited options

<ExporterTypeOptions />