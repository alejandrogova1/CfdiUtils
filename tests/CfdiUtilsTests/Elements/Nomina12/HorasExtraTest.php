<?php

namespace CfdiUtilsTests\Elements\Nomina12;

use CfdiUtils\Elements\Nomina12\HorasExtra;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CfdiUtils\Elements\Nomina12\HorasExtra
 */
final class HorasExtraTest extends TestCase
{
    /** @var HorasExtra */
    public $element;

    protected function setUp(): void
    {
        parent::setUp();
        $this->element = new HorasExtra();
    }

    public function testConstructedObject(): void
    {
        $this->assertSame('nomina12:HorasExtra', $this->element->getElementName());
    }
}
