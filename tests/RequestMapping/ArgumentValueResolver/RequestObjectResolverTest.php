<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeZoneNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ProblemNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zajca\Extensions\RequestMapping\ArgumentValueResolver\RequestObjectResolver;
use Zajca\Extensions\RequestMapping\Domain\Attribute\HttpHeader;
use Zajca\Extensions\RequestMapping\Domain\Attribute\JsonPayload;
use Zajca\Extensions\RequestMapping\Domain\Attribute\QueryString;
use Zajca\Extensions\RequestMapping\Domain\Attribute\RouteParameter;
use Zajca\Extensions\RequestMapping\Domain\Attribute\SourceAttributeInterface;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\HttpHeadersExtractor;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\JsonPayloadExtractor;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\QueryStringExtractor;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\RouteParametersExtractor;
use Zajca\Extensions\RequestMapping\RequestDataExtractor;
use Zajca\Extensions\RequestMapping\RequestMapper;
use Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver\Example\ControllerStub;
use Zajca\Extensions\Tests\RequestMapping\Stubs\ClassAnnotation;
use Zajca\Extensions\Tests\RequestMapping\Stubs\ClassPropsAnnotation;
use Zajca\Extensions\Tests\RequestMapping\Stubs\NoAnnotation;
use Zajca\Extensions\Tests\RequestMapping\Stubs\Resolver;

class RequestObjectResolverTest extends TestCase
{
    public function testSupportOfNotSupportedClass(): void
    {
        $request = new Request([], [], ['_route_params' => ['id' => 15]]);

        $argument = (new ArgumentMetadataFactory())->createArgumentMetadata([
            ControllerStub::class,
            'notResolvedObjectAction',
        ])[0];

        $resolver = new RequestObjectResolver($this->getRequestMapper(), new ServiceLocator([]));
        self::assertFalse($resolver->supports($request, $argument));
    }

    private function getRequestMapper(): RequestMapper
    {
        return new RequestMapper(
            $this->getDenormalizer(),
            $this->getValidator(),
            $this->getRequestDataExtractor()
        );
    }

