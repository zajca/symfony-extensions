<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\RequestObject\Attribute;

use PHPUnit\Framework\TestCase;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\Attribute\ResolveClass;
use Zajca\Extensions\Tests\RequestMapping\Domain\RequestObject\PreflightStub;
use Zajca\Extensions\Tests\RequestMapping\Domain\RequestObject\RequestObjectClassResolverInterfaceStub;

class ResolveClassTest extends TestCase
{
    public function test(): void
    {
        $resolve = new ResolveClass(
            RequestObjectClassResolverInterfaceStub::class,
            PreflightStub::class
        );

        self::assertSame(RequestObjectClassResolverInterfaceStub::class, $resolve->getClassResolver());
        self::assertSame(PreflightStub::class, $resolve->getPreflightObject());
    }
}
