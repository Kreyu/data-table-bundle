<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Form\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('columns', CollectionType::class, [
            'entry_type' => PersonalizationColumnDataType::class,
            'allow_add' => true,
        ]);

        $builder->get('columns')->addEventListener(FormEvents::PRE_SET_DATA, $this->keyColumnsByFormName(...), 1);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $dataTableView = $options['data_table_view'];

        if (!$dataTableView instanceof DataTableView) {
            throw new \LogicException('Unable to create personalization form view without the data table view.');
        }

        foreach ($view['columns'] as $columnFormView) {
            /** @var string $columnName */
            $columnName = $columnFormView['name']->vars['value'];
            $columnView = $dataTableView->nonPersonalizedHeaderRow[$columnName];

            $columnFormView->vars = array_replace($columnFormView->vars, [
                'label' => $columnView->vars['label'],
                'translation_domain' => $columnView->vars['translation_domain'],
                'translation_parameters' => $columnView->vars['translation_parameters'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => PersonalizationData::class,
                'data_table_view' => null,
            ])
            ->setAllowedTypes('data_table_view', ['null', DataTableView::class])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_personalization_data';
    }

    private function keyColumnsByFormName(PreSetDataEvent $event): void
    {
        /** @var array<string, PersonalizationColumnData> $columns */
        $columns = $event->getData() ?? [];
        $columnsByFormName = [];

        foreach ($columns as $column) {
            $formName = str_replace(search: '.', replace: '__', subject: $column->getName());
            $columnsByFormName[$formName] = $column;
        }

        $event->setData($columnsByFormName);
    }
}
