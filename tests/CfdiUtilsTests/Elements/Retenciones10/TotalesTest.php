<?php

namespace CfdiUtilsTests\Elements\Retenciones10;

use CfdiUtils\Elements\Retenciones10\ImpRetenidos;
use CfdiUtils\Elements\Retenciones10\Totales;
use PHPUnit\Framework\TestCase;

final class TotalesTest extends TestCase
{
    public Totales $element;

    protected function setUp(): void
    {
        parent::setUp();
        $this->element = new Totales();
    }

    public function testGetElementName(): void
    {
        $this->assertSame('retenciones:Totales', $this->element->getElementName());
    }

    public function testAddCfdiRelacionado(): void
    {
        // no childs
        $this->assertCount(0, $this->element);

        // add first child
        $first = $this->element->addImpRetenidos(['name' => 'first']);
        $this->assertInstanceOf(ImpRetenidos::class, $first);
        $this->assertSame('first', $first['name']);
        $this->assertCount(1, $this->element);

        // add second child
        $second = $this->element->addImpRetenidos();
        $this->assertCount(2, $this->element);

        // test that first and second are not the same
        $this->assertNotSame($first, $second);
    }

    public function testAddImpRetenidos(): void
    {
        $first = $this->element->addImpRetenidos(['var' => 'FOO']);
        $this->assertInstanceOf(ImpRetenidos::class, $first);
        $this->assertSame('FOO', $first['var']);
        $this->assertCount(1, $this->element);
    }

    public function testMultiImpRetenidos(): void
    {
        $self = $this->element->multiImpRetenidos(
            ['var' => 'FOO'],
            ['var' => 'BAR']
        );
        $this->assertSame($this->element, $self);
        $this->assertCount(2, $this->element);
        $this->assertSame('FOO', $this->element->children()->get(0)['var']);
        $this->assertSame('BAR', $this->element->children()->get(1)['var']);
    }
}
