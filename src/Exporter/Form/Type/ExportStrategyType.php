<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportStrategyType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => ExportStrategy::class,
            'choice_translation_domain' => 'KreyuDataTable',
            'choice_label' => 'label',
            // TODO: Remove after removing deprecated export strategy enum cases
            'choices' => [
                ExportStrategy::IncludeCurrentPage,
                ExportStrategy::IncludeAll,
            ],
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}
