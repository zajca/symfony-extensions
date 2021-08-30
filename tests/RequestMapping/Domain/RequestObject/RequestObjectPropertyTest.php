<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\RequestObject;

use PHPUnit\Framework\TestCase;
use Zajca\Extensions\RequestMapping\Domain\Attribute\JsonPayload;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\RequestObjectProperty;

final class RequestObjectPropertyTest extends TestCase
{
    public function test(): void
    {
        $attribute = new JsonPayload('propName');
        $testInstance = new RequestObjectProperty(
            'propName',
            'targetProp',
            $attribute
        );
        self::assertSame('propName', $testInstance->getSourcePropertyName());
        self::assertSame('targetProp', $testInstance->getTargetPropertyName());
        self::assertSame($attribute, $testInstance->getAttribute());
    }
}
