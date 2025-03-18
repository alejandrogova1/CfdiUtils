<?php

namespace CfdiUtilsTests\Certificado;

use CfdiUtils\Certificado\SerialNumber;
use PHPUnit\Framework\TestCase;

final class SerialNumberTest extends TestCase
{
    public function testAsDecimalAsAscii(): void
    {
        $input = '3330303031303030303030333030303233373038';
        $expectedDecimal = '292233162870206001759766198425879490508935868472';
        $expectedAscii = '30001000000300023708';
        $serial = new SerialNumber($input);
        $this->assertSame($input, $serial->getHexadecimal());
        $this->assertSame($expectedDecimal, $serial->asDecimal());
        $this->assertSame($expectedAscii, $serial->asAscii());
    }

    /**
     * @testWith ["3330303031303030303030333030303233373038", "30001000000300023708"]
     */
    public function testLoadHexadecimal(string $input, string $expected): void
    {
        $serial = new SerialNumber('');
        $serial->loadHexadecimal($input);
        $this->assertSame($input, $serial->getHexadecimal());
        $this->assertSame($expected, $serial->asAscii());
    }

    public function testLoadHexadecimalInvalidInput(): void
    {
        $serial = new SerialNumber('');
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('invalid characters');
        $serial->loadHexadecimal('X');
    }

    /**
     * @testWith ["0x3330303031303030303030333030303233373038", "30001000000300023708"]
     *           ["292233162870206001759766198425879490508935868472", "30001000000300023708"]
     */
    public function testLoadDecimal(string $input, string $expected): void
    {
        $serial = new SerialNumber('');
        $serial->loadDecimal($input);
        $this->assertSame($expected, $serial->asAscii());
    }

    /**
     * @testWith ["30001000000300023708", "3330303031303030303030333030303233373038"]
     */
    public function testLoadAscii(string $input, string $expected): void
    {
        $serial = new SerialNumber('');
        $serial->loadAscii($input);
        $this->assertSame($expected, $serial->getHexadecimal());
    }
}
