<?php

namespace CfdiUtilsTests\Validate\Cfdi33\RecepcionPagos\Pagos\DoctoRelacionado;

use CfdiUtils\Elements\Pagos10\DoctoRelacionado;
use CfdiUtils\Validate\Cfdi33\RecepcionPagos\Pagos\DoctoRelacionado\ImporteSaldoAnteriorValor;
use CfdiUtils\Validate\Cfdi33\RecepcionPagos\Pagos\DoctoRelacionado\ValidateDoctoException;
use PHPUnit\Framework\TestCase;

final class ImporteSaldoAnteriorValorTest extends TestCase
{
    /**
     * @param string $input
     * @testWith ["0.01"]
     *           ["123456.78"]
     */
    public function testValid(string $input): void
    {
        $docto = new DoctoRelacionado([
            'ImpSaldoAnt' => $input,
        ]);
        $validator = new ImporteSaldoAnteriorValor();
        $validator->setIndex(0);

        $this->assertTrue($validator->validateDoctoRelacionado($docto));
    }

    /**
     * @param string|null $input
     * @testWith ["0"]
     *           ["-123.45"]
     *           [""]
     *           [null]
     */
    public function testInvalid(?string $input): void
    {
        $docto = new DoctoRelacionado([
            'ImpSaldoAnt' => $input,
        ]);
        $validator = new ImporteSaldoAnteriorValor();
        $validator->setIndex(0);

        $this->expectException(ValidateDoctoException::class);
        $validator->validateDoctoRelacionado($docto);
    }
}
