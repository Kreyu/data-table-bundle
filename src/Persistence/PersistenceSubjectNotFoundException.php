<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

class PersistenceSubjectNotFoundException extends \Exception
{
    public function __construct(string $message = 'Persistence subject not found')
    {
        parent::__construct($message);
    }

    public static function createForProvider(PersistenceSubjectProviderInterface $provider): static
    {
        return new static(sprintf('Persistence subject not found by the "%s"', get_class($provider)));
    }
}
