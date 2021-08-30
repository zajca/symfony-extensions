<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\Attribute;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\RequestExtractorInterface;

#[Autoconfigure(tags: [SourceAttributeInterface::TAG])]
interface SourceAttributeInterface
{
    public const TAG = 'zajca.extensions.sourceAttribute';

    public function getSourceName(): ?string;

    /**
     * @return class-string<RequestExtractorInterface>
     */
    public static function getExtractorClass(): string;
}
