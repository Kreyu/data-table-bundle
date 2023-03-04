# TemplateType

The [TemplateType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/TemplateType.php) represents a column with value displayed as a link.

## Options

### `template_path`

**type**: `string` or `\Closure`

Sets the path to the template that should be rendered.  
Closure can be used to provide an option value based on a row value, which is passed as a first argument.

### `template_vars`

**type**: `string` or `\Closure` **default**: `'#'`

Sets the variables used within the template.  
Closure can be used to provide an option value based on a row value, which is passed as a first argument.

## Inherited options

See [base column type documentation](column.md).
