<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\RequestExtractor;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\JsonPayloadException;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\JsonPayloadExtractor;

class JsonPayloadExtractorTest extends TestCase
{
    public function testContent(): void
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [],
            '{"i-am":"json"}'
        );

        self::assertEquals([
            'i-am' => 'json',
        ], (new JsonPayloadExtractor())->content($request));
    }

    public function testContentInvalidJson(): void
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [],
            '}"i-am":"json"}'
        );

        $this->expectException(JsonPayloadException::class);
        (new JsonPayloadExtractor())->content($request);
    }
}
