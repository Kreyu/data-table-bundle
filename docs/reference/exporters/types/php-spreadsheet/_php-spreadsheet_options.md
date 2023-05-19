### `pre_calculate_formulas`

- **type**: `bool` 
- **default**: `true`

By default, the PhpSpreadsheet writers pre-calculates all formulas in the spreadsheet.
This can be slow on large spreadsheets, and maybe even unwanted.
The value of this option determines whether the formula pre-calculation is enabled.

{{ include '../_exporter_options' }}