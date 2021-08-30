<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\Attribute;

abstract class BaseAttribute implements SourceAttributeInterface
{
    public function __construct(
        private ?string $sourceName = null
    ) {
    }

    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }
}
