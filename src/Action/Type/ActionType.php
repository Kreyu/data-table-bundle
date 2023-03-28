<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
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

        $view->vars = array_replace($view->vars, [
            'data_table' => $dataTable,
            'block_prefixes' => $this->getActionBlockPrefixes($action, $options),
            'label' => $options['label'] ?? StringUtil::camelToSentence($action->getName()),
            'translation_domain' => $options['translation_domain'] ?? $dataTable->vars['translation_domain'],
            'translation_parameters' => $options['translation_parameters'],
            'attr' => $options['attr'],
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
            ])
            ->setAllowedTypes('label', ['null', 'bool', 'string', 'callable', TranslatableMessage::class])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('translation_parameters', ['array'])
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
}
