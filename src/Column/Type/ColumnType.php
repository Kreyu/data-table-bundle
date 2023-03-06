<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Closure;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Component\Translation\TranslatableMessage;

final class ColumnType implements ColumnTypeInterface
{
    public const DEFAULT_BLOCK_PREFIX = 'kreyu_data_table_column_';

    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        // Put the data table view as the option.
        // Thanks to that, it can be accessed in the templates if needed.
        $options['data_table'] = $view->parent;

        $options = array_merge($options, $this->resolveDefaultOptions($view, $column, $options));

        $view->vars = array_merge($view->vars, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'label_translation_parameters' => [],
                'translation_domain' => null,
                'property_path' => null,
                'sort' => false,
                'block_name' => null,
                'block_prefix' => null,
                'value' => null,
                'display_personalization_button' => false,
                'property_accessor' => PropertyAccess::createPropertyAccessor(),
                'export' => true,
                'formatter' => null,
                'non_resolvable_options' => [],
            ])
            ->setAllowedTypes('label', ['null', 'string', TranslatableMessage::class])
            ->setAllowedTypes('label_translation_parameters', ['array', Closure::class])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('property_path', ['null', 'bool', 'string', PropertyPathInterface::class])
            ->setAllowedTypes('sort', ['bool', 'string'])
            ->setAllowedTypes('block_name', ['null', 'string'])
            ->setAllowedTypes('block_prefix', ['null', 'string'])
            ->setAllowedTypes('display_personalization_button', ['bool'])
            ->setAllowedTypes('property_accessor', [PropertyAccessorInterface::class])
            ->setAllowedTypes('export', ['bool', 'array'])
            ->setAllowedTypes('formatter', ['null', Closure::class])
            ->setAllowedTypes('non_resolvable_options', ['string[]'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'column';
    }

    public function getParent(): ?string
    {
        return null;
    }

    /**
     * Resolve the callable options, by calling each one, passing as the arguments
     * the column value, an instance of the column object, and a whole options array.
     */
    private function resolveCallableOptions(array $resolvableOptions, ColumnInterface $column, array $options): array
    {
        foreach ($resolvableOptions as $key => $option) {
            if (is_array($option)) {
                $option = $this->resolveCallableOptions($option, $column, $options);
            }

            if ($option instanceof Closure) {
                $option = $option($options['value'], $options['data'], $column, $options);
            }

            $resolvableOptions[$key] = $option;
        }

        return $resolvableOptions;
    }

    /**
     * Resolve default values of the options, based on the column view and column object.
     * This is the place where, for example, the label can be defaulted to the column name.
     */
    private function resolveDefaultOptions(ColumnView $view, ColumnInterface $column, array $options): array
    {
        // Put the column data as the "data" option.
        // This way, it can be referenced later if needed.
        $options['data'] = $column->getData();

        // The column can have "value" option preconfigured.
        // In that case, disable the property accessor feature.
        if (isset($options['value'])) {
            $options['property_path'] = false;

            // The "value" option can be callable, that should be called with the column data.
            // This way, the user can retrieve a column value manually.
            if (is_callable($options['value']) && null !== $options['data']) {
                $options['value'] = $options['value']($options['data'], $column, $options);
            }
        }

        // The column "value" option by default should contain the column data.
        $options['value'] ??= $column->getData();

        // If the label is not given, then it should be replaced with a column name.
        // Thanks to that, the boilerplate code is reduced, and the labels work as expected.
        $options['label'] ??= ucfirst($column->getName());

        // If the translation domain is not given, then it should be inherited
        // from the parent data table view "label_translation_domain" option.
        $options['translation_domain'] ??= $view->parent->vars['label_translation_domain'];

        // If the property path is not given, then the column name should be used.
        // Thanks to that, similar to "label" option, the boilerplate code is reduced.
        $options['property_path'] ??= $column->getName();

        // If the block prefix or name is not specified, then the values
        // should be inherited from the column type.
        $options['block_prefix'] ??= $column->getType()->getBlockPrefix();
        $options['block_name'] ??= self::DEFAULT_BLOCK_PREFIX . $options['block_prefix'];

        // Because by default, the sorting feature is disabled, the user can enable it
        // by setting the "sort" option to either sort field path, or just a true.
        // Setting the value to true means the column name should be used as the sort field path.
        if (true === $options['sort']) {
            $options['sort'] = $column->getName();
        }

        $value = $options['value'];
        $propertyPath = $options['property_path'];
        $propertyAccessor = $options['property_accessor'];

        // Use property accessor to retrieve the value from the configured property path.
        // Note: property accessor only supports array and object values!
        if (false !== $propertyPath && (is_array($value) || is_object($value))) {
            if ($propertyAccessor->isReadable($value, $propertyPath)) {
                $options['value'] = $propertyAccessor->getValue($value, $propertyPath);
            }
        }

        // Because the user can provide callable options, every single one of those
        // should be called with resolved column value, whole column for reference and an array of options.
        if (null !== $options['data']) {
            // Because every callable option is resolved by default, a way to exclude
            // some options from this process may be necessary - a "non_resolvable_option" option,
            // just in case if the user actually expects the option to be a callable.
            $resolvableOptions = array_diff_key($options, array_flip($options['non_resolvable_options']) + [
                'formatter' => true,
            ]);

            // Because "value" options are getting resolved earlier only if the column data is present,
            // they have to get excluded from the latter resolving whatsoever.
            unset($resolvableOptions['value']);

            if (isset($resolvableOptions['export']['value'])) {
                unset($resolvableOptions['export']['value']);
            }

            // Resolve the callable options, passing the value, column and options.
            // Note: the options passed to the callables are not resolved yet!
            $resolvedOptions = $this->resolveCallableOptions($resolvableOptions, $column, $options);

            $options = array_merge($options, $resolvedOptions);
        }

        // The "export" option has to inherit options from the column.
        if (false !== ($options['export'] ?? false)) {
            // Exclude the "export" option, as it's irrelevant to the export options whatsoever.
            $inheritedExportOptions = array_diff_key($options, ['export' => true]);

            if (true === $options['export']) {
                $options['export'] = $inheritedExportOptions;
            }

            // Provided export options should be filled with inherited column options.
            // Merging it this way, allows user to override only some export options.
            $options['export'] = array_merge(
                $this->resolveDefaultOptions($view, $column, $inheritedExportOptions),
                $options['export'],
            );
        }

        // Apply the formatter at the end of the process.
        if (null !== $options['data'] && is_callable($options['formatter'])) {
            $options['value'] = $options['formatter']($options['value']);
        }

        return $options;
    }
}
