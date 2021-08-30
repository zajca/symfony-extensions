<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver;

interface ToArrayInterface
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;
}
