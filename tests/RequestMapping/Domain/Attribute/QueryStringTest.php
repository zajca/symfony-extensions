<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\Attribute;

use PHPUnit\Framework\TestCase;
use Zajca\Extensions\RequestMapping\Domain\Attribute\QueryString;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\QueryStringExtractor;

class QueryStringTest extends TestCase
{
    public function test(): void
    {
        $attr = new QueryString('query');
        self::assertSame($attr->getSourceName(), 'query');
        self::assertSame($attr::getExtractorClass(), QueryStringExtractor::class);
    }
}
