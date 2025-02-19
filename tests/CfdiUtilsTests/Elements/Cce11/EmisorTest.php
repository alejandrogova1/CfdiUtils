<?php

namespace CfdiUtilsTests\Elements\Cce11;

use CfdiUtils\Elements\Cce11\Domicilio;
use CfdiUtils\Elements\Cce11\Emisor;
use PHPUnit\Framework\TestCase;

final class EmisorTest extends TestCase
{
    /** @var Emisor */
    public $element;

    protected function setUp(): void
    {
        parent::setUp();
        $this->element = new Emisor();
    }

    public function testConstructedObject(): void
    {
        $this->assertSame('cce11:Emisor', $this->element->getElementName());
    }

    public function testDomicilio(): void
    {
        // object is empty
        $this->assertCount(0, $this->element);

        // get retrieve and insert the element
        $first = $this->element->getDomicilio();
        $this->assertInstanceOf(Domicilio::class, $first);
        $this->assertCount(1, $this->element);

        // get (again) retrieve the same element
        $this->assertSame($first, $this->element->getDomicilio());
        $this->assertCount(1, $this->element);

        // add works with the same element
        $second = $this->element->addDomicilio(['foo' => 'bar']);
        $this->assertInstanceOf(Domicilio::class, $second);
        $this->assertCount(1, $this->element);
        $this->assertSame($second, $first);
        $this->assertSame('bar', $first['foo']);
    }
}
