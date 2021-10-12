<?php

declare(strict_types=1);

namespace Zajca\Extensions\RequestMapping\Domain\RequestObject;

use Symfony\Component\Validator\Constraint;

interface CustomMethodValidation
{
    /**
     * @return Constraint[]
     */
    public static function getConstraint(): array;
}
