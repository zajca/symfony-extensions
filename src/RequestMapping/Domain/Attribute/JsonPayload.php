<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\Attribute;

use Attribute;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\JsonPayloadExtractor;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class JsonPayload extends BaseAttribute
{
    public static function getExtractorClass(): string
    {
        return JsonPayloadExtractor::class;
    }
}
