<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestExtractor;

use JsonException;
use Symfony\Component\HttpFoundation\Request;

class JsonPayloadExtractor implements RequestExtractorInterface
{
    private const MAX_JSON_DEPTH = 512;

    public function content(Request $request): array
    {
        $content = $request->getContent();
        if (!is_string($content) || '' === $content) {
            return [];
        }

        try {
            $data = json_decode($content, true, self::MAX_JSON_DEPTH, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex) {
            throw JsonPayloadException::createInvalidPayload($ex);
        }

        if (null === $data) {
            throw JsonPayloadException::createEmptyBody();
        }

        return $data;
    }
}
