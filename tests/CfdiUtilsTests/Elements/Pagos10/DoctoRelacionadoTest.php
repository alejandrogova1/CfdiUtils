<?php

namespace CfdiUtilsTests\Elements\Pagos10;

use CfdiUtils\Elements\Pagos10\DoctoRelacionado;
use PHPUnit\Framework\TestCase;

final class DoctoRelacionadoTest extends TestCase
{
    /** @var DoctoRelacionado */
    public $element;

    protected function setUp(): void
    {
        parent::setUp();
        $this->element = new DoctoRelacionado();
    }

    public function testConstructedObject(): void
    {
        $this->assertSame('pago10:DoctoRelacionado', $this->element->getElementName());
    }
}
