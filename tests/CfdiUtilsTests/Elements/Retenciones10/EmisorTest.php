<?php

namespace CfdiUtilsTests\Elements\Retenciones10;

use CfdiUtils\Elements\Retenciones10\Emisor;
use PHPUnit\Framework\TestCase;

final class EmisorTest extends TestCase
{
    /** @var Emisor */
    public $element;

    public function setUp(): void
    {
        parent::setUp();
        $this->element = new Emisor();
    }

    public function testGetElementName(): void
    {
        $this->assertSame('retenciones:Emisor', $this->element->getElementName());
    }
}
