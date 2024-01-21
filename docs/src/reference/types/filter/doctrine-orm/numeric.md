<script setup>
    import FilterTypeOptions from "../options/filter.md";
    import DoctrineOrmFilterTypeOptions from "../options/doctrine-orm.md";
</script>

# Doctrine ORM NumericFilterType

The Doctrine ORM [`NumericFilterType`](https://github.com/Kreyu/data-table-bundle/blob/main/src/Bridge/Doctrine/Orm/Filter/Type/NumericFilterType.php) represents a filter that operates on a numeric values.

## Options

This column type has no additional options.

## Inherited options

<FilterTypeOptions :defaults="{
    formType: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\NumberType'
}" />

<DoctrineOrmFilterTypeOptions/>