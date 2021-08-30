<?php

declare(strict_types=1);

namespace Zajca\Extensions\Route;

use Attribute;
use Symfony\Component\Routing\Annotation\Route;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class ApiRoute extends Route
{
    /**
     * @param array|mixed  $data
     * @param array<mixed> $requirements
     * @param array<mixed> $options
     * @param array<mixed> $defaults
     * @param array<mixed> $methods
     * @param array<mixed> $schemes
     */
    public function __construct(
        $data = [],
        string $path = null,
        float $sinceVersion = null,
        float $untilVersion = null,
        string $name = null,
        array $requirements = [],
        array $options = [],
        array $defaults = [],
        string $host = null,
        array $methods = [],
        array $schemes = [],
        string $condition = null,
        int $priority = null,
        string $locale = null,
        string $format = null,
        bool $utf8 = null,
        bool $stateless = null
    ) {
        if (null !== $sinceVersion) {
            $condition = sprintf('request.attributes.get(\'version\') >= %s', $sinceVersion);
            if (null !== $untilVersion) {
                $condition = sprintf('%s and request.attributes.get(\'version\') < %s', $condition, $untilVersion);
            }
        }

        parent::__construct($data, $path, $name, $requirements, $options, $defaults, $host, $methods, $schemes, $condition, $priority, $locale, $format, $utf8, $stateless);
    }
}
