<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\RequestObject;

use Zajca\Extensions\RequestMapping\Domain\RequestObject\RequestObjectClassResolverInterface;

class RequestObjectClassResolverInterfaceStub implements RequestObjectClassResolverInterface
{
    public function resolve(object $preflight): string
    {
        throw new \Exception();
        // TODO: Implement resolve() method.
    }
}
