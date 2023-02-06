# ExporterType

The [ExporterType](#) represents a base exporter, used as a parent for every other type in the bundle.

## Options

### `use_headers`

**type**: `bool` **default**: `true`

If this value is true, the output will contain data table headers.

### `label`

**type**: `string` **default**: `[]`

Sets the label that will be used when rendering the exporter to the user.
This is used in the export form, where user can select desired exporter (e.g. "CSV" or "XLSX"). 

### `label_translation_domain`

**type**: `false` or `string` **default**: the default `KreyuDataTable` is used

Sets the translation domain used when translating the exporter label.  
Setting the option to `false` disables translation.

### `tempnam_dir`

**type**: `string`  **default**: `/tmp`

Sets the directory, that the temporarily created export files will be saved to.
Internally, this value is passed as the first argument to the `tempnam()` function.

### `tempnam_prefix`

**type**: `string`  **default**: `exporter_`

Sets the prefix of the temporarily created export files.
Internally, this value is passed as the second argument to the `tempnam()` function.
