<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestObject;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: [RequestObjectClassResolverInterface::TAG])]
interface RequestObjectClassResolverInterface
{
    public const TAG = 'zajca.extensions.requestObjectClassResolver';

    /**
     * @return class-string
     */
    public function resolve(object $preflight): string;
}
