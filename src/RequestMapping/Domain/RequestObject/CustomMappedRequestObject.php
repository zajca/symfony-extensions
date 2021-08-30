<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestObject;

interface CustomMappedRequestObject
{
    /**
     * @param array<mixed> $data
     */
    public static function mapFromRequestData(array $data): object;
}
