<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\ArgumentValueResolver;

use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Zajca\Extensions\RequestMapping\Domain\Attribute\SourceAttributeInterface;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\Attribute\ResolveClass;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\RequestObjectClassResolverInterface;
use Zajca\Extensions\RequestMapping\RequestMapper;
use Zajca\Extensions\RequestMapping\RequestObjectAttributeReader;

class RequestObjectResolver implements ArgumentValueResolverInterface
{
    /**
     * @param RequestMapper  $requestMapper
     * @param ServiceLocator $resolversLocator
     */
    public function __construct(
        private RequestMapper $requestMapper,
        #[TaggedLocator(RequestObjectClassResolverInterface::TAG)] private ContainerInterface $resolversLocator
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        // no type and non existent classes should be ignored
        if (
            !is_string($argument->getType())
            || '' === $argument->getType()
            || !(class_exists($argument->getType()) || interface_exists($argument->getType()))
        ) {
            return false;
        }

        // parameter attribute
        if ($argument->getAttributes(SourceAttributeInterface::class, ArgumentMetadata::IS_INSTANCEOF)) {
            return true;
        }

        // class attribute
        $ref = new ReflectionClass($argument->getType());
        $attributes = $ref->getAttributes(SourceAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        if (1 === count($attributes)) {
            return true;
        }

        // request object class will determinate by resolver
        if (null !== $this->getClassResolverAttribute($argument)) {
            return true;
        }

        // property based
        $reader = new RequestObjectAttributeReader($ref, null);

        return $reader->isRequestObject();
    }

    private function getClassResolverAttribute(
        ArgumentMetadata $argument
    ): ?ResolveClass {
        $attributes = $argument->getAttributes(ResolveClass::class, ArgumentMetadata::IS_INSTANCEOF);
        $nOfAttributes = count($attributes);

        if (0 === $nOfAttributes) {
            return null;
        }
        if (1 === $nOfAttributes) {
            /** @var ResolveClass $attribute */
            $attribute = $attributes[0];

            return $attribute;
        }

        throw new \LogicException(sprintf('More than one "%s" attribute used for argument "%s".', ResolveClass::class, $argument->getName()));
    }

    /**
     * @return \Generator<object>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @var class-string $requestObjectClass */
        $requestObjectClass = $argument->getType();

        $classResolver = $this->getClassResolverAttribute($argument);
        if (null !== $classResolver) {
            if (!$this->resolversLocator->has($classResolver->getClassResolver())) {
                throw new \LogicException(sprintf('Request object class resolver "%s" was not found in container.', $classResolver->getClassResolver()));
            }

            $preflight = $this->requestMapper->mapRequestToObject(
                $request,
                $classResolver->getPreflightObject(),
                null
            );
            /** @var RequestObjectClassResolverInterface $resolver */
            $resolver = $this->resolversLocator->get($classResolver->getClassResolver());
            $requestObjectClass = $resolver->resolve($preflight);
        }

        /** @var SourceAttributeInterface[] $argumentAttributes */
        $argumentAttributes = $argument->getAttributes(SourceAttributeInterface::class, ArgumentMetadata::IS_INSTANCEOF);
        if (count($argumentAttributes) > 1) {
            throw new \LogicException(sprintf('More than one "%s" attribute used for argument "%s".', SourceAttributeInterface::class, $argument->getName()));
        }

        yield $this->requestMapper->mapRequestToObject(
            $request,
            $requestObjectClass,
            0 === count($argumentAttributes) ? null : $argumentAttributes[0]
        );
    }
}
