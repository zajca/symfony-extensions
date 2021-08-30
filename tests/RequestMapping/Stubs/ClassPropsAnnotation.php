<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Stubs;

use Symfony\Component\Validator\Constraints as Assert;
use Zajca\Extensions\RequestMapping\Domain\Attribute\JsonPayload;
use Zajca\Extensions\RequestMapping\Domain\Attribute\QueryString;
use Zajca\Extensions\RequestMapping\Domain\Attribute\RouteParameter;
use Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver\ToArrayInterface;

#[JsonPayload]
class ClassPropsAnnotation implements WithPreflightInterface, ToArrayInterface
{
    #[Assert\NotNull]
    #[RouteParameter]
    private int $int;

    #[Assert\NotNull]
    private string $string;
    #[QueryString]
    private float $float;
    private bool $bool;

    #[Assert\Valid]
    private SubObj $subObj;

    private string $notSetValue;

    public function getInt(): int
    {
        return $this->int;
    }

    public function setInt(int $int): void
    {
        $this->int = $int;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function setString(string $string): void
    {
        $this->string = $string;
    }

    public function getFloat(): float
    {
        return $this->float;
    }

    public function setFloat(float $float): void
    {
        $this->float = $float;
    }

    public function isBool(): bool
    {
        return $this->bool;
    }

    public function setBool(bool $bool): void
    {
        $this->bool = $bool;
    }

    public function getNotSetValue(): ?string
    {
        return $this->notSetValue;
    }

    public function getSubObj(): SubObj
    {
        return $this->subObj;
    }

    public function setSubObj(SubObj $subObj): void
    {
        $this->subObj = $subObj;
    }

    public function toArray(): array
    {
        return [
            'int' => $this->int,
            'string' => $this->string,
            'float' => $this->float,
            'bool' => $this->bool,
            'subObj' => $this->subObj->toArray(),
        ];
    }
}
