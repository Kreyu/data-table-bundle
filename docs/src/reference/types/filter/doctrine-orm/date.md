<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM DateFilterType

The Doctrine ORM [`DateFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DateFilterType.php) represents a filter that operates on a date (without time) values.

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateType',
    formOptions: `['widget' => 'single_text']`
}" />

<DoctrineOrmFilterTypeOptions/>