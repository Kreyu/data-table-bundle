<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Util\StringUtil;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ActionType implements ActionTypeInterface
{
    public function buildAction(ActionBuilderInterface $builder, array $options = []): void
    {
        $builder->setConfirmable(false !== $options['confirmation']);
    }

    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $dataTable = $view->getDataTable();

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

        if ($action->getConfig()->isConfirmable()) {
            $options['confirmation']['identifier'] = vsprintf(
                format: '%s-action-confirmation-%s',
                values: [
                    $dataTable->vars['name'],
                    $action->getName(),
                ],
            );

            if ($view->parent instanceof ColumnValueView) {
                $options['confirmation']['identifier'] .= '-'.$view->parent->parent->index;
            }
        }

        $blockPrefixes = $action->getConfig()->getType()->getBlockPrefixHierarchy();

        if (null !== $blockPrefix = $options['block_prefix']) {
            array_unshift($blockPrefixes, $blockPrefix);
        }

        $view->vars = array_replace($view->vars, [
            'data_table' => $dataTable,
            'name' => $action->getName(),
            'block_prefixes' => $blockPrefixes,
            'label' => $options['label'] ?? StringUtil::camelToSentence($action->getName()),
            'translation_domain' => $options['translation_domain'] ?? $dataTable->vars['translation_domain'],
            'translation_parameters' => $options['translation_parameters'],
            'attr' => $options['attr'],
            'icon_attr' => $options['icon_attr'],
            'confirmation' => $options['confirmation'],
            'batch' => $action->getConfig()->isBatch(),
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
            ->setAllowedTypes('label', ['null', 'bool', 'string', 'callable', TranslatableInterface::class])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string', 'callable'])
            ->setAllowedTypes('translation_parameters', ['array', 'callable'])
            ->setAllowedTypes('block_prefix', ['null', 'string', 'callable'])
            ->setAllowedTypes('attr', ['array', 'callable'])
            ->setAllowedTypes('icon_attr', ['array', 'callable'])
            ->setAllowedTypes('confirmation', ['bool', 'array', 'callable'])
            ->setNormalizer('confirmation', function (Options $options, mixed $value) {
                if (false === $value) {
                    return false;
                }

                if (true === $value) {
                    $value = [];
                }

                ($resolver = new OptionsResolver())
                    ->setDefault('confirmation', function (OptionsResolver $resolver) {
                        $resolver
                            ->setDefaults([
                                'translation_domain' => 'KreyuDataTable',
                                'label_title' => 'Action confirmation',
                                'label_description' => 'Are you sure you want to execute this action?',
                                'label_confirm' => 'Confirm',
                                'label_cancel' => 'Cancel',
                                'type' => 'danger',
                            ])
                            ->setAllowedTypes('translation_domain', ['null', 'string'])
                            ->setAllowedTypes('label_title', ['null', 'string', TranslatableInterface::class])
                            ->setAllowedTypes('label_description', ['null', 'string', TranslatableInterface::class])
                            ->setAllowedTypes('label_confirm', ['null', 'string', TranslatableInterface::class])
                            ->setAllowedTypes('label_cancel', ['null', 'string', TranslatableInterface::class])
                            ->setAllowedTypes('type', ['null', 'string'])
                            ->setAllowedValues('type', ['danger', 'warning', 'info'])
                        ;
                    })
                ;

                $value = $resolver->resolve(['confirmation' => $value]);

                return $value['confirmation'];
            })
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
}
