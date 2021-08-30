<?php

declare(strict_types=1);

namespace Zajca\Extensions;

final class Environment
{
    private const ENV_DEV = 'dev';

    public function __construct(
        private string $appEnv
    ) {
    }

    public function isDev(): bool
    {
        return self::ENV_DEV === $this->appEnv;
    }
}
