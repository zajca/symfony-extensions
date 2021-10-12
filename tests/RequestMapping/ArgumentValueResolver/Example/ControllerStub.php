<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver\Example;

use Zajca\Extensions\RequestMapping\Domain\Attribute\QueryString;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\Attribute\ResolveClass;
use Zajca\Extensions\Tests\RequestMapping\Stubs\ClassAnnotation;
use Zajca\Extensions\Tests\RequestMapping\Stubs\ClassAnnotationWithCustomMethodValidation;
use Zajca\Extensions\Tests\RequestMapping\Stubs\ClassPropsAnnotation;
use Zajca\Extensions\Tests\RequestMapping\Stubs\ClassPropsRenameAnnotation;
use Zajca\Extensions\Tests\RequestMapping\Stubs\ClassPropsRenameAnnotationWithCustomMapping;
use Zajca\Extensions\Tests\RequestMapping\Stubs\EmptyObj;
use Zajca\Extensions\Tests\RequestMapping\Stubs\NoAnnotation;
use Zajca\Extensions\Tests\RequestMapping\Stubs\Preflight;
use Zajca\Extensions\Tests\RequestMapping\Stubs\Resolver;
use Zajca\Extensions\Tests\RequestMapping\Stubs\WithPreflightInterface;

class ControllerStub
{
    public function notResolvedObjectAction(EmptyObj $reqObj): void
    {
    }

    public function classAttrAction(ClassAnnotation $reqObj): void
    {
    }

    public function classAttrWithPropertiesAction(ClassPropsAnnotation $reqObj): void
    {
    }

    public function classAttrWithPropertiesRenameAction(ClassPropsRenameAnnotation $reqObj): void
    {
    }

    public function classAttrWithPropertiesCustomMappingAction(ClassPropsRenameAnnotationWithCustomMapping $reqObj): void
    {
    }

    public function argumentAttrAction(#[QueryString] NoAnnotation $reqObj): void
    {
    }

    public function argumentAttrWithClassAttrAction(#[QueryString] ClassAnnotation $reqObj): void
    {
    }

    public function argumentAttrWithClassAttrWithPropertyAttrAction(#[QueryString] ClassPropsAnnotation $reqObj): void
    {
    }

    public function resolverClassAttrAction(#[ResolveClass(Resolver::class, Preflight::class)] WithPreflightInterface $reqObj): void
    {
    }

    public function resolverArgumentAttrAction(
        #[ResolveClass(Resolver::class, Preflight::class)] #[QueryString] WithPreflightInterface $reqObj
    ): void {
    }

    public function resolverWithCustomMethodValidationAction(ClassAnnotationWithCustomMethodValidation $reqObj): void
    {
    }
}
