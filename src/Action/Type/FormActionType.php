<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormActionType extends AbstractActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        if ($view->parent instanceof ColumnValueView) {
            $value = $view->parent->value;

            foreach (['method', 'action', 'button_attr'] as $optionName) {
                if (is_callable($options[$optionName])) {
                    $options[$optionName] = $options[$optionName]($value);
                }
            }
        }

        $method = $htmlFriendlyMethod = strtoupper($options['method']);

        if ('GET' !== $method) {
            $htmlFriendlyMethod = 'POST';
        }

        $view->vars = array_replace($view->vars, [
            'method' => $method,
            'html_friendly_method' => $htmlFriendlyMethod,
            'action' => $options['action'],
            'button_attr' => $options['button_attr'],
            'form_id' => $this->getFormId($view, $action),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'method' => 'GET',
                'action' => '#',
                'button_attr' => [],
            ])
            ->setAllowedTypes('method', ['string', 'callable'])
            ->setAllowedTypes('action', ['string', 'callable'])
            ->setAllowedTypes('button_attr', ['array', 'callable'])
        ;
    }

    private function getFormId(ActionView $view, ActionInterface $action): string
    {
        /** @var DataTableView $dataTable */
        $dataTable = $view->vars['data_table'];

        $formId = $dataTable->vars['name'].'-action-'.$action->getName().'-form';

        if ($view->parent instanceof ColumnValueView) {
            $formId .= '-'.$view->parent->parent->index;
        }

        return $formId;
    }
}
