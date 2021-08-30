<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\Attribute;

use PHPUnit\Framework\TestCase;
use Zajca\Extensions\RequestMapping\Domain\Attribute\HttpHeader;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\HttpHeadersExtractor;

class HttpHeaderTest extends TestCase
{
    public function test(): void
    {
        $attr = new HttpHeader('x-my-header');
        self::assertSame($attr->getSourceName(), 'x-my-header');
        self::assertSame($attr::getExtractorClass(), HttpHeadersExtractor::class);
    }
}
