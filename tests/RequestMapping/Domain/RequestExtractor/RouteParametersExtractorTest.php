<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\RequestExtractor;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\RouteParametersExtractor;

class RouteParametersExtractorTest extends TestCase
{
    public function testContent(): void
    {
        $request = new Request(
            [],
            [],
            [
                '_route_params' => [
                    'name' => '/api/v1',
                ],
            ],
        );

        self::assertEquals([
            'name' => '/api/v1',
        ], (new RouteParametersExtractor())->content($request));
    }
}
