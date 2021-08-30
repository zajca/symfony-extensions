<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestObject;

use Zajca\Extensions\RequestMapping\Domain\Attribute\SourceAttributeInterface;

final class RequestObjectProperty
{
    public function __construct(
        private string $sourcePropertyName,
        private string $targetPropertyName,
        private SourceAttributeInterface $attribute
    ) {
    }

    public function getSourcePropertyName(): string
    {
        return $this->sourcePropertyName;
    }

    public function getTargetPropertyName(): string
    {
        return $this->targetPropertyName;
    }

    public function getAttribute(): SourceAttributeInterface
    {
        return $this->attribute;
    }
}
