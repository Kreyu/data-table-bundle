<script setup>
    import ColumnTypeOptions from "./options/column.md";
</script>

# HtmlColumnType

The [`HtmlColumnType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/HtmlColumnType.php) represents a column with value displayed as HTML.

## Options

### `raw`

- **type**: `bool`
- **default**: `true`

Defines whether the value should be rendered as raw HTML.

For example, if your column contains a string `<strong>Foo</strong>`:
- setting it to `true` will render the value as a bold text: **Foo**
- setting it to `false` will render the value as a plain text: `<strong>Foo</strong>`

### `strip_tags`

- **type**: `bool`
- **default**: `false`

Defines whether the tags should be stripped. Internally uses the [`strip_tags`](https://twig.symfony.com/doc/3.x/filters/striptags.html) function.

For example, if your column contains a string `<strong>Foo</strong>`:
- setting it to `true` will render the value as a simple text: Foo
- setting it to `false` will render the value as is: `<strong>Foo</strong>`

### `allowed_tags`

- **type**: `null`, `string` or `string[]`
- **default**: `null`

Defines tags which should not be stripped if `strip_tags` is set to `true`, e.g. `<br><p>`.

For example, if your column contains a string `<strong>Foo</strong><br/>`:
- setting it to `"<strong>"` will render the value as: `Foo<br/>`
- setting it to `"<strong><br>"` will render the value as: `<strong>Foo</strong><br/>`
- setting it to `["<strong>", "<br>"]` will render the value as: `<strong>Foo</strong><br/>`
- setting it to `null` (by default) will render the value as: `<strong>Foo</strong><br/>`

> [!WARNING]
> In Twig, this option is ignored if `strip_tags` option is set to `false`.

## Inherited options

<ColumnTypeOptions/>
