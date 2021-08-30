<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestExtractor;

use JsonException;
use Symfony\Component\HttpFoundation\Response;
use Zajca\Extensions\Exception\PublicException;

class JsonPayloadException extends PublicException
{
    public static function createInvalidPayload(JsonException $exception): self
    {
        return new self(
            'Invalid json request.',
            'Provided json payload is not valid json object.',
            [],
            Response::HTTP_BAD_REQUEST,
            null,
            $exception
        );
    }

    public static function createEmptyBody(): self
    {
        return new self('The request body could not be decoded.');
    }
}
