<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\RequestExtractor;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\QueryStringExtractor;

class QueryStringExtractorTest extends TestCase
{
    public function testContent(): void
    {
        $request = new Request(
            ['q' => 'search-me'],
        );

        self::assertEquals([
            'q' => 'search-me',
        ], (new QueryStringExtractor())->content($request));
    }
}
