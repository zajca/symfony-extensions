<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestExtractor;

use Symfony\Component\HttpFoundation\Request;

class HttpHeadersExtractor implements RequestExtractorInterface
{
    public function content(Request $request): array
    {
        return $request->headers->all();
    }
}
