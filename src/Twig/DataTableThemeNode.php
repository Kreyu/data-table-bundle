<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Twig\Compiler;
use Twig\Node\Node;

class DataTableThemeNode extends Node
{
    public function __construct(Node $dataTable, Node $themes, int $lineno, ?string $tag = null, bool $only = false)
    {
        if ($this->requiresTagBackwardsCompatibility()) {
            parent::__construct(['data_table' => $dataTable, 'themes' => $themes], ['only' => $only], $lineno, $tag);
        } else {
            parent::__construct(['data_table' => $dataTable, 'themes' => $themes], ['only' => $only], $lineno);
        }
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf('$this->env->getExtension("%s")->setDataTableThemes(', DataTableExtension::class))
            ->subcompile($this->getNode('data_table'))
            ->raw(', ')
            ->subcompile($this->getNode('themes'))
            ->raw(sprintf(", %s);\n", $this->getAttribute('only') ? 'true' : 'false'))
        ;
    }

    public function requiresTagBackwardsCompatibility(): bool
    {
        $reflection = new \ReflectionClass($this);

        $parentConstructorParameters = $reflection->getParentClass()->getConstructor()->getParameters();

        foreach ($parentConstructorParameters as $parentConstructorParameter) {
            if ('tag' === $parentConstructorParameter->getName()) {
                return true;
            }
        }

        return false;
    }
}
