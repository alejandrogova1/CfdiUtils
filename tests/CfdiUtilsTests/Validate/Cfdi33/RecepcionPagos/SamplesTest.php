<?php

namespace CfdiUtilsTests\Validate\Cfdi33\RecepcionPagos;

use CfdiUtils\CfdiValidator33;
use CfdiUtils\Validate\Status;
use CfdiUtilsTests\TestCase;

final class SamplesTest extends TestCase
{
    /**
     * @testWith ["sample-factura123.xml"]
     *           ["sample-facturador01.xml"]
     *           ["sample-facturador02.xml"]
     *           ["sample-validacfd01.xml"]
     *           ["sample-validacfd02.xml"]
     *           ["sample-validacfd03.xml"]
     *           ["sample-validacfd04.xml"]
     *           ["sample-validacfd05.xml"]
     */
    public function testSamplesFiles(string $sampleName): void
    {
        $sampleFile = $this->utilAsset('pagos10/' . $sampleName);
        $this->assertFileExists($sampleFile);

        $validator = new CfdiValidator33();
        $asserts = $validator->validateXml(strval(file_get_contents($sampleFile)));
        // Remove these assertions because we are using manipulated cfdi
        $asserts->removeByCode('SELLO08');
        $errors = $asserts->errors();
        if ($errors !== []) { // display errors!
            echo PHP_EOL, 'source: ', $sampleName;
            foreach ($asserts->errors() as $error) {
                echo PHP_EOL, ' *** ', strval($error), ' => ', $error->getExplanation();
            }
        }
        $this->assertFalse($asserts->hasErrors());
    }

    public function testSamplesWithErrors(): void
    {
        $sampleFile = $this->utilAsset('pagos10/sample-errors.xml');
        $this->assertFileExists($sampleFile);

        $validator = new CfdiValidator33();
        $asserts = $validator->validateXml(strval(file_get_contents($sampleFile)));
        // Remove this tests! we are using manipulated cfdi
        $asserts->removeByCode('SELLO08');
        $asserts->removeByCode('EMISORRFC01');

        // Check that this codes are in error state
        $expectedErrorCodes = [
            'PAGO09', // MontoBetweenIntervalSumOfDocuments
            'PAGO09-00',
            'PAGO17', // CuentaBeneficiariaProhibida
            'PAGO17-00',
            'PAGO18',  // CuentaBeneficiariaPatron
            'PAGO18-00',
            'PAGO28',  // ImporteSaldoInsolutoValor
            'PAGO28-00',
            'PAGO28-00-00',
        ];
        foreach ($expectedErrorCodes as $expectedErrorCode) {
            $this->assertEquals(Status::error(), $asserts->get($expectedErrorCode)->getStatus());
            $asserts->removeByCode($expectedErrorCode);
        }
        $this->assertFalse($asserts->hasErrors(), 'Asserts has more errors than expected');
    }
}
