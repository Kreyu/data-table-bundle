<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Model;

use Symfony\Contracts\Translation\TranslatableInterface;

class User
{
    public function __construct(
        public null|string|TranslatableInterface $firstName = null,
    ) {
    }

    public function getFirstNameUppercased(): string
    {
        return strtoupper($this->firstName);
    }
}