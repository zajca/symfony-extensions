<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\Attribute;

use PHPUnit\Framework\TestCase;
use Zajca\Extensions\RequestMapping\Domain\Attribute\RouteParameter;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\RouteParametersExtractor;

class RouteParameterTest extends TestCase
{
    public function test(): void
    {
        $attr = new RouteParameter('query');
        self::assertSame($attr->getSourceName(), 'query');
        self::assertSame($attr::getExtractorClass(), RouteParametersExtractor::class);
    }
}
