<?php

namespace CfdiUtilsTests\Elements\Tfd11;

use CfdiUtils\Elements\Cfdi33\Comprobante;
use CfdiUtils\Elements\Tfd11\TimbreFiscalDigital;
use PHPUnit\Framework\TestCase;

final class TimbreFiscalDigitalTest extends TestCase
{
    /**@var Comprobante */
    public $element;

    protected function setUp(): void
    {
        parent::setUp();
        $this->element = new TimbreFiscalDigital();
    }

    public function testGetElementName(): void
    {
        $this->assertSame('tfd:TimbreFiscalDigital', $this->element->getElementName());
    }

    public function testHasFixedAttributes(): void
    {
        $namespace = 'http://www.sat.gob.mx/TimbreFiscalDigital';
        $this->assertSame('1.1', $this->element['Version']);
        $this->assertSame($namespace, $this->element['xmlns:tfd']);
        $this->assertStringStartsWith($namespace . ' http://', $this->element['xsi:schemaLocation']);
    }
}
