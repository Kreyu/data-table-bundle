<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\DependencyInjection\DataTablePass;
use Kreyu\Bundle\DataTableBundle\DependencyInjection\DefaultConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KreyuDataTableBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DataTablePass());
        $container->addCompilerPass(new DefaultConfigurationPass());
    }
}
