<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM BooleanFilterType

The Doctrine ORM [`BooleanFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/BooleanFilterType.php) represents a filter that operates on a boolean values.

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType',
    formOptions: `'choices' => ['Yes' => true, 'No' => false], 'choice_translation_domain' => 'KreyuDataTable'`
}" />

<DoctrineOrmFilterTypeOptions/>
