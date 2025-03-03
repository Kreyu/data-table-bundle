<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Action\ActionFactory;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionRegistry;
use Kreyu\Bundle\DataTableBundle\Action\ActionRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\RefreshActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactory;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.action.resolved_type_factory', ResolvedActionTypeFactory::class)
        ->alias(ResolvedActionTypeFactoryInterface::class, 'kreyu_data_table.action.resolved_type_factory')
    ;

    $services
        ->set('kreyu_data_table.action.registry', ActionRegistry::class)
        ->args([
            tagged_iterator('kreyu_data_table.action.type'),
            tagged_iterator('kreyu_data_table.action.type_extension'),
            service('kreyu_data_table.action.resolved_type_factory'),
        ])
        ->alias(ActionRegistryInterface::class, 'kreyu_data_table.action.registry')
    ;

    $services
        ->set('kreyu_data_table.action.factory', ActionFactory::class)
        ->args([service('kreyu_data_table.action.registry')])
        ->alias(ActionFactoryInterface::class, 'kreyu_data_table.action.factory')
    ;

    $services
        ->set('kreyu_data_table.action.type.action', ActionType::class)
        ->tag('kreyu_data_table.action.type')
    ;

    $services
        ->set('kreyu_data_table.action.type.link', LinkActionType::class)
        ->tag('kreyu_data_table.action.type')
    ;

    $services
        ->set('kreyu_data_table.action.type.refresh', RefreshActionType::class)
        ->tag('kreyu_data_table.action.type')
        ->args([
            service('request_stack'),
            service(UrlGeneratorInterface::class),
            service(TranslatorInterface::class),
        ])
    ;

    $services
        ->set('kreyu_data_table.action.type.button', ButtonActionType::class)
        ->tag('kreyu_data_table.action.type')
    ;

    $services
        ->set('kreyu_data_table.action.type.form', FormActionType::class)
        ->tag('kreyu_data_table.action.type')
    ;

    $services
        ->set('kreyu_data_table.action.type.modal', ModalActionType::class)
        ->tag('kreyu_data_table.action.type')
        ->args([service(UrlGeneratorInterface::class)])
    ;

    $services
        ->set('kreyu_data_table.action.type.dropdown', DropdownActionType::class)
        ->tag('kreyu_data_table.action.type')
    ;

    $services
        ->set('kreyu_data_table.action.type.link_dropdown_item', LinkDropdownItemActionType::class)
        ->tag('kreyu_data_table.action.type')
    ;
};
