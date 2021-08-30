<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestObject\Attribute;

use Attribute;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\RequestObjectClassResolverInterface;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ResolveClass
{
    /**
     * @param class-string<RequestObjectClassResolverInterface> $classResolver
     * @param class-string                                      $preflightObject
     */
    public function __construct(
        private string $classResolver,
        private string $preflightObject
    ) {
    }

    /**
     * @return class-string<RequestObjectClassResolverInterface>
     */
    public function getClassResolver(): string
    {
        return $this->classResolver;
    }

    /**
     * @return class-string
     */
    public function getPreflightObject(): string
    {
        return $this->preflightObject;
    }
}