    private function getDenormalizer(): DenormalizerInterface
    {
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ProblemNormalizer(),
            new JsonSerializableNormalizer(),
            new DateTimeNormalizer(),
            new ConstraintViolationListNormalizer(),
            new DateTimeZoneNormalizer(),
            new DateIntervalNormalizer(),
            new DataUriNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, $extractor),
        ];

        return new Serializer($normalizers, $encoders);
    }

    private function getValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
    }

    private function getRequestDataExtractor(): RequestDataExtractor
    {
        return new RequestDataExtractor(
            $this->getSourceAttributes(),
            $this->getExtractors(),
        );
    }

    /**
     * @return \Generator<SourceAttributeInterface>
     */
    private function getSourceAttributes(): \Generator
    {
        yield new HttpHeader();
        yield new JsonPayload();
        yield new QueryString();
        yield new RouteParameter();
    }

    private function getExtractors(): ServiceLocator
    {
        return new ServiceLocator([
            HttpHeadersExtractor::class => function () {
                return new HttpHeadersExtractor();
            },
            JsonPayloadExtractor::class => function () {
                return new JsonPayloadExtractor();
            },
            QueryStringExtractor::class => function () {
                return new QueryStringExtractor();
            },
            RouteParametersExtractor::class => function () {
                return new RouteParametersExtractor();
            },
        ]);
    }

    public function testSupportOfNotExistingClass(): void
    {
        $request = new Request([], [], ['_route_params' => ['id' => 5]]);
        $argument = $this->createArgumentMetadata('NotExistingClass');

        $resolver = new RequestObjectResolver($this->getRequestMapper(), new ServiceLocator([]));
        self::assertFalse($resolver->supports($request, $argument));
    }

    /**
     * @param object[] $attributes
     */
    private function createArgumentMetadata(string $class, array $attributes = []): ArgumentMetadata
    {
        return new ArgumentMetadata('requestObj', $class, false, false, null, false, $attributes);
    }

    public function testSupportWithNull(): void
    {
        $request = new Request([], [], ['_route_params' => ['id' => 5]]);
        $argument = new ArgumentMetadata('routeParameterDto', null, false, false, null);

        $resolver = new RequestObjectResolver($this->getRequestMapper(), new ServiceLocator([]));
        self::assertFalse($resolver->supports($request, $argument));
    }

    /**
     * @param array<mixed> $expectedOutput
     * @dataProvider validRequestsProvider
     */
    public function testValid(Request $request, string $methodName, array $expectedOutput): void
    {
        $argument = (new ArgumentMetadataFactory())->createArgumentMetadata([
            ControllerStub::class,
            $methodName,
        ])[0];

        $resolver = new RequestObjectResolver(
            $this->getRequestMapper(),
            new ServiceLocator([
                Resolver::class => function () {
                    return new Resolver();
                },
            ])
        );
        self::assertTrue($resolver->supports($request, $argument));
        /** @var \Generator<ToArrayInterface> $result */
        $result = $resolver->resolve($request, $argument);
        self::assertSame($expectedOutput, $result->current()->toArray());
    }

    /**
     * @return \Generator<string, array{request: Request, methodName: string, expectedOutput: array<mixed>}>
     */
    public function validRequestsProvider(): \Generator
    {
        yield 'class scope attribute' => [
            'request' => new Request(
                [],
                [],
                ['_route_params' => ['id' => 5]],
                [],
                [],
                [],
                json_encode([
                    'int' => 10,
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ], JSON_THROW_ON_ERROR),
            ),
            'methodName' => 'classAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'mixed props, class scope attribute' => [
            'request' => new Request(
                [
                    'float' => 10.5,
                ],
                [],
                ['_route_params' => ['int' => 10]],
                [],
                [],
                [],
                json_encode([
                    'string' => 'myText',
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ], JSON_THROW_ON_ERROR),
            ),
            'methodName' => 'classAttrWithPropertiesAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'mixed props, class scope attribute with renaming' => [
            'request' => new Request(
                [
                    'floatRename' => 10.5,
                ],
                [],
                ['_route_params' => ['intRename' => 10]],
                [],
                [],
                [],
                json_encode([
                    'string' => 'myText',
                    'bootRename' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ], JSON_THROW_ON_ERROR),
            ),
            'methodName' => 'classAttrWithPropertiesRenameAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'mixed props, class scope attribute with custom mapping' => [
            'request' => new Request(
                [
                    'floatRename' => 10.5,
                ],
                [],
                ['_route_params' => ['intRename' => 10]],
                [],
                [],
                [],
                json_encode([
                    'string' => 'myText',
                    'bootRename' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ], JSON_THROW_ON_ERROR),
            ),
            'methodName' => 'classAttrWithPropertiesCustomMappingAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'action argument scope attribute' => [
            'request' => new Request(
                [
                    'int' => 10,
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ],
                [],
            ),
            'methodName' => 'argumentAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'action argument scope attribute overwrites class scope' => [
            'request' => new Request(
                [
                    'int' => 10,
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ],
                [],
            ),
            'methodName' => 'argumentAttrWithClassAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'action argument scope attribute overwrites class scope, props persists' => [
            'request' => new Request(
                [
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ],
                [],
                ['_route_params' => ['int' => 10]],
            ),
            'methodName' => 'argumentAttrWithClassAttrWithPropertyAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'resolver class scope attribute' => [
            'request' => new Request(
                [],
                [],
                ['_route_params' => ['id' => 5, 'className' => ClassAnnotation::class]],
                [],
                [],
                [],
                json_encode([
                    'int' => 10,
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ], JSON_THROW_ON_ERROR),
            ),
            'methodName' => 'resolverClassAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'resolver mixed props, class scope attribute' => [
            'request' => new Request(
                [
                    'float' => 10.5,
                ],
                [],
                ['_route_params' => ['int' => 10, 'className' => ClassPropsAnnotation::class]],
                [],
                [],
                [],
                json_encode([
                    'string' => 'myText',
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ], JSON_THROW_ON_ERROR),
            ),
            'methodName' => 'resolverClassAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'resolver action argument scope attribute' => [
            'request' => new Request(
                [
                    'int' => 10,
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ],
                [],
                ['_route_params' => ['int' => 100, 'className' => NoAnnotation::class]],
            ),
            'methodName' => 'resolverArgumentAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'resolver action argument scope attribute overwrites class scope' => [
            'request' => new Request(
                [
                    'int' => 10,
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ],
                [],
                ['_route_params' => ['int' => 100, 'className' => ClassAnnotation::class]],
            ),
            'methodName' => 'resolverArgumentAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];

        yield 'resolver action argument scope attribute overwrites class scope, props persists' => [
            'request' => new Request(
                [
                    'int' => 100,
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ],
                [],
                ['_route_params' => ['int' => 10, 'className' => ClassPropsAnnotation::class]],
            ),
            'methodName' => 'resolverArgumentAttrAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];
        yield 'custom method validation' => [
            'request' => new Request(
                [],
                [],
                ['_route_params' => ['id' => 5]],
                [],
                [],
                [],
                json_encode([
                    'int' => 10,
                    'string' => 'myText',
                    'float' => 10.5,
                    'bool' => false,
                    'subObj' => [
                        'test' => 'title',
                    ],
                ], JSON_THROW_ON_ERROR),
            ),
            'methodName' => 'resolverWithCustomMethodValidationAction',
            'expectedOutput' => [
                'int' => 10,
                'string' => 'myText',
                'float' => 10.5,
                'bool' => false,
                'subObj' => [
                    'test' => 'title',
                ],
            ],
        ];
    }
}
