<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

use Kreyu\Bundle\DataTableBundle\Exception\ExceptionInterface;

class PersistenceSubjectNotFoundException extends \Exception implements ExceptionInterface
{
    public function __construct(string $message = 'Persistence subject not found')
    {
        parent::__construct($message);
    }

    public static function createForProvider(PersistenceSubjectProviderInterface $provider): self
    {
        return new self(sprintf('Persistence subject not found by the "%s"', get_class($provider)));
    }
}
