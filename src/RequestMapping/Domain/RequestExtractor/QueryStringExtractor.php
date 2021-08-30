<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestExtractor;

use Symfony\Component\HttpFoundation\Request;

class QueryStringExtractor implements RequestExtractorInterface
{
    public function content(Request $request): array
    {
        return $request->query->all();
    }
}
