<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Twig;

use Kreyu\Bundle\DataTableBundle\Column\ColumnSortUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup\ColumnVisibilityGroupView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterClearUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TwigFilter;

/**
 * Renders the real base.html.twig and asserts that the visibility group selector form
 * preserves current URL query parameters (page, per_page, sort, filters) as hidden inputs,
 * so switching visibility group doesn't reset pagination/sort/filter state.
 */
class ColumnVisibilityGroupSelectorBlockTest extends TestCase
{
    public function testFormIncludesHiddenInputsForFlatQueryParameters(): void
    {
        $html = $this->renderSelector([
            'page_foo' => 2,
            'limit_foo' => 25,
        ]);

        $this->assertStringContainsString('<input type="hidden" name="page_foo" value="2">', $html);
        $this->assertStringContainsString('<input type="hidden" name="limit_foo" value="25">', $html);
    }

    public function testFormIncludesHiddenInputsForNestedQueryParameters(): void
    {
        $html = $this->renderSelector([
            'sort_foo' => ['id' => 'asc', 'name' => 'desc'],
        ]);

        $this->assertStringContainsString('<input type="hidden" name="sort_foo[id]" value="asc">', $html);
        $this->assertStringContainsString('<input type="hidden" name="sort_foo[name]" value="desc">', $html);
    }

    public function testFormIncludesHiddenInputsForDeeplyNestedQueryParameters(): void
    {
        $html = $this->renderSelector([
            'filter_foo' => [
                'status' => ['value' => 'active', 'operator' => 'eq'],
            ],
        ]);

        $this->assertStringContainsString('<input type="hidden" name="filter_foo[status][value]" value="active">', $html);
        $this->assertStringContainsString('<input type="hidden" name="filter_foo[status][operator]" value="eq">', $html);
    }

    public function testFormHasNoHiddenInputsWhenNoQueryParameters(): void
    {
        $html = $this->renderSelector([]);

        $this->assertStringNotContainsString('<input type="hidden"', $html);
    }

    public function testBlockRendersNothingWithoutColumnVisibilityGroups(): void
    {
        $html = $this->renderSelector([], columnVisibilityGroups: []);

        $this->assertSame('', trim($html));
    }

    private function renderSelector(array $urlQueryParameters, ?array $columnVisibilityGroups = null): string
    {
        $loader = new FilesystemLoader(__DIR__.'/../../../src/Resources/views/themes');
        $twig = new Environment($loader, ['strict_variables' => false]);

        // Avoid Symfony TranslationExtension: its TranslationDefaultDomainNodeVisitor is incompatible
        // with the lowest supported Twig version (raises "EmptyNode cannot have children").
        // A stub that no-ops `|trans` and `{% trans_default_domain %}` is enough for our purpose —
        // we only render one block that doesn't translate anything.
        $twig->addExtension(new NoOpTranslationExtension());

        $twig->addExtension(new FormExtension());
        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new DataTableExtension(
            $this->createStub(ColumnSortUrlGeneratorInterface::class),
            $this->createStub(FilterClearUrlGeneratorInterface::class),
            $this->createStub(PaginationUrlGeneratorInterface::class),
        ));

        $template = $twig->load('base.html.twig');

        $view = new DataTableView();
        $view->vars = [
            'url_query_parameters' => $urlQueryParameters,
            'column_visibility_group_parameter_name' => 'column_visibility_group_foo',
        ];
        $view->columnVisibilityGroups = $columnVisibilityGroups ?? [
            'default' => new ColumnVisibilityGroupView('default', 'Default', true, true),
        ];

        return $template->renderBlock('column_visibility_group_selector', [
            'data_table' => $view,
            'theme' => $template,
        ]);
    }
}

final class NoOpTranslationExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('trans', static fn (mixed $value) => $value),
        ];
    }

    public function getTokenParsers(): array
    {
        return [new NoOpTransDefaultDomainTokenParser()];
    }
}

final class NoOpTransDefaultDomainTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();

        while (!$stream->test(Token::BLOCK_END_TYPE)) {
            $stream->next();
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new Node([], [], $token->getLine());
    }

    public function getTag(): string
    {
        return 'trans_default_domain';
    }
}
