# LinkType

The [LinkType](../../../src/Column/Type/LinkType.php) represents a column with value displayed as a link.

## Options

### `href`

**type**: `string` or `callable` **default**: `'#'`

Sets the value that will be used as a link `href` attribute (see [href attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-href)).  
Callable can be used to provide an option value based on a row value, which is passed as a first argument.

```php
$columns
    ->add('category', LinkType::class, [
        'property_path' => false,
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

**type**: `string` or `callable` **default**: `'_self'`

Sets the value that will be used as an anchor `target` attribute (see [target attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a#attr-target)).  
Callable can be used to provide an option value based on a row value, which is passed as a first argument.

## Inherited options

See [abstract column type documentation](abstract.md).
