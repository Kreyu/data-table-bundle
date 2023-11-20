<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test;

use Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Loader\FilesystemLoader;

abstract class RenderingTestCase extends TestCase
{
    protected const THEME_BASE = '@KreyuDataTable/themes/base.html.twig';
    protected const THEME_BOOTSTRAP = '@KreyuDataTable/themes/bootstrap_5.html.twig';
    protected const THEME_TABLER = '@KreyuDataTable/themes/tabler.html.twig';
    protected const THEMES = [
        'base' => self::THEME_BASE,
        'bootstrap' => self::THEME_BOOTSTRAP,
        'tabler' => self::THEME_TABLER,
    ];

    protected const DEFAULT_TIMEZONE = 'Europe/Warsaw';

    private Environment $twig;

    protected function getTwig(): Environment
    {
        if (!isset($this->twig)) {
            $loader = new FilesystemLoader();
            $loader->addPath(realpath(__DIR__.'/../Resources/views'), 'KreyuDataTable');
            $loader->addPath(realpath(__DIR__.'/../../tests/Fixtures/Resources/views'), 'KreyuDataTableTest');

            $twig = new Environment($loader);
            $twig->addExtension(new DataTableExtension());
            $twig->addExtension(new TranslationExtension());
            $twig->addExtension(new FormExtension());
            $twig->addExtension(new RoutingExtension($this->createMock(UrlGenerator::class)));
            $twig->addGlobal('app', $this->getAppVariableMock());

            $twig->getExtension(CoreExtension::class)->setTimezone(static::DEFAULT_TIMEZONE);

            $this->twig = $twig;
        }

        return $this->twig;
    }

    protected function getAppVariableMock(): MockObject&AppVariable
    {
        $appVariable = $this->createMock(AppVariable::class);
        $appVariable->method('getRequest')->willReturn($this->getRequestMock());

        return $appVariable;
    }

    protected function getRequestMock(): MockObject&Request
    {
        $request = $this->createMock(Request::class);
        $request->method('get')->with('_route')->willReturn('app_test');

        $request->query = new InputBag();
        $request->attributes = new ParameterBag(['_route_params' => ['foo' => 'bar']]);

        return $request;
    }

    protected function assertHtmlEquals(string $expected, string $actual): void
    {
        $actual = str_replace(["\n", "\r"], '', $actual);

        $expected = '<html>'.trim($expected).'</html>';
        $actual = '<html>'.trim($actual).'</html>';

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }
}
