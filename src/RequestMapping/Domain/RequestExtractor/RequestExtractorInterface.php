<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestExtractor;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;

#[Autoconfigure(tags: [RequestExtractorInterface::TAG])]
interface RequestExtractorInterface
{
    public const TAG = 'zajca.extenstions.requestExtractor';

    /**
     * @return array<string, mixed>
     */
    public function content(Request $request): array;
}
