### `auto_alias_resolving`

- **type**: `bool`
- **default**: `true`

Determines whether the root alias should be automatically resolved.
This means that filtering on the `name` (no dot, therefore no alias e.g. `product.name`) 
the field will automatically resolve to `product.name` if the root alias is `product`.
