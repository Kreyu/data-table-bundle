<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Request;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class HttpFoundationRequestHandler implements RequestHandlerInterface
{
    private readonly PropertyAccessorInterface $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function handle(DataTableInterface $dataTable, mixed $request = null): void
    {
        if ($request === null) {
            return;
        }

        if (!$request instanceof Request) {
            throw new UnexpectedTypeException($request, Request::class);
        }

        $this->handleRequest($dataTable, $request);
    }

    private function handleRequest(DataTableInterface $dataTable, Request $request): void
    {
        $this->filter($dataTable, $request);
        $this->sort($dataTable, $request);
        $this->personalize($dataTable, $request);
        $this->selectColumnVisibilityGroup($dataTable, $request);
        $this->paginate($dataTable, $request);
        $this->export($dataTable, $request);
        $this->turbo($dataTable, $request);
    }

    private function filter(DataTableInterface $dataTable, Request $request): void
    {
        if (!$dataTable->getConfig()->isFiltrationEnabled()) {
            return;
        }

        $form = $dataTable->createFiltrationFormBuilder()->getForm();
        $isSubmittedAndValid = $this->submitForm($form, $request);

        if (!$isSubmittedAndValid) {
            return;
        }

        /** @var FiltrationData $filtrationData */
        $filtrationData = $form->getData();

        $dataTable->filter($filtrationData);
    }

    private function sort(DataTableInterface $dataTable, Request $request): void
    {
        if (!$dataTable->getConfig()->isSortingEnabled()) {
            return;
        }

        $parameterName = $dataTable->getConfig()->getSortParameterName();
        $sortingData = $this->extractQueryParameter($request, $parameterName);

        if (!is_array($sortingData)) {
            return;
        }

        if ($sortingData === []) {
            return;
        }

        $dataTable->sort(SortingData::fromArray($sortingData));
    }

    private function paginate(DataTableInterface $dataTable, Request $request): void
    {
        if (!$dataTable->getConfig()->isPaginationEnabled()) {
            return;
        }

        $defaultPaginationData = $dataTable->getConfig()->getDefaultPaginationData();

        $pageParameterName = $dataTable->getConfig()->getPageParameterName();
        $perPageParameterName = $dataTable->getConfig()->getPerPageParameterName();

        $page = $this->extractQueryParameter($request, $pageParameterName);
        $perPage = $this->extractQueryParameter($request, $perPageParameterName);

        $perPage ??= $defaultPaginationData?->getPerPage() ?? PaginationInterface::DEFAULT_PER_PAGE;

        if ($page === null) {
            return;
        }

        $dataTable->paginate(new PaginationData((int) $page, (int) $perPage));
    }

    private function personalize(DataTableInterface $dataTable, Request $request): void
    {
        if (!$dataTable->getConfig()->isPersonalizationEnabled()) {
            return;
        }

        $form = $dataTable->createPersonalizationFormBuilder()->getForm();
        $isSubmittedAndValid = $this->submitForm($form, $request);

        if (!$isSubmittedAndValid) {
            return;
        }

        /** @var PersonalizationData $personalizationData */
        $personalizationData = $form->getData();

        $dataTable->personalize($personalizationData);
    }

    private function selectColumnVisibilityGroup(DataTableInterface $dataTable, Request $request): void
    {
        $parameterName = $dataTable->getConfig()->getColumnVisibilityGroupParameterName();
        $columnVisibilityGroup = $this->extractQueryParameter($request, $parameterName);
        $columnVisibilityGroups = $dataTable->getConfig()->getColumnVisibilityGroups();

        if (!is_string($columnVisibilityGroup)) {
            return;
        }

        if (!array_key_exists($columnVisibilityGroup, $columnVisibilityGroups)) {
            return;
        }

        $dataTable->setColumnVisibilityGroup($columnVisibilityGroup);
    }

    private function export(DataTableInterface $dataTable, Request $request): void
    {
        if (!$dataTable->getConfig()->isExportingEnabled()) {
            return;
        }

        $form = $dataTable->createExportFormBuilder()->getForm();
        $isSubmittedAndValid = $this->submitForm($form, $request);

        if (!$isSubmittedAndValid) {
            return;
        }

        /** @var ExportData|null $exportData */
        $exportData = $form->getData();

        $dataTable->setExportData($exportData);
    }

    private function turbo(DataTableInterface $dataTable, Request $request): void
    {
        $turboFrameId = $request->headers->get('Turbo-Frame');

        if ($turboFrameId === null) {
            return;
        }

        $dataTable->setTurboFrameId($turboFrameId);
    }

    private function submitForm(FormInterface $form, Request $request): bool
    {
        $data = $this->getRequestParameter($request, $form->getName());
        $isDataEmpty = in_array($data, [null, [], '', '0'], strict: true);

        if ($isDataEmpty) {
            return false;
        }

        $form->submit($data);

        return $form->isValid();
    }

    /**
     * @return array<mixed>|string|null
     */
    private function getRequestParameter(Request $request, string $name): array|string|null
    {
        $parameterBag = $request->query->has($name) ? $request->query : $request->request;
        $parameters = $parameterBag->all();

        return $this->getArrayOrStringValue($parameters[$name] ?? null);
    }

    /**
     * @return array<mixed>|string|null
     */
    private function extractQueryParameter(Request $request, string $name): array|string|null
    {
        $parameters = $request->query->all();

        return $this->getArrayOrStringValue($this->propertyAccessor->getValue($parameters, "[{$name}]"));
    }

    /**
     * @return array<mixed>|string|null
     */
    private function getArrayOrStringValue(mixed $value): array|string|null
    {
        $isArrayOrString = is_array($value) || is_string($value);

        return $isArrayOrString ? $value : null;
    }
}
