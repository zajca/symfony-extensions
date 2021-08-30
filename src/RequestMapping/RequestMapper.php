<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping;

use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zajca\Extensions\RequestMapping\Domain\Attribute\SourceAttributeInterface;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\CustomMappedRequestObject;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\InvalidDataException;

class RequestMapper
{
    public function __construct(
        private DenormalizerInterface $serializer,
        private ValidatorInterface $validator,
        private RequestDataExtractor $extractor
    ) {
    }

    /**
     * @param class-string $targetClass
     *
     * @throws InvalidDataException
     */
    public function mapRequestToObject(
        Request $request,
        string $targetClass,
        ?SourceAttributeInterface $overwriteSource
    ): object {
        $data = $this->extractor->getDataForRequestObjectFromRequest(
            $request,
            $targetClass,
            $overwriteSource
        );
        $ref = new ReflectionClass($targetClass);
        if ($ref->implementsInterface(CustomMappedRequestObject::class)) {
            $obj = $ref->getMethod('mapFromRequestData')->invoke(null, $data);
        } else {
            $obj = $this->denormalize($data, $targetClass);
        }
        $this->validate($obj);

        return $obj;
    }

    /**
     * @param array<mixed> $data
     */
    private function denormalize(array $data, string $class): object
    {
        try {
            if (!empty($data)) {
                $dto = $this->serializer->denormalize($data, $class, null, [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);
            } else {
                $dto = new $class();
            }

            return $dto;
        } catch (NotNormalizableValueException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        }
    }

    private function validate(object $dto): void
    {
        $violations = $this->validator->validate($dto);
        if ($violations->count() > 0) {
            throw new InvalidDataException($violations);
        }
    }
}
