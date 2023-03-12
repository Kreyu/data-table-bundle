# LinkColumnType

The [:material-github: LinkColumnType](https://github.com/Kreyu/data-table-bundle/blob/main/src/Column/Type/LinkColumnType.php) represents a column with value displayed as a link.

## Options

### `href`

**type**: `string` or `\Closure` **default**: `'#'`

Sets the value that will be used as a link `href` attribute (see [href attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-href)).  
Closure can be used to provide an option value based on a row value, which is passed as a first argument.

```php
$columns
    ->add('category', LinkColumnType::class, [
        'value' => function (Category $category): string {
            return $category->getName(),
        },
        'href' => function (Category $category): string {
            return $this->urlGenerator->generate('category_show', [
                'id' => $category->getId(),
            ]);
        },
    ])
;
```

### `target`

**type**: `string` or `\Closure` **default**: `'_self'`

Sets the value that will be used as an anchor `target` attribute (see [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target)).  
Closure can be used to provide an option value based on a row value, which is passed as a first argument.

### `display_icon`

**type**: `bool` **default**: `true`

If this value is true, an icon will be visible next to the link label.

## Inherited options

{% include-markdown "_column_options.md" heading-offset=2 %}