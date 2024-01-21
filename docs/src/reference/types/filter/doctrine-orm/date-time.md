<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM DateTimeFilterType

The Doctrine ORM [`DateTimeFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DateTimeFilterType.php) represents a filter that operates on a date time values.

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateTimeType',
    formOptions: `['widget' => 'single_text']`
}" />

<DoctrineOrmFilterTypeOptions/>