<script setup>
const props = defineProps({
    defaults: {
        type: Object,
        default: {
            formType: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType',
            formOptions: '[]',
            defaultOperator: 'Kreyu\\Bundle\\DataTableBundle\\Filter\\Operator::Equals'
        }
    },
})
</script>

### `label`

- **type**: `null`, `false`, `string` or `Symfony\Component\Translation\TranslatableInterface`
- **default**: `null`

Sets the label that will be used when rendering the filter.

When value is `null`, a sentence cased filter name is used as a label, for example:

| Filter name | Guessed label |
|-------------|---------------|
| name        | Name          |
| firstName   | First name    |

### `label_translation_parameters`

- **type**: `array`
- **default**: `[]`

Sets the parameters used when translating the `label` option.

### `translation_domain`

- **type**: `false` or `string`
- **default**: `'KreyuDataTable'`

Sets the translation domain used when translating the translatable filter values.  
Setting the option to `false` disables translation for the filter.

### `query_path`

- **type**: `null` or `string`
- **default**: `null` the query path is guessed from the filter name

Sets the path used in the proxy query to perform the filtering on.

### `form_type`

- **type**: `string`
- **default**: `'{{ defaults.formType }}'`

This is the form type used to render the filter value field.

### `form_options`

- **type**: `array`
- **default**: `{{ defaults.formOptions }}`

This is the array that's passed to the form type specified in the `form_type` option.

### `operator_form_type`

- **type**: `string`
- **default**: `'Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType'`

This is the form type used to render the filter operator field.

### `operator_form_options`

- **type**: `array`
- **default**: `[]`

This is the array that's passed to the form type specified in the `operator_form_type` option.

### `operator_selectable`

- **type**: `bool`
- **default**: `false`

Determines whether the operator can be selected by the user.

### `default_operator`

- **type**: `Kreyu\Bundle\DataTableBundle\Filter\Operator`
- **default**: `{{ defaults.defaultOperator }}`

Determines a default operator for the filter.
