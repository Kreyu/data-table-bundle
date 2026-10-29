<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class FormActionType extends AbstractActionType
{
    public function __construct(
        private readonly ?CsrfTokenManagerInterface $csrfTokenManager = null,
    ) {}

    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $columnValueView = $view->parent;

        if ($columnValueView instanceof ColumnValueView) {
            foreach (['method', 'action', 'button_attr', 'csrf_token_id'] as $optionName) {
                if (!$options[$optionName] instanceof \Closure) {
                    continue;
                }

                $options[$optionName] = $options[$optionName]($columnValueView->value);
            }
        }

        if ($options['csrf_token_id'] instanceof \Closure) {
            throw new LogicException(
                'Closure used for option "csrf_token_id", but it\'s only available for row actions.',
            );
        }

        /** @var string $methodOption */
        $methodOption = $options['method'];

        $method = strtoupper($methodOption);
        $htmlFriendlyMethod = $method;

        if (!in_array($method, ['GET', 'POST'], strict: true)) {
            $htmlFriendlyMethod = 'POST';
        }

        $view->vars = array_replace($view->vars, [
            'method' => $method,
            'html_friendly_method' => $htmlFriendlyMethod,
            'action' => $options['action'],
            'button_attr' => $options['button_attr'],
            'form_id' => $this->getFormId($view, $action),
            'csrf_token' => $this->getCsrfToken($options['csrf_token_id']),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'method' => 'GET',
                'action' => '#',
                'button_attr' => [],
                'csrf_token_id' => null,
            ])
            ->setAllowedTypes('method', ['string', \Closure::class])
            ->setAllowedTypes('action', ['string', \Closure::class])
            ->setAllowedTypes('button_attr', ['array', \Closure::class])
            ->setAllowedTypes('csrf_token_id', ['null', 'string', \Closure::class])
        ;
    }

    private function getCsrfToken(mixed $csrfTokenId): ?string
    {
        if ($csrfTokenId === null) {
            return null;
        }

        $isString = is_string($csrfTokenId);

        if (!$isString) {
            throw new LogicException(sprintf(
                'The "csrf_token_id" option must resolve to a string or null, "%s" given.',
                get_debug_type($csrfTokenId),
            ));
        }

        if ($this->csrfTokenManager === null) {
            throw new LogicException(
                'The "csrf_token_id" option requires "symfony/security-csrf". Run "composer require symfony/security-csrf" and enable "framework.csrf_protection".',
            );
        }

        return $this->csrfTokenManager->getToken($csrfTokenId)->getValue();
    }

    private function getFormId(ActionView $view, ActionInterface $action): string
    {
        /** @var DataTableView $dataTable */
        $dataTable = $view->vars['data_table'];

        /** @var string $dataTableName */
        $dataTableName = $dataTable->vars['name'];

        $formId = $dataTableName.'-action-'.$action->getName().'-form';

        if ($view->parent instanceof ColumnValueView) {
            $formId .= '-'.$view->parent->parent->index;
        }

        return $formId;
    }
}
