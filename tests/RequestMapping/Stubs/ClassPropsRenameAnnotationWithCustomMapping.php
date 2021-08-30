<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Stubs;

use Symfony\Component\Validator\Constraints as Assert;
use Zajca\Extensions\RequestMapping\Domain\Attribute\JsonPayload;
use Zajca\Extensions\RequestMapping\Domain\Attribute\QueryString;
use Zajca\Extensions\RequestMapping\Domain\Attribute\RouteParameter;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\CustomMappedRequestObject;
use Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver\ToArrayInterface;

#[JsonPayload]
class ClassPropsRenameAnnotationWithCustomMapping implements ToArrayInterface, CustomMappedRequestObject
{
    #[Assert\NotNull]
    #[RouteParameter(sourceName: 'intRename')]
    private int $int;

    #[Assert\NotNull]
    private string $string;

    #[QueryString(sourceName: 'floatRename')]
    private float $float;

    #[JsonPayload(sourceName: 'bootRename')]
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

    public static function mapFromRequestData(array $data): object
    {
        $self = new self();
        $self->int = $data['int'];
        $self->string = $data['string'];
        $self->float = $data['float'];
        $self->bool = $data['bool'];
        $sub = new SubObj();
        $sub->setTest($data['subObj']['test']);
        $self->subObj = $sub;

        return $self;
    }
}
