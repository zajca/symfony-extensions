<?php

declare(strict_types=1);

namespace Zajca\Extensions\Exception;

use Psr\Log\LoggerInterface;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Uid\Uuid;
use Zajca\Extensions\Environment;
use Zajca\Extensions\Exception\Attribute\ExceptionUrl;

class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
        private Environment $env
    ) {
    }

    /**
     * @return array<string, array{0:string, 1:?int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => ['onKernelException', 255],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $message = $exception->getMessage();

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $headers = [];
        $isInternalError = true;
        if ($exception instanceof HttpExceptionInterface) {
            $isInternalError = false;
            $statusCode = $exception->getStatusCode();
            $headers = $exception->getHeaders();
        }
        if (u($message)->isEmpty()) {
            $message = \array_key_exists($statusCode, Response::$statusTexts) ? Response::$statusTexts[$statusCode] : 'error';
        }

        $responseData = [
            'status' => $statusCode,
            'title' => $message,
            'exceptionId' => (Uuid::v4())->toRfc4122(),
        ];

        if ($exception instanceof ExceptionInterface) {
            // return form validation exceptions
            $statusCode = $exception->httpStatusCode();
            $responseData['title'] = $exception->title();
            if (null !== $exception->detail()) {
                $responseData['detail'] = $exception->detail();
            }
            $responseData['params'] = $exception->params();
            $responseData['stringCode'] = $exception->stringCode();
            $responseData['status'] = $statusCode;
        }

        if ($exception instanceof PublicException) {
            $isInternalError = false;
        }

        if ($isInternalError) {
            if ($this->env->isDev()) {
                $responseData['stackTrace'] = $exception->getTraceAsString();
            }
            $this->logger->critical($responseData['title'], $responseData);
            if (!$this->env->isDev()) {
                $responseData['title'] = 'Internal error.';
                $responseData['detail'] = 'Please contact support.';
            }
        }

        $ref = new ReflectionClass($exception);

        /** @var array<ReflectionAttribute> $attributes */
        $attributes = $ref->getAttributes(ExceptionUrl::class, ReflectionAttribute::IS_INSTANCEOF);
        if (1 === count($attributes)) {
            /** @var ExceptionUrl $attribute */
            $attribute = $attributes[0]->newInstance();
            $responseData['url'] = $attribute->url();
        }

        $json = $this->serializer->serialize($responseData, 'json');
        $response = new JsonResponse(null, $statusCode, $headers);
        $response->setJson($json);
        $event->setResponse($response);
        $response->headers->set('Content-Type', 'application/problem+json');
        $event->stopPropagation();
    }
}
