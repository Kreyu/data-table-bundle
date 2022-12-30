# AbstractFilter

Every filter should extend from [AbstractFilter](#), to inherit necessary options.

## Options

### `label`

**type**: `string` or `TranslatableMessage` **default**: the label is "guessed" from the filter name

Sets the label that will be used when rendering the filter label.

### `label_translation_parameters`

**type**: `array` **default**: `[]`

Sets the parameters used when translating the `label` option.

### `translation_domain`

**type**: `false` or `string` **default**: the default `KreyuDataTable` is used

Sets the translation domain used when translating the filter translatable values.  
Setting the option to `false` disables translation for the filter.

### `field_name`

**type**: `string` **default**: the field name is "guessed" from the filter name

### `field_type`

**type**: `string` **default**: `'Symfony\Component\Form\Extension\Core\Type\TextType'`

### `field_options`

**type**: `array` **default**: `[]`

### `operator_type`

**type**: `string` **default**: `'Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType'`

### `operator_options`

**type**: `array` **default**: `[]`
