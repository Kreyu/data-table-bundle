<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

final class ActionType implements ActionTypeInterface
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        if ($view->parent instanceof DataTableView) {
            $dataTable = $view->parent;
        } else {
            $dataTable = $view->parent->parent->parent;
        }

        if ($view->parent instanceof ColumnValueView) {
            $value = $view->parent->value;

            $callableOptions = [
                'label',
                'translation_domain',
                'translation_parameters',
                'block_prefix',
                'attr',
                'icon_attr',
                'confirmation',
            ];

            foreach ($callableOptions as $optionName) {
                if (is_callable($options[$optionName])) {
                    $options[$optionName] = $options[$optionName]($value);
                }
            }
        }

        if (false !== $confirmation = $options['confirmation']) {
            if (true === $confirmation) {
                $confirmation = [];
            }

            $confirmation = $this->getConfirmationOptionsResolver()->resolve($confirmation);
            $confirmation['translation_domain'] ??= $options['translation_domain'];
            $confirmation['identifier'] = $dataTable->vars['name'].'-action-confirmation-'.$action->getName();

            if ($view->parent instanceof ColumnValueView) {
                $confirmation['identifier'] .= '-'.$view->parent->parent->index;
            }
        }

        $view->vars = array_replace($view->vars, [
            'data_table' => $dataTable,
            'name' => $action->getName(),
            'block_prefixes' => $this->getActionBlockPrefixes($action, $options),
            'label' => $options['label'] ?? StringUtil::camelToSentence($action->getName()),
            'translation_domain' => $options['translation_domain'] ?? $dataTable->vars['translation_domain'],
            'translation_parameters' => $options['translation_parameters'],
            'attr' => $options['attr'],
            'icon_attr' => $options['icon_attr'],
            'confirmation' => $confirmation,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'translation_domain' => null,
                'translation_parameters' => [],
                'block_prefix' => null,
                'attr' => [],
                'icon_attr' => [],
                'confirmation' => false,
            ])
            ->setAllowedTypes('label', ['null', 'bool', 'string', 'callable', TranslatableMessage::class])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string', 'callable'])
            ->setAllowedTypes('translation_parameters', ['array', 'callable'])
            ->setAllowedTypes('block_prefix', ['null', 'string', 'callable'])
            ->setAllowedTypes('attr', ['array', 'callable'])
            ->setAllowedTypes('icon_attr', ['array', 'callable'])
            ->setAllowedTypes('confirmation', ['bool', 'array', 'callable'])
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
     * Retrieves the action block prefixes, respecting the type hierarchy.
     *
     * For example, take a look at the ButtonActionType. It is based on the ActionType,
     * therefore its block prefixes are: ["button", "action"].
     *
     * @return array<string>
     */
    private function getActionBlockPrefixes(ActionInterface $action, array $options): array
    {
        $type = $action->getType();

        $blockPrefixes = [
            $type->getBlockPrefix(),
        ];

        while (null !== $type->getParent()) {
            $blockPrefixes[] = ($type = $type->getParent())->getBlockPrefix();
        }

        if ($blockPrefix = $options['block_prefix']) {
            array_unshift($blockPrefixes, $blockPrefix);
        }

        return array_unique($blockPrefixes);
    }

    private function getConfirmationOptionsResolver(): OptionsResolver
    {
        return (new OptionsResolver)
            ->setDefaults([
                'translation_domain' => 'KreyuDataTable',
                'label_title' => 'Action confirmation',
                'label_description' => 'Are you sure you want to execute this action?',
                'label_confirm' => 'Confirm',
                'label_cancel' => 'Cancel',
                'type' => 'danger',
                'href' => null,
            ])
            ->setAllowedTypes('translation_domain', ['null', 'string'])
            ->setAllowedTypes('label_title', ['null', 'string'])
            ->setAllowedTypes('label_description', ['null', 'string'])
            ->setAllowedTypes('label_confirm', ['null', 'string'])
            ->setAllowedTypes('label_cancel', ['null', 'string'])
            ->setAllowedTypes('type', ['null', 'string'])
            ->setAllowedTypes('href', ['null', 'string'])
            ->setAllowedValues('type', ['info', 'warning', 'danger'])
        ;
    }
}
