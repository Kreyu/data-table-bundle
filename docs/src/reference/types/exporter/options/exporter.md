### `use_headers`

- **type**: `bool`
- **default**: `true`

Determines whether the exporter should add headers to the output file.

### `label`

- **type**: `null` or `string`
- **default**: `null` the label is "guessed" from the exporter name

Sets the label of the exporter, visible in the export action modal.

### `tempnam_dir`

- **type**: `string`
- **default**: the value returned by the `sys_get_temp_dir()` function

Sets the directory used to store temporary file during the export process.

### `tempnam_prefix`

- **type**: `string`
- **default**: `exporter_`

Sets the prefix used to generate temporary file names during the export process.