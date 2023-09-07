<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;

/**
 * @template TType
 *
 * @internal
 */
abstract class AbstractDependencyInjectionExtension
{
    public function __construct(
        private readonly ContainerInterface $typeContainer,
        private readonly array $typeExtensionServices = [],
    ) {
    }

    protected function doGetType(string $name)
    {
        if (!$this->typeContainer->has($name)) {
            throw new InvalidArgumentException(sprintf('The %s type "%s" is not registered in the service container.', $this->getErrorContextName(), $name));
        }

        return $this->typeContainer->get($name);
    }

    public function hasType(string $name): bool
    {
        return $this->typeContainer->has($name);
    }

    public function getTypeExtensions(string $name): array
    {
        $extensions = [];

        if (isset($this->typeExtensionServices[$name])) {
            foreach ($this->typeExtensionServices[$name] as $extension) {
                $extensions[] = $extension;

                $extendedTypes = [];
                foreach ($extension::getExtendedTypes() as $extendedType) {
                    $extendedTypes[] = $extendedType;
                }

                // validate the result of getExtendedTypes() to ensure it is consistent with the service definition
                if (!\in_array($name, $extendedTypes, true)) {
                    throw new InvalidArgumentException(sprintf('The extended %s type "%s" specified for the type extension class "%s" does not match any of the actual extended types (["%s"]).', $this->getErrorContextName(), $name, $extension::class, implode('", "', $extendedTypes)));
                }
            }
        }

        return $extensions;
    }

    public function hasTypeExtensions(string $name): bool
    {
        return isset($this->typeExtensionServices[$name]);
    }

    /**
     * @return class-string<TType>
     */
    abstract protected function getTypeClass(): string;

    abstract protected function getErrorContextName(): string;
}
