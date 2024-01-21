<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM DateRangeFilterType

The Doctrine ORM [`DateRangeFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/DateRangeFilterType.php) represents a filter that operates on a two date values that make a range.

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Kreyu\\Bundle\\DataTableBundle\\Filter\\Form\\Type\\DateRangeType',
    defaultOperator: 'Kreyu\\Bundle\\DataTableBundle\\Filter\\Operator::Between'
}" />

<DoctrineOrmFilterTypeOptions/>