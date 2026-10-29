<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class FormActionTypeTest extends TestCase
{
    public function testRowClosureCsrfTokenIdPutsTokenInView(): void
    {
        $csrfTokenManager = $this->createStub(CsrfTokenManagerInterface::class);
        $csrfTokenManager
            ->method('getToken')
            ->willReturnCallback(static fn (string $id) => new CsrfToken($id, 'token-'.$id))
        ;

        $view = $this->createRowActionView(rowValue: 7);

        $this->buildView(new FormActionType($csrfTokenManager), $view, [
            'csrf_token_id' => static fn (int $id) => 'delete-'.$id,
        ]);

        $this->assertSame('token-delete-7', $view->vars['csrf_token']);
    }

    public function testCsrfTokenIdWithoutManagerThrows(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/^The "csrf_token_id" option requires "symfony\/security-csrf"\./');

        $this->buildView(new FormActionType(), $this->createRowActionView(rowValue: 7), [
            'csrf_token_id' => 'delete',
        ]);
    }

    public function testCsrfTokenIdClosureOnGlobalActionThrows(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches(
            '/^Closure used for option "csrf_token_id", but it\'s only available for row actions\.$/',
        );

        $csrfTokenManager = $this->createStub(CsrfTokenManagerInterface::class);

        $this->buildView(new FormActionType($csrfTokenManager), new ActionView($this->createDataTableView()), [
            'csrf_token_id' => static fn () => 'delete',
        ]);
    }

    private function buildView(FormActionType $type, ActionView $view, array $options): void
    {
        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);

        $action = $this->createStub(ActionInterface::class);
        $action->method('getName')->willReturn('delete');

        $view->vars['data_table'] = $view->getDataTable();

        $type->buildView($view, $action, $resolver->resolve($options));
    }

    private function createRowActionView(mixed $rowValue): ActionView
    {
        $columnValueView = new ColumnValueView(new ValueRowView($this->createDataTableView(), 0, null));
        $columnValueView->value = $rowValue;

        return new ActionView($columnValueView);
    }

    private function createDataTableView(): DataTableView
    {
        $dataTableView = new DataTableView();
        $dataTableView->vars['name'] = 'products';

        return $dataTableView;
    }
}
