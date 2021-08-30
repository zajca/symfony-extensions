<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\Attribute;

use PHPUnit\Framework\TestCase;
use Zajca\Extensions\RequestMapping\Domain\Attribute\JsonPayload;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\JsonPayloadExtractor;

class JsonPayloadTest extends TestCase
{
    public function test(): void
    {
        $attr = new JsonPayload('x-my-header');
        self::assertSame($attr->getSourceName(), 'x-my-header');
        self::assertSame($attr::getExtractorClass(), JsonPayloadExtractor::class);
    }
}
