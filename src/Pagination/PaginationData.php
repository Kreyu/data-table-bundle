<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginationData
{
    public function __construct(
        private int $page = PaginationInterface::DEFAULT_PAGE,
        private ?int $perPage = PaginationInterface::DEFAULT_PER_PAGE,
    ) {}

    /**
     * @param array{page: int, perPage: int} $data
     */
    public static function fromArray(array $data): self
    {
        ($resolver = new OptionsResolver())
            ->setDefault('page', null)
            ->setDefault('perPage', null)
            ->addNormalizer('page', static fn (Options $options, mixed $value) => $value !== null ? (int) $value : null)
            ->addNormalizer('perPage', static fn (Options $options, mixed $value) => $value !== null
                ? (int) $value
                : null)
            ->setAllowedValues('page', static fn (int $value): bool => $value > 0)
            ->setAllowedValues('perPage', static fn (?int $value): bool => $value === null || $value > 0)
        ;

        /** @var array{page: int, perPage: int|null} $resolvedData */
        $resolvedData = $resolver->resolve($data);

        return new self($resolvedData['page'], $resolvedData['perPage']);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getPerPage(): ?int
    {
        return $this->perPage;
    }

    public function setPerPage(?int $perPage): void
    {
        $this->perPage = $perPage;
    }

    public function getOffset(): int
    {
        if ($this->perPage === null) {
            return 0;
        }

        return $this->perPage * ($this->page - 1);
    }
}
