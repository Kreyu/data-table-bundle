<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class ResponsiveThemeFallbackTest extends TestCase
{
    /**
     * Tests that responsive theme blocks fall back to the parent (base) blocks
     * when responsive_enabled is false. This mirrors the aliasing pattern used
     * in bootstrap_5_responsive.html.twig and tabler_responsive.html.twig.
     *
     * @see \Kreyu\Bundle\DataTableBundle\Resources\views\themes\bootstrap_5_responsive.html.twig
     */
    public function testResponsiveBlocksFallBackToParentWhenDisabled(): void
    {
        $environment = new Environment(new ArrayLoader([
            'base.html.twig' => '{% block table_head_row %}base_head_row{% endblock %}',
            '_responsive.html.twig' => '{% block table_head_row %}responsive_head_row{% endblock %}',
            'responsive_theme.html.twig' => <<<'TWIG'
                {% extends 'base.html.twig' %}
                {% use '_responsive.html.twig' with table_head_row as _responsive_table_head_row %}
                {% block table_head_row %}{{ responsive_enabled|default(false) ? block('_responsive_table_head_row') : parent() }}{% endblock %}
                TWIG,
        ]));

        $template = $environment->load('responsive_theme.html.twig');

        $disabledHtml = $template->renderBlock('table_head_row', ['responsive_enabled' => false]);
        $this->assertStringContainsString('base_head_row', $disabledHtml);
        $this->assertStringNotContainsString('responsive_head_row', $disabledHtml);

        $enabledHtml = $template->renderBlock('table_head_row', ['responsive_enabled' => true]);
        $this->assertStringContainsString('responsive_head_row', $enabledHtml);
        $this->assertStringNotContainsString('base_head_row', $enabledHtml);
    }
}
