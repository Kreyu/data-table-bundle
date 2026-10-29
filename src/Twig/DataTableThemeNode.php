<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Twig;

use Twig\Compiler;
use Twig\Node\Node;

class DataTableThemeNode extends Node
{
    public function __construct(Node $dataTable, Node $themes, int $lineno, ?string $tag = null, bool $only = false)
    {
        $nodes = ['data_table' => $dataTable, 'themes' => $themes];
        $attributes = ['only' => $only];
        $requiresTag = $this->requiresTagBackwardsCompatibility();

        if ($requiresTag) {
            parent::__construct($nodes, $attributes, $lineno, $tag);

            return;
        }

        parent::__construct($nodes, $attributes, $lineno);
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
        $parentConstructor = new \ReflectionMethod(Node::class, '__construct');
        $parentConstructorParameters = $parentConstructor->getParameters();

        foreach ($parentConstructorParameters as $parentConstructorParameter) {
            if ('tag' === $parentConstructorParameter->getName()) {
                return true;
            }
        }

        return false;
    }
}
