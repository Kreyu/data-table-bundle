<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Pagination\PaginationUrlGenerator;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.pagination.url_generator', PaginationUrlGenerator::class)
        ->args([
            service('request_stack'),
            service(UrlGeneratorInterface::class),
        ])
        ->alias(PaginationUrlGeneratorInterface::class, 'kreyu_data_table.pagination.url_generator')
    ;
};
