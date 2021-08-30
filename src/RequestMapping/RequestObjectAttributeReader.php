<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping;

use ReflectionAttribute;
use ReflectionClass;
use Zajca\Extensions\RequestMapping\Domain\Attribute\SourceAttributeInterface;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\RequestObjectProperty;

class RequestObjectAttributeReader
{
    /**
     * @var RequestObjectProperty[]
     */
    private array $propCache = [];

    private ?SourceAttributeInterface $classScopeMappingAttribute = null;

    /**
     * @param ReflectionClass<object> $ref
     */
    public function __construct(
        private ReflectionClass $ref,
        ?SourceAttributeInterface $overwriteSource
    ) {
        $attributes = $ref->getAttributes(SourceAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        if (1 === count($attributes)) {
            /** @var SourceAttributeInterface $attribute */
            $attribute = $attributes[0]->newInstance();
            $this->classScopeMappingAttribute = $attribute;
        }
        if (null !== $overwriteSource) {
            $this->classScopeMappingAttribute = $overwriteSource;
        }
    }

    public function isRequestObject(): bool
    {
        return count($this->getPropertiesAttributes()) > 0;
    }

    /**
     * @return RequestObjectProperty[]
     */
    public function getPropertiesAttributes(): array
    {
        if (0 !== count($this->propCache)) {
            return $this->propCache;
        }

        $result = [];
        foreach ($this->ref->getProperties() as $prop) {
            $propertyScopeAttributes = $prop->getAttributes(SourceAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            if (count($propertyScopeAttributes) > 1) {
                throw new \LogicException(sprintf('Property "%s" has more than one "%s".', $prop->getName(), SourceAttributeInterface::class));
            }
            if (1 === count($propertyScopeAttributes)) {
                /** @var SourceAttributeInterface $attribute */
                $attribute = $propertyScopeAttributes[0]->newInstance();

                $result[] = new RequestObjectProperty(
                    $attribute->getSourceName() ?? $prop->getName(),
                    $prop->getName(),
                    $attribute
                );
            } elseif (null !== $this->classScopeMappingAttribute) {
                $result[] = new RequestObjectProperty(
                    $prop->getName(),
                    $prop->getName(),
                    $this->classScopeMappingAttribute
                );
            }
            // ignore when property has no attribute and no global attribute
        }

        $this->propCache = $result;

        return $result;
    }

    /**
     * @param class-string<SourceAttributeInterface> $attributeClassName
     */
    public function hasParameterWithAttribute(string $attributeClassName): bool
    {
        return $this->hasAttributeInstanceOf($attributeClassName);
    }

    /**
     * @param class-string<SourceAttributeInterface> $expectedInstance
     */
    private function hasAttributeInstanceOf(string $expectedInstance): bool
    {
        foreach ($this->getPropertiesAttributes() as $prop) {
            if ($prop->getAttribute() instanceof $expectedInstance) {
                return true;
            }
        }

        return false;
    }
}
