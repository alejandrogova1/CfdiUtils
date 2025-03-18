<?php

namespace CfdiUtilsTests\ConsultaCfdiSat;

use CfdiUtils\Cfdi;
use CfdiUtils\ConsultaCfdiSat\RequestParameters;
use CfdiUtilsTests\TestCase;

final class RequestParametersTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $parameters = new RequestParameters(
            '3.3',
            'EKU9003173C9',
            'COSC8001137NA',
            '1,234.5678',
            'CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC',
            '0123456789'
        );
        $this->assertSame('3.3', $parameters->getVersion());
        $this->assertSame('EKU9003173C9', $parameters->getRfcEmisor());
        $this->assertSame('COSC8001137NA', $parameters->getRfcReceptor());
        $this->assertSame('1,234.5678', $parameters->getTotal());
        $this->assertEqualsWithDelta(1234.5678, $parameters->getTotalFloat(), 0.0000001);
        $this->assertSame('CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC', $parameters->getUuid());
        $this->assertSame('0123456789', $parameters->getSello());

        $expected40 = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx'
            . '?id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC'
            . '&re=EKU9003173C9'
            . '&rr=COSC8001137NA'
            . '&tt=1234.5678'
            . '&fe=23456789';
        $this->assertSame($expected40, $parameters->expression());

        $expected33 = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx'
            . '?id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC'
            . '&re=EKU9003173C9'
            . '&rr=COSC8001137NA'
            . '&tt=1234.5678'
            . '&fe=23456789';
        $this->assertSame($expected33, $parameters->expression());

        $expected32 = ''
            . '?re=EKU9003173C9'
            . '&rr=COSC8001137NA'
            . '&tt=0000001234.567800'
            . '&id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC';
        $parameters->setVersion('3.2');
        $this->assertSame($expected32, $parameters->expression());
    }

    public function testConstructorWithWrongVersion(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('version');

        new RequestParameters(
            '1.1',
            'EKU9003173C9',
            'COSC8001137NA',
            '1,234.5678',
            'CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC',
            '0123456789'
        );
    }

    public function testCreateFromCfdiVersion32(): void
    {
        $cfdi = Cfdi::newFromString(strval(file_get_contents($this->utilAsset('cfdi32-real.xml'))));
        $parameters = RequestParameters::createFromCfdi($cfdi);

        $this->assertSame('3.2', $parameters->getVersion());
        $this->assertSame('CTO021007DZ8', $parameters->getRfcEmisor());
        $this->assertSame('XAXX010101000', $parameters->getRfcReceptor());
        $this->assertSame('80824F3B-323E-407B-8F8E-40D83FE2E69F', $parameters->getUuid());
        $this->assertStringEndsWith('YRbgmmVYiA==', $parameters->getSello());
        $this->assertEqualsWithDelta(4685.00, $parameters->getTotalFloat(), 0.001);
    }

    public function testCreateFromCfdiVersion33(): void
    {
        $cfdi = Cfdi::newFromString(strval(file_get_contents($this->utilAsset('cfdi33-real.xml'))));
        $parameters = RequestParameters::createFromCfdi($cfdi);

        $this->assertSame('3.3', $parameters->getVersion());
        $this->assertSame('POT9207213D6', $parameters->getRfcEmisor());
        $this->assertSame('DIM8701081LA', $parameters->getRfcReceptor());
        $this->assertSame('CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC', $parameters->getUuid());
        $this->assertStringEndsWith('XmE4/OAgdg==', $parameters->getSello());
        $this->assertEqualsWithDelta(2010.01, $parameters->getTotalFloat(), 0.001);
    }

    public function testCreateFromCfdiVersion40(): void
    {
        $cfdi = Cfdi::newFromString(strval(file_get_contents($this->utilAsset('cfdi40-real.xml'))));
        $parameters = RequestParameters::createFromCfdi($cfdi);

        $this->assertSame('4.0', $parameters->getVersion());
        $this->assertSame('ISD950921HE5', $parameters->getRfcEmisor());
        $this->assertSame('COSC8001137NA', $parameters->getRfcReceptor());
        $this->assertSame('C2832671-DA6D-11EF-A83D-00155D012007', $parameters->getUuid());
        $this->assertStringEndsWith('FoYRhNjeNw==', $parameters->getSello());
        $this->assertEqualsWithDelta(1000.00, $parameters->getTotalFloat(), 0.001);
    }

    /**
     *
     * @testWith ["9.123456", "9.123456"]
     *           ["0.123456", "0.123456"]
     *           ["1", "1.0"]
     *           ["0.1", "0.1"]
     *           ["1.1", "1.1"]
     *           ["0", "0.0"]
     *           ["0.1234567", "0.123457"]
     */
    public function testExpressionTotalExamples(string $total, string $expected): void
    {
        $parameters = new RequestParameters(
            '3.3',
            'EKU9003173C9',
            'COSC8001137NA',
            $total,
            'CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC',
            '0123456789'
        );

        $this->assertStringContainsString('&tt=' . $expected . '&', $parameters->expression());
    }

    public function testRfcWithAmpersand(): void
    {
        /*
         * This is not an error. SAT is using XML encoding on URL instead of URL encoding,
         * this is why the ampersand `&` should be `&amp;` instead of `%26`, and `Ñ` is the same.
         */

        $parameters = new RequestParameters(
            '3.3',
            'Ñ&A010101AAA',
            'Ñ&A991231AA0',
            '1,234.5678',
            'CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC',
            '0123456789'
        );

        $this->assertSame('Ñ&A010101AAA', $parameters->getRfcEmisor());
        $this->assertSame('Ñ&A991231AA0', $parameters->getRfcReceptor());

        $expected32 = '?re=Ñ&amp;A010101AAA'
            . '&rr=Ñ&amp;A991231AA0'
            . '&tt=0000001234.567800'
            . '&id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC';
        $this->assertSame($expected32, $parameters->expressionVersion32());

        $expected33 = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx'
            . '?id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC'
            . '&re=Ñ&amp;A010101AAA'
            . '&rr=Ñ&amp;A991231AA0'
            . '&tt=1234.5678'
            . '&fe=23456789';
        $this->assertSame($expected33, $parameters->expressionVersion33());

        // Same as CFDI 3.3
        $this->assertSame($expected33, $parameters->expressionVersion40());
    }
}
