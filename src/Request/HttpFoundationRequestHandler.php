<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Request;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
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
        if (null === $request) {
            return;
        }

        if (!$request instanceof Request) {
            throw new UnexpectedTypeException($request, Request::class);
        }

        $this->filter($dataTable, $request);
        $this->sort($dataTable, $request);
        $this->personalize($dataTable, $request);
        $this->paginate($dataTable, $request);
        $this->export($dataTable, $request);
    }

    private function filter(DataTableInterface $dataTable, Request $request): void
    {
        if (!$dataTable->getConfig()->isFiltrationEnabled()) {
            return;
        }

        $form = $dataTable->createFiltrationFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $dataTable->filter($form->getData());
        }
    }

    private function sort(DataTableInterface $dataTable, Request $request): void
    {
        if (!$dataTable->getConfig()->isSortingEnabled()) {
            return;
        }

        $parameterName = $dataTable->getConfig()->getSortParameterName();

        $sortingData = $this->extractQueryParameter($request, "[$parameterName]");

        if (empty($sortingData)) {
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

        $page = $this->extractQueryParameter($request, "[$pageParameterName]");
        $perPage = $this->extractQueryParameter($request, "[$perPageParameterName]");

        $perPage ??= $defaultPaginationData?->getPerPage() ?? PaginationInterface::DEFAULT_PER_PAGE;

        if (null === $page) {
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
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $dataTable->personalize($form->getData());
        }
    }

    private function export(DataTableInterface $dataTable, Request $request): void
    {
        if (!$dataTable->getConfig()->isExportingEnabled()) {
            return;
        }

        $form = $dataTable->createExportFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $dataTable->export($form->getData());
        }
    }

    private function extractQueryParameter(Request $request, string $path): mixed
    {
        return $this->propertyAccessor->getValue($request->query->all(), $path);
    }
}
