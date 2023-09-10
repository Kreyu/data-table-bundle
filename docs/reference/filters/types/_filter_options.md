### `label`

- **type**: `null`, `false`, `string` or `Symfony\Component\Translation\TranslatableInterface` 
- **default**: {{ option_label_default_value ?? '`null` - the label is "guessed" from the column name' }}

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

### `form_type`

- **type**: `string` 
- **default**: {{ option_form_type_default_value ?? '`\'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType\'`' }}

This is the form type used to render the filter value field.

### `form_options`

- **type**: `array`
- **default**: {{ option_form_options_default_value ?? '`[]`' }}

This is the array that's passed to the form type specified in the `form_type` option.

The normalizer ensures the default `['required' => false]` is added.

{{ option_form_options_notes }}

### `operator_form_type`

- **type**: `string` 
- **default**: `Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType`

This is the form type used to render the filter operator field.

!!!
**Note**: if the `operator_selectable` option is `false`, the form type is changed to `Symfony\Component\Form\Extension\Core\Type\HiddenType` by the normalizer.
!!!

### `operator_form_options`

- **type**: `array` 
- **default**: `[]`

This is the array that's passed to the form type specified in the `operator_form_type` option.

!!! Note
The normalizer can change default value of this option based on another options:

- if the `operator_selectable` option is `false`, the `default_operator` is used as a `data` option
- if the `operator_form_type` is `OperatorType`, the `choices` array defaults to the `supported_operators` option
- if the `operator_form_type` is `OperatorType`, the `empty_data` defaults to the `default_operator` option value.
!!!

### `default_operator`

- **type**: `Kreyu\Bundle\DataTableBundle\Filter\Operator`
- **default**: `Kreyu\Bundle\DataTableBundle\Filter\Operator\Operator::Equals`

The default operator used for the filter.

### `supported_operators`

- **type**: `Kreyu\Bundle\DataTableBundle\Filter\Operator[]`
- **default**: depends on the filters, see "supported operators" at the top of the page

The operators supported by the filter.

### `operator_selectable`

- **type**: `bool`
- **default**: `false`

Determines whether the operator can be selected by the user.

By setting this option to `false`, the normalizer changes the `operator_form_type` to `Symfony\Component\Form\Extension\Core\Type\HiddenType`. 

### `empty_data`

- **type**: `string` or `array`
- **default**: {{ option_empty_data_default_value ?? '`\'\'`' }}

Represents a value of the filter when it's empty.

{{ option_empty_data_note }}
