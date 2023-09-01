### `label`

- **type**: `string` or `Symfony\Component\Translation\TranslatableInterface` 
- **default**: the label is "guessed" from the filter name

Sets the label that will be used when rendering the filter.

### `label_translation_parameters`

- **type**: `array` 
- **default**: `[]`

Sets the parameters used when translating the `label` option.

### `translation_domain`

- **type**: `false` or `string`
- **default**: the default `KreyuDataTable` is used

Sets the translation domain used when translating the translatable filter values.  
Setting the option to `false` disables translation for the filter.

### `query_path`

- **type**: `null` or `string` 
- **default**: `null` the query path is "guessed" from the filter name

Sets the path used in the proxy query to perform the filtering on.

### `field_type`

- **type**: `string` 
- **default**: `'Symfony\Component\Form\Extension\Core\Type\TextType`

This is the form type used to render the filter field.

### `field_options`

- **type**: `array`
- **default**: `[]`

This is the array that's passed to the form type specified in the `field_type` option.

### `operator_type`

- **type**: `string` 
- **default**: `Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType`

This is the form type used to render the operator field.

### `operator_options`

- **type**: `array` 
- **default**: `[]`

This is the array that's passed to the form type specified in the `operator_type` option.
