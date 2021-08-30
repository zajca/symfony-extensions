<?php

declare(strict_types=1);

namespace Zajca\Extensions\Tests\RequestMapping\Domain\RequestObject;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Zajca\Extensions\RequestMapping\Domain\RequestObject\InvalidDataException;

class InvalidDataExceptionTest extends TestCase
{
    public function test(): void
    {
        $violationsList = new ConstraintViolationList([
            new ConstraintViolation(
                'error',
                null,
                [],
                'obj',
                'obj.name',
                'val',
                null,
            ),
            new ConstraintViolation(
                'error2',
                null,
                [],
                'obj',
                'obj.xxx',
                'val',
                null,
            ),
        ]);
        $exception = new InvalidDataException(
            $violationsList
        );

        self::assertSame('Your request parameters didn\'t validate.', $exception->getMessage());
        self::assertSame('Your request parameters didn\'t validate.', $exception->title());
        self::assertSame('error', $exception->detail());
        self::assertSame(
            [
                [
                    'name' => 'obj.name',
                    'reason' => 'error',
                    'value' => '"val"',
                ],
                [
                    'name' => 'obj.xxx',
                    'reason' => 'error2',
                    'value' => '"val"',
                ],
            ],
            $exception->params()
        );
        self::assertSame($violationsList, $exception->violationList());
    }
}
