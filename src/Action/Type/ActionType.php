<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

final class ActionType implements ActionTypeInterface
{
    public const DEFAULT_BLOCK_PREFIX = 'kreyu_data_table_action_';

    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        // Put the data table view as the option.
        // Thanks to that, it can be accessed in the templates if needed.
        $options['data_table'] = $view->parent;

        $options = array_merge($options, $this->resolveDefaultOptions($view, $action, $options));

        $view->vars = array_merge($view->vars, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'label_translation_parameters' => [],
                'translation_domain' => null,
                'block_name' => null,
                'block_prefix' => null,
                'attr' => [],
            ])
            ->setAllowedTypes('label', ['null', 'string', TranslatableMessage::class])
            ->setAllowedTypes('label_translation_parameters', ['array'])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('block_name', ['null', 'string'])
            ->setAllowedTypes('block_prefix', ['null', 'string'])
            ->setAllowedTypes('attr', ['array'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'action';
    }

    public function getParent(): ?string
    {
        return null;
    }

    /**
     * Resolve default values of the options, based on the action view and action object.
     * This is the place where, for example, the label can be defaulted to the action name.
     */
    private function resolveDefaultOptions(ActionView $view, ActionInterface $action, array $options): array
    {
        // Put the action data as the "data" option.
        // This way, it can be referenced later if needed.
        $options['data'] = $action->getData();

        // If the label is not given, then it should be replaced with a action name.
        // Thanks to that, the boilerplate code is reduced, and the labels work as expected.
        $options['label'] ??= ucfirst($action->getName());

        // If the translation domain is not given, then it should be inherited
        // from the parent data table view "label_translation_domain" option.
        $options['translation_domain'] ??= $view->parent->vars['label_translation_domain'] ?? false;

        // If the block prefix or name is not specified, then the values
        // should be inherited from the action type.
        $options['block_prefix'] ??= $action->getType()->getBlockPrefix();
        $options['block_name'] ??= self::DEFAULT_BLOCK_PREFIX.$options['block_prefix'];

        // Because the user can provide callable options, every single one of those
        // should be called with resolved action value, whole action for reference and an array of options.
        if (null !== $options['data']) {
            // Resolve the callable options, passing the value, action and options.
            // Note: the options passed to the callables are not resolved yet!
            $resolvedOptions = $this->resolveCallableOptions($options, $action);

            $options = array_merge($options, $resolvedOptions);
        }

        return $options;
    }

    /**
     * Resolve the callable options, by calling each one, passing as the arguments
     * the action data, an instance of the action object, and a whole options array.
     */
    private function resolveCallableOptions(array $options, ActionInterface $action): array
    {
        foreach ($options as $key => $option) {
            if (is_array($option)) {
                $option = $this->resolveCallableOptions($option, $action);
            }

            if ($option instanceof \Closure) {
                $option = $option($options['data'], $action, $options);
            }

            $options[$key] = $option;
        }

        return $options;
    }
}
