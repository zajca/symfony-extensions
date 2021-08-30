<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestExtractor;

use Symfony\Component\HttpFoundation\Request;

class RouteParametersExtractor implements RequestExtractorInterface
{
    public function content(Request $request): array
    {
        $params = $request->attributes->get('_route_params', []);

        foreach ($params as $key => $param) {
            $value = filter_var($param, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $params[$key] = $value ?? $param;
        }

        return $params;
    }
}
