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
    ) {
    }

    /**
     * @param array{page: int, perPage: int} $data
     */
    public static function fromArray(array $data): self
    {
        ($resolver = new OptionsResolver())
            ->setDefault('page', null)
            ->setDefault('perPage', null)
            ->addNormalizer('page', function (Options $options, mixed $value) {
                return null !== $value ? (int) $value : null;
            })
            ->addNormalizer('perPage', function (Options $options, mixed $value) {
                return null !== $value ? (int) $value : null;
            })
            ->setAllowedValues('page', function (int $value): bool {
                return $value > 0;
            })
            ->setAllowedValues('perPage', function (?int $value): bool {
                return null === $value || $value > 0;
            })
        ;

        $data = $resolver->resolve($data);

        return new self($data['page'], $data['perPage']);
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
        return $this->perPage * ($this->page - 1);
    }
}
