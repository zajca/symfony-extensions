<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;
use Zajca\Extensions\RequestMapping\Domain\Attribute\SourceAttributeInterface;
use Zajca\Extensions\RequestMapping\Domain\RequestExtractor\RequestExtractorInterface;

class RequestDataExtractor
{
    /**
     * @var SourceAttributeInterface[]
     */
    private array $sources;

    /**
     * @param iterable<SourceAttributeInterface> $sources
     * @param ServiceLocator                     $extractors
     */
    public function __construct(
        #[TaggedIterator(SourceAttributeInterface::TAG)] iterable $sources,
        #[TaggedLocator(RequestExtractorInterface::TAG)] private ContainerInterface $extractors
    ) {
        // convert to array as generator obtained cannot be rewinded
        $this->sources = $sources instanceof \Traversable ? iterator_to_array($sources) : $sources;
    }

    /**
     * @param class-string $class
     *
     * @return array<mixed>
     */
    public function getDataForRequestObjectFromRequest(
        Request $request,
        string $class,
        ?SourceAttributeInterface $overwriteSource
    ): array {
        $reader = new RequestObjectAttributeReader(
            new ReflectionClass($class),
            $overwriteSource
        );

        /**
         * @var array<class-string<SourceAttributeInterface>,array<string, mixed>> $extractedData
         */
        $extractedData = [];
        foreach ($this->sources as $source) {
            if ($reader->hasParameterWithAttribute($source::class)) {
                /** @var RequestExtractorInterface $extractor */
                $extractor = $this->extractors->get($source::getExtractorClass());
                $extractedData[$source::class] = $extractor->content($request);
            }
        }

        if (null !== $overwriteSource) {
            /** @var RequestExtractorInterface $extractor */
            $extractor = $this->extractors->get($overwriteSource::getExtractorClass());
            $extractedData[$overwriteSource::class] = $extractor->content($request);
        }

        $outputData = [];
        $propertiesAttributes = $reader->getPropertiesAttributes();
        if (0 === count($propertiesAttributes)) {
            $outputData = $extractedData;
        } else {
            foreach ($propertiesAttributes as $prop) {
                $attr = $prop->getAttribute();
                $sourceName = $prop->getSourcePropertyName();
                Assert::inArray($attr::class, array_keys($extractedData), 'Expected SourceAttribute: %2$s is probably missing extractor.');
                if (array_key_exists($sourceName, $extractedData[$attr::class])) {
                    $outputData[$prop->getTargetPropertyName()] = $extractedData[$attr::class][$sourceName];
                } else {
                    $outputData[$prop->getTargetPropertyName()] = null;
                }
            }
        }

        return $outputData;
    }
}
