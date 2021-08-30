<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\RequestExtractor;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\HttpHeadersExtractor;

class HttpHeadersExtractorTest extends TestCase
{
    public function testContent(): void
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [
                'SOME_SERVER_VARIABLE' => 'value',
                'SOME_SERVER_VARIABLE2' => 'value',
                'ROOT' => 'value',
                'HTTP_CONTENT_TYPE' => 'text/html',
                'HTTP_CONTENT_LENGTH' => '0',
                'HTTP_ETAG' => 'asdf',
                'PHP_AUTH_USER' => 'foo',
                'PHP_AUTH_PW' => 'bar',
            ],
        );

        self::assertSame([
            'content-type' => ['text/html'],
            'content-length' => ['0'],
            'etag' => ['asdf'],
            'php-auth-user' => ['foo'],
            'php-auth-pw' => ['bar'],
            'authorization' => ['Basic '.base64_encode('foo:bar')],
        ], (new HttpHeadersExtractor())->content($request));
    }
}
