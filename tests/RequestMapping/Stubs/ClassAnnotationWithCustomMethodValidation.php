<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Stubs;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Zajca\Extensions\RequestMapping\Domain\Attribute\JsonPayload;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\CustomMethodValidation;
use Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver\ToArrayInterface;

#[JsonPayload]
class ClassAnnotationWithCustomMethodValidation implements ToArrayInterface, CustomMethodValidation
{
    #[Assert\IsNull] // is ignored
    private int $int;

    private string $string;

    private float $float;

    private bool $bool;

    private SubObj $subObj;

    private string $notSetValue;

    /**
     * @return Constraint[]
     */
    public static function getConstraint(): array
    {
        return [
            new Assert\Collection([
                'allowExtraFields' => true,
                'fields' => [
                    'int' => [
                        new Assert\NotNull(),
                    ],
                    'string' => [
                        new Assert\NotNull(),
                    ],
                ],
            ]),
        ];
    }

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

    /**
     * @return array<mixed>
     */
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
