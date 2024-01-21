<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM StringFilterType

The Doctrine ORM [`StringFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/StringFilterType.php) represents a filter that operates on a string values.

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    defaultOperator: 'Kreyu\\Bundle\\DataTableBundle\\Filter\\Operator::Contains'
}" />

<DoctrineOrmFilterTypeOptions/>