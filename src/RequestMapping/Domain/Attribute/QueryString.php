<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\Attribute;

use Attribute;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\QueryStringExtractor;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class QueryString extends BaseAttribute
{
    public static function getExtractorClass(): string
    {
        return QueryStringExtractor::class;
    }
}
