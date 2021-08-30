<?php

declare(strict_types=1);

namespace Zajca\Extensions\Exception;

use Throwable;

interface ExceptionInterface extends Throwable
{
    /**
     * @return array<string, mixed>
     */
    public function params(): array;

    public function httpStatusCode(): int;

    public function stringCode(): ?string;

    public function title(): string;

    public function detail(): ?string;
}
