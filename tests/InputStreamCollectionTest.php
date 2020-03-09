<?php declare(strict_types=1);

namespace Parable\GetSet\Tests;

use Parable\GetSet\InputStreamCollection;
use Parable\GetSet\Exception;
use PHPUnit\Framework\TestCase;

class InputStreamTest extends TestCase
{
    public function testInputStreamThrowsExceptionIfSourceUnreadable(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Could not read from input source 'This file definitely does not exist'.");

        new class extends InputStreamCollection
        {
            protected const INPUT_SOURCE = 'This file definitely does not exist';
        };
    }

    public function testJsonParsedCorrectly(): void
    {
        $inputStream = new class extends InputStreamCollection
        {
            protected const INPUT_SOURCE = __DIR__ . '/Files/InputSourceJson.json';
        };

        self::assertSame(
            [
                'test' => 'value-from-json',
            ],
            $inputStream->getAll()
        );
    }

    public function testParameterStringParsedCorrectly(): void
    {
        $inputStream = new class extends InputStreamCollection
        {
            protected const INPUT_SOURCE = __DIR__ . '/Files/InputSourceRaw.txt';
        };

        self::assertSame(
            [
                'test' => 'value-from-raw',
            ],
            $inputStream->getAll()
        );
    }

    public function testGetAndGetAllMethodsAllWork(): void
    {
        $inputStream = new class extends InputStreamCollection
        {
            protected const INPUT_SOURCE = __DIR__ . '/Files/InputSourceRaw.txt';
        };

        self::assertSame(
            [
                'test' => 'value-from-raw',
            ],
            $inputStream->getAll()
        );
        self::assertSame('value-from-raw', $inputStream->get('test'));
    }
}
