<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Stubs;

use Webmozart\Assert\Assert;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\RequestObjectClassResolverInterface;

class Resolver implements RequestObjectClassResolverInterface
{
    /**
     * @param Preflight $preflight
     *
     * @return class-string
     */
    public function resolve(object $preflight): string
    {
        Assert::isInstanceOf($preflight, Preflight::class);

        return $preflight->getClassName();
    }
}
