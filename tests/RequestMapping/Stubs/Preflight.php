<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Stubs;

use Symfony\Component\Validator\Constraints as Assert;
use Zajca\Extensions\RequestMapping\Domain\Attribute\RouteParameter;
use Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver\ToArrayInterface;

class Preflight implements ToArrayInterface
{
    /**
     * @var class-string
     */
    #[Assert\NotNull]
    #[RouteParameter]
    private string $className;

    /**
     * @return class-string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param class-string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    public function toArray(): array
    {
        return [
            'className' => $this->className,
        ];
    }
}
