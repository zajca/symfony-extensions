<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Stubs;

use Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver\ToArrayInterface;

class SubObj implements ToArrayInterface
{
    public string $test = '';

    public function getTest(): string
    {
        return $this->test;
    }

    public function setTest(string $test): void
    {
        $this->test = $test;
    }

    /**
     * @return array{test: string}
     */
    public function toArray(): array
    {
        return ['test' => $this->test];
    }
}
